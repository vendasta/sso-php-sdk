<?php
namespace Vendasta\Sso\V1;

/**
 * The Identity Provider service provides RPC's for an identity provider application to complete the session transfer
 * process. All RPC's are accessed by an identity provider application, such as VBC, or a partner dashboard.
 */
class IdentityProviderGRPCClient
{

    private $default_timeout;
    private $client;

    /**
     * @param string $hostname provided by Vendasta
     * @param string $scope provided by Vendasta
     * @param int $default_timeout the default timeout for RPC calls
     */
    public function __construct($hostname, $scope, $default_timeout = 10000)
    {
        $this->default_timeout = $default_timeout * 1000; // Microseconds
        $opts = $this->get_client_options($scope);
        $opts['timeout'] = $default_timeout;
        $this->client = new \Sso\V1\IdentityProviderClient($hostname, $opts);
    }

    protected function get_client_options($scope)
    {
        $auth = new VendastaCredentialsManager();

        $opts = [
            'credentials' => \Grpc\ChannelCredentials::createSsl(),
            'update_metadata' => function ($metadata) use ($auth) {
                $result = $auth->fetchAuthToken();
                $metadata_copy = $metadata;
                $metadata_copy['authorization'] = array('Bearer ' . $result);
                return $metadata_copy;
            },
        ];

        return $opts;
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
        $req = new \Sso\V1\GetEntryURLRequest();
        $req->setServiceProviderId($service_provider_id);
        $service_context = $this->buildContext($b64_str_service_context);
        $req->setContext($service_context);
        list($response, $status) = $this->client->getEntryURL($req)->wait();
        if ($status->code) {
            throw new SDKException($status->details);
        }
        return $response->getEntryUrl();
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
        $req = new \Sso\V1\GetEntryURLWithCodeRequest();
        $req->setServiceProviderId($service_provider_id);
        $service_context = $this->buildContext($b64_str_service_context);
        $req->setContext($service_context);
        $req->setSessionId($session_id);
        $req->setEmail($email);
        $req->setUserId($user_id);
        if ($next_url) {
            $req->setNextUrl($next_url);
        }
        list($response, $status) = $this->client->getEntryURLWithCode($req)->wait();
        if ($status->code) {
            throw new SDKException($status->details);
        }
        return $response->getEntryUrl();
    }

    /**
     * Logout of the session for a user.
     * @param string $session_id The session ID that was provided to the getEntryURLWithCode RPC
     */
    public function logout($session_id)
    {
        $req = new \Sso\V1\LogoutRequest();
        $req->setSessionId($session_id);
        list($response, $status) = $this->client->logout($req)->wait();
        if ($status->code) {
            throw new SDKException($status->details);
        }
    }

    private function buildContext($b64_str_service_context)
    {
        $json_str_service_context = base64_decode($b64_str_service_context);
        $json_service_context = json_decode($json_str_service_context);
        $service_context = new \Sso\V1\ServiceContext();
        switch ($json_service_context->_type) {
            case 'account':
                $account_context = new \Sso\V1\ServiceContext_Account();
                $account_context->setAccountId($json_service_context->account_id);
                $service_context->setAccount($account_context);
                break;
            case 'partner':
                $partner_context = new \Sso\V1\ServiceContext_Partner();
                $partner_context->setPartnerId($json_service_context->partner_id);
                $service_context->setPartner($partner_context);
                break;
            case 'superadmin':
                $superadmin_context = new \Sso\V1\ServiceContext_SuperAdmin();
                $service_context->setSuperAdmin($superadmin_context);
                break;
            default:
                throw new \InvalidArgumentException("Invalid context. The context may not be valid json, or it may be missing the '_type' field");
                break;
        }
        return $service_context;
    }
}
