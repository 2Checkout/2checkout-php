<?php

require_once(dirname(__FILE__) . '/../lib/Twocheckout.php');

class TestCharge extends PHPUnit_Framework_TestCase
{

  public function testChargeForm()
  {
    $params = array(
    'sid' => '1817037',
    'mode' => '2CO',
    'li_0_name' => 'Test Product',
    'li_0_price' => '0.01'
    );
    $test = '<form id="2checkout" action="https://www.2checkout.com/checkout/purchase" method="post">' +
              '<input type="hidden" name="sid" value="1817037"/>' +
              '<input type="hidden" name="mode" value="2CO"/>' +
              '<input type="hidden" name="li_0_name" value="Test Product"/>' +
              '<input type="hidden" name="li_0_price" value="0.01"/>' +
              '<input type="submit" value="Click Here!" />' +
             '</form>';
    $result = Twocheckout_Charge::form($params, "Click Here!");
    $this->assertEquals($test, $result);
  }

  public function testChargeFormAuto()
  {
    $params = array(
    'sid' => '1817037',
    'mode' => '2CO',
    'li_0_name' => 'Test Product',
    'li_0_price' => '0.01'
    );
    $test = '<form id="2checkout" action="https://www.2checkout.com/checkout/purchase" method="post">' +
              '<input type="hidden" name="sid" value="1817037"/>' +
              '<input type="hidden" name="mode" value="2CO"/>' +
              '<input type="hidden" name="li_0_name" value="Test Product"/>' +
              '<input type="hidden" name="li_0_price" value="0.01"/>' +
              '<input type="submit" value="Click here if you are not redirected automatically" /></form>' +
              '<script type="text/javascript">document.getElementById(\'2checkout\').submit();</script>';
    $result = Twocheckout_Charge::form($params, 'auto');
    $this->assertEquals($test, $result);
  }

  public function testChargeLink()
  {
    $params = array(
    'sid' => '1817037',
    'mode' => '2CO',
    'li_0_name' => 'Test Product',
    'li_0_price' => '0.01'
    );
    $test = 'https://www.2checkout.com/checkout/purchase?sid=1817037&amp;mode=2CO&amp;li_0_name=Test+Product&amp;li_0_price=0.01';
    $result = Twocheckout_Charge::link($params);
    $this->assertEquals($test, $result);
  }

}
