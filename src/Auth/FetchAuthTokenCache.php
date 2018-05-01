<?php
namespace Vendasta\Sso\V1\Auth;

class FetchAuthTokenCache implements FetchAuthToken {
    private $fetcher;
    private $token;
    private $tokenExpiry;

    public function __construct(FetchAuthToken $fetcher) {
        $this->fetcher = $fetcher;
    }

    public function fetchToken():string {
        $now = time();
        if ($this->token == null || ($this->tokenExpiry != null && $this->tokenExpiry < $now)) {
            $this->token = $this->fetcher->fetchToken();
            $this->tokenExpiry = self::parseExpiry($this->token);
        }
        
        if ($this->token == null) {
            throw new Exception("Could not refresh token");
        }

        return $this->token;
    }

    public function invalidateToken() {
        $this->token = null;
        $this->tokenExpiry = null;
    }

    private static function parseExpiry(string $token):?int{
        if ($token == null) {
            return null;
        }

        $jwt_parts = explode(".", $token);
        if (sizeof($jwt_parts) !== 3) {
            return null;
        }

        $claims = json_decode(base64_decode($jwt_parts[1]));
        return $claims->exp;
    }
}
