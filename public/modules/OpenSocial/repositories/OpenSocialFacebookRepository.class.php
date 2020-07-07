<?php

/**
 * OpenSocialFacebookRepository
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class OpenSocialFacebookRepository {

    const DEFAULT_GRAPH_VERSION = 'v2.2';

    /** @var bool $enabled */
    private $enabled;
    /** @var string $appID */
    private $appID;
    /** @var string $appSecret */
    private $appSecret;

    /** @var Facebook\Facebook $Facebook */
    private $Facebook;

    /**
     * OpenSocialFacebookRepository constructor.
     */
    function __construct(){
        require 'lib/Facebook/autoload.php';
        self::decorate();
    }

    /**
     * Decorate connector
     */
    private function decorate(){
        $this->enabled = CoreStringUtils::evaluateBoolean(CoreModule::getProp('OpenSocialModule', 'facebook.enabled'));
        if(!$this->enabled){
            return;
        }
        $this->appID = CoreModule::getProp('OpenSocialModule', 'facebook.application.id', CoreStringUtils::EMPTY_STRING);
        $this->appSecret = CoreModule::getProp('OpenSocialModule', 'facebook.application.secret', CoreStringUtils::EMPTY_STRING);
        $this->Facebook = new Facebook\Facebook([
            'app_id' => $this->appID,
            'app_secret' => $this->appSecret,
            'default_graph_version' => self::DEFAULT_GRAPH_VERSION
        ]);
    }

    /**
     * Is Enabled
     *
     * @return bool
     */
    public function isEnabled(){
        return $this->enabled;
    }

    /**
     * Get connect url
     *
     * @param null $url
     * @param array $permissions
     * @return string
     */
    public function getConnectUrl($url = null, $permissions = array('email')){
        $helper = $this->Facebook->getRedirectLoginHelper();
        return $helper->getLoginUrl($url, $permissions);
    }

    /**
     * Connect callback
     *
     * @return OpenSocialFacebookCallbackUserObject
     * @throws OpenSocialFacebookException
     */
    public function connectCallback(){

        $helper = $this->Facebook->getRedirectLoginHelper();

        try {
            $accessToken = $helper->getAccessToken();
        } catch(Facebook\Exceptions\FacebookResponseException $e) {
            throw new OpenSocialFacebookException($e);
        } catch(Facebook\Exceptions\FacebookSDKException $e) {
            throw new OpenSocialFacebookException($e);
        }

        /**
         * Assert access token
         */
        if(! isset($accessToken)){
            throw new OpenSocialFacebookException('No access token');
        }

        try {

            /**
             * Get client
             */
            $oAuth2Client = $this->Facebook->getOAuth2Client();

            /**
             * Token assertions
             */
            $tokenMetadata = $oAuth2Client->debugToken($accessToken);
            $tokenMetadata->validateAppId($this->appID);
            $tokenMetadata->validateExpiration();

            /**
             * Exchange for long lived token
             */
            if (!$accessToken->isLongLived()) {
                try {
                    $accessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);
                } catch (Facebook\Exceptions\FacebookSDKException $e) {
                    //throw
                }
            }

            /**
             * Get Facebook data
             */
            $response = $this->Facebook->get('/me?locale=en_US&fields=name,email,first_name,last_name,picture.width(500).height(500)', $accessToken);
            $userNode = $response->getGraphUser();

            /** @var OpenSocialFacebookCallbackUserObject $OpenSocialFacebookCallbackUserObject */
            $OpenSocialFacebookCallbackUserObject = CoreLogic::getObject('OpenSocialFacebookCallbackUserObject');
            $OpenSocialFacebookCallbackUserObject->setId($userNode->getId());
            $OpenSocialFacebookCallbackUserObject->setFirstName($userNode->getFirstName());
            $OpenSocialFacebookCallbackUserObject->setLastName($userNode->getLastName());
            $OpenSocialFacebookCallbackUserObject->setEmail($userNode->getEmail());
            $Picture = $userNode->getPicture();
            if (!empty($Picture)) {
                $OpenSocialFacebookCallbackUserObject->setPicture($Picture->getUrl());
            }

            return $OpenSocialFacebookCallbackUserObject;

        } catch(Exception $e){
            throw new OpenSocialFacebookException(CoreStringUtils::limitString($e->getMessage(), 144));
        }

    }

}