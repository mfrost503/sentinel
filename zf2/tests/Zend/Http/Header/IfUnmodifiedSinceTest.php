<?php

namespace ZendTest\Http\Header;

use Zend\Http\Header\IfUnmodifiedSince;

class IfUnmodifiedSinceTest extends \PHPUnit_Framework_TestCase
{

    public function testIfUnmodifiedSinceFromStringCreatesValidIfUnmodifiedSinceHeader()
    {
        $ifUnmodifiedSinceHeader = IfUnmodifiedSince::fromString('If-Unmodified-Since: Sun, 06 Nov 1994 08:49:37 GMT');
        $this->assertInstanceOf('Zend\Http\Header\HeaderInterface', $ifUnmodifiedSinceHeader);
        $this->assertInstanceOf('Zend\Http\Header\IfUnmodifiedSince', $ifUnmodifiedSinceHeader);
    }

    public function testIfUnmodifiedSinceGetFieldNameReturnsHeaderName()
    {
        $ifUnmodifiedSinceHeader = new IfUnmodifiedSince();
        $this->assertEquals('If-Unmodified-Since', $ifUnmodifiedSinceHeader->getFieldName());
    }

    public function testIfUnmodifiedSinceGetFieldValueReturnsProperValue()
    {
        $ifUnmodifiedSinceHeader = new IfUnmodifiedSince();
        $ifUnmodifiedSinceHeader->setDate('Sun, 06 Nov 1994 08:49:37 GMT');
        $this->assertEquals('Sun, 06 Nov 1994 08:49:37 GMT', $ifUnmodifiedSinceHeader->getFieldValue());
    }

    public function testIfUnmodifiedSinceToStringReturnsHeaderFormattedString()
    {
        $ifUnmodifiedSinceHeader = new IfUnmodifiedSince();
        $ifUnmodifiedSinceHeader->setDate('Sun, 06 Nov 1994 08:49:37 GMT');
        $this->assertEquals('If-Unmodified-Since: Sun, 06 Nov 1994 08:49:37 GMT', $ifUnmodifiedSinceHeader->toString());
    }

    /**
     * Implementation specific tests are covered by DateTest
     * @see ZendTest\Http\Header\DateTest
     */
    
}

