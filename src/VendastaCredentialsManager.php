<?php
namespace Vendasta\Sso\V1;

use GuzzleHttp\Client;
use Mdanter\Ecc\Crypto\Signature\SignHasher;
use Mdanter\Ecc\EccFactory;
use Mdanter\Ecc\Crypto\Signature\Signer;
use Mdanter\Ecc\Serializer\PrivateKey\PemPrivateKeySerializer;
use Mdanter\Ecc\Serializer\PrivateKey\DerPrivateKeySerializer;
use Mdanter\Ecc\Crypto\Signature\Signature;
use Mdanter\Ecc\Random\RandomGeneratorFactory;

class VendastaCredentialsManager
{
    private $token_uri;
    private $key;
    private $email;
    private $key_id;
    private $client;

    public function __construct() {
        $jsonKey = getenv("VENDASTA_APPLICATION_CREDENTIALS");
        if (is_string($jsonKey)) {
            if (!file_exists($jsonKey)) {
                throw new \InvalidArgumentException('file does not exist');
            }
            $jsonKeyStream = file_get_contents($jsonKey);
            if (!$jsonKey = json_decode($jsonKeyStream, true)) {
                throw new \LogicException('invalid json for auth config');
            }
        } else {
            throw new \InvalidArgumentException('VENDASTA_APPLICATION_CREDENTIALS not set');
        }

        if (!array_key_exists('client_email', $jsonKey)) {
            throw new \InvalidArgumentException(
                'json key is missing the client_email field');
        }
        if (!array_key_exists('private_key', $jsonKey)) {
            throw new \InvalidArgumentException(
                'json key is missing the private_key field');
        }

        $this->token_uri = $jsonKey['token_uri'];
        $this->key = $jsonKey['private_key'];
        $this->email = $jsonKey['client_email'];
        $this->key_id = $jsonKey['private_key_id'];        

        $this->client = new Client([
            'timeout' => 5,
        ]);
    }

    public function __invoke(callable $handler)
    {
        return function (\Psr\Http\Message\RequestInterface $request, array $options) use ($handler) {
            $request = $request->withHeader('authorization', 'Bearer ' . $this->fetchAuthToken());
            return $handler($request, $options);
        };
    }

    private function fetchAuthToken() {
        $token = $this->buildJWT();

        $response = $this->client->request(
            'POST',
            $this->token_uri,
            [
                'json' => [
                    'token' => sprintf('%s', $token),
                ],
            ]
        );

        $body = (string) $response->getBody();
        $json_body = json_decode($body);
        return $json_body->token;
    }

    private function buildJWT() {
        $now = time();

        $header = [
            'typ' => 'JWT',
            'alg' => 'ES256',
        ];

        $token = [
            'sub' => $this->email,
            'aud' => 'vendasta.com',
            'iat' => $now,
            'exp' => $now + 3600,
            'kid' => $this->key_id,
        ];

        $adapter = EccFactory::getAdapter();
        $generator = EccFactory::getNistCurves()->generator256();
        $algorithm = 'sha256';
        $document = self::encode(json_encode($header, true)).'.'.self::encode(json_encode($token));
        $pemSerializer = new PemPrivateKeySerializer(new DerPrivateKeySerializer($adapter));
        $key = $pemSerializer->parse($this->key);
        $hasher = new SignHasher($algorithm, $adapter);
        $hash = $hasher->makeHash($document, $generator);
        $random = RandomGeneratorFactory::getRandomGenerator();
        $randomK = $random->generate($generator->getOrder());
        $signer = new Signer($adapter);
        $signature = $signer->sign($key, $hash, $randomK);

        $signed = $document.".".self::encode(self::createSignatureHash($signature, $adapter));
        return $signed;
    }

    private static function encode($data, $use_padding = false)
    {
        $encoded = strtr(base64_encode($data), '+/', '-_');
        return true === $use_padding ? $encoded : rtrim($encoded, '=');
    }

    private static function createSignatureHash(Signature $signature, $adapter)
    {
        $length = 64;
        return pack(
            'H*',
            sprintf(
                '%s%s',
                str_pad($adapter->decHex($signature->getR()), $length, '0', STR_PAD_LEFT),
                str_pad($adapter->decHex($signature->getS()), $length, '0', STR_PAD_LEFT)
            )
        );
    }
}