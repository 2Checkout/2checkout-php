<?php
/**
 * Order Payment Callback controller example.
 * Here we creater or validate the order based on cartId && RefNo for TCO validation
 */

require_once __DIR__ . '/../../../autoloader.php';

use Tco\Examples\Common;
use Tco\TwocheckoutFacade;
use Tco\Exceptions\TcoException;

$config = new Common\ExampleConfig();

try {
    $tco = new TwocheckoutFacade( $config->config() );
} catch ( Exception $exception ) {
    throw new TcoException( $exception->getMessage() );
}

$params = $_GET;
$refNo  = isset( $params['refno'] ) ? $params['refno'] : null;
$cartId = isset( $params['cartId'] ) ? $params['cartId'] : null;

if ( ! $refNo ) {
    $msg = 'Twocheckout Payment Callback - No module name found. Invalid!';
    throw new TcoException( $msg );
}

if ( ! $cartId ) {
    $msg = 'Twocheckout Payment Callback - No cart found. Invalid!';
    throw new TcoException( $msg );
}

/**
 * Next the order is created from CartId / or just validate order data, depending on implementation
 */
if ( $refNo ) {

    //Get order using API for security check && validation
    $orderData = $tco->order()->getOrder( array( 'RefNo' => $refNo ) );
    if ( isset( $orderData['RefNo'] ) && isset( $orderData['ExternalReference'] ) ) {
        /**
         * Your custom Order creation/update implementation goes here....
         * If everything goes well redirect to success page
         * .....
         */
        $redirectUrl = Common\Helpers\MyHelper::generateUrl( '', array(
            'success' => true,
            'refno'   => $refNo
        ) );


    } else {
        //else just redirect to cart or failure page. We redirect back to index page
        $redirectUrl = Common\Helpers\MyHelper::generateUrl( '', array(
            'success' => false,
            'error'   => 'No valid TCO order found for RefNo: ' . $refNo
        ) );
    }
    header( 'Location:' . $redirectUrl );
    exit;
}
