<?php

namespace ZendTest\Http\Header;

use Zend\Http\Header\Age;

class AgeTest extends \PHPUnit_Framework_TestCase
{

    public function testAgeFromStringCreatesValidAgeHeader()
    {
        $ageHeader = Age::fromString('Age: 12');
        $this->assertInstanceOf('Zend\Http\Header\HeaderInterface', $ageHeader);
        $this->assertInstanceOf('Zend\Http\Header\Age', $ageHeader);
        $this->assertEquals('12', $ageHeader->getDeltaSeconds());
    }

    public function testAgeGetFieldNameReturnsHeaderName()
    {
        $ageHeader = new Age();
        $this->assertEquals('Age', $ageHeader->getFieldName());
    }

    public function testAgeGetFieldValueReturnsProperValue()
    {
        $ageHeader = new Age();
        $ageHeader->setDeltaSeconds('12');
        $this->assertEquals('12', $ageHeader->getFieldValue());
    }

    public function testAgeToStringReturnsHeaderFormattedString()
    {
        $ageHeader = new Age();
        $ageHeader->setDeltaSeconds('12');
        $this->assertEquals('Age: 12', $ageHeader->toString());
    }

    public function testAgeCorrectsDeltaSecondsOverflow()
    {
        $ageHeader = new Age();
        $ageHeader->setDeltaSeconds(PHP_INT_MAX);
        $this->assertEquals('Age: 2147483648', $ageHeader->toString());
    }
}

