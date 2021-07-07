<?php

namespace Tco\Examples\Common;

class ExampleConfig {
    private $sellerId = '';
    private $secretKey = '';
    private $secretWord = '';
    private $secureSSL = 0;
    private $jwtExpireTime = 20; //minutes
    protected $config = array();

    public function __construct() {
        $this->config = array(
            'sellerId'          => $this->sellerId(),
            'secretKey'         => $this->secretKey(),
            'curlVerifySsl'     => $this->secureSSL(),
            'buyLinkSecretWord' => $this->secretWord(),
            'jwtExpireTime'     => $this->jwtExpireTime
    );
    }

    public function config() {
        return $this->config;
    }

    public function sellerId() {
        return $this->sellerId;
    }

    public function secretKey() {
        return $this->secretKey;
    }

    public function secretWord() {
        return $this->secretWord;
    }

    public function secureSSL() {
        return $this->secureSSL;
    }
}
