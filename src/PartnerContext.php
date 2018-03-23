<?php
namespace Vendasta\Sso\V1;

class PartnerContext
{
    private $partner_id = '';

    public function __construct($partner_id)
    {
        $this->partner_id = $partner_id;
    }

    public function getPartnerID()
    {
        return $this->partner_id;
    }
}
