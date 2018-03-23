<?php
namespace Vendasta\Sso\V1;

/**
 * The base client for all of the SDK clients
 */
class BaseClient
{
    protected function get_client_options($scope)
    {
        $auth = \Google\Auth\ApplicationDefaultCredentials::getCredentials($scope);

        $opts = [
            'credentials' => \Grpc\ChannelCredentials::createSsl(),
            'update_metadata' => function () use ($auth) {
                $result = $auth->fetchAuthToken(null);
                if (!isset($result['id_token'])) {
                    return $metadata;
                }
                $metadata_copy = $metadata;
                $metadata_copy[$auth::AUTH_METADATA_KEY] = array('Bearer ' . $result['id_token']);

                return $metadata_copy;
            },
        ];

        return $opts;
    }
}
