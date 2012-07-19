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
 * @package    Zend_Config
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Config\Reader;

use Zend\Config\Reader\Xml;

/**
 * @category   Zend
 * @package    Zend_Config
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Config
 */
class XmlTest extends AbstractReaderTestCase
{
    public function setUp()
    {
        $this->reader = new Xml();
    }
    
    /**
     * getTestAssetPath(): defined by AbstractReaderTestCase.
     * 
     * @see    AbstractReaderTestCase::getTestAssetPath()
     * @return string
     */
    protected function getTestAssetPath($name)
    {
        return __DIR__ . '/TestAssets/Xml/' . $name . '.xml';
    }
    
    public function testInvalidXmlFile()
    {
        $this->reader = new Xml();
        $this->setExpectedException('Zend\Config\Exception\RuntimeException');
        $arrayXml = $this->reader->fromFile($this->getTestAssetPath('invalid'));
    }
    
    public function testFromString()
    {
        $xml = <<<ECS
<?xml version="1.0" encoding="UTF-8"?>
<zend-config>
    <test>foo</test>
    <bar>baz</bar>
    <bar>foo</bar>
</zend-config>

ECS;
        
        $arrayXml= $this->reader->fromString($xml);
        $this->assertEquals($arrayXml['test'], 'foo');
        $this->assertEquals($arrayXml['bar'][0], 'baz');
        $this->assertEquals($arrayXml['bar'][1], 'foo');
    }
    
    public function testInvalidString()
    {
        $xml = <<<ECS
<?xml version="1.0" encoding="UTF-8"?>
<zend-config>
    <bar>baz</baz>
</zend-config>

ECS;
        $this->setExpectedException('Zend\Config\Exception\RuntimeException');
        $arrayXml = $this->reader->fromString($xml);
    }

    public function testZF300_MultipleKeysOfTheSameName()
    {
        $config = $this->reader->fromFile($this->getTestAssetPath('array'));

        $this->assertEquals('2a', $config['one']['two'][0]);
        $this->assertEquals('2b', $config['one']['two'][1]);
        $this->assertEquals('4', $config['three']['four'][1]);
        $this->assertEquals('5', $config['three']['four'][0]['five']);
    }

    public function testZF300_ArraysWithMultipleChildren()
    {
        $config = $this->reader->fromFile($this->getTestAssetPath('array'));

        $this->assertEquals('1', $config['six']['seven'][0]['eight']);
        $this->assertEquals('2', $config['six']['seven'][1]['eight']);
        $this->assertEquals('3', $config['six']['seven'][2]['eight']);
        $this->assertEquals('1', $config['six']['seven'][0]['nine']);
        $this->assertEquals('2', $config['six']['seven'][1]['nine']);
        $this->assertEquals('3', $config['six']['seven'][2]['nine']);
    }
}
