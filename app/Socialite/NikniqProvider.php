<?php

namespace App\Socialite;

use GuzzleHttp\RequestOptions;
use Laravel\Socialite\Two\AbstractProvider;
use Laravel\Socialite\Two\User;

/**
 * OAuth2 provider for the nikniq identity server (Laravel Passport style endpoints).
 * Endpoints are configurable via services.nikniq.*.
 */
class NikniqProvider extends AbstractProvider
{
    protected $scopes = [];

    protected $scopeSeparator = ' ';

    protected function base(): string
    {
        return rtrim(config('services.nikniq.base_url'), '/');
    }

    protected function getAuthUrl($state)
    {
        return $this->buildAuthUrlFromBase($this->base().config('services.nikniq.authorize_path'), $state);
    }

    protected function getTokenUrl()
    {
        return $this->base().config('services.nikniq.token_path');
    }

    protected function getUserByToken($token)
    {
        $response = $this->getHttpClient()->get($this->base().config('services.nikniq.user_path'), [
            RequestOptions::HEADERS => [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer '.$token,
            ],
        ]);

        $data = json_decode((string) $response->getBody(), true);

        return $data['data'] ?? $data;
    }

    protected function mapUserToObject(array $user)
    {
        return (new User)->setRaw($user)->map([
            'id' => $user['id'] ?? $user['sub'] ?? null,
            'name' => $user['name'] ?? null,
            'email' => $user['email'] ?? null,
            'avatar' => $user['avatar'] ?? $user['picture'] ?? null,
        ]);
    }
}
