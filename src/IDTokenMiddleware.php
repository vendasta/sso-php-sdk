<?php
namespace Vendasta\Sso\V1;

class IDTokenMiddleware
{
    private $fetcher;

    public function __construct(
        \Google\Auth\FetchAuthTokenInterface $fetcher,
        callable $httpHandler = null
    ) {
        $this->fetcher = $fetcher;
    }

    public function __invoke(callable $handler)
    {
        return function (\Psr\Http\Message\RequestInterface $request, array $options) use ($handler) {
            // Requests using "auth"="google_auth" will be authorized.
            if (!isset($options['auth']) || $options['auth'] !== 'google_auth') {
                return $handler($request, $options);
            }

            $request = $request->withHeader('authorization', 'Bearer ' . $this->fetchToken());

            return $handler($request, $options);
        };
    }

    private function fetchToken()
    {
        $auth_tokens = $this->fetcher->fetchAuthToken(null);
        if (array_key_exists('id_token', $auth_tokens)) {
            return $auth_tokens['id_token'];
        }
    }
}
