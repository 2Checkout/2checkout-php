<?php
require_once __DIR__ . '/../../../../../../Tco/Autoloader/autoloader.php';

use Tco\TwocheckoutFacade;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../../../Fixtures/TestsConfig.php';

/**
 * If not running PhpUnit tests in PhpSotrm or other IDE just open a terminal at the root folder and run:
 * ./vendor/bin/phpunit --configuration phpunit.xml --testsuite OrderCatalogProductTest
 */
class OrderCatalogProductTest extends TestCase {
    private $tco;
    private $lastRefNo;
    const REFUND_REASON = 'Other';

    private $catalogProductOrderParamsSuccess = array(
        'Country'           => 'br',
        'Currency'          => 'brl',
        'CustomerIP'        => '91.220.121.21',
        'ExternalReference' => 'CustOrderCatProd100',
        'Language'          => 'en',
        'BillingDetails'    =>
            array(
                'Address1'    => 'Test Address',
                'City'        => 'LA',
                'CountryCode' => 'BR',
                'Email'       => 'customer@2Checkout.com',
                'FirstName'   => 'Customer',
                'FiscalCode'  => '056.027.963-98',
                'LastName'    => '2Checkout',
                'Phone'       => '556133127400',
                'State'       => 'DF',
                'Zip'         => '70403-900',
            ),
        'Items'             =>
            array(
                0 =>
                    array(
                        'Code'     => 'E377076E6A_COPY1', //Check in CPANEL at Setup->Products->Code column
                        'Quantity' => '1',
                    ),
            ),
        'PaymentDetails'    =>
            array(
                'Type'          => 'TEST',
                'Currency'      => 'BRL',
                'CustomerIP'    => '91.220.121.21',
                'PaymentMethod' =>
                    array(
                        'CCID'               => '123',
                        'CardNumber'         => '4111111111111111',
                        'CardNumberTime'     => '12',
                        'CardType'           => 'VISA',
                        'ExpirationMonth'    => '12',
                        'ExpirationYear'     => '2023',
                        'HolderName'         => 'John Doe',
                        'HolderNameTime'     => '12',
                        'RecurringEnabled'   => true,
                        'Vendor3DSReturnURL' => 'www.test.com',
                        'Vendor3DSCancelURL' => 'www.test.com',
                    ),
            ),
    );
    private $catalogProductOrderParamsFail = array(
        'Country'           => 'br',
        'Currency'          => 'brl',
        'CustomerIP'        => '91.220.121.21',
        'ExternalReference' => 'CustOrderCatProd100',
        'Language'          => 'en',
        'BillingDetails'    =>
            array(
                'Address1'    => 'Test Address',
                'City'        => 'LA',
                'CountryCode' => 'BR',
                'Email'       => 'customer@2Checkout.com',
                'FirstName'   => 'Customer',
                'FiscalCode'  => '056.027.963-98',
                'LastName'    => '2Checkout',
                'Phone'       => '556133127400',
                'State'       => 'DF',
                'Zip'         => '70403-900',
            ),
        'Items'             =>
            array(
                0 =>
                    array(
                        'Code'     => 'E377076E6A_COPY1', //Check in CPANEL at Setup->Products->Code column
                        'Quantity' => '1',
                    ),
            ),
        'PaymentDetails'    =>
            array(
                'Type'          => 'CC',
                'Currency'      => 'BRL',
                'CustomerIP'    => '91.220.121.21',
                'PaymentMethod' =>
                    array(
                        'CCID'               => '123',
                        'CardNumber'         => '4111111111111111',
                        'CardNumberTime'     => '12',
                        'CardType'           => 'VISA',
                        'ExpirationMonth'    => '12',
                        'ExpirationYear'     => '2023',
                        'HolderName'         => 'John Doe',
                        'HolderNameTime'     => '12',
                        'RecurringEnabled'   => true,
                        'Vendor3DSReturnURL' => 'www.test.com',
                        'Vendor3DSCancelURL' => 'www.test.com',
                    ),
            ),
    );
    private $catalogProductOrderParamsFail2 = array(
        'Country'           => 'br',
        'Currency'          => 'brl',
        'CustomerIP'        => '91.220.121.21',
        'ExternalReference' => 'CustOrderCatProd100',
        'Language'          => 'en',
        'BillingDetails'    =>
            array(
                'Address1'    => 'Test Address',
                'City'        => 'LA',
                'CountryCode' => 'BR',
                'Email'       => 'customer@2Checkout.com',
                'FirstName'   => 'Customer',
                'FiscalCode'  => '056.027.963-98',
                'LastName'    => '2Checkout',
                'Phone'       => '556133127400',
                'State'       => 'DF',
                'Zip'         => '70403-900',
            ),
        'PaymentDetails'    =>
            array(
                'Type'          => 'TEST',
                'Currency'      => 'BRL',
                'CustomerIP'    => '91.220.121.21',
                'PaymentMethod' =>
                    array(
                        'CCID'               => '123',
                        'CardNumber'         => '4111111111111111',
                        'CardNumberTime'     => '12',
                        'CardType'           => 'VISA',
                        'ExpirationMonth'    => '12',
                        'ExpirationYear'     => '2023',
                        'HolderName'         => 'John Doe',
                        'HolderNameTime'     => '12',
                        'RecurringEnabled'   => true,
                        'Vendor3DSReturnURL' => 'www.test.com',
                        'Vendor3DSCancelURL' => 'www.test.com',
                    ),
            ),
    );

    public function setUp(): void {
        $config    = array(
            'sellerId'      => TestsConfig::SELLER_ID,
            'secretKey'     => TestsConfig::SECRET_KEY,
            'curlVerifySsl' => false
        );
        $this->tco = new TwocheckoutFacade( $config );

    }

    public function __toString() {
        return 'OrderCatalogProductTest';
    }

    /**
     * In order to use $lastRefNo we need to run tests
     */
    public function testAllSubtests() {
        $this->_testPlace();
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
        $this->_testFullRefundCatalogProducts();
    }

    public function _testPlace() {
        $response = $this->tco->order()->place( $this->catalogProductOrderParamsSuccess );

        $this->assertArrayHasKey( 'RefNo', $response );
        fwrite( STDOUT, print_r( sprintf( 'Order with Catalog Product placed successfully. REFNO: %s' . PHP_EOL,
            $response['RefNo'] ), true ) );
        $this->lastRefNo = $response['RefNo'];
        $this->assertNotEmpty( $response['RefNo'] );
    }

    public function _testPlaceFail() {
        $response     = $this->tco->order()->place( $this->catalogProductOrderParamsFail );
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
        $response = $this->tco->order()->place( $this->catalogProductOrderParamsFail2 );

        $this->assertArrayNotHasKey( 'RefNo', $response );
        $this->assertArrayHasKey( 'error_code', $response );
        $this->assertArrayHasKey( 'message', $response );

    }

    /**
     * Here we just test the accepted list of valid params
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

    public function _testGetByRefNo() {
        /**
         *Test by RefNo from last successful placed Order.
         */
        $response = $this->tco->order()->getOrder( array( 'RefNo' => $this->lastRefNo ) );
        $this->assertEquals( $response['RefNo'], $this->lastRefNo );
    }

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
        //var_dump($response);
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
        //var_dump($response);
        $this->assertNotEmpty( $response['Items'] );
    }

    public function _testFullRefundCatalogProducts() {
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

        $response = $this->tco->order()->issueRefund( $refundData );
        $this->assertTrue( $response );
    }
}
