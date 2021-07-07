<?php

namespace Tco\Source\Ipn;

use Tco\Exceptions\TcoException;
use Tco\Source\TcoConfig;

class IpnSignature {

    /**
     * @var TcoConfig
     */
    private $tcoConfig;

    public function __construct( $config) {
        $this->tcoConfig = $config;
    }

    public function isIpnValid( $params ) {
        try {
            $result       = '';
            $receivedHash = $params['HASH'];
            foreach ( $params as $key => $val ) {

                if ( $key != "HASH" ) {
                    if ( is_array( $val ) ) {
                        $result .= $this->arrayExpand( $val );
                    } else {
                        $size   = strlen( stripslashes( $val ) );
                        $result .= $size . stripslashes( $val );
                    }
                }
            }

            if ( isset( $params['REFNO'] ) && ! empty( $params['REFNO'] ) ) {
                $calcHash = $this->hmac( $this->tcoConfig->getSecretKey(), $result );
                if ( $receivedHash === $calcHash ) {
                    return true;
                }
            }

            return false;
        }
        catch (\Exception $e){
            throw new TcoException(sprintf('Exception validating ipn signature: %s',$e->getMessage()));
        }
    }

    /**
     * @param $ipnParams
     * @param $secret_key
     *
     * @return string
     */
    public function calculateIpnResponse( $ipnParams ) {
        try {
            $resultResponse    = '';
            $ipnParamsResponse = [];
            // we're assuming that these always exist, if they don't then the problem is on avangate side
            $ipnParamsResponse['IPN_PID'][0]   = $ipnParams['IPN_PID'][0];
            $ipnParamsResponse['IPN_PNAME'][0] = $ipnParams['IPN_PNAME'][0];
            $ipnParamsResponse['IPN_DATE']     = $ipnParams['IPN_DATE'];
            $ipnParamsResponse['DATE']         = date( 'YmdHis' );

            foreach ( $ipnParamsResponse as $key => $val ) {
                $resultResponse .= $this->arrayExpand( (array) $val );
            }

            return sprintf(
                '<EPAYMENT>%s|%s</EPAYMENT>',
                $ipnParamsResponse['DATE'],
                $this->hmac( $this->tcoConfig->getSecretKey(), $resultResponse )
            );
        }catch (\Exception $e){
            throw new TcoException(sprintf('Exception generating ipn response: %s', $e->getMessage()));
        }
    }

    /**
     * @param $array
     *
     * @return string
     */
    private function arrayExpand( $array ) {
        $retval = '';
        foreach ( $array as $key => $value ) {
            $size   = strlen( stripslashes( $value ) );
            $retval .= $size . stripslashes( $value );
        }

        return $retval;
    }

    /**
     * @param $key
     * @param $data
     *
     * @return string
     */
    private function hmac( $key, $data ) {
        $b = 64; // byte length for md5
        if ( strlen( $key ) > $b ) {
            $key = pack( "H*", md5( $key ) );
        }

        $key    = str_pad( $key, $b, chr( 0x00 ) );
        $ipad   = str_pad( '', $b, chr( 0x36 ) );
        $opad   = str_pad( '', $b, chr( 0x5c ) );
        $k_ipad = $key ^ $ipad;
        $k_opad = $key ^ $opad;

        return md5( $k_opad . pack( "H*", md5( $k_ipad . $data ) ) );
    }
}
