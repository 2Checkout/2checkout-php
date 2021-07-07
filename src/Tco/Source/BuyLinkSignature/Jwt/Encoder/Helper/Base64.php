<?php

namespace Tco\Source\BuyLinkSignature\Jwt\Encoder\Helper;

trait Base64 {
    /**
     * Convert a base64 string to a base64 Url string.
     */
    public function toBase64Url( $base64 ) {
        return str_replace( [ '+', '/', '=' ], [ '-', '_', '' ], $base64 );
    }

    /**
     * Convert a base64 URL string to a base64 string.
     */
    public function toBase64( $urlString ) {
        return str_replace( [ '-', '_' ], [ '+', '/' ], $urlString );
    }
}
