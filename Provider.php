<?php

namespace SocialiteProviders\Bexio;

use GuzzleHttp\RequestOptions;
use SocialiteProviders\Manager\OAuth2\AbstractProvider;
use SocialiteProviders\Manager\OAuth2\User;

class Provider extends AbstractProvider
{
    public const IDENTIFIER = 'BEXIO';

    /**
     * {@inheritdoc}
     */
    protected $scopes = ['openid profile'];

    /**
     * {@inheritdoc}
     */
    protected function getAuthUrl($state)
    {
        return $this->buildAuthUrlFromBase('https://auth.bexio.com/realms/bexio/protocol/openid-connect/auth', $state);
    }

    /**
     * {@inheritdoc}
     */
    protected function getTokenUrl()
    {
        return 'https://auth.bexio.com/realms/bexio/protocol/openid-connect/token';
    }

    /**
     * {@inheritdoc}
     */
    protected function getUserByToken($token)
    {
        $response = $this->getHttpClient()->get('https://auth.bexio.com/realms/bexio/protocol/openid-connect/userinfo', [
            RequestOptions::HEADERS => [
                'Authorization' => 'Bearer '.$token,
            ],
        ]);

        return json_decode((string) $response->getBody(), true);
    }

    /**
     * {@inheritdoc}
     */
    protected function mapUserToObject(array $user): User
    {
        return (new User)->setRaw($user)->map([
            'name'        => trim(($user['given_name'] ?? '').' '.($user['family_name'] ?? '')),
            'email'       => $user['sub'],
            'given_name'  => $user['given_name'],
            'family_name' => $user['family_name'],
            'gender'      => $user['gender'] ?? '',
            'locale'      => $user['locale'] ?? '',
        ]);
    }
}
