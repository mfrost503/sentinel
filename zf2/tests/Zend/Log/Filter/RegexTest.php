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
 * @package    Zend_Log
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Log\Filter;

use Zend\Log\Logger;
use Zend\Log\Filter\Regex;
use Zend\Config\Config;

/**
 * @category   Zend
 * @package    Zend_Log
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Log
 */
class RegexTest extends \PHPUnit_Framework_TestCase
{
    public function testMessageFilterRecognizesInvalidRegularExpression()
    {
        $this->setExpectedException('Zend\Log\Exception\InvalidArgumentException', 'invalid reg');
        new Regex('invalid regexp');
    }

    public function testMessageFilter()
    {
        $filter = new Regex('/accept/');
        $this->assertTrue($filter->filter(array('message' => 'foo accept bar')));
        $this->assertFalse($filter->filter(array('message' => 'foo reject bar')));
    }
}
