<?php
require_once __DIR__ . '/../../../autoloader.php';
use Tco\TwocheckoutFacade;
use Tco\Examples\Common;
use Tco\Exceptions\TcoException;
$params = $_POST;

try {
    if ( strtoupper( $_SERVER['REQUEST_METHOD'] ) !== 'POST' ) {
        die;
    }

    $params = $_POST;
    if ( ! isset( $params['REFNOEXT'] ) && ( ! isset( $params['REFNO'] ) && empty( $params['REFNO'] ) ) ) {
        throw new TcoException( sprintf( 'Cannot identify order: "%s".',
            $params['REFNOEXT'] ) );
    }

    $config = new Common\ExampleConfig();
    $tco    = new TwocheckoutFacade( $config->config() );
    if ( ! $tco->validateIpnResponse($params) ) {
        throw new TcoException( sprintf( 'MD5 hash mismatch for 2Checkout IPN with date: "%s".',
            $params['IPN_DATE'] ) );
    }

    $internalReference       = (int) $params['REFNOEXT']; //can be cart id, order id or other code by which you can
    // identify your order / payment details

    $responseToken = $tco->generateIpnResponse($params);
    echo $responseToken;
} catch ( Exception $e ) {
    throw new TcoException(sprintf('Error in IPN response: %s', $e->getMessage()) );
}

