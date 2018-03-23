<?php
namespace Vendasta\Sso\V1;

class AccountContext
{
    private $account_id = '';

    public function __construct($account_id)
    {
        $this->account_id = $account_id;
    }

    public function getAccountID()
    {
        return $this->account_id;
    }
}
