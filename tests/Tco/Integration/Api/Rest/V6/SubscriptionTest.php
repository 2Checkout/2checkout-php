<?php
//var_dump nesting levels when xdebug is active
ini_set( 'xdebug.var_display_max_depth', '10' );
ini_set( 'xdebug.var_display_max_children', '256' );
ini_set( 'xdebug.var_display_max_data', '1024' );
require_once __DIR__ . '/../../../../../../Tco/Autoloader/autoloader.php';

use Tco\TwocheckoutFacade;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../../../Fixtures/TestsConfig.php';

class SubscriptionTest extends TestCase {
    private $tco;
    private $dpOrderRefno;
    const REFUND_REASON = 'Other';

    /**
     * Simple Dynamic Product Subscription
     */
    private $dynamicProductSubscriptionParamsSuccess = array(
        'Country'           => 'us',
        'Currency'          => 'USD',
        'CustomerIP'        => '91.220.121.21',
        'ExternalReference' => 'CustOrd100Subscription',
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
                        'Name'             => 'DP Subscription', //(Dynamic Product subscription)
                        'Description'      => 'Test description',
                        'Quantity'         => 1,
                        'IsDynamic'        => true,
                        'Tangible'         => false,
                        'PurchaseType'     => 'PRODUCT',
                        'Price'            =>
                            array(
                                'Amount' => 2, //value
                                'Type'   => 'CUSTOM',
                            ),

                        //Dynamic product subscription.
                        'PriceOptions'     =>
                            array(
                                0 =>
                                    array(
                                        'Name'    => 'OPT1',
                                        'Options' =>
                                            array(
                                                0 =>
                                                    array(
                                                        'Name'      => 'Dp Option 1',
                                                        'Value'     => 'Value DP opt1',
                                                        'Surcharge' => 3, //value for this product option
                                                    ),
                                            ),
                                    ),
                            ),
                        'RecurringOptions' =>
                            array(
                                'CycleLength'    => 1,
                                'CycleUnit'      => 'MONTH',
                                'CycleAmount'    => 3,
                                'ContractLength' => 3,
                                'ContractUnit'   => 'Year',
                            ),
                    ),
                1 =>
                    array(
                        'Name'             => 'DP2WithSub', //(Dynamic Product subscription)
                        'Description'      => 'Dynamic prod Test with description 2',
                        'Quantity'         => 3,
                        'IsDynamic'        => true,
                        'Tangible'         => false,
                        'PurchaseType'     => 'PRODUCT',
                        'Price'            =>
                            array(
                                'Amount' => 2, //value
                                'Type'   => 'CUSTOM',
                            ),

                        //Dynamic product subscription.
                        /*'PriceOptions'     =>
                            array(
                                0 =>
                                    array(
                                        'Name'    => 'OPT1',
                                        'Options' =>
                                            array(
                                                0 =>
                                                    array(
                                                        'Name'      => 'Dp Option 1',
                                                        'Value'     => 'Value DP opt1',
                                                        'Surcharge' => 3, //value for this product option
                                                    ),
                                            ),
                                    ),
                            ),*/
                        'RecurringOptions' =>
                            array(
                                'CycleLength'    => 1,
                                'CycleUnit'      => 'MONTH',
                                'CycleAmount'    => 6,
                                'ContractLength' => 2,
                                'ContractUnit'   => 'Year',
                            ),
                    ),
                2 =>
                    array(
                        'Name'             => 'DP3WithSub', //(Dynamic Product subscription)
                        'Description'      => 'Dynamic prod Test with description 3',
                        'Quantity'         => 5,
                        'IsDynamic'        => true,
                        'Tangible'         => false,
                        'PurchaseType'     => 'PRODUCT',
                        'Price'            =>
                            array(
                                'Amount' => 1, //value
                                'Type'   => 'CUSTOM',
                            ),

                        //Dynamic product subscription.
                        /*'PriceOptions'     =>
                            array(
                                0 =>
                                    array(
                                        'Name'    => 'OPT1',
                                        'Options' =>
                                            array(
                                                0 =>
                                                    array(
                                                        'Name'      => 'Dp Option 1',
                                                        'Value'     => 'Value DP opt1',
                                                        'Surcharge' => 3, //value for this product option
                                                    ),
                                            ),
                                    ),
                            ),*/
                        'RecurringOptions' =>
                            array(
                                'CycleLength'    => 36,
                                'CycleUnit'      => 'MONTH',
                                'CycleAmount'    => 5,
                                'ContractLength' => 3,
                                'ContractUnit'   => 'Year',
                            ),
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

    public function setUp(): void {
        $config             = array(
            'sellerId'      => TestsConfig::SELLER_ID,
            'secretKey'     => TestsConfig::SECRET_KEY,
            'curlVerifySsl' => false
        );
        $this->tco          = new TwocheckoutFacade( $config );
        $this->dpOrderRefno = '146089136';
    }

    public function __toString() {
        return 'SubscriptionTest';
    }

    public function testCheckAttributes() {
        //class scope attributes
        $this->assertClassHasAttribute( 'core', 'Tco\Api\Rest\V6\Subscription' );
        $this->assertClassHasAttribute( 'acceptedSearchSubscriptionParams', 'Tco\Api\Rest\V6\Subscription' );
    }

    public function testAllSubtests() {
        //$this->_testPlaceOrderWithDynamicProductsAndSubscriptions();
        $this->_testValidateSubscriptionSearchParams();
        $this->_testGetSubscriptionsRefByOrderRefNo();
        $this->_testSubscriptionSearchBySubscriptionRef();
    }

    public function _testPlaceOrderWithDynamicProductsAndSubscriptions() {
        $response = $this->tco->order()->place( $this->dynamicProductSubscriptionParamsSuccess );

        $this->assertArrayHasKey( 'RefNo', $response );
        fwrite( STDOUT,
            print_r( sprintf( 'Order with dynamic product with SUBSCRIPTION placed successfully. REFNO: %s' . PHP_EOL,
                $response['RefNo'] ), true ) );

        $this->dpOrderRefno = $response['RefNo'];
        $this->assertNotEmpty( $this->dpOrderRefno );
    }

    /**
     * Test the accepted list of valid params
     */
    public function _testValidateSubscriptionSearchParams() {
        $testParams     = array(
            'SubscriptionReference'     => '',
            'CustomerEmail'             => '',
            'DeliveredCode'             => '',
            'AvangateCustomerReference' => '',
            'ExternalCustomerReference' => '',
            'Aggregate'                 => '',
            'SubscriptionEnabled'       => '',
            'RecurringEnabled'          => '',
            'ProductCodes'              => '',
            'CountryCodes'              => '',
            'PurchasedAfter'            => '',
            'PurchasedBefore'           => '',
            'ExpireAfter'               => '',
            'ExpireBefore'              => '',
            'RenewedAfter'              => '',
            'RenewedBefore'             => '',
            'NotificationAfter'         => '',
            'NotificationBefore'        => '',
            'ModifiedAfter'             => '',
            'ModifiedBefore'            => '',
            'NextBillingDateAfter'      => '',
            'NextBillingDateBefore'     => '',
            'LifetimeSubscription'      => '',
            'ModifiedAfter'             => '',
            'MerchantCode'              => ''
        );
        $rejectedParams = $this->tco->subscription()->validateSubscriptionSearchParams( $testParams );
        $this->assertEmpty( $rejectedParams );
    }

    public function _testGetSubscriptionsRefByOrderRefNo() {
        /**We need to wait a few seconds, I chose 15 seconds to be sure the subscription is attached to Order
         * Product in TCO*/

        sleep( 7 );
        $productLineItemRefSubscriptionRefArray = $this->tco->subscription()->getSubscriptionsByOrderRefNo(
            $this->dpOrderRefno );
        $this->assertEquals(count($this->dynamicProductSubscriptionParamsSuccess['Items']), count
        ($productLineItemRefSubscriptionRefArray));
    }

    public function _testSubscriptionSearchBySubscriptionRef() {
        $productLineItemRefSubscriptionRefArray = $this->tco->subscription()->getSubscriptionsByOrderRefNo(
            $this->dpOrderRefno );

        //get first Subscription Reference
        $subscriptionSearch['SubscriptionReference'] = reset($productLineItemRefSubscriptionRefArray)[0];
        if ( ! empty( $subscriptionSearch ) ) {
            $result = $this->tco->subscription()->searchSubscriptions( $subscriptionSearch );
            $this->assertEquals( $result['LatestSubscriptionOrder']['RefNo'], $this->dpOrderRefno );
        } else {
            fwrite( STDERR, 'No search parameters found for subscription!' );
            return;
        }
    }
}
