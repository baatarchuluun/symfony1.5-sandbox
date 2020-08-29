<?php

class OauthClient
{
    const CODE_SUCCESS = 0;

    private $baseUri;
    private $client;
    private $clientId;
    private $clientSecret;

    public function __construct($baseUri, $clientId, $clientSecret)
    {
        $this->baseUri = $baseUri;
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->client =  new CurlHttpClient();
    }

    /**
     * @param $code
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function getAccessToken($code)
    {
        $result = $this->client->post($this->baseUri . '/api/oauth/v2/token', [
            'data' => [
                'code' => $code,
                'clientId' => $this->clientId,
                'clientSecret' => $this->clientSecret,
            ],
        ]);

        return $result['response']['accessToken'];
    }

    /**
     * @param $accessToken
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function getUserInfo($accessToken)
    {
        $result = $this->client->get($this->baseUri . '/api/me', [
            'header' => [
                'Authorization: Bearer '.$accessToken,
            ],
        ]);

        return $result;
    }

    /**
     * @param $accessToken
     *
     * @return bool
     *
     * @throws Exception
     */
    public function isUserLogged($accessToken)
    {
        $result = $this->client->get($this->baseUri . '/api/user/logged', [
            'header' => [
                'Authorization: Bearer '.$accessToken,
            ],
        ]);

        if (self::CODE_SUCCESS === $result['code']) {
            return true;
        }

        return false;
    }

    /**
     * @param $refreshToken
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function getAccessTokenByRefreshToken($refreshToken)
    {
        $result = $this->client->post($this->baseUri . '/api/token/refresh', [
            'data' => [
                'refresh_token' => $refreshToken,
            ],
        ]);

        return $result;
    }
}
