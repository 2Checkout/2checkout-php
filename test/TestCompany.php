<?php
require_once(dirname(__FILE__) . '/../lib/Twocheckout.php');
class TwocheckoutTest extends PHPUnit_Framework_TestCase
{

  public function setUp()
  {
  	Twocheckout::setCredentials("APIuser1817037", "APIpass1817037");
  }

  public function testCompanyRetrieve()
  {
	$company = Twocheckout_Company::retrieve('array');
    $this->assertEquals("1817037", $company['vendor_company_info']['vendor_id']);
  }

  public function testContactRetrieve()
  {
  $company = Twocheckout_Contact::retrieve('array');
    $this->assertEquals("1817037", $company['vendor_contact_info']['vendor_id']);
  }
  
}