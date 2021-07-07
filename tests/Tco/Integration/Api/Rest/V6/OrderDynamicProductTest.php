<?php
//var_dump nesting levels when xdebug is active
ini_set( 'xdebug.var_display_max_depth', '10' );
ini_set( 'xdebug.var_display_max_children', '256' );
ini_set( 'xdebug.var_display_max_data', '1024' );
require_once __DIR__ . '/../../../../../../autoloader.php';
require_once __DIR__ . '/../../../../Fixtures/TestsConfig.php';

use Tco\TwocheckoutFacade;
use PHPUnit\Framework\TestCase;

/**
 * If not running PhpUnit tests in PhpSotrm or other IDE just open a terminal at the root folder and run:
 * ./vendor/bin/phpunit --configuration phpunit.xml --testsuite OrderDynamicProductTest
 */
class OrderDynamicProductTest extends TestCase {
    private $tco;
    private $lastRefNo;
    private $refNoForPartialRefund;
    const REFUND_REASON = 'Other';

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
                        'Name'         => 'Dynamic product',
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
    private $dynamicProductParamsSuccessForPartialRefund = array(
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
                        'Name'         => 'Dynamic product2',
                        'Description'  => 'Product 2',
                        'Quantity'     => 3, //units
                        'IsDynamic'    => true,
                        'Tangible'     => false,
                        'PurchaseType' => 'PRODUCT',
                        'Price'        =>
                            array(
                                'Amount' => 6, //value
                                'Type'   => 'CUSTOM',
                            ),
                    ),
                1 =>
                    array(
                        'Name'         => 'Dynamic product',
                        'Description'  => 'Test description',
                        'Quantity'     => 4,
                        'IsDynamic'    => true,
                        'Tangible'     => false,
                        'PurchaseType' => 'PRODUCT',
                        'Price'        =>
                            array(
                                'Amount' => 1,
                                'Type'   => 'CUSTOM',
                            )
                    ),
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
    private $dynamicProductParamsFail = array(
        'Country'           => 'us',
        'Currency'          => 'USD',
        'CustomerIP'        => '91.220.121.21',
        'ExternalReference' => 'REST_API_TCO_TEST',
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
                        'Name'         => 'Dynamic product',
                        'Description'  => 'Test description',
                        'Quantity'     => 1,
                        'IsDynamic'    => true,
                        'Tangible'     => false,
                        'PurchaseType' => 'PRODUCT',
                        'Price'        =>
                            array(
                                'Amount' => 100,
                                'Type'   => 'CUSTOM',
                            ),
                        'PriceOptions' =>
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
                            ),
                    ),
            ),
        'PaymentDetails'    =>
            array(
                'Type'          => 'CC',
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
    private $dynamicProductParamsFaill2 = array(
        'Country'           => 'us',
        'Currency'          => 'USD',
        'CustomerIP'        => '91.220.121.21',
        'ExternalReference' => 'REST_API_TCO_TEST',
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
        'PaymentDetails'    =>
            array(
                'Type'          => 'TEST',
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

    public function setUp(): void {

        $config    = array(
            'sellerId'      => TestsConfig::SELLER_ID,
            'secretKey'     => TestsConfig::SECRET_KEY,
            'curlVerifySsl' => 0
        );
        $this->tco = new TwocheckoutFacade( $config );
    }

    public function __toString() {
        return 'OrderDynamicProductTest';
    }

    public function testCheckAttributes() {
        //class scope attributes
        $this->assertClassHasAttribute( 'core', 'Tco\Source\Api\Rest\V6\Order' );
        $this->assertClassHasAttribute( 'acceptedSearchOrderParams', 'Tco\Source\Api\Rest\V6\Order' );
    }

    /**
     * In order to use $lastRefNo we need to run tests
     * PhpUnit will reinitialise class members for each tests
     */
    public function testAllSubtests() {
        $this->_testPlace();
        $this->_testPlaceForPartialRefunds();
        $this->_testPlaceFail();
        $this->_testPlaceFailNoRefNo();
        $this->_testValidateGetParams();
        $this->_testGetByRefNo();

        /**Commented tests work only for live orders.
         * You need to have "Orders params structure lists" changed for live orders.*/
        /*$this->_testGetByStartDateEndDate();
        $this->_testGetByNewer();
        $this->_testGetByApprovedStatus();
        $this->_testGetByStatus();
        $this->_testGetByExternalRefNo();
        $this->_testGetByPageAndLimit();*/
        $this->_testFullRefund();
        //$this->_testPartialRefund();
    }

    /**
     * Test placing Orders for full refunds (_testPlace) && partial refunds (_testPlaceForPartialRefunds)
     */
    public function _testPlace() {
        $response = $this->tco->order()->place( $this->dynamicProductParamsSuccess );

        $this->assertArrayHasKey( 'RefNo', $response );
        fwrite( STDOUT, print_r( sprintf( 'Order with dynamic product placed successfully. REFNO: %s' . PHP_EOL,
            $response['RefNo'] ), true ) );
        $this->lastRefNo = $response['RefNo'];
        $this->assertNotEmpty( $response['RefNo'] );
    }

    public function _testPlaceForPartialRefunds() {
        $response = $this->tco->order()->place( $this->dynamicProductParamsSuccessForPartialRefund );
        $this->assertArrayHasKey( 'RefNo', $response );
        fwrite( STDOUT,
            print_r( sprintf( 'Order with dynamic product (for PARTIAL Refunds) placed successfully. REFNO: %s' . PHP_EOL,
                $response['RefNo'] ), true ) );
        $this->refNoForPartialRefund = $response['RefNo'];
        $this->assertNotEmpty( $response['RefNo'] );
    }

    /**
     * Test placing Orders that fail
     */
    public function _testPlaceFail() {
        $response     = $this->tco->order()->place( $this->dynamicProductParamsFail );
        $errorMessage = '';
        if ( isset( $response['RefNo'] ) && $response['Errors'] ) {
            $errorMessage = '';
            foreach ( $response['Errors'] as $key => $value ) {
                $errorMessage .= $value . PHP_EOL;
            }
        }
        $this->assertNotEmpty( $errorMessage );
    }

    public function _testPlaceFailNoRefNo() {
        $response = $this->tco->order()->place( $this->dynamicProductParamsFaill2 );
        $this->assertArrayNotHasKey( 'RefNo', $response );
        $this->assertArrayHasKey( 'error_code', $response );
        $this->assertArrayHasKey( 'message', $response );
    }

    /**
     * Test the accepted list of valid params
     */

    public function _testValidateGetParams() {

        $currentDate    = new DateTime();
        $twoDaysDT      = $currentDate->sub( new DateInterval( 'P2D' ) );
        $testParams     = array(
            'RefNo'         => '',
            'ApproveStatus' => 'OK',
            'Newer'         => strtotime( '2021-02-14' ),
            'Status'        => 'PENDING',
            'StartDate'     => $currentDate->format( 'Y-m-d' ),
            'EndDate'       => $twoDaysDT->format( 'Y-m-d' ),
            'PartnerOrders' => false,
            'ExternalRefNo' => 'CustOrd100',
            'Page'          => '',
            'Limit'         => ''
        );
        $rejectedParams = $this->tco->order()->validateOrderSearchParams( $testParams );
        $this->assertEmpty( $rejectedParams );
    }

    /**
     *Test by RefNo from Dynamic Product, full refunds, (first order) successful placed Order.
     */
    public function _testGetByRefNo() {
        $response = $this->tco->order()->getOrder( array( 'RefNo' => $this->lastRefNo ) );
        $this->assertEquals( $response['RefNo'], $this->lastRefNo );
    }

    /**
     * REAL CARD TESTS BLOCK
     * In order to correctly tests the next block of tests you nee to place the order from $dynamicProductParamsSuccess
     * using a real card!
     */

    public function _testGetByStartDateEndDate() {
        $currentDate = new DateTime();
        $twoDaysDT   = $currentDate->sub( new DateInterval( 'P5D' ) );
        $response    = $this->tco->order()->getOrder( array(
            'StartDate' => $twoDaysDT->format( 'Y-m-d' ),
            'EndDate'   => $currentDate->format( 'Y-m-d' )
        ) );

        $this->assertNotEmpty( $response['Items'] );
    }

    public function _testGetByNewer() {
        $currentDate = new DateTime();
        $twoDaysDT   = $currentDate->sub( new DateInterval( 'P5D' ) );
        $response    = $this->tco->order()->getOrder( array( 'Newer' => $twoDaysDT->getTimestamp() ) );
        $this->assertNotEmpty( $response['Items'] );
    }

    public function _testGetByExternalRefNo() {
        $response = $this->tco->order()->getOrder( array( 'ExternalRefNo' => $this->dynamicProductParamsSuccess['ExternalReference'] ) );
        $this->assertNotEmpty( $response['Items'] );
    }

    public function _testGetByApprovedStatus() {
        $currentDate = new DateTime();
        $twoDaysDT   = $currentDate->sub( new DateInterval( 'P5D' ) );
        $response    = $this->tco->order()->getOrder( array(
                'ApproveStatus' => 'OK',
                'StartDate'     => $twoDaysDT->format( 'Y-m-d' ),
                'EndDate'       => $currentDate->format( 'Y-m-d' )
            )
        );
        $this->assertNotEmpty( $response['Items'] );
    }

    public function _testGetByStatus() {
        $currentDate = new DateTime();
        $twoDaysDT   = $currentDate->sub( new DateInterval( 'P5D' ) );
        $response    = $this->tco->order()->getOrder( array(
                'Status'    => 'COMPLETE',
                'StartDate' => $twoDaysDT->format( 'Y-m-d' ),
                'EndDate'   => $currentDate->format( 'Y-m-d' )
            )
        );
        $this->assertNotEmpty( $response['Items'] );
    }

    public function _testGetByPageAndLimit() {
        /**
         * For this to make sense you need to have Count > Limit (in results).
         */
        $response = $this->tco->order()->getOrder( array(
                'Status' => 'COMPLETE',
                'Limit'  => '2',
                'Page'   => '2'
            )
        );
        $this->assertNotEmpty( $response['Items'] );
    }

    /**
     * END REAL CARD TESTS BLOCK
     */

    public function _testFullRefund() {
        /**
         * Refund message structure done by requesting for order info from TCO Api
         */
        $orderData = $this->tco->order()->getOrder( array( 'RefNo' => $this->lastRefNo ) );
        $this->assertEquals( $orderData['RefNo'], $this->lastRefNo );

        // Constructing FULL Refund Details
        $refundData = array(
            'RefNo'        => $orderData['RefNo'],
            'refundParams' => array(
                "amount"  => $orderData["GrossPrice"],
                "comment" => 'Integration test REFUND.',
                "reason"  => self::REFUND_REASON
            )
        );
        $response   = $this->tco->order()->issueRefund( $refundData );
        $this->assertTrue( $response );
    }

    public function _testPartialRefund() {
        /**
         * Refund message structure created by requesting for order info from TCO Api
         */
        $orderData = $this->tco->order()->getOrder( array( 'RefNo' => $this->refNoForPartialRefund ) );
        $this->assertEquals( $orderData['RefNo'], $this->refNoForPartialRefund );

        $lineItems = $orderData["Items"];

        //just get the second product
        $lineitemReference = $lineItems[1]["LineItemReference"];
        // Refund Item Details
        $itemsArray[] = [
            "Quantity"          => "2",
            "LineItemReference" => $lineitemReference,
            "Amount"            => 2
        ];

        // Refund Details
        $refundData = [
            'RefNo'        => $orderData['RefNo'],
            'refundParams' => array(
                "amount"  => $orderData["GrossPrice"],
                "comment" => 'Integration test Partial REFUND.',
                "reason"  => self::REFUND_REASON,
                "items"   => $itemsArray
            )
        ];
        //If running in PhpStorm
        //fwrite( STDOUT, print_r($refundData, true) );

        //if running from terminal
        //var_dump( $refundData );
        $response = $this->tco->order()->issueRefund( $refundData );

        //var_dump( $response );
        //$this->assertTrue($response);
    }
}
