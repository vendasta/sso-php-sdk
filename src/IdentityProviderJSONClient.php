<?php
namespace Vendasta\Sso\V1;

use Google\Auth\ApplicationDefaultCredentials;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;

/**
 * The Identity Provider service provides RPC's for an identity provider application to complete the session transfer
 * process. All RPC's are accessed by an identity provider application, such as VBC, or a partner dashboard.
 */
class IdentityProviderJSONClient
{
    private $hostname;
    private $default_timeout;
    private $client;

    /**
     * @param string $hostname provided by Vendasta
     * @param string $scope provided by Vendasta
     * @param int $default_timeout the default timeout for RPC calls
     */
    public function __construct($hostname, $scope, $default_timeout = 10000)
    {
        $this->hostname = $hostname;
        $this->default_timeout = $default_timeout;

        $scopes = [$scope];
        $creds = ApplicationDefaultCredentials::getCredentials($scope, null, null, null);
        $middleware = new IDTokenMiddleware($creds);

        $stack = HandlerStack::create();
        $stack->push($middleware);

        $this->client = new Client([
            'handler' => $stack,
            'auth' => 'google_auth',
            'timeout' => $default_timeout / 1000,
        ]);
    }

    /**
     * Get the entry URL of a service provider.
     * @param string $service_provider_id The ID of the service provider (such as a marketplace application)
     * @param string $b64_str_service_context A base64 encoded JSON string representing a particular ServiceContext,
     * provided in the serviceContext query param of a session transfer handler request. The context helps form the
     * entry URL by providing parametrized fields, such as an account ID.
     */
    public function getEntryURL($service_provider_id, $b64_str_service_context)
    {
        $response = $this->client->request(
            'POST',
            'https://' . $this->hostname . '/sso.v1.IdentityProvider/GetEntryURL',
            [
                'json' => [
                    'service_provider_id' => $service_provider_id,
                    'context' => $this->buildContext($b64_str_service_context),
                ],
            ]
        );

        $body = (string) $response->getBody();
        $json_body = json_decode($body);
        return $json_body->entryUrl;
    }

    /**
     * Get the entry URL of a service provider with a code that will be exchanged for a user session.
     * @param string $service_provider_id The ID of the service provider (such as a marketplace application)
     * @param string $b64_str_service_context A base64 encoded JSON string representing a particular ServiceContext,
     * provided in the serviceContext query param of a session transfer handler request. The context helps form the
     * entry URL by providing parametrized fields, such as an account ID.
     * @param string $session_id The user's session ID. This can be the session ID directly from your application, or
     * it can be a hashed version, or something else unique. Whatever it is, the same session ID must be passed
     * to the logout RPC.
     * @param string $email The user's email
     * @param string $user_id The user's ID
     * @param string $next_url The next URL to send the user to, once the code exchange is complete on the entry URL
     */
    public function getEntryURLWithCode($service_provider_id, $b64_str_service_context, $session_id, $email, $user_id, $next_url = null)
    {
        $response = $this->client->request(
            'POST',
            'https://' . $this->hostname . '/sso.v1.IdentityProvider/GetEntryURLWithCode',
            [
                'json' => [
                    'service_provider_id' => $service_provider_id,
                    'context' => $this->buildContext($b64_str_service_context),
                    'session_id' => $session_id,
                    'email' => $email,
                    'user_id' => $user_id,
                    'next_url' => $next_url,
                ],
            ]
        );

        $body = (string) $response->getBody();
        $json_body = json_decode($body);
        return $json_body->entryUrl;
    }

    /**
     * Logout of the session for a user.
     * @param string $session_id The session ID that was provided to the getEntryURLWithCode RPC
     */
    public function logout($session_id)
    {
        $response = $this->client->request(
            'POST',
            'https://' . $this->hostname . '/sso.v1.IdentityProvider/Logout',
            [
                'json' => [
                    'session_id' => $session_id,
                ],
            ]
        );
    }

    private function buildContext($b64_str_service_context)
    {
        $json_str_service_context = base64_decode($b64_str_service_context);
        $json_service_context = json_decode($json_str_service_context);
        $service_context = [];
        switch ($json_service_context->_type) {
            case 'account':
                $service_context['account'] = [
                    'account_id' => $json_service_context->account_id,
                ];
                break;
            case 'partner':
                $service_context['partner'] = [
                    'partner_id' => $json_service_context->partner_id,
                ];
                break;
            case 'superadmin':
                $service_context['superadmin'] = [];
                break;
            default:
                throw new \InvalidArgumentException("Invalid context. The context may not be valid json, or it may be missing the '_type' field");
                break;
        }
        return $service_context;
    }
}
