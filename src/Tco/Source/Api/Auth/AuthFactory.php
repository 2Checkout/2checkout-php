<?php

namespace Tco\Source\Api\Auth;

use Tco\Source\TcoConfig;


class AuthFactory {

    private $config;

    public function __construct( $config) {
        $this->config = $config;
    }

    public function getAuth($type='Api'){
        if($type=='Api'){
            return new AuthApi($this->config->getSellerId(), $this->config->getSecretKey());
        }elseif ($type == 'BuyLink'){
            return new AuthBuyLink($this->config->getSellerId(), $this->config->getBuyLinkSecretWord());
        }
    }

}
