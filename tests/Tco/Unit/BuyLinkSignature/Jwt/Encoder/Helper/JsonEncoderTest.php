<?php
require_once __DIR__ . '/../../../../../../../Tco/Autoloader/autoloader.php';

use Tco\BuyLinkSignature\Jwt\Encoder\Helper\JsonEncoder;
use PHPUnit\Framework\TestCase;

class JsonEncoderTest extends TestCase {
    use JsonEncoder;

    public function testJsonEncode(): void {
        $array = [ 'hello' => 'world' ];

        $this->assertSame( '{"hello":"world"}', $this->jsonEncode( $array ) );
    }

    public function testJsonEncodeEmptyArray(): void {
        $array = [];
        $this->assertSame( '[]', $this->jsonEncode( $array ) );
    }

    public function testJsonDecode(): void {
        $json = '{"hello":"world"}';
        $this->assertSame( [ 'hello' => 'world' ], $this->jsonDecode( $json ) );
    }

    public function testJsonDecodeEmptyString(): void {
        $json = '';
        $this->assertSame( [], $this->jsonDecode( $json ) );
    }
}
