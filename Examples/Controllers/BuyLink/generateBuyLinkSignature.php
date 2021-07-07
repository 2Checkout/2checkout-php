<?php
require_once __DIR__ . '/../../../autoloader.php';

use Tco\TwocheckoutFacade;
use Tco\Examples\Common\OrderParams\DynamicProducts;
use Tco\Examples\Common\ExampleConfig;
use Tco\Examples\Common\Helpers\MyHelper;


function convertOrderApiParamsToBuyLinkParams( $orderApiParams, $exampleConfig, $test = 1 ) {
    $shipping = array(
        'ship-name'    => $orderApiParams['BillingDetails']['FirstName'] . ' ' . $orderApiParams['BillingDetails']['LastName'],
        'ship-address' => $orderApiParams['BillingDetails']['Address1'],
        'ship-city'    => $orderApiParams['BillingDetails']['City'],
        'ship-country' => $orderApiParams['BillingDetails']['CountryCode'],
        'ship-email'   => $orderApiParams['BillingDetails']['Email'],
        'ship-state'   => $orderApiParams['BillingDetails']['State'],
        //'ship-address2' => 'Test address 2'
    );

    $billing = array(
        'address' => $orderApiParams['BillingDetails']['Address1'],
        'city'    => $orderApiParams['BillingDetails']['City'],
        'country' => $orderApiParams['BillingDetails']['CountryCode'],
        'name'    => $orderApiParams['BillingDetails']['FirstName'] . ' ' . $orderApiParams['BillingDetails']['LastName'],
        'phone'   => '0770678987',
        'zip'     => $orderApiParams['BillingDetails']['Zip'],
        'email'   => $orderApiParams['BillingDetails']['Email'],
        'company-name' => 'Verifone',
        'state'   => $orderApiParams['BillingDetails']['State'],
        //'address2' => '',
        //'fiscal-code'=>''
    );


    $buyLinkParams             = [];
    $buyLinkParams             = array_merge( $buyLinkParams, $billing, $shipping );
    $buyLinkParams['prod']     = $orderApiParams['Items'][0]['Name'];
    $buyLinkParams['price']    = (string) $orderApiParams['Items'][0]['Quantity'] *
                                       $orderApiParams['Items'][0]['Price']['Amount'];
    $buyLinkParams['qty']      = $orderApiParams['Items'][0]['Quantity'];
    $buyLinkParams['type']     = $orderApiParams['Items'][0]['PurchaseType'];
    $buyLinkParams['tangible'] = (int)$orderApiParams['Items'][0]['Tangible'];
    $buyLinkParams['src']      = 'phpLibrary';
// url NEEDS a protocol(http or https)
    $buyLinkParams['return-url']       = MyHelper::generateUrl( 'BuyLink/paymentCallback.php',
        array() );
    $buyLinkParams['return-type']      = 'redirect';
    $buyLinkParams['expiration']       = time() + ( 3600 * 5 );
    $buyLinkParams['order-ext-ref']    = $orderApiParams['ExternalReference'];
    $buyLinkParams['item-ext-ref']     = date( 'YmdHis' );
    $buyLinkParams['customer-ext-ref'] = $orderApiParams['BillingDetails']['Email'];
    $buyLinkParams['currency']         = strtolower($orderApiParams['Currency']);
    $buyLinkParams['language']         = $orderApiParams['Language'];
    $buyLinkParams['test']             = (int) $test;
// sid in this case is the merchant code
    $buyLinkParams['merchant'] = $exampleConfig->sellerId();
    $buyLinkParams['dynamic']  = 1;

    $buyLinkParams['recurrence'] = '1:MONTH';
    $buyLinkParams['duration'] = '12:MONTH';
    $buyLinkParams['renewal-price'] = (string) $orderApiParams['Items'][0]['Quantity'] *
                                       $orderApiParams['Items'][0]['Price']['Amount'];

    return $buyLinkParams;
}

//1. Post request params
$testMode = $_POST['testMode'] == 'true' ? 1 : 0;
$useCore  = $_POST['useCore'] == 'true';

//2. Get buy link order structure
$orderParams               = new DynamicProducts();
$buyLinkOrderParamsExample = $orderParams->getDynamicProductSuccessParams();

$exampleConfig = new ExampleConfig();

$params = convertOrderApiParamsToBuyLinkParams( $buyLinkOrderParamsExample, $exampleConfig, $testMode );

//die(json_encode($params));

try {
    $tco              = new TwocheckoutFacade( $exampleConfig->config() );
    $buyLinkSignature = $tco->getBuyLinkSignature( $params );
    //signature needs to be in request params!
    $params['signature'] = $buyLinkSignature;
} catch ( Exception $e ) {
    throw new Exception( $e->getMessage() );
}


$redirectTo = 'https://secure.2checkout.com/checkout/buy/?' . ( http_build_query( $params ) );

$result = [ 'status' => true, 'errors' => null, 'redirect' => $redirectTo ];

header( 'Content-Type: application/json' );
exit( json_encode( $result ) );
