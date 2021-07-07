<?php
/**
 * This is a full flow example with 3DS using 2PayJs generated token and the PHP API Library to place Orders
 */

require_once __DIR__ . '/../../../autoloader.php';

use Tco\Examples\Common;
use Tco\TwocheckoutFacade;

$result = null;
if ( isset( $_POST['refno'] ) ) {
    $orderParams   = new Common\OrderParams\DynamicProducts();
    $exampleConfig = new Common\ExampleConfig();
    $tco           = new TwocheckoutFacade( $exampleConfig->config() );


    if ( isset( $_POST['refno'] ) ) {
        $refNo            = $_POST['refno'];
        $SubscriptionData = $tco->subscription()->getSubscriptionsByOrderRefNo( $refNo );

        if ( ! empty( $SubscriptionData ) ) {
            //he order payment callback url + cartid/orderId params + tco for $returnUrl
            $result = array(
                'success' => true,
                'refno'   => $refNo,
                'msg'     => 'Found subscriptions: ' . print_r( $SubscriptionData, true )
        );
        } else {
            $result = array( 'success' => false, 'error' => 'No Subscription found!' );
        }
    } else {
        $result = array( 'success' => false, 'error' => 'No Cookie found!' );
    }


} else {
    echo 'Only Post';
}
$returnUrl = Common\Helpers\MyHelper::generateUrl( '', $result );
exit( json_encode( $result ) );
