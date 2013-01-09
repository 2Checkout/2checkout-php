<?php

require_once(dirname(__FILE__) . '/../lib/Twocheckout.php');

class TestCoupon extends PHPUnit_Framework_TestCase
{
  public function setUp()
  {
    Twocheckout::setCredentials("APIuser1817037", "APIpass1817037");
  }

  public function testCouponListRetrieve()
  {
    $params = array(
      'pagesize' => 10
     );
    $coupons = Twocheckout_Coupon::retrieve($params, 'array');
    $this->assertEquals("OK", $coupons['response_code']);
  }

  public function testCouponCreate()
  {
    $params = array(
      'date_expire' => '2020-01-01',
      'type' => 'shipping',
      'minimum_purchase' => 1.00
    );
    $response = Twocheckout_Coupon::create($params, 'array');
    $this->assertEquals("Coupon successfully created", $response['response_message']);
    $params = array('coupon_code' => $response['coupon_code']);
    Twocheckout_Coupon::delete($params);
  }

  public function testCouponRetrieve()
  {
    $params = array(
    'coupon_code' => "7EBHJFP"
    );
    $coupon = Twocheckout_Coupon::retrieve($params, 'array');
    $this->assertEquals("7EBHJFP", $coupon['coupon']['coupon_code']);
  }

  public function testCouponUpdate()
  {
    $params = array(
      'date_expire' => "2020-01-01",
      'coupon_code' => "7EBHJFP"
    );
    $response = Twocheckout_Coupon::update($params, 'array');
    $this->assertEquals("Coupon updated successfully", $response['response_message']);
  }

  public function testCouponDelete()
  {
    $params = array(
      'date_expire' => '2020-01-01',
      'type' => 'shipping',
      'minimum_purchase' => 1.00
    );
    $response = Twocheckout_Coupon::create($params, 'array');
    $params = array('coupon_code' => $response['coupon_code']);
    $response = Twocheckout_Coupon::delete($params, 'array');
    $this->assertEquals("Coupon successfully deleted.", $response['response_message']);
  }

}