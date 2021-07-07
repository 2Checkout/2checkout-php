<?php

namespace Tco\Source;
use Tco\Exceptions\TcoException;
use Tco\Exceptions\TcoExceptionMsgs;

class TcoConfig {

    //path between host DocumentRoot and library root directory
    public static $ROOT = '';
    /**
     * @var curl_verify_ssl
     * @value can be true or false (use false when debugging/testing )
     * This option controls checking the server's certificate's claimed identity. The server could be lying.
     */
    private $configKeys = array(
        'sellerId', //string
        'secretKey', //string
        'curlVerifySsl', //int 0 or 1
        'buyLinkSecretWord', //string
        'jwtExpireTime' // after how many minutes the jwt will expire, by default 30 min
        //also pass the nr of minutes for witch the token should expire
    );

    private $defaultRequiredFields = array(
        'sellerId',
        'secretKey'
    );

    private $apiUrls = array(
        'restApi' => 'https://api.2checkout.com/rest/6.0',
        'buyLink' => 'https://secure.2checkout.com/checkout/api/encrypt/generate/signature'
    );

    private $config;

    public function __construct( $config ) {
        try{
            $this->setConfig( $config );
        } catch (TcoException $exception ) {
            throw new TcoException($exception->getMessage());
        }
    }

    public function getCurlVerifySsl() {
        return $this->config['curlVerifySsl'];
    }

    public function getSellerId() {
        return $this->config['sellerId'];
    }

    public function getSecretKey() {
        return $this->config['secretKey'];
    }

    public function getBuyLinkSecretWord() {
        if(!isset($this->config['buyLinkSecretWord'])){
            throw new TcoException('Buy link Secret Word is not set in Tco Config! First set it and then use it.');
        }
        return $this->config['buyLinkSecretWord'];
    }

    public function getJwtExpirationTimeInSeconds() {
        return (int) (!isset($this->config['jwtExpireTime']) ? 30*60 : $this->config['jwtExpireTime']*60);
    }

    public function getAcceptedConfigParams(){
        $this->configKeys;
    }

    public function getApiEndpoints(){
        return $this->apiUrls;
    }

    /**
     * Checks if @config has the accepted keys and values are set
     */
    //rename validateConfig to isConfigValid
    public function validateConfig( $config ) {
        $rejected = array();
        foreach ( $config as $k => $v ) {
            if ( $this->isConfigParameterAccepted( $k ) ) {
                if ( $this->isConfigParamRequired( $k ) && $this->isEmpty( $v ) ) {
                    $rejected[] = $k;
                }
            } else {
                $rejected[] = $k;
            }
        }

        return empty( $rejected );
    }

    private function isConfigParameterAccepted( $param ) {
        return in_array( $param, $this->configKeys );
    }

    private function isConfigParamRequired( $param ) {
        return in_array( $param, $this->defaultRequiredFields );
    }

    private function isEmpty( $param ) {
        return ( is_null( $param ) || $param == '' );
    }

    public function setConfig( $config ) {
        if ( $this->validateConfig( $config ) ) {
            $this->config = $config;
            if(!isset($this->config['curlVerifySsl'])){
                $this->config['curlVerifySsl'] = 0;
            }
            if(!isset($this->config['root'])){
                $this->config['root'] = self::$ROOT;
            }
        } else {
            throw new TcoException('Some configuration keys are not accepted or not set!');
        }

    }
}
