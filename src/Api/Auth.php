<?php
namespace WotWrap\Api;

use WotWrap\Dto;

class Auth extends AbstractApi
{
    /**
     * Login to the wargaming services and get an access_token 
     * 
     * @param $redirectUri
     * @param array $params
     * @return Dto\Auth
     * @throws \WotWrap\Exception\CacheNotFoundException
     */
    public function login($redirectUri, $params = [])
    {
        if (isset($_GET['access_token']) && isset($_GET['expires_at'])) {
            //we check access_token by calling prolongate service
            return $this->prolongate($_GET['access_token'], $_GET['expires_at']);
        }
        
        $params['redirect_uri'] = $redirectUri;
        $params['nofollow'] = 1;
        $authArray = $this->request('auth/login/', $params);
        $location = $authArray['data']['location'];
        header("Location: $location");
    }

    /**
     * Prolongs the given access token for the certain amount of time 
     * 
     * @param $accessToken
     * @param int $expiresAt (default value = 2 weeks)
     * @return Dto\Auth
     * @throws \WotWrap\Exception\CacheNotFoundException
     */
    public function prolongate($accessToken, $expiresAt = 1209600)
    {
        $this->setRequestType('POST');
        $prolongateResult = $this->request('auth/prolongate/', [
            'access_token' => $accessToken,
            'expires_at' => $expiresAt
        ]);
        
        return new Dto\Auth($prolongateResult['data']);
    }

    /**
     * Destroys the access token
     * 
     * @param $accessToken
     * @return void
     * @throws \WotWrap\Exception\CacheNotFoundException
     */
    public function logout($accessToken)
    {
        $this->setRequestType('POST');
        $this->request('auth/logout/', ['access_token' => $accessToken]);
    }
}
