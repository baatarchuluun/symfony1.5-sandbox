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
            $accessToken = $oauthClient->getAccessToken($code);
            $user = $oauthClient->getUserInfo($accessToken);

            if (array_key_exists('id', $user)) {
                $this->getUser()->signIn($user, $accessToken);
                $this->user = $user;
            }
        }
    }

    public function executeUserCheck()
    {
        $accessToken = $this->getUser()->getAccessToken();

        if ($accessToken) {
            $oauthClient = new OauthClient(sfConfig::get('app_oauth_base_uri'), sfConfig::get('app_oauth_client_id'), sfConfig::get('app_oauth_client_secret'));
            $isLogged = $oauthClient->isUserLogged($accessToken);

            if (!$isLogged) {
                // todo: get access token by refresh token
            }
        }
    }
}
