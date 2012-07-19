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
    Zend\Db\Sql\Predicate\IsNull,
    Zend\Db\Sql\Predicate\Predicate;

class PredicateTest extends TestCase
{

    public function testEqualToCreatesOperatorPredicate()
    {
        $predicate = new Predicate();
        $predicate->equalTo('foo.bar', 'bar');
        $parts = $predicate->getExpressionData();
        $this->assertEquals(1, count($parts));
        $this->assertContains('%s = %s', $parts[0]);
        $this->assertContains(array('foo.bar', 'bar'), $parts[0]);
    }

    public function testLessThanCreatesOperatorPredicate()
    {
        $predicate = new Predicate();
        $predicate->lessThan('foo.bar', 'bar');
        $parts = $predicate->getExpressionData();
        $this->assertEquals(1, count($parts));
        $this->assertContains('%s < %s', $parts[0]);
        $this->assertContains(array('foo.bar', 'bar'), $parts[0]);
    }

    public function testGreaterThanCreatesOperatorPredicate()
    {
        $predicate = new Predicate();
        $predicate->greaterThan('foo.bar', 'bar');
        $parts = $predicate->getExpressionData();
        $this->assertEquals(1, count($parts));
        $this->assertContains('%s > %s', $parts[0]);
        $this->assertContains(array('foo.bar', 'bar'), $parts[0]);
    }

    public function testLessThanOrEqualToCreatesOperatorPredicate()
    {
        $predicate = new Predicate();
        $predicate->lessThanOrEqualTo('foo.bar', 'bar');
        $parts = $predicate->getExpressionData();
        $this->assertEquals(1, count($parts));
        $this->assertContains('%s <= %s', $parts[0]);
        $this->assertContains(array('foo.bar', 'bar'), $parts[0]);
    }

    public function testGreaterThanOrEqualToCreatesOperatorPredicate()
    {
        $predicate = new Predicate();
        $predicate->greaterThanOrEqualTo('foo.bar', 'bar');
        $parts = $predicate->getExpressionData();
        $this->assertEquals(1, count($parts));
        $this->assertContains('%s >= %s', $parts[0]);
        $this->assertContains(array('foo.bar', 'bar'), $parts[0]);
    }

    public function testLikeCreatesLikePredicate()
    {
        $predicate = new Predicate();
        $predicate->like('foo.bar', 'bar%');
        $parts = $predicate->getExpressionData();
        $this->assertEquals(1, count($parts));
        $this->assertContains('%1$s LIKE %2$s', $parts[0]);
        $this->assertContains(array('foo.bar', 'bar%'), $parts[0]);
    }

    public function testLiteralCreatesLiteralPredicate()
    {
        $predicate = new Predicate();
        $predicate->literal('foo.bar = ?', 'bar');
        $parts = $predicate->getExpressionData();
        $this->assertEquals(1, count($parts));
        $this->assertContains('foo.bar = %s', $parts[0]);
        $this->assertContains(array('bar'), $parts[0]);
    }

    public function testIsNullCreatesIsNullPredicate()
    {
        $predicate = new Predicate();
        $predicate->isNull('foo.bar');
        $parts = $predicate->getExpressionData();
        $this->assertEquals(1, count($parts));
        $this->assertContains('%1$s IS NULL', $parts[0]);
        $this->assertContains(array('foo.bar'), $parts[0]);
    }

    public function testIsNotNullCreatesIsNotNullPredicate()
    {
        $predicate = new Predicate();
        $predicate->isNotNull('foo.bar');
        $parts = $predicate->getExpressionData();
        $this->assertEquals(1, count($parts));
        $this->assertContains('%1$s IS NOT NULL', $parts[0]);
        $this->assertContains(array('foo.bar'), $parts[0]);
    }

    public function testInCreatesInPredicate()
    {
        $predicate = new Predicate();
        $predicate->in('foo.bar', array('foo', 'bar'));
        $parts = $predicate->getExpressionData();
        $this->assertEquals(1, count($parts));
        $this->assertContains('%s IN (%s, %s)', $parts[0]);
        $this->assertContains(array('foo.bar', 'foo', 'bar'), $parts[0]);
    }

    public function testBetweenCreatesBetweenPredicate()
    {
        $predicate = new Predicate();
        $predicate->between('foo.bar', 1, 10);
        $parts = $predicate->getExpressionData();
        $this->assertEquals(1, count($parts));
        $this->assertContains('%1$s BETWEEN %2$s AND %3$s', $parts[0]);
        $this->assertContains(array('foo.bar', 1, 10), $parts[0]);
    }

    public function testCanChainPredicateFactoriesBetweenOperators()
    {
        $predicate = new Predicate();
        $predicate->isNull('foo.bar')
                  ->or
                  ->isNotNull('bar.baz')
                  ->and
                  ->equalTo('baz.bat', 'foo');
        $parts = $predicate->getExpressionData();
        $this->assertEquals(5, count($parts));

        $this->assertContains('%1$s IS NULL', $parts[0]);
        $this->assertContains(array('foo.bar'), $parts[0]);

        $this->assertEquals(' OR ', $parts[1]);

        $this->assertContains('%1$s IS NOT NULL', $parts[2]);
        $this->assertContains(array('bar.baz'), $parts[2]);

        $this->assertEquals(' AND ', $parts[3]);

        $this->assertContains('%s = %s', $parts[4]);
        $this->assertContains(array('baz.bat', 'foo'), $parts[4]);
    }

    public function testCanNestPredicates()
    {
        $predicate = new Predicate();
        $predicate->isNull('foo.bar')
                  ->nest()
                  ->isNotNull('bar.baz')
                  ->and
                  ->equalTo('baz.bat', 'foo')
                  ->unnest();
        $parts = $predicate->getExpressionData();

        $this->assertEquals(7, count($parts));

        $this->assertContains('%1$s IS NULL', $parts[0]);
        $this->assertContains(array('foo.bar'), $parts[0]);

        $this->assertEquals(' AND ', $parts[1]);

        $this->assertEquals('(', $parts[2]);

        $this->assertContains('%1$s IS NOT NULL', $parts[3]);
        $this->assertContains(array('bar.baz'), $parts[3]);

        $this->assertEquals(' AND ', $parts[4]);

        $this->assertContains('%s = %s', $parts[5]);
        $this->assertContains(array('baz.bat', 'foo'), $parts[5]);
        
        $this->assertEquals(')', $parts[6]);
    }
}
