<?php

namespace Tco\Source\BuyLinkSignature\Jwt;


use Tco\Source\BuyLinkSignature\Jwt\Encoder\EncodeHS512;
use Tco\Source\BuyLinkSignature\Jwt\Encoder\Helper\Base64;
use Tco\Source\BuyLinkSignature\Jwt\Encoder\Helper\JsonEncoder;
use Tco\Source\TcoConfig;
use Tco\Exceptions\TcoException;

class JwtGenerator {
    use JsonEncoder;
    use Base64;

    private $header = array( 'typ' => 'JWT', 'alg' => 'HS512' );
    private $claims;
    private $sellerId;
    private $secretWord;
    private $encoder;
    private $tokenValidSecondsDiff;
    private $defaultJwtExpireAfter = 30; //nr of seconds

    /**
     * JwtGenerator constructor.
     *
     * @param TcoConfig $config
     *
     */
    public function __construct( $config ) {
        $this->setCredentials( $config );
        $this->encoder = new EncodeHS512();
        $this->setTokenExpireAfterSeconds( $config->getJwtExpirationTimeInSeconds() );
        $this->getClaimsArray();
    }

    /**
     * @param int $expirationSeconds
     */
    public function setTokenExpireAfterSeconds( $expirationSeconds ) {
        $this->tokenValidSecondsDiff = $expirationSeconds;
    }

    /**
     * @return string
     */
    public function generateToken() {
        $signature = $this->encoder->generateSignature( $this->getHeaderArray(),
            $this->getClaimsArray(), $this->secretWord );

        return $this->encoder->encode( $this->getHeaderArray() ) . '.' .
               $this->encoder->encode( $this->getClaimsArray() ) . '.' . $signature;
    }

    /**
     * @param TcoConfig $config
     *
     * @throws \TcoException
     */
    public function setCredentials( $config ) {
        //sellerId is required in TcoConfig
        try {
            $this->sellerId   = $config->getSellerId();
            $this->secretWord = $config->getBuyLinkSecretWord();
        } catch ( TcoException $ex ) {
            throw new TcoException( $ex->getMessage() );
        }
    }

    /**
     * @return array
     */
    private function getClaimsArray() {
        if ( ! $this->claims ) {
            $this->claims = array( 'sub' => $this->sellerId, 'iat' => time() );
            if ( $this->tokenValidSecondsDiff ) {
                $expTime = time() + (int) $this->tokenValidSecondsDiff;
                if ( !$this->validateExpiration($expTime) ) {
                    $expTime = time() + ( 60 * $this->defaultJwtExpireAfter );
                }
                $this->claims['exp'] = $expTime;
            }
        }

        return $this->claims;
    }

    /**
     * @return array
     */
    private function getHeaderArray() {
        return $this->header;
    }


    /**
     * @param int $expiration
     *
     * @return bool
     */
    private function validateExpiration( $expiration ) {
        return $expiration > time();
    }
}
