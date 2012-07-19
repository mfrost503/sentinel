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
 * @package    Zend_Form
 * @subpackage UnitTest
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Form\View\Helper;

use Zend\Form\Element;
use Zend\Form\View\Helper\FormFile as FormFileHelper;

/**
 * @category   Zend
 * @package    Zend_Form
 * @subpackage UnitTest
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class FormFileTest extends CommonTestCase
{
    public function setUp()
    {
        $this->helper = new FormFileHelper();
        parent::setUp();
    }

    public function testRaisesExceptionWhenNameIsNotPresentInElement()
    {
        $element = new Element();
        $this->setExpectedException('Zend\Form\Exception\DomainException', 'name');
        $this->helper->render($element);
    }

    public function testGeneratesFileInputTagWithElement()
    {
        $element = new Element('foo');
        $markup  = $this->helper->render($element);
        $this->assertContains('<input ', $markup);
        $this->assertContains('type="file"', $markup);
    }

    public function testGeneratesFileInputTagRegardlessOfElementType()
    {
        $element = new Element('foo');
        $element->setAttribute('type', 'email');
        $markup  = $this->helper->render($element);
        $this->assertContains('<input ', $markup);
        $this->assertContains('type="file"', $markup);
    }

    public function validAttributes()
    {
        return array(
            array('name', 'assertContains'),
            array('accept', 'assertContains'),
            array('alt', 'assertNotContains'),
            array('autocomplete', 'assertNotContains'),
            array('autofocus', 'assertContains'),
            array('checked', 'assertNotContains'),
            array('dirname', 'assertNotContains'),
            array('disabled', 'assertContains'),
            array('form', 'assertContains'),
            array('formaction', 'assertNotContains'),
            array('formenctype', 'assertNotContains'),
            array('formmethod', 'assertNotContains'),
            array('formnovalidate', 'assertNotContains'),
            array('formtarget', 'assertNotContains'),
            array('height', 'assertNotContains'),
            array('list', 'assertNotContains'),
            array('max', 'assertNotContains'),
            array('maxlength', 'assertNotContains'),
            array('min', 'assertNotContains'),
            array('multiple', 'assertContains'),
            array('pattern', 'assertNotContains'),
            array('placeholder', 'assertNotContains'),
            array('readonly', 'assertNotContains'),
            array('required', 'assertContains'),
            array('size', 'assertNotContains'),
            array('src', 'assertNotContains'),
            array('step', 'assertNotContains'),
            array('value', 'assertContains'),
            array('width', 'assertNotContains'),
        );
    }

    public function getCompleteElement()
    {
        $element = new Element('foo');
        $element->setAttributes(array(
            'accept'             => 'value',
            'alt'                => 'value',
            'autocomplete'       => 'on',
            'autofocus'          => 'autofocus',
            'checked'            => 'checked',
            'dirname'            => 'value',
            'disabled'           => 'disabled',
            'form'               => 'value',
            'formaction'         => 'value',
            'formenctype'        => 'value',
            'formmethod'         => 'value',
            'formnovalidate'     => 'value',
            'formtarget'         => 'value',
            'height'             => 'value',
            'id'                 => 'value',
            'list'               => 'value',
            'max'                => 'value',
            'maxlength'          => 'value',
            'min'                => 'value',
            'multiple'           => 'multiple',
            'name'               => 'value',
            'pattern'            => 'value',
            'placeholder'        => 'value',
            'readonly'           => 'readonly',
            'required'           => 'required',
            'size'               => 'value',
            'src'                => 'value',
            'step'               => 'value',
            'value'              => 'value',
            'width'              => 'value',
        ));
        return $element;
    }

    /**
     * @dataProvider validAttributes
     */
    public function testAllValidFormMarkupAttributesPresentInElementAreRendered($attribute, $assertion)
    {
        $element = $this->getCompleteElement();
        $markup  = $this->helper->render($element);
        $expect  = sprintf('%s="%s"', $attribute, $element->getAttribute($attribute));
        $this->$assertion($expect, $markup);
    }

    public function testInvokeProxiesToRender()
    {
        $element = new Element('foo');
        $markup  = $this->helper->__invoke($element);
        $this->assertContains('<input', $markup);
        $this->assertContains('name="foo"', $markup);
        $this->assertContains('type="file"', $markup);
    }

    public function testInvokeWithNoElementChainsHelper()
    {
        $this->assertSame($this->helper, $this->helper->__invoke());
    }
}
