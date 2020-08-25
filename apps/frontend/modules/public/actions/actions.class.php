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
     * @param sfWebRequest $request
     *
     * @throws Exception
     */
    public function executeIndex(sfWebRequest $request)
    {
        $code = $request->getParameter('code');

        if ($code) {
            $baseUri = sfConfig::get('app_oauth_base_uri');
            // Client ID, Client Secret зэргийг аккаунт систем дээр үүсгүүлнэ
            $clientId = 'client ID';
            $clientSecret = 'client SECRET';

            $oauthClient = new OauthClient($baseUri, $clientId, $clientSecret);
            $accessToken = $oauthClient->getAccessToken($code);
            $user = $oauthClient->getUserInfo($accessToken);

            $this->getUser()->setAuthenticated(true);
            $this->user = $user;
        }
    }
}
