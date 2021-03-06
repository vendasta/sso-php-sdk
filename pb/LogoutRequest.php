<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: sso/v1/identity_provider.proto

namespace Sso\V1;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * Protobuf type <code>sso.v1.LogoutRequest</code>
 */
class LogoutRequest extends \Google\Protobuf\Internal\Message
{
    /**
     * <pre>
     * The session ID to logout
     * </pre>
     *
     * <code>string session_id = 1;</code>
     */
    private $session_id = '';

    public function __construct() {
        \GPBMetadata\Sso\V1\IdentityProvider::initOnce();
        parent::__construct();
    }

    /**
     * <pre>
     * The session ID to logout
     * </pre>
     *
     * <code>string session_id = 1;</code>
     */
    public function getSessionId()
    {
        return $this->session_id;
    }

    /**
     * <pre>
     * The session ID to logout
     * </pre>
     *
     * <code>string session_id = 1;</code>
     */
    public function setSessionId($var)
    {
        GPBUtil::checkString($var, True);
        $this->session_id = $var;
    }

}

