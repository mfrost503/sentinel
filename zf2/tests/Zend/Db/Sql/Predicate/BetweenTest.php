<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Db
 * @subpackage UnitTest
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Db\Sql\Predicate;

use PHPUnit_Framework_TestCase as TestCase,
    Zend\Db\Sql\Predicate\Between;

class BetweenTest extends TestCase
{
    /**
     * @var Between
     */
    protected $between = null;

    public function setUp()
    {
        $this->between = new Between();
    }

    /**
     * @covers Zend\Db\Sql\Predicate\Between::__construct
     * @covers Zend\Db\Sql\Predicate\Between::getIdentifier
     * @covers Zend\Db\Sql\Predicate\Between::getMinValue
     * @covers Zend\Db\Sql\Predicate\Between::getMaxValue
     */
    public function testConstructorYieldsNullIdentifierMinimumAndMaximumValues()
    {
        $this->assertNull($this->between->getIdentifier());
        $this->assertNull($this->between->getMinValue());
        $this->assertNull($this->between->getMaxValue());
    }

    /**
     * @covers Zend\Db\Sql\Predicate\Between::__construct
     * @covers Zend\Db\Sql\Predicate\Between::getIdentifier
     * @covers Zend\Db\Sql\Predicate\Between::getMinValue
     * @covers Zend\Db\Sql\Predicate\Between::getMaxValue
     */
    public function testConstructorCanPassIdentifierMinimumAndMaximumValues()
    {
        $between = new Between('foo.bar', 1, 300);
        $this->assertEquals('foo.bar', $between->getIdentifier());
        $this->assertEquals(1, $between->getMinValue());
        $this->assertEquals(300, $between->getMaxValue());
    }

    /**
     * @covers Zend\Db\Sql\Predicate\Between::getSpecification
     */
    public function testSpecificationHasSaneDefaultValue()
    {
        $this->assertEquals('%1$s BETWEEN %2$s AND %3$s', $this->between->getSpecification());
    }



    /**
     * @covers Zend\Db\Sql\Predicate\Between::setIdentifier
     * @covers Zend\Db\Sql\Predicate\Between::getIdentifier
     */
    public function testIdentifierIsMutable()
    {
        $this->between->setIdentifier('foo.bar');
        $this->assertEquals('foo.bar', $this->between->getIdentifier());
    }

    /**
     * @covers Zend\Db\Sql\Predicate\Between::setMinValue
     * @covers Zend\Db\Sql\Predicate\Between::getMinValue
     */
    public function testMinValueIsMutable()
    {
        $this->between->setMinValue(10);
        $this->assertEquals(10, $this->between->getMinValue());
    }

    /**
     * @covers Zend\Db\Sql\Predicate\Between::setMaxValue
     * @covers Zend\Db\Sql\Predicate\Between::getMaxValue
     */
    public function testMaxValueIsMutable()
    {
        $this->between->setMaxValue(10);
        $this->assertEquals(10, $this->between->getMaxValue());
    }

    /**
     * @covers Zend\Db\Sql\Predicate\Between::setSpecification
     * @covers Zend\Db\Sql\Predicate\Between::getSpecification
     */
    public function testSpecificationIsMutable()
    {
        $this->between->setSpecification('%1$s IS INBETWEEN %2$s AND %3$s');
        $this->assertEquals('%1$s IS INBETWEEN %2$s AND %3$s', $this->between->getSpecification());
    }

    /**
     * @covers Zend\Db\Sql\Predicate\Between::getExpressionData
     */
    public function testRetrievingWherePartsReturnsSpecificationArrayOfIdentifierAndValuesAndArrayOfTypes()
    {
        $this->between->setIdentifier('foo.bar')
                      ->setMinValue(10)
                      ->setMaxValue(19);
        $expected = array(array(
            $this->between->getSpecification(),
            array('foo.bar', 10, 19),
            array(Between::TYPE_IDENTIFIER, Between::TYPE_VALUE, Between::TYPE_VALUE),
        ));
        $this->assertEquals($expected, $this->between->getExpressionData());
    }
}
