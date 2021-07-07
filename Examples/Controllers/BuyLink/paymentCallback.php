<?php
/**
 * Order Payment Callback controller example.
 * Here we creater or validate the order based on cartId && RefNo for TCO validation
 */

require_once __DIR__ . '/../../../autoloader.php';

use Tco\Examples\Common;
use Tco\TwocheckoutFacade;

$config = new Common\ExampleConfig();
$tco    = new TwocheckoutFacade( $config->config() );

$params = $_GET;
$refNo  = isset( $params['refno'] ) ? $params['refno'] : null;

if ( ! $refNo ) {
    $msg = 'Twocheckout Payment Callback - No module name found. Invalid!';
    throw new Exception( $msg );
}

$orderData = $tco->order()->getOrder(array('RefNo'=>$refNo));

$cartId = isset( $orderData['ExternalReference'] ) ? $orderData['ExternalReference'] : null;
if ( ! $cartId ) {
    $msg = 'Twocheckout Payment Callback - No cart found. Invalid!';
    throw new Exception( $msg );
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
        setcookie('BuyLinkRefNo', $orderData['RefNo'], time() + (120), "/"); // 86400 = 1 day
        $redirectUrl = Common\Helpers\MyHelper::generateUrl('', array(
            'success' => true,
            'refno'   => $refNo,
            'msg' => 'Successful payment for order with RefNo - '.$refNo
        ) );


    } else {
        //else just redirect to cart or failure page. We redirect back to index page
        $redirectUrl = Common\Helpers\MyHelper::generateUrl( '', array(
            'success' => false,
            'error'   => 'No valid TCO order found for RefNo: '.$refNo
        ) );
    }
    header( 'Location:' . $redirectUrl );
    exit;
}
