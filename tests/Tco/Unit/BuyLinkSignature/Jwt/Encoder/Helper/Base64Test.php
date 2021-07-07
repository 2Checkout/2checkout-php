<?php

require_once __DIR__ . '/../../../../../../../Tco/Autoloader/autoloader.php';

use PHPUnit\Framework\TestCase;
use Tco\BuyLinkSignature\Jwt\Encoder\Helper\Base64;

class Base64Test extends TestCase {
    use Base64;

    public function testToBase64Url(): void {
        $this->assertSame( 'hello-_', $this->toBase64Url( 'he=llo+/' ) );
    }

    public function testToBase64UrlTwo(): void {
        $this->assertSame( '_Wor-_12-_', $this->toBase64Url( '/Wo==r+/12+/=' ) );
    }

    public function testToBase64(): void {
        $this->assertSame( 'QFDvv71ZLO+/ve+/vVF777', $this->toBase64( 'QFDvv71ZLO-_ve-_vVF777' ) );
    }
}
