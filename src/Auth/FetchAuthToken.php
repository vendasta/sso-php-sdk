<?php
namespace Vendasta\Sso\V1\Auth;

interface FetchAuthToken {
    public function fetchToken():string;
    public function invalidateToken();
}
