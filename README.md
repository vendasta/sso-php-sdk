# vendasta/sso

## Description

This is Vendasta's official PHP SDK for integrating SSO. Both Identity Provider and Service Provider interfaces are provided through this SDK.

## Requirements

- PHP 5.5 and above or PHP 7.0 and above
- [PECL](https://pecl.php.net/) (may be used to install the required PHP extensions)
- [Composer](https://getcomposer.org/)
- [PHP gmp extension](http://php.net/manual/en/book.gmp.php)
- OPTIONAL (but recommended): [PHP grpc extension](https://cloud.google.com/php/grpc)

## Installation

Install the requirements from above, then:

```bash
composer require vendasta/sso
```

## Authentication

To authenticate your SDK calls, you must provision a service account from within the Vendasta platform.

_This feature is currently in alpha, therefore your service account credentials file will be provided to you manually._

You must put this file on your server, and set an environment variable to it's path:

```bash
export VENDASTA_APPLICATION_CREDENTIALS=<path to credentials.json>
```

## Client Initialization

It is highly recommended that you use a singleton client instance. Each client initilization will open it's own connection, therefore using a singleton results in reusing a connection, saving time and resources.

To instantiate the client:

```php
// Uncomment to use the sandbox environment, as setup for you by Vendasta
// $hostname = 'sso-api-demo.vendasta-internal.com';
// $scope = 'https://sso-api-demo.vendasta-internal.com';

// Production
$hostname = 'sso-api-prod.vendasta-internal.com';
$scope = 'https://sso-api-prod.vendasta-internal.com';

// Reuse this instance for all of your SDK calls
$client = new Vendasta\Sso\V1\IdentityProviderClient($hostname, $scope); // For Identity Providers
// $client = new Vendasta\Sso\V1\ServiceProviderClient($hostname, $scope); // For Service Providers
```

## Next Steps

At this point, your usage depends on whether you are using this SDK as an Identity Provider or a Service Provider. Identity Providers are applications which replace the Vendasta login screens, and provide the identity verification of users. Service Providers are applications that provide functionality for users, such as [Marketplace](https://www.vendasta.com/marketplace) applications.

## Identity Provider

As an identity provider, the functions in this SDK are provided to faciliate the login/logout SSO flow.

See the functions in `Vendasta\Sso\V1\IdentityProviderClient` for more information.

## Service Provider

As a service provider, the functions in this SDK are provided to initiate a session transfer from the identity provider, which is known as _service provider initiated SSO_.

_Service provider functionality is currently not yet implemented in this SDK. If you are a service provider and you want to use this SDK, please talk to your Vendasta representative about having it added_.

## Understanding Service Provider ID's and Service Context

The `Service Provider ID` is assigned by Vendasta, which is simply a unique identifier for describing a service. For example, `RM` is the service provider ID assigned to Reputation Management. Marketplace applications typically have a service provider ID that starts with `MP-`. _TODO: Add a function to this RPC to list service provider IDs._

The `Service Context` is a little bit harder to explain, but the concept is simple - the service context describes the resource that the user is attempting to access through the SSO process. A resource could be an account (business), a partner (typically represents screens that are not tied to a single account, such as a user profile screen), or several others. Unless your SSO configuration is setup for just-in-time identity and access management, you will not typically need to care about the service context - access checks will happen after the SSO process is completed, you simply have to pass the context through.

However, if you are using JiT IAM, you _must_ parse the service context and do access checks as a part of your SSO flow. 

_TODO: Link to a more in depth article on service context instructions._

## Development Notes

### Tests

To run the integration test suite, you must first set your credentials environment variable, then you can run phpunit directly from the vendor folder:

```bash
export VENDASTA_APPLICATION_CREDENTIALS=<path to credentials.json>
./vendor/bin/phpunit --bootstrap vendor/autoload.php test
```
