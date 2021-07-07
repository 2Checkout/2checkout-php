<?php

declare( strict_types=1 );

namespace Tco\Source\BuyLinkSignature\Jwt\Encoder\Helper;

/**
 * A helper trait to encode and decode json.
 */
trait JsonEncoder {
    /**
     * Consumes an associative array of data and returns a json string. Will
     * return the string 'false' if it fails to encode.
     *
     * @param mixed[] $jsonArray
     */
    public function jsonEncode( $jsonArray ) {
        return (string) json_encode( $jsonArray );
    }

    /**
     * Consumes a json string and decodes it, will always return an
     * associative array.
     *
     * @return mixed[]
     */
    public function jsonDecode( $json ) {
        return (array) json_decode( $json, true );
    }
}
