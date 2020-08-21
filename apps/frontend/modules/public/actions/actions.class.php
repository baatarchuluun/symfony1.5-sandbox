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
            $baseUri = 'http://api.accounts.greensoft.mn';
            // Client ID, Client Secret зэргийг аккаунт систем дээр үүсгүүлнэ
            $clientId = 'client ID';
            $clientSecret = 'client SECRET';

            $oauthClient = new OauthClient($baseUri, $clientId, $clientSecret);
            $accessToken = $oauthClient->getAccessToken($code);
            $user = $oauthClient->getUserInfo($accessToken);

            echo 'FirstName: '.$user['firstName'].'<br/>';
            echo 'LastName: '.$user['lastName'].'<br/>';
            echo 'UserId: '.$user['id'].'<br/>';
            echo 'Email address: '.$user['email'];

            die;
        } else {
            throw new Exception('Authorization code not found!');
        }
    }
}
