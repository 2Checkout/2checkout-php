<?php
/**
 * 3DS callback controller example.
 */
require_once __DIR__ . '/../../../autoloader.php';
use Tco\Examples\Common;


if ( isset( $_GET['action'] ) ) {
    if ( $_GET['action'] === 'success' ) {
        return success();
    }
    if ( $_GET['action'] === 'cancel' ) {
        return cancel();
    }
}
/**
 * success callback from 3ds
 * @return mixed
 * @throws Exception
 */

function success() {
    $exampleConfig = new Common\ExampleConfig();
    /**
     * Get current cart object from session
     */
    $cartId       = $_GET['cartId'];

    $refNo = $_GET[ 'REFNO' ];
    if ( ! $refNo ) {
        throw new Exception( 'Cannot handle 3ds redirect without TRANSACTION ID' );
    }

    $redirectUrl = Helpers\MyHelper::generateUrl('Order/paymentCallback', array(
        'id_cart'   => $cartId,
        'refno'     => $refNo
    ));

    header('Location:'.$redirectUrl);
    exit;
}

/**
 * cancel payment from 3ds
 * returns to cart summary
 * @return mixed
 */
function cancel() {

    $exampleConfig = new Common\ExampleConfig();
    $refNo = $_GET[ 'REFNO' ];

    //Do any cart updates, if necessary and redirect to Cart and retry payment.
    //We will just redirect to index here.

    $redirectUrl = MyHelper::generateUrl('', array( 'success'=>false, 'error'=>'3DS payment failed for RefNo - '.$refNo.'!' ) );
    header('Location:'.$redirectUrl);
    exit;
}
