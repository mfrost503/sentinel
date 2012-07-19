<?php

namespace ZendTest\Http\Header;

use Zend\Http\Header\Expires;

class ExpiresTest extends \PHPUnit_Framework_TestCase
{
    public function testExpiresFromStringCreatesValidExpiresHeader()
    {
        $expiresHeader = Expires::fromString('Expires: Sun, 06 Nov 1994 08:49:37 GMT');
        $this->assertInstanceOf('Zend\Http\Header\HeaderInterface', $expiresHeader);
        $this->assertInstanceOf('Zend\Http\Header\Expires', $expiresHeader);
    }

    public function testExpiresGetFieldNameReturnsHeaderName()
    {
        $expiresHeader = new Expires();
        $this->assertEquals('Expires', $expiresHeader->getFieldName());
    }

    public function testExpiresGetFieldValueReturnsProperValue()
    {
        $expiresHeader = new Expires();
        $expiresHeader->setDate('Sun, 06 Nov 1994 08:49:37 GMT');
        $this->assertEquals('Sun, 06 Nov 1994 08:49:37 GMT', $expiresHeader->getFieldValue());
    }

    public function testExpiresToStringReturnsHeaderFormattedString()
    {
        $expiresHeader = new Expires();
        $expiresHeader->setDate('Sun, 06 Nov 1994 08:49:37 GMT');
        $this->assertEquals('Expires: Sun, 06 Nov 1994 08:49:37 GMT', $expiresHeader->toString());
    }

    /**
     * Implementation specific tests are covered by DateTest
     * @see ZendTest\Http\Header\DateTest
     */
    
}

