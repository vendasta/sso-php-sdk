<?php
// GENERATED CODE -- DO NOT EDIT!

namespace Sso\V1;

/**
 * The Service Provider service provides RPC's for a service provider, such as a marketplace application, to initiate a 
 * session transfer. All RPC
 */
class ServiceProviderClient extends \Grpc\BaseStub {

    /**
     * @param string $hostname hostname
     * @param array $opts channel options
     * @param \Grpc\Channel $channel (optional) re-use channel object
     */
    public function __construct($hostname, $opts, $channel = null) {
        parent::__construct($hostname, $opts, $channel);
    }

    /**
     * Get the session transfer URL for a given context.
     * @param \Sso\V1\GetSessionTransferURLRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     */
    public function GetSessionTransferURL(\Sso\V1\GetSessionTransferURLRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/sso.v1.ServiceProvider/GetSessionTransferURL',
        $argument,
        ['\Sso\V1\GetSessionTransferURLResponse', 'decode'],
        $metadata, $options);
    }

}
