<?php

class myUser extends sfBasicSecurityUser
{
    /**
     * Нэвтрэх
     *
     * @param $user
     * @param $accessToken
     * @param $refreshToken
     */
    public function signIn($user, $accessToken, $refreshToken)
    {
        $this->setAuthenticated(true);
        $this->setAttribute('id', $user['id']);
        $this->setAttribute('name', $user['firstName'].' '.$user['lastName']);
        $this->setAttribute('accessToken', $accessToken);
        $this->setAttribute('refreshToken', $refreshToken);
    }

    /**
     * @return mixed|null
     */
    public function getId()
    {
        return $this->getAttribute('id', 0);
    }

    /**
     * @return mixed|null
     */
    public function getName()
    {
        return $this->getAttribute('name', '');
    }

    /**
     * @return mixed|null
     */
    public function getAccessToken()
    {
        return $this->getAttribute('accessToken', null);
    }

    /**
     * @return mixed|null
     */
    public function getRefreshToken()
    {
        return $this->getAttribute('refreshToken', null);
    }

    /**
     * Гарах
     */
    public function signOut()
    {
        $this->setAuthenticated(false);
        $this->getAttributeHolder()->clear();
    }
}
