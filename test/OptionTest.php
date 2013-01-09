<?php

require_once(dirname(__FILE__) . '/../lib/Twocheckout.php');

class TestOption extends PHPUnit_Framework_TestCase
{
  public function setUp()
  {
    Twocheckout::setCredentials("APIuser1817037", "APIpass1817037");
  }         

  public function testOptionListRetrieve()
  {
    $params = array(
      'pagesize' => 20
     );
    $options = Twocheckout_Option::retrieve($params, 'array');
    $this->assertEquals(20, sizeof($options['options']));
  }

  public function testOptionCreate()
  {
    $params = array(
      'option_name' => "test",
      'option_value_name' => "test",
      'option_value_surcharge' => 0.01
    );
    $response = Twocheckout_Option::create($params, 'array');
    $this->assertEquals("Option created successfully", $response['response_message']);
    $params = array('option_id' => $response['option_id']);
    Twocheckout_Option::delete($params);
  }

  public function testOptionRetrieve()
  {
    $params = array(
    'option_id' => 4774404362
    );
    $product = Twocheckout_Option::retrieve($params, 'array');
    $this->assertEquals("4774404362", $product['option'][0]['option_id']);
  }

  public function testOptionUpdate()
  {
    $params = array(
      'option_name' => "test1",
      'option_id' => 4774404362
    );
    $response = Twocheckout_Option::update($params, 'array');
    $this->assertEquals("Option updated successfully", $response['response_message']);
  }

  public function testOptionDelete()
  {
    $params = array(
      'option_name' => "test",
      'option_value_name' => "test",
      'option_value_surcharge' => 0.01
    );
    $response = Twocheckout_Option::create($params, 'array');
    $params = array('option_id' => $response['option_id']);
    $response = Twocheckout_Option::delete($params, 'array');
    $this->assertEquals("Option deleted successfully", $response['response_message']);
  }

}