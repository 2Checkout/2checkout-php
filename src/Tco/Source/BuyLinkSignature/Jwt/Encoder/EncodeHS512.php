<?php

namespace Tco\Source\BuyLinkSignature\Jwt\Encoder;

use Tco\Source\BuyLinkSignature\Jwt\Encoder\Helper\JsonEncoder;
use Tco\Interfaces\Encode;
use Tco\Source\BuyLinkSignature\Jwt\Encoder\Helper\Base64;

class EncodeHS512 implements Encode {
    use Base64;
    use JsonEncoder;

    private const HASH_ALGORITHM = 'sha512';

    /**
     * @param array $header
     * @param array $payload
     * @param string $secret
     *
     * @return string
     */
    public function generateSignature( $header, $payload, $secret ) {
        return $this->urlEncode(
            $this->hash(
                $this->getHashAlgorithm(),
                $this->encode( $header ) . '.' . $this->encode( $payload ),
                $secret
            )
        );
    }

    /**
     * @param array $toEncode
     *
     * @return string
     */
    public function encode( $toEncode ) {
        return $this->urlEncode( $this->jsonEncode( $toEncode ) );
    }

    /**
     * @param string $toEncode
     *
     * @return string
     */
    private function urlEncode( $toEncode ) {
        return $this->toBase64Url( base64_encode( $toEncode ) );
    }

    /**
     * @return string
     */
    private function getHashAlgorithm() {
        return self::HASH_ALGORITHM;
    }

    /**
     * @param string $algorithm
     * @param string $toHash
     * @param string $secret
     *
     * @return string
     */
    private function hash( $algorithm, $toHash,  $secret ) {
        return hash_hmac( $algorithm, $toHash, $secret, true );
    }
}

