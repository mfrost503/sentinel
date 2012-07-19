<?php

namespace ZendTest\Http\Header;

use Zend\Http\Header\ContentLocation;
use Zend\Uri\Http as HttpUri;

class ContentLocationTest extends \PHPUnit_Framework_TestCase
{

    public function testContentLocationFromStringCreatesValidLocationHeader()
    {
        $contentLocationHeader = ContentLocation::fromString('Content-Location: http://www.example.com/');
        $this->assertInstanceOf('Zend\Http\Header\HeaderInterface', $contentLocationHeader);
        $this->assertInstanceOf('Zend\Http\Header\ContentLocation', $contentLocationHeader);
    }

    public function testContentLocationGetFieldValueReturnsProperValue()
    {
        $contentLocationHeader = new ContentLocation();
        $contentLocationHeader->setUri('http://www.example.com/');
        $this->assertEquals('http://www.example.com/', $contentLocationHeader->getFieldValue());

        $contentLocationHeader->setUri('/path');
        $this->assertEquals('/path', $contentLocationHeader->getFieldValue());
    }

    public function testContentLocationToStringReturnsHeaderFormattedString()
    {
        $contentLocationHeader = new ContentLocation();
        $contentLocationHeader->setUri('http://www.example.com/path?query');

        $this->assertEquals('Content-Location: http://www.example.com/path?query', $contentLocationHeader->toString());
    }

    /** Implementation specific tests  */

    public function testContentLocationCanSetAndAccessAbsoluteUri()
    {
        $contentLocationHeader = ContentLocation::fromString('Content-Location: http://www.example.com/path');
        $uri = $contentLocationHeader->uri();
        $this->assertInstanceOf('Zend\Uri\Http', $uri);
        $this->assertTrue($uri->isAbsolute());
        $this->assertEquals('http://www.example.com/path', $contentLocationHeader->getUri());
    }

    public function testContentLocationCanSetAndAccessRelativeUri()
    {
        $contentLocationHeader = ContentLocation::fromString('Content-Location: /path/to');
        $uri = $contentLocationHeader->uri();
        $this->assertInstanceOf('Zend\Uri\Http', $uri);
        $this->assertFalse($uri->isAbsolute());
        $this->assertEquals('/path/to', $contentLocationHeader->getUri());
    }

}

