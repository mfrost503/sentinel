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
    Zend\Db\Sql\Predicate\PredicateSet;

class PredicateSetTest extends TestCase
{

    public function testEmptyConstructorYieldsCountOfZero()
    {
        $predicateSet = new PredicateSet();
        $this->assertEquals(0, count($predicateSet));
    }

    public function testCombinationIsAndByDefault()
    {
        $predicateSet = new PredicateSet();
        $predicateSet->addPredicate(new IsNull('foo'))
                  ->addPredicate(new IsNull('bar'));
        $parts = $predicateSet->getExpressionData();
        $this->assertEquals(3, count($parts));
        $this->assertContains('AND', $parts[1]);
        $this->assertNotContains('OR', $parts[1]);
    }

    public function testCanPassPredicatesAndDefaultCombinationViaConstructor()
    {
        $predicateSet = new PredicateSet();
        $set = new PredicateSet(array(
            new IsNull('foo'),
            new IsNull('bar'),
        ), 'OR');
        $parts = $set->getExpressionData();
        $this->assertEquals(3, count($parts));
        $this->assertContains('OR', $parts[1]);
        $this->assertNotContains('AND', $parts[1]);
    }

    public function testCanPassBothPredicateAndCombinationToAddPredicate()
    {
        $predicateSet = new PredicateSet();
        $predicateSet->addPredicate(new IsNull('foo'), 'OR')
                  ->addPredicate(new IsNull('bar'), 'AND')
                  ->addPredicate(new IsNull('baz'), 'OR')
                  ->addPredicate(new IsNull('bat'), 'AND');
        $parts = $predicateSet->getExpressionData();
        $this->assertEquals(7, count($parts));

        $this->assertNotContains('OR', $parts[1], var_export($parts, 1));
        $this->assertContains('AND', $parts[1]);

        $this->assertContains('OR', $parts[3]);
        $this->assertNotContains('AND', $parts[3]);

        $this->assertNotContains('OR', $parts[5]);
        $this->assertContains('AND', $parts[5]);
    }

    public function testCanUseOrPredicateAndAndPredicateMethods()
    {
        $predicateSet = new PredicateSet();
        $predicateSet->orPredicate(new IsNull('foo'))
                  ->andPredicate(new IsNull('bar'))
                  ->orPredicate(new IsNull('baz'))
                  ->andPredicate(new IsNull('bat'));
        $parts = $predicateSet->getExpressionData();
        $this->assertEquals(7, count($parts));

        $this->assertNotContains('OR', $parts[1], var_export($parts, 1));
        $this->assertContains('AND', $parts[1]);

        $this->assertContains('OR', $parts[3]);
        $this->assertNotContains('AND', $parts[3]);

        $this->assertNotContains('OR', $parts[5]);
        $this->assertContains('AND', $parts[5]);
    }
}
