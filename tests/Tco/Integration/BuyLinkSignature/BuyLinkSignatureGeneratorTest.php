<?php
require_once __DIR__ . '/../../../../autoloader.php';
require_once __DIR__ . '/../../../../tests/Tco/Fixtures/TestsConfig.php';

use PHPUnit\Framework\TestCase;
use Tco\TwocheckoutFacade;

final class BuyLinkSignatureGeneratorTest extends TestCase {

    private $configArr;

    //Orders params structure lists
    private $dynamicProductParamsSuccess = array(
        'Country'           => 'us',
        'Currency'          => 'USD',
        'CustomerIP'        => '91.220.121.21',
        'ExternalReference' => 'CustOrd100',
        'Language'          => 'en',
        'Source'            => 'tcolib.local',
        'BillingDetails'    =>
            array(
                'Address1'    => 'Test Address',
                'City'        => 'LA',
                'State'       => 'California',
                'CountryCode' => 'US',
                'Email'       => 'testcustomer@2Checkout.com',
                'FirstName'   => 'Customer',
                'LastName'    => '2Checkout',
                'Zip'         => '12345',
            ),
        'Items'             =>
            array(
                0 =>
                    array(
                        'Name'         => 'Buy link Dynamic product test product from API',
                        'Description'  => 'Test description',
                        'Quantity'     => 1,
                        'IsDynamic'    => true,
                        'Tangible'     => false,
                        'PurchaseType' => 'PRODUCT',
                        'Price'        =>
                            array(
                                'Amount' => 1, //value
                                'Type'   => 'CUSTOM',
                            ),
                        /*'PriceOptions' =>
                            array(
                                0 =>
                                    array(
                                        'Name'    => 'OPT1',
                                        'Options' =>
                                            array(
                                                0 =>
                                                    array(
                                                        'Name'      => 'Name LR',
                                                        'Value'     => 'Value LR',
                                                        'Surcharge' => 7,
                                                    ),
                                            ),
                                    ),
                            ),*/
                        /*'RecurringOptions' =>
                            array (
                                'CycleLength' => 2,
                                'CycleUnit' => 'DAY',
                                'CycleAmount' => 12.2,
                                'ContractLength' => 3,
                                'ContractUnit' => 'DAY',
                            ),*/ //Only when one has a dynamic product subscription.
                    )
            ),
        'PaymentDetails'    =>
            array(
                'Type'          => 'TEST', //test mode
                'Currency'      => 'USD',
                'CustomerIP'    => '91.220.121.21',
                'PaymentMethod' =>
                    array(
                        'CardNumber'         => '4111111111111111',
                        'CardType'           => 'VISA',
                        'Vendor3DSReturnURL' => 'www.success.com',
                        'Vendor3DSCancelURL' => 'www.fail.com',
                        'ExpirationYear'     => '2023',
                        'ExpirationMonth'    => '12',
                        'CCID'               => '123',
                        'HolderName'         => 'John Doe',
                        'RecurringEnabled'   => false,
                        'HolderNameTime'     => 1,
                        'CardNumberTime'     => 1,
                    ),
            ),
    );

    public function __toString() {
        return 'BuyLinkSignatureGeneratorTest';
    }

    public function setUp(): void {
        $this->configArr          = array(
            'sellerId'      => TestsConfig::SELLER_ID,
            'secretKey'     => TestsConfig::SECRET_KEY,
            'buyLinkSecretWord'    => TestsConfig::SECRET_WORD,
            'jwtExpireTime' => 30, //minutes
            'curlVerifySsl' => 0
        );
    }

    public function testAll(){
        $this->_testGenerateSignature();
        $this->_testGenerateSignatureException();
    }

    public function _testGenerateSignature() {
        $params = $this->convertOrderApiParamsToBuyLinkParams($this->dynamicProductParamsSuccess, $this->configArr );
        $tco              = new TwocheckoutFacade($this->configArr );
        $buyLinkSignature = $tco->getBuyLinkSignature( $params );
        $this->assertNotEmpty($buyLinkSignature);

    }

    public function _testGenerateSignatureException() {
        $configArr          = array(
            'sellerId'      => TestsConfig::SELLER_ID,
            'secretKey'     => TestsConfig::SECRET_KEY,
            'buyLinkSecretWord'    => 'asjfdDFlkaj43jt@234#jt23rjt%j$3lj4t_34',
            'jwtExpireTime' => 30, //minutes
            'curlVerifySsl' => 0
        );
        $params = $this->convertOrderApiParamsToBuyLinkParams($this->dynamicProductParamsSuccess, $configArr );
        $tco              = new TwocheckoutFacade($configArr );
        $this->expectException(Exception::class);
        $buyLinkSignature = $tco->getBuyLinkSignature( $params );

    }

    private function convertOrderApiParamsToBuyLinkParams( $orderApiParams, $exampleConfig, $test = 1 ) {
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
    $buyLinkParams['price']    = $orderApiParams['Items'][0]['Quantity'] * $orderApiParams['Items'][0]['Price']['Amount'];
    $buyLinkParams['qty']      = $orderApiParams['Items'][0]['Quantity'];
    $buyLinkParams['type']     = $orderApiParams['Items'][0]['PurchaseType'];
    $buyLinkParams['tangible'] = (int) $orderApiParams['Items'][0]['Tangible'];
    $buyLinkParams['src']      = 'phpLibrary';
// url NEEDS a protocol(http or https)
    $buyLinkParams['return-url']       = 'http://tcoLib.example/paymentCallback.php';
    $buyLinkParams['return-type']      = 'redirect';
    $buyLinkParams['expiration']       = time() + ( 3600 * 5 );
    $buyLinkParams['order-ext-ref']    = $orderApiParams['ExternalReference'];
    $buyLinkParams['item-ext-ref']     = date( 'YmdHis' );
    $buyLinkParams['customer-ext-ref'] = $orderApiParams['BillingDetails']['Email'];
    $buyLinkParams['currency']         = strtolower($orderApiParams['Currency']);
    $buyLinkParams['language']         = $orderApiParams['Language'];
    $buyLinkParams['test']             = (int) $test;
// sid in this case is the merchant code
    $buyLinkParams['merchant'] = $exampleConfig['sellerId'];
    $buyLinkParams['dynamic']  = 1;

    return $buyLinkParams;
}
}

