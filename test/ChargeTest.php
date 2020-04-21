<?php

require_once(dirname(__FILE__) . '/../lib/Twocheckout.php');

class TestCharge extends PHPUnit_Framework_TestCase
{

    public function testChargeForm()
    {
        $params = array(
            'sid' => 'your seller id',
            'mode' => '2CO',
            'li_0_name' => 'Test Product',
            'li_0_price' => '0.01'
        );
        Twocheckout_Charge::form($params, "Click Here!");
    }

    public function testChargeFormAuto()
    {
        $params = array(
            'sid' => 'your seller id',
            'mode' => '2CO',
            'li_0_name' => 'Test Product',
            'li_0_price' => '0.01'
        );
        Twocheckout_Charge::form($params, 'auto');
    }

    public function testDirect()
    {
        $params = array(
            'sid' => 'your seller id',
            'mode' => '2CO',
            'li_0_name' => 'Test Product',
            'li_0_price' => '0.01',
            'card_holder_name' => 'John Doe',
            'email' => 'christensoncraig@gmail.com',
            'street_address' => '123 test st',
            'city' => 'Columbus',
            'state' => 'Ohio',
            'zip' => '43123',
            'country' => 'USA'
        );
        Twocheckout_Charge::direct($params, "Click Here!");
    }

    public function testDirectAuto()
    {
        $params = array(
            'sid' => 'your seller id',
            'mode' => '2CO',
            'li_0_name' => 'Test Product',
            'li_0_price' => '0.01',
            'card_holder_name' => 'John Doe',
            'email' => 'christensoncraig@gmail.com',
            'street_address' => '123 test st',
            'city' => 'Columbus',
            'state' => 'Ohio',
            'zip' => '43123',
            'country' => 'USA'
        );
        Twocheckout_Charge::direct($params, 'auto');
    }

    public function testChargeLink()
    {
        $params = array(
            'sid' => 'your seller id',
            'mode' => '2CO',
            'li_0_name' => 'Test Product',
            'li_0_price' => '0.01'
        );
        Twocheckout_Charge::link($params);
    }

    public function testChargeAuth()
    {
        Twocheckout::privateKey('your private key');
        Twocheckout::sellerId('your seller id');

        try {
            $charge = Twocheckout_Charge::auth(array(
                "sellerId" => "your seller id",
                "demo" =>true,
                "merchantOrderId" => "123",
                "token" => 'MDY3OTMwMWUtODg5NS00NmFmLWJhNjgtYjMxYTI1ZjhkOWU3',
                "currency" => 'USD',
                "total" => '10.00',
                "billingAddr" => array(
                    "name" => 'John Doe',
                    "addrLine1" => '123 Test St',
                    "city" => 'Columbus',
                    "state" => 'OH',
                    "zipCode" => '43123',
                    "country" => 'USA',
                    "email" => 'testingtester@2co.com',
                    "phoneNumber" => '555-555-5555'
                ),
                "shippingAddr" => array(
                    "name" => 'John Doe',
                    "addrLine1" => '123 Test St',
                    "city" => 'Columbus',
                    "state" => 'OH',
                    "zipCode" => '43123',
                    "country" => 'USA',
                    "email" => 'testingtester@2co.com',
                    "phoneNumber" => '555-555-5555'
                )
            ));
            $this->assertSame('APPROVED', $charge['response']['responseCode']);
        } catch (Twocheckout_Error $e) {
            $this->assertSame('Bad request - parameter error', $e->getMessage());
        }
    }
}
