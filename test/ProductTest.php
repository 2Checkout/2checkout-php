<?php

require_once(dirname(__FILE__) . '/../lib/Twocheckout.php');

class TestProduct extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        Twocheckout::username('username');
        Twocheckout::password('pass');
    }

    public function testProductListRetrieve()
    {
        $params = array(
            'pagesize' => 2
        );
        $products = Twocheckout_Product::retrieve($params);
        $this->assertSame(2, sizeof($products['products']));
    }

    public function testProductCreate()
    {
        $params = array(
            'name' => "test",
            'price' => 0.01
        );
        $response = Twocheckout_Product::create($params);
        $this->assertSame("Product successfully created.", $response['response_message']);

    }

}
