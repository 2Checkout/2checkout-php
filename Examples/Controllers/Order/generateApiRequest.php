<?php
/**
 * This is a full flow example with 3DS using 2PayJs generated token and the PHP API Library to place Orders
 */

require_once __DIR__ . '/../../../autoloader.php';

use Tco\Examples\Common;
use Tco\TwocheckoutFacade;

/**
 * Generates urls for success after 3ds redirect && for failure/cancel
 * Use unix timestamp fort cart id for the sake of example
 */
function get3DsCallbackUrl( string $controller, array $params ) {
    $url = Common\Helpers\MyHelper::generateUrl( $controller, $params );

    return $url;
}

/**
 * @param $has3ds
 *
 * @return string|null
 */
function hasAuthorize3DS( $has3ds ) {
    if ( isset( $has3ds ) && isset( $has3ds['Href'] ) && ! empty( $has3ds['Href'] ) ) {

        return $has3ds['Href'] . '?avng8apitoken=' . $has3ds['Params']['avng8apitoken'];
    }

    return null;
}

function getPaymentDetailsWith3DsUrls( string $token, string $cartId, bool $testMode ) {
    //########## Set cart id to unixtimestamp just for demonstration purposes. You need to have a unique ID!!! ########
    if ( $cartId == 0 || empty( $cartId ) ) {
        $date   = new DateTime();
        $cartId = $date->getTimestamp();
    }

    return [
        'Type'          => $testMode ? 'TEST' : 'EES_TOKEN_PAYMENT',
        'PaymentMethod' => [
            'EesToken'           => $token,
            'Vendor3DSReturnURL' => get3DsCallbackUrl( 'Redirect3ds', array
            (
                'action' => 'success',
                'cartId' => $cartId
            ) ),
            'Vendor3DSCancelURL' => get3DsCallbackUrl( 'Redirect3ds', array
            (
                'action' => 'cancel',
                'cartId' => $cartId
            ) )
        ]
    ];
}

if ( isset( $_POST ) && isset( $_POST['ess_token'] ) ) {
    $orderParams   = new Common\OrderParams\DynamicProducts();
    $exampleConfig = new Common\ExampleConfig();
    $result = null;
    try {
        $tco = new TwocheckoutFacade( $exampleConfig->config() );

        $token                        = $_POST['ess_token'];
        $testMode                     = $_POST['testMode'] == 'true';
        $useCore                      = $_POST['useCore'] == 'true';
        $predefinedDynamicOrderParams = $orderParams->getDynamicProductSuccessParams();
        $originalPaymentDetails       = $predefinedDynamicOrderParams['PaymentDetails'];

        $newPaymentDetails    = getPaymentDetailsWith3DsUrls( $token, 0, $testMode );
        $mergedPaymentDetails = array_merge_recursive( $originalPaymentDetails, $newPaymentDetails );

        $predefinedDynamicOrderParams['PaymentDetails'] = $mergedPaymentDetails;


        if ( ! $useCore ) {
            $response = $tco->order()->place( $predefinedDynamicOrderParams );
        } else {
            $response = $tco->apiCore()->call( '/orders/', $predefinedDynamicOrderParams );
        }


        if ( ! isset( $response['RefNo'] ) ) {
            $result = array( 'success' => false, 'errors' => $response['message'] );
        } elseif ( $response['Errors'] ) {
            $errorMessage = '';
            foreach ( $response['Errors'] as $key => $value ) {
                $errorMessage .= $value . PHP_EOL;
            }
            $result = array( 'success' => false, 'error' => $errorMessage );

        } else {
            //One could have the order payment callback url + cartid/orderId params + tco for $returnUrl
            $returnUrl = Common\Helpers\MyHelper::generateUrl( '', array(
                'success' => true,
                'refno'   => $response['RefNo'],
                'msg'     => 'Successful payment for order with RefNo - ' . $response['RefNo']
            ) );

            $hasAuthorize3ds = false;
            // RefNo value
            if ( isset( $response['PaymentDetails']['PaymentMethod']['Authorize3DS'] ) ) {
                $hasAuthorize3ds = hasAuthorize3DS( $response['PaymentDetails']['PaymentMethod']['Authorize3DS'] );
            }
            $redirectTo = $hasAuthorize3ds ? $hasAuthorize3ds : $returnUrl;
            $result     = array( 'success'   => true,
                            'refno'    => $response['RefNo'],
                            'redirect' => $redirectTo
            );
        }

    }
    catch(Exception $exception){
        $result     = array( 'success'   => false,
                             'errors' => $exception->getMessage()
        );
    }
    exit( json_encode( $result ) );

}
