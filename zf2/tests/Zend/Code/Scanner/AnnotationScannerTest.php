<?php

namespace ZendTest\Code\Scanner;

use Zend\Code\Scanner\AnnotationScanner;
use Zend\Code\NameInformation;
use Zend\Code\Annotation\AnnotationManager;
use Zend\Code\Annotation\Parser\GenericAnnotationParser;

class AnnotationScannerTest extends \PHPUnit_Framework_TestCase
{

    public function testScannerWorks()
    {
        $annotationManager = new AnnotationManager();
        $parser = new GenericAnnotationParser();
        $parser->registerAnnotations(array(
            $foo = new TestAsset\Annotation\Foo(),
            $bar = new TestAsset\Annotation\Bar()
        ));
        $annotationManager->attach($parser);

        $docComment = '/**' . "\n"
            . ' * @Test\Foo(\'anything I want()' . "\n" . ' * to be\')' . "\n"
            . ' * @Test\Bar' . "\n */";

        $nameInfo = new NameInformation();
        $nameInfo->addUse('ZendTest\Code\Scanner\TestAsset\Annotation', 'Test');

        $annotationScanner = new AnnotationScanner($annotationManager, $docComment, $nameInfo);
        $this->assertEquals(get_class($foo), get_class($annotationScanner[0]));
        $this->assertEquals("'anything I want()\n to be'", $annotationScanner[0]->getContent());
        $this->assertEquals(get_class($bar), get_class($annotationScanner[1]));
    }

}
