<?php
require_once __DIR__ . '/../../../Tco/Autoloader/autoloader.php';

use Tco\TwocheckoutFacade;
use PHPUnit\Framework\TestCase;
require_once __DIR__ . '/../Fixtures/TestsConfig.php';
require_once __DIR__ . '/../Fixtures/Tokens.php';

class TwocheckoutFacadeTest extends TestCase {

    public $config;

    public function setUp(): void {

        $this->config    = array(
            'sellerId'      => TestsConfig::SELLER_ID,
            'secretKey'     => TestsConfig::SECRET_KEY,
            'curlVerifySsl' => true
        );
    }

    public function __toString() {
        return 'TwocheckoutFacadeTest';
    }

    public function testAll(){
        $this->_testCheckAttributes();
        $this->_testException();
    }

    public function _testCheckAttributes() {
        //class scope attributes
        $this->assertClassHasAttribute( 'tcoConfig', 'Tco\TwocheckoutFacade' );
        $this->assertClassHasAttribute( 'logger', 'Tco\TwocheckoutFacade' );
        $this->assertClassHasAttribute( 'auth', 'Tco\TwocheckoutFacade' );
        $this->assertClassHasAttribute( 'apiCore', 'Tco\TwocheckoutFacade' );
    }

    public function _testException(){
        $config    = array(
            'sellerId'      => TestsConfig::SELLER_ID,
            'secretKey'     => '',
            'curlVerifySsl' => true
        );
        $this->expectException(Exception::class);
        $tcoConfig = new TwocheckoutFacade($config);
    }
}
