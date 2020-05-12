<?php
require_once(dirname(__FILE__) . '/../lib/Twocheckout.php');
class TwocheckoutTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        Twocheckout::username('username');
        Twocheckout::password('pass');
    }

    public function testCompanyRetrieve()
    {
        $company = Twocheckout_Company::retrieve();
        $this->assertSame("250111206876", $company['vendor_company_info']['vendor_id']);
    }

    public function testContactRetrieve()
    {
        $company = Twocheckout_Contact::retrieve();
        $this->assertSame("250111206876", $company['vendor_contact_info']['vendor_id']);
    }
  
}
