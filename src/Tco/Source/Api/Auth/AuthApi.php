<?php

namespace Tco\Source\Api\Auth;

use Tco\Interfaces\Auth;

class AuthApi implements Auth {

    private $sellerId;
    private $secretKey;

    /**
     * Auth
     *
     * @param auth Params strings: sellerId && secretKey
     * Usage example: new Auth('11220000', 'askljfasjf384r8'))
     */

    public function __construct( $sellerId, $secretKey ) {
        $this->sellerId  = $sellerId;
        $this->secretKey = $secretKey;
    }

    /**
     *  sets the header with auth hash
     * @return array
     */
    public function getHeaders() {

        $gmtDate = gmdate( 'Y-m-d H:i:s' );
        $string  = strlen( $this->sellerId ) . $this->sellerId . strlen( $gmtDate ) . $gmtDate;
        $hash    = hash_hmac( 'md5', $string, $this->secretKey );

        $headers[] = 'Content-Type: application/json';
        $headers[] = 'Accept: application/json';
        $headers[] = 'X-Avangate-Authentication: code="' . $this->sellerId . '" date="' . $gmtDate . '" hash="' . $hash . '"';

        return $headers;
    }

    public function getSellerId() {
        return $this->sellerId;
    }

    public function getSecretKey() {
        return $this->secretKey;
    }
}
