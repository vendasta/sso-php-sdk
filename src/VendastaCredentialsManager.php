<?php
namespace Vendasta\Sso\V1;

use GuzzleHttp\Client;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Keychain;
use Lcobucci\JWT\Signer\Ecdsa\Sha256;

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
        $signer = new Sha256();
        $keychain = new Keychain();

        $token = (new Builder())
            ->setSubject($this->email)
            ->setAudience('vendasta.com')
            ->setIssuedAt($now)
            ->setExpiration($now + 3600)
            ->set('kid', $this->key_id)
            ->sign($signer, $this->key)
            ->getToken();

        return $token;
    }
}