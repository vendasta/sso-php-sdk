<?php
// GENERATED CODE -- DO NOT EDIT!

namespace Sso\V1;

/**
 * The Identity Provider service provides RPC's for an identity provider application to complete the session transfer 
 * process. All RPC's are accessed by an identity provider application, such as VBC, or a partner dashboard.
 */
class IdentityProviderClient extends \Grpc\BaseStub {

    /**
     * @param string $hostname hostname
     * @param array $opts channel options
     * @param \Grpc\Channel $channel (optional) re-use channel object
     */
    public function __construct($hostname, $opts, $channel = null) {
        parent::__construct($hostname, $opts, $channel);
    }

    /**
     * Get the entry URL of a service provider.
     * @param \Sso\V1\GetEntryURLRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     */
    public function GetEntryURL(\Sso\V1\GetEntryURLRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/sso.v1.IdentityProvider/GetEntryURL',
        $argument,
        ['\Sso\V1\GetEntryURLResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * Get the entry URL of a service provider with a code that will be exchanged for a user session.
     * @param \Sso\V1\GetEntryURLWithCodeRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     */
    public function GetEntryURLWithCode(\Sso\V1\GetEntryURLWithCodeRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/sso.v1.IdentityProvider/GetEntryURLWithCode',
        $argument,
        ['\Sso\V1\GetEntryURLWithCodeResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * Logout of the session for a user.
     * @param \Sso\V1\LogoutRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     */
    public function Logout(\Sso\V1\LogoutRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/sso.v1.IdentityProvider/Logout',
        $argument,
        ['\Google\Protobuf\GPBEmpty', 'decode'],
        $metadata, $options);
    }

}
