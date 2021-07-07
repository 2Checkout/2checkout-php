<?php
//var_dump nesting levels when xdebug is active
ini_set( 'xdebug.var_display_max_depth', '10' );
ini_set( 'xdebug.var_display_max_children', '256' );
ini_set( 'xdebug.var_display_max_data', '1024' );
require_once __DIR__ . '/../../../../../../autoloader.php';
require_once __DIR__ . '/../../../../Fixtures/TestsConfig.php';

use PHPUnit\Framework\TestCase;
use Tco\Source\Api\Rest\V6\ApiCore;
use Tco\Source\TcoConfig;
use Tco\Source\Api\Auth\AuthFactory;

final class ApiCoreTest extends TestCase {

    private $tcoConfig;

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

    public function __toString() {
        return 'ApiCoreTest';

    }

    public function setUp() {
        $config          = array(
            'sellerId'      => TestsConfig::SELLER_ID,
            'secretKey'     => TestsConfig::SECRET_KEY,
            'curlVerifySsl' => 0
        );
        $this->tcoConfig = new TcoConfig( $config );
    }

    public function testAllSubtests() {
        $this->testCheckAttributes();
        $this->testCall();
    }

    public function testCheckAttributes() {
        //class scope attributes
        $this->assertClassHasAttribute( 'tcoConfig', 'Tco\Source\Api\Rest\V6\ApiCore' );
        $this->assertClassHasAttribute( 'auth', 'Tco\Source\Api\Rest\V6\ApiCore' );
    }

    public function testCall() {
        $auth = (new AuthFactory($this->tcoConfig))->getAuth();
        $apiCore = new ApiCore( $this->tcoConfig, $auth );
        //Just for testing purposes we will request all regular subscriptions.
        $endpoint = '/orders/';
        $response = $apiCore->call( $endpoint, $this->dynamicProductParamsSuccess );
        $this->assertArrayHasKey( 'RefNo', $response );
    }
}
