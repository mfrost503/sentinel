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
 * @package    Zend_Reflection
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Code\Reflection;

use Zend\Code\Annotation\AnnotationManager;
use Zend\Code\Annotation\Parser\GenericAnnotationParser;

/**
 * @category   Zend
 * @package    Zend_Reflection
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Reflection
 * @group      Zend_Reflection_Property
 */
class PropertyReflectionTest extends \PHPUnit_Framework_TestCase
{
    public function testDeclaringClassReturn()
    {
        $property = new \Zend\Code\Reflection\PropertyReflection('ZendTest\Code\Reflection\TestAsset\TestSampleClass2', '_prop1');
        $this->assertInstanceOf('Zend\Code\Reflection\ClassReflection', $property->getDeclaringClass());
        $this->assertEquals('ZendTest\Code\Reflection\TestAsset\TestSampleClass2', $property->getDeclaringClass()->getName());
    }

    public function testAnnotationScanningIsPossible()
    {
        $manager = new AnnotationManager();
        $parser = new GenericAnnotationParser();
        $parser->registerAnnotation(new TestAsset\SampleAnnotation());
        $manager->attach($parser);

        $property = new \Zend\Code\Reflection\PropertyReflection('ZendTest\Code\Reflection\TestAsset\TestSampleClass2', '_prop2');
        $annotations = $property->getAnnotations($manager);
        $this->assertInstanceOf('Zend\Code\Annotation\AnnotationCollection', $annotations);
        $this->assertTrue($annotations->hasAnnotation('ZendTest\Code\Reflection\TestAsset\SampleAnnotation'));
        $found = false;
        foreach ($annotations as $key => $annotation) {
            if (!$annotation instanceof TestAsset\SampleAnnotation) {
                continue;
            }
            $this->assertEquals(get_class($annotation) . ': {"foo":"bar"}', $annotation->content);
            $found = true;
            break;
        }
        $this->assertTrue($found);
    }
}
