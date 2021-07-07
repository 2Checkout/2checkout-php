<?php

namespace Tco\Examples\Common\Helpers;
use Tco\Source\TcoConfig;

class MyHelper {

    public static function calculateTotal( $orderParameters ) {
        $total = 0;
        foreach ( $orderParameters['Items'] as $k => $product ) {
            $itemValue = $product['Price']['Amount'] * $product['Quantity'];
            $total     += $itemValue;
        }

        return $total;
    }

    public static function generateUrl( $controllerPath, $params ) {
        $rootdir =  TcoConfig::$ROOT;
        $protocol = stripos( $_SERVER['SERVER_PROTOCOL'], 'https' ) === 0 ? 'https://' : 'http://';
        $base_url = $protocol . $_SERVER['SERVER_NAME'] . '/' . $rootdir;

        if ( ! empty( $controllerPath ) ) {
            $base_url .= '/Examples/Controllers/' . $controllerPath;
        } else {
            $base_url .= '/Examples/index.php';
        }
        $url = $base_url;

        if ( ! empty( $params ) ) {
            $url .= '?' . http_build_query( $params );
        }

        return $url;
    }
}
