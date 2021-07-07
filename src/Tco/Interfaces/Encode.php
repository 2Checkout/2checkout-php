<?php

namespace Tco\Interfaces;

/**
 * Interface for Encode classes, enables custom signature encoding dependent
 * on security requirements.
 */
interface Encode {

    public function generateSignature( $header, $payload, $secret );

    public function encode( $toEncode );
}
