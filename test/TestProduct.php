<?php

require_once(dirname(__FILE__) . '/../lib/Twocheckout.php');

class TestProduct extends PHPUnit_Framework_TestCase
{
  public function setUp()
  {
    Twocheckout::setCredentials("APIuser1817037", "APIpass1817037");
  }

  public function testProductListRetrieve()
  {
    $params = array(
      'pagesize' => 20
     );
    $products = Twocheckout_Product::retrieve($params, 'array');
    $this->assertEquals(20, sizeof($products['products']));
  }

  public function testProductCreate()
  {
    $params = array(
      'name' => "test",
      'price' => 0.01
    );
    $response = Twocheckout_Product::create($params, 'array');
    $this->assertEquals("Product successfully created", $response['response_message']);
    $params = array('product_id' => $response['product_id']);
    Twocheckout_Product::delete($params);
  }

  public function testProductRetrieve()
  {
    $params = array(
    'product_id' => 4774387610
    );
    $product = Twocheckout_Product::retrieve($params, 'array');
    $this->assertEquals("4774387610", $product['product']['product_id']);
  }

  public function testProductUpdate()
  {
    $params = array(
      'name' => "test",
      'product_id' => 4774387610
    );
    $response = Twocheckout_Product::update($params, 'array');
    $this->assertEquals("Product successfully updated", $response['response_message']);
  }

  public function testProductDelete()
  {
    $params = array(
      'name' => "test",
      'price' => 0.01
    );
    $response = Twocheckout_Product::create($params, 'array');
    $params = array('product_id' => $response['product_id']);
    $response = Twocheckout_Product::delete($params, 'array');
    $this->assertEquals("Product successfully deleted.", $response['response_message']);
  }

}