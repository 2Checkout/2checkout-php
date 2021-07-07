<?php
require_once __DIR__ . '/../../../../autoloader.php';
require_once __DIR__ . '/../../../../tests/Tco/Fixtures/TestsConfig.php';
require_once __DIR__ . '/../../../../tests/Tco/Fixtures/Tokens.php';

use PHPUnit\Framework\TestCase;
use Tco\Source\Ipn\IpnSignature;

final class IpnSignatureTest extends TestCase {

    private $ipnParamsMockArray;
    public $configArr;


    public function __toString() {
        return 'IpnSignatureTest';
    }

    public function setUp(): void {
        $this->configArr = array(
            'sellerId'          => TestsConfig::SELLER_ID,
            'secretKey'         => TestsConfig::SECRET_KEY,
            'buyLinkSecretWord' => TestsConfig::SECRET_WORD,
            'jwtExpireTime'     => 30, //minutes
            'curlVerifySsl'     => 0
        );

        $this->ipnParamsMockArray = json_decode(Tokens::IPN_CALLBACK_JSON, true);
    }

    public function testAll() {
        $this->_testIsIpnValid();
        $this->_testCalculateIpnResponse();
        $this->_testCalculateIpnResponseFailure();
    }

    public function _testIsIpnValid() {
        $tcoConfig = new \Tco\Source\TcoConfig($this->configArr);
        $ipnSignature = new IpnSignature($tcoConfig);
        $isValid = $ipnSignature->isIpnValid($this->ipnParamsMockArray);
        $this->assertTrue($isValid);
    }

    public function _testCalculateIpnResponse(){
        $tcoConfig = new \Tco\Source\TcoConfig($this->configArr);
        $ipnSignature = new IpnSignature($tcoConfig);
        $response = $ipnSignature->calculateIpnResponse($this->ipnParamsMockArray);
        $this->assertNotEmpty($response);
    }

    public function _testCalculateIpnResponseFailure(){
        $tcoConfig = new \Tco\Source\TcoConfig($this->configArr);
         unset($this->ipnParamsMockArray['IPN_PID']);
        $ipnSignature = new IpnSignature($tcoConfig);
        $this->expectException(Exception::class);
        $response = $ipnSignature->calculateIpnResponse($this->ipnParamsMockArray);
    }
}
