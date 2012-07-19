<?php

namespace ZendTest\Http\Header;

use Zend\Http\Header\AcceptLanguage;

class AcceptLanguageTest extends \PHPUnit_Framework_TestCase
{

    public function testAcceptLanguageFromStringCreatesValidAcceptLanguageHeader()
    {
        $acceptLanguageHeader = AcceptLanguage::fromString('Accept-Language: xxx');
        $this->assertInstanceOf('Zend\Http\Header\HeaderInterface', $acceptLanguageHeader);
        $this->assertInstanceOf('Zend\Http\Header\AcceptLanguage', $acceptLanguageHeader);
    }

    public function testAcceptLanguageGetFieldNameReturnsHeaderName()
    {
        $acceptLanguageHeader = new AcceptLanguage();
        $this->assertEquals('Accept-Language', $acceptLanguageHeader->getFieldName());
    }

    public function testAcceptLanguageGetFieldValueReturnsProperValue()
    {
        $acceptLanguageHeader = AcceptLanguage::fromString('Accept-Language: xxx');
        $this->assertEquals('xxx', $acceptLanguageHeader->getFieldValue());
    }

    public function testAcceptLanguageToStringReturnsHeaderFormattedString()
    {
        $acceptLanguageHeader = new AcceptLanguage();
        $acceptLanguageHeader->addLanguage('da', 0.8)
                             ->addLanguage('en-gb', 1);
        
        $this->assertEquals('Accept-Language: da;q=0.8,en-gb', $acceptLanguageHeader->toString());
    }

    /** Implmentation specific tests here */
    
    public function testCanParseCommaSeparatedValues()
    {
        $header = AcceptLanguage::fromString('Accept-Language: da;q=0.8,en-gb');
        $this->assertTrue($header->hasLanguage('da'));
        $this->assertTrue($header->hasLanguage('en-gb'));
    }

    public function testPrioritizesValuesBasedOnQParameter()
    {
        $header   = AcceptLanguage::fromString('Accept-Language: da;q=0.8,en-gb,*;q=0.4');
        $expected = array(
            'en-gb',
            'da',
            '*'
        );

        $test = array();
        foreach($header->getPrioritized() as $type) {
            $test[] = $type;
        }
        $this->assertEquals($expected, $test);
    }
    
    public function testWildcharLanguage()
    {
        $acceptHeader = new AcceptLanguage();
        $acceptHeader->addLanguage('da', 0.8)
                     ->addLanguage('*', 0.4);
        
        $this->assertTrue($acceptHeader->hasLanguage('da'));
        $this->assertTrue($acceptHeader->hasLanguage('en'));
        $this->assertEquals('Accept-Language: da;q=0.8,*;q=0.4', $acceptHeader->toString());
    }
}

