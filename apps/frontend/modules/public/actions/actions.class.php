<?php

/**
 * public actions.
 *
 * @package    Sf 1.5 sandbox
 * @subpackage public
 * @author     Baatarchuluun
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class publicActions extends sfActions
{
    /**
     * Authorization code хүлээж авах.
     *
     * @throws Exception
     */
    public function executeIndex(sfWebRequest $request)
    {
        $code = $request->getParameter('code');

        if ($code) {
            $baseUri = sfConfig::get('app_oauth_base_uri');
            $clientId = sfConfig::get('app_oauth_client_id');
            $clientSecret = sfConfig::get('app_oauth_client_secret');

            $oauthClient = new OauthClient($baseUri, $clientId, $clientSecret);
            $token = $oauthClient->getAccessToken($code);
            $accessToken = $token['accessToken'];
            $refreshToken = $token['refreshToken'];
            $user = $oauthClient->getUserInfo($accessToken);

            if (array_key_exists('id', $user)) {
                $this->getUser()->signIn($user, $accessToken, $refreshToken);
                $this->user = $user;
            }
        }
    }

    /**
     * Хэрэглэгчийн accessToken дууссан бол дахин шинэчилж авах логик.
     *
     * @throws Exception
     */
    public function executeUserCheck()
    {
        $accessToken = $this->getUser()->getAccessToken();

        if ($accessToken) {
            $oauthClient = new OauthClient(sfConfig::get('app_oauth_base_uri'), sfConfig::get('app_oauth_client_id'), sfConfig::get('app_oauth_client_secret'));
            $isLogged = $oauthClient->isUserLogged($accessToken);

            if (!$isLogged) {
                $refreshToken = $this->getUser()->getRefreshToken();
                $accessToken = $oauthClient->getAccessTokenByRefreshToken($refreshToken);
                $user = $oauthClient->getUserInfo($accessToken);
                $this->getUser()->signIn($user, $accessToken, $refreshToken);
            }
        }
    }

    /**
     * Гарах. Oauth server-ээс accessToken-уудаа цэвэрлэх логик.
     *
     * @throws Exception
     */
    public function executeLogout()
    {
        $accessToken = $this->getUser()->getAccessToken();

        if ($accessToken) {
            $oauthClient = new OauthClient(sfConfig::get('app_oauth_base_uri'), sfConfig::get('app_oauth_client_id'), sfConfig::get('app_oauth_client_secret'));
            $oauthClient->revokeAccessToken($accessToken);

            $this->getUser()->signOut();
        }
    }
}
