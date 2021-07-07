<?php
namespace Tco\Examples\Common\OrderParams;

class DynamicProducts {

    public function getDynamicProductSuccessParams() {
        $dynamicProductParamsSuccess = array(
            'Country'           => 'US',
            'Currency'          => 'USD',
            'CustomerIP'        => '91.220.121.21',
            'ExternalReference' => 'CustOrd101',
            'Language'          => 'en',
            'Source'            => 'tcolib.local',
            'BillingDetails'    =>
                array(
                    'Address1'    => 'Street 1',
                    'City'        => 'Cleveland',
                    'State'       => 'Ohio',
                    'CountryCode' => 'US',
                    'Email'       => 'testcustomer@2Checkout.com',
                    'FirstName'   => 'John',
                    'LastName'    => 'Doe',
                    'Zip'         => '20034',
                ),
            'Items'             =>
                array(
                    0 =>
                        array(
                            'Name'         => 'Colored Pencil',
                            'Description'  => 'Test description',
                            'Quantity'     => 1,
                            'IsDynamic'    => true,
                            'Tangible'     => false,
                            'PurchaseType' => 'PRODUCT',
                            'Price'        =>
                                array(
                                    'Amount' => 2, //value
                                    'Type'   => 'CUSTOM',
                                )
                        )
                ),
            'PaymentDetails'    =>
                array(
                    //'Type'          => 'EES_TOKEN_PAYMENT', //'TEST' or 'EES_TOKEN_PAYMENT'
                    'Currency'      => 'USD',
                    'CustomerIP'    => '91.220.121.21',
                    'PaymentMethod' =>
                        array(
                            'RecurringEnabled' => false,
                            'HolderNameTime'   => 1,
                            'CardNumberTime'   => 1,
                        ),
                ),
        );

        return $dynamicProductParamsSuccess;
    }
}
