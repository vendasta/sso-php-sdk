<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: sso/v1/service_provider.proto

namespace Sso\V1;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * Protobuf type <code>sso.v1.GetSessionTransferURLRequest</code>
 */
class GetSessionTransferURLRequest extends \Google\Protobuf\Internal\Message
{
    /**
     * <pre>
     * An ID unique to a service/application (such as a marketplace application ID)
     * </pre>
     *
     * <code>string service_provider_id = 1;</code>
     */
    private $service_provider_id = '';
    /**
     * <pre>
     * The service context.
     * </pre>
     *
     * <code>.sso.v1.ServiceContext context = 2;</code>
     */
    private $context = null;

    public function __construct() {
        \GPBMetadata\Sso\V1\ServiceProvider::initOnce();
        parent::__construct();
    }

    /**
     * <pre>
     * An ID unique to a service/application (such as a marketplace application ID)
     * </pre>
     *
     * <code>string service_provider_id = 1;</code>
     */
    public function getServiceProviderId()
    {
        return $this->service_provider_id;
    }

    /**
     * <pre>
     * An ID unique to a service/application (such as a marketplace application ID)
     * </pre>
     *
     * <code>string service_provider_id = 1;</code>
     */
    public function setServiceProviderId($var)
    {
        GPBUtil::checkString($var, True);
        $this->service_provider_id = $var;
    }

    /**
     * <pre>
     * The service context.
     * </pre>
     *
     * <code>.sso.v1.ServiceContext context = 2;</code>
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * <pre>
     * The service context.
     * </pre>
     *
     * <code>.sso.v1.ServiceContext context = 2;</code>
     */
    public function setContext(&$var)
    {
        GPBUtil::checkMessage($var, \Sso\V1\ServiceContext::class);
        $this->context = $var;
    }

}

