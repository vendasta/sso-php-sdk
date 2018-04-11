<?php
namespace Vendasta\Sso\V1;

/**
 * The Identity Provider service provides RPC's for an identity provider application to complete the session transfer
 * process. All RPC's are accessed by an identity provider application, such as VBC, or a partner dashboard.
 */
class IdentityProviderClient
{
    private $transport;

    /**
     * @param string $hostname provided by Vendasta
     * @param string $scope provided by Vendasta
     * @param int $default_timeout the default timeout for RPC calls
     */
    public function __construct($hostname, $scope, $default_timeout = 10000)
    {
        if (class_exists('Grpc\ChannelCredentials', true)) {
            $this->transport = new IdentityProviderGRPCClient($hostname, $scope, $default_timeout);
        } else {
            $this->transport = new IdentityProviderJSONClient($hostname, $scope, $default_timeout);
        }
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
        return $this->transport->getEntryURL($service_provider_id, $b64_str_service_context);
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
        return $this->transport->getEntryURLWithCode($service_provider_id, $b64_str_service_context, $session_id, $email, $user_id, $next_url);
    }

    /**
     * Logout of the session for a user.
     * @param string $session_id The session ID that was provided to the getEntryURLWithCode RPC
     */
    public function logout($session_id)
    {
        return $this->transport->logout($session_id);
    }
}
