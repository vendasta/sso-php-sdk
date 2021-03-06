<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: sso/v1/service_provider.proto

namespace Sso\V1;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * Protobuf type <code>sso.v1.GetSessionTransferURLResponse</code>
 */
class GetSessionTransferURLResponse extends \Google\Protobuf\Internal\Message
{
    /**
     * <pre>
     * The session transfer URL to send the user to (which may result in a redirect to a login screen).
     * </pre>
     *
     * <code>string session_transfer_url = 1;</code>
     */
    private $session_transfer_url = '';

    public function __construct() {
        \GPBMetadata\Sso\V1\ServiceProvider::initOnce();
        parent::__construct();
    }

    /**
     * <pre>
     * The session transfer URL to send the user to (which may result in a redirect to a login screen).
     * </pre>
     *
     * <code>string session_transfer_url = 1;</code>
     */
    public function getSessionTransferUrl()
    {
        return $this->session_transfer_url;
    }

    /**
     * <pre>
     * The session transfer URL to send the user to (which may result in a redirect to a login screen).
     * </pre>
     *
     * <code>string session_transfer_url = 1;</code>
     */
    public function setSessionTransferUrl($var)
    {
        GPBUtil::checkString($var, True);
        $this->session_transfer_url = $var;
    }

}

