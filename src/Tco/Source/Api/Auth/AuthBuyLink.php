<?php
namespace Tco\Source\Api\Auth;

use Tco\Interfaces\Auth;

class AuthBuyLink implements Auth{

    private $sellerId;
    private $secretWord;
    private $jwtToken;

    /**
     * Auth
     *
     * @param auth Params strings: sellerId && secretKey
     * Usage example: new Auth('11220000', 'askljfasjf384r8'))
     */

    public function __construct( $sellerId, $buyLinkSecretWord) {
        $this->sellerId  = $sellerId;
        $this->secretWord = $buyLinkSecretWord;
    }

    public function setMerchantToken( $token){
        $this->jwtToken = $token;
    }

    /**
     *  sets the header with auth hash
     * @return array
     */
    public function getHeaders() {

        $headers[] = 'Content-Type: application/json';
        $headers[] = 'Accept: application/json';
        $headers[] = 'merchant-token: ' . $this->jwtToken;

        return $headers;
    }

    public function getSellerId() {
        return $this->sellerId;
    }

    public function getSecretKey() {
        return $this->secretKey;
    }
}
