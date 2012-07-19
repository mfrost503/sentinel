<?php

namespace ZendTest\Di\Definition;

use Zend\Di\Definition\BuilderDefinition;
use Zend\Di\Definition\Builder;
use Zend\Config\Factory as ConfigFactory;
use PHPUnit_Framework_TestCase as TestCase;

class BuilderDefinitionTest extends TestCase
{
    
    public function testBuilderImplementsDefinition()
    {
        $builder = new BuilderDefinition();
        $this->assertInstanceOf('Zend\Di\Definition\DefinitionInterface', $builder);
    }
    
    public function testBuilderCanBuildClassWithMethods()
    {
        $class = new Builder\PhpClass();
        $class->setName('Foo');
        $class->addSuperType('Parent');
        
        $injectionMethod = new Builder\InjectionMethod();
        $injectionMethod->setName('injectBar');
        $injectionMethod->addParameter('bar', 'Bar');
        
        $class->addInjectionMethod($injectionMethod);
        
        $definition = new BuilderDefinition();
        $definition->addClass($class);
        
        $this->assertTrue($definition->hasClass('Foo'));
        $this->assertEquals('__construct', $definition->getInstantiator('Foo'));
        $this->assertContains('Parent', $definition->getClassSupertypes('Foo'));
        $this->assertTrue($definition->hasMethods('Foo'));
        $this->assertTrue($definition->hasMethod('Foo', 'injectBar'));
        $this->assertContains('injectBar', $definition->getMethods('Foo'));
        $this->assertEquals(
            array('Foo::injectBar:0' => array('bar', 'Bar', true)),
            $definition->getMethodParameters('Foo', 'injectBar')
        );
    }
    
    public function testBuilderCanBuildFromArray()
    {
        $ini = ConfigFactory::fromFile(__DIR__ . '/../_files/sample.ini');
        $iniAsArray = $ini['section-b'];
        $definitionArray = $iniAsArray['di']['definitions'][1];
        unset($definitionArray['class']);
        
        $definition = new BuilderDefinition();
        $definition->createClassesFromArray($definitionArray);
        
        $this->assertTrue($definition->hasClass('My\DbAdapter'));
        $this->assertEquals('__construct', $definition->getInstantiator('My\DbAdapter'));
        $this->assertEquals(
            array(
                'My\DbAdapter::__construct:0' => array('username', null, true),
                'My\DbAdapter::__construct:1' => array('password', null, true)
            ),
            $definition->getMethodParameters('My\DbAdapter', '__construct')
        );
        
        $this->assertTrue($definition->hasClass('My\Mapper'));
        $this->assertEquals('__construct', $definition->getInstantiator('My\Mapper'));
        $this->assertEquals(
            array('My\Mapper::__construct:0' => array('dbAdapter', 'My\DbAdapter', true)),
            $definition->getMethodParameters('My\Mapper', '__construct')
        );
        
        $this->assertTrue($definition->hasClass('My\Repository'));
        $this->assertEquals('__construct', $definition->getInstantiator('My\Repository'));
        $this->assertEquals(
            array('My\Repository::__construct:0' => array('mapper', 'My\Mapper', true)),
            $definition->getMethodParameters('My\Repository', '__construct')
        );
        
    }

    public function testCanCreateClassFromFluentInterface()
    {
        $builder = new BuilderDefinition();
        $class = $builder->createClass('Foo');

        $this->assertTrue($builder->hasClass('Foo'));
    }
    
    public function testCanCreateInjectionMethodsAndPopulateFromFluentInterface()
    {
        $builder = new BuilderDefinition();
        $foo     = $builder->createClass('Foo');
        $foo->setName('Foo');
        $foo->createInjectionMethod('setBar')
            ->addParameter('bar', 'Bar');
        $foo->createInjectionMethod('setConfig')
            ->addParameter('config', null);

        $this->assertTrue($builder->hasClass('Foo'));
        $this->assertTrue($builder->hasMethod('Foo', 'setBar'));
        $this->assertTrue($builder->hasMethod('Foo', 'setConfig'));

        $this->assertEquals(
            array('Foo::setBar:0' => array('bar', 'Bar', true)),
            $builder->getMethodParameters('Foo', 'setBar')
        );
        $this->assertEquals(
            array('Foo::setConfig:0' => array('config', null, true)),
            $builder->getMethodParameters('Foo', 'setConfig')
        );
    }

    public function testBuilderCanSpecifyClassToUseWithCreateClass()
    {
        $builder = new BuilderDefinition();
        $this->assertEquals('Zend\Di\Definition\Builder\PhpClass', $builder->getClassBuilder());

        $builder->setClassBuilder('Foo');
        $this->assertEquals('Foo', $builder->getClassBuilder());
    }

    public function testClassBuilderCanSpecifyClassToUseWhenCreatingInjectionMethods()
    {
        $builder = new BuilderDefinition();
        $class   = $builder->createClass('Foo');

        $this->assertEquals('Zend\Di\Definition\Builder\InjectionMethod', $class->getMethodBuilder());

        $class->setMethodBuilder('Foo');
        $this->assertEquals('Foo', $class->getMethodBuilder());
    }
}
