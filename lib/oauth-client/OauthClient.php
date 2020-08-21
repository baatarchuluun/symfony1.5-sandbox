<?php

class OauthClient
{
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
}
