<?php
require_once __DIR__ . '/../../../autoloader.php';

use Tco\Source\TcoConfig;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../Fixtures/TestsConfig.php';

class TcoConfigTest extends TestCase {

    public $config;

    public function setUp(): void {

        $this->config = array(
            'sellerId'      => 'test',
            'secretKey'     => 'testKey',
            'curlVerifySsl' => 1
        );
    }

    public function __toString() {
        return 'TcoConfigTest';
    }

    public function testAll() {
        $this->_testInstantiation();
        $this->_testValidateConfig();
        $this->_testValidateConfigRequired();
        $this->_testSetConfig();
        $this->_testSetConfigException();
        $this->_testGetJwtExpirationTimeInSeconds();
        $this->_testGetJwtExpirationTimeInSecondsFromConfig();
    }

    public function _testInstantiation() {
        $tcoConfig = new TcoConfig( $this->config );
        $this->assertNotEmpty( $tcoConfig->getSellerId() );
        $this->assertNotEmpty( $tcoConfig->getSecretKey() );
        $this->assertNotEmpty( $tcoConfig->getCurlVerifySsl() );
    }

    public function _testValidateConfig() {
        $config        = array(
            'sellerId'          => 'test',
            'secretKey'         => 'testSecretKey',
            'curlVerifySsl'     => 0,
            'buyLinkSecretWord' => 'askjrghait8943275dfss294325asfa'
        );
        $tcoConfig     = new TcoConfig( array() );
        $configIsValid = $tcoConfig->validateConfig( $config );
        $this->assertTrue( $configIsValid );
    }

    public function _testValidateConfigRequired() {
        $config        = array(
            'sellerId'  => 'test',
            'secretKey' => ''
        );
        $tcoConfig     = new TcoConfig( array() );
        $configIsValid = $tcoConfig->validateConfig( $config );
        $this->assertFalse( $configIsValid );
    }

    public function _testSetConfig() {
        $config    = array(
            'sellerId'  => 'test',
            'secretKey' => 'test',
        );
        $expected  = array(
            'sellerId'      => 'test',
            'secretKey'     => 'test',
            'curlVerifySsl' => 0,
        );
        $tcoConfig = new TcoConfig( array() );
        $tcoConfig->setConfig( $config );
        $this->assertEquals( $expected['sellerId'], $tcoConfig->getSellerId() );
        $this->assertEquals( $expected['secretKey'], $tcoConfig->getSecretKey() );
        $this->assertEquals( $expected['curlVerifySsl'], $tcoConfig->getCurlVerifySsl() );
    }

    public function _testSetConfigException() {
        $config    = array(
            'sellerId'  => 'test',
            'secretKey' => '',
        );
        $this->expectException('Tco\Exceptions\TcoException');
        $tcoConfig = new TcoConfig( $config );

    }

    public function _testGetJwtExpirationTimeInSeconds() {
        $config    = array(
            'sellerId'          => 'test',
            'secretKey'         => 'testSecretKey',
            'curlVerifySsl'     => 0,
            'buyLinkSecretWord' => 'askjrghait8943275dfss294325asfa'
        );
        $tcoConfig = new TcoConfig( array() );
        $tcoConfig->setConfig( $config );
        $expectedJwtExpirationTimeInSeconds = 30 * 60;
        $actualJwtExpirationTimeInSeconds   = $tcoConfig->getJwtExpirationTimeInSeconds();
        $this->assertEquals( $expectedJwtExpirationTimeInSeconds, $actualJwtExpirationTimeInSeconds );
    }

    public function _testGetJwtExpirationTimeInSecondsFromConfig() {
        $config    = array(
            'sellerId'          => 'test',
            'secretKey'         => 'testSecretKey',
            'curlVerifySsl'     => 0,
            'buyLinkSecretWord' => 'askjrghait8943275dfss294325asfa',
            'jwtExpireTime'     => 20
        );
        $tcoConfig = new TcoConfig( array() );
        $tcoConfig->setConfig( $config );
        $expectedJwtExpirationTimeInSeconds = $config['jwtExpireTime'] * 60;
        $actualJwtExpirationTimeInSeconds   = $tcoConfig->getJwtExpirationTimeInSeconds();
        $this->assertEquals( $expectedJwtExpirationTimeInSeconds, $actualJwtExpirationTimeInSeconds );
    }
}
