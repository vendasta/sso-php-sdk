<?php
use PHPUnit\Framework\TestCase;

final class IdentityProviderGRPCClientTest extends TestCase{
    private static $client;

    public static function setUpBeforeClass() {
        $hostname = 'sso-api-test.vendasta-internal.com';
        $scope = 'https://sso-api-test.vendasta-internal.com';
        self::$client = new Vendasta\Sso\V1\IdentityProviderGRPCClient($hostname, $scope);
    }
    
    public function testGetEntryURL(): void {
        $entryURL = self::$client->getEntryURL('RM', 'eyJfdHlwZSI6ImFjY291bnQiLCJhY2NvdW50X2lkIjoiQUctVFBWNVRNRzUifQ==');
        $this->assertSame('http://abc.steprep-test-hrd.appspot.com/account/AG-TPV5TMG5/overview/', $entryURL);
    }

    public function testGetEntryURLWithCode(): void {
        $entryURL = self::$client->getEntryURLWithCode('RM', 'eyJfdHlwZSI6ImFjY291bnQiLCJhY2NvdW50X2lkIjoiQUctVFBWNVRNRzUifQ==', '123', 'byates+abc@vendasta.com', 'UID-123');
        $url = parse_url($entryURL);
        $entryURL = $url['scheme'].'://'.$url['host'].$url['path'];
        $this->assertSame('http://abc.steprep-test-hrd.appspot.com/account/AG-TPV5TMG5/overview/', $entryURL);
        $this->assertSame('code=', substr($url['query'], 0, 5));
        self::$client->logout('123');
    }
}