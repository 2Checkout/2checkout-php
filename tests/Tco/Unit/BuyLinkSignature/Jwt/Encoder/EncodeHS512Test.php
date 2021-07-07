<?php
require_once __DIR__ . '/../../../../../../Tco/Autoloader/autoloader.php';

use PHPUnit\Framework\TestCase;
use Tco\BuyLinkSignature\Jwt\Encoder\EncodeHS512;
require_once __DIR__ . '/../../../../Fixtures/Tokens.php';

class EncodeHS512Test extends TestCase
{
    public function testEncode()
    {
        $encode = new EncodeHS512();

        $this->assertSame(Tokens::ENCODED_HEADER, $encode->encode(Tokens::DECODED_HEADER));
        $this->assertSame(Tokens::ENCODED_PAYLOAD, $encode->encode(Tokens::DECODED_PAYLOAD));
    }

    public function testGenerateSignature()
    {
        $encode = new EncodeHS512();

        $signature = $encode->generateSignature(
            Tokens::DECODED_HEADER,
            Tokens::DECODED_PAYLOAD,
            Tokens::SECRET
        );

        $this->assertSame(Tokens::SIGNATURE, $signature);
    }

    public function testUrlEncode(): void
    {
        $encode = new EncodeHS512();

        $method = new ReflectionMethod(EncodeHS512::class, 'urlEncode');
        $method->setAccessible(true);

        $result = $method->invokeArgs($encode, ['!"£$%^&*()1235_-+={POp}[]:;@abE~#,><.?/|\¬']);
        $this->assertSame('ISLCoyQlXiYqKCkxMjM1Xy0rPXtQT3B9W106O0BhYkV-Iyw-PC4_L3xcwqw', $result);
    }


    public function testUrlEncodeIsBase64Url(): void
    {
        $encode = new EncodeHS512();

        $method = new ReflectionMethod(EncodeHS512::class, 'urlEncode');
        $method->setAccessible(true);

        $result = $method->invokeArgs($encode, ['crayon+/=']);

        $this->assertMatchesRegularExpression('/^[a-zA-Z0-9\-\_\=]+$/', $result);
    }


    public function testHash(): void
    {
        $encode = new EncodeHS512();

        $method = new ReflectionMethod(EncodeHS512::class, 'hash');
        $method->setAccessible(true);

        $result = $method->invokeArgs($encode, [ 'sha512', 'hello', '123']);

        $this->assertNotSame('hello', $result);
    }


    public function testGetHash(): void
    {
        $encode = new EncodeHS512();

        $method = new ReflectionMethod(EncodeHS512::class, 'getHashAlgorithm');
        $method->setAccessible(true);

        $result = $method->invoke($encode);

        $this->assertSame('sha512', $result);
    }
}
