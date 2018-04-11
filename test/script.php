<?php
require __DIR__ . '/vendor/autoload.php';

$hostname = 'sso-api-test.vendasta-internal.com';
$scope = 'https://sso-api-test.vendasta-internal.com';

$client = new Vendasta\Sso\V1\IdentityProviderClient($hostname, $scope);
$entryURL = $client->getEntryURL('RM', 'eyJfdHlwZSI6ImFjY291bnQiLCJhY2NvdW50X2lkIjoiQUctVFBWNVRNRzUifQ==');
echo $entryURL . "\n";

$entryURL = $client->getEntryURLWithCode('RM', 'eyJfdHlwZSI6ImFjY291bnQiLCJhY2NvdW50X2lkIjoiQUctVFBWNVRNRzUifQ==', '123', 'byates+abc@vendasta.com', 'UID-123');
echo $entryURL . "\n";

$client->logout('123');
