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
 * @package    Zend_Captcha
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Captcha;

use Zend\Captcha\ReCaptcha;
use Zend\Service\ReCaptcha\ReCaptcha as ReCaptchaService;
/**
 * @category   Zend
 * @package    Zend_Captcha
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Captcha
 */
class ReCaptchaTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    public function setUp()
    {
        if (isset($this->word)) {
            unset($this->word);
        }

        $this->captcha = new ReCaptcha(array(
            'sessionClass' => 'ZendTest\Captcha\TestAsset\SessionContainer'
        ));
    }

    public function testConstructorShouldSetOptions()
    {
        $options = array(
            'privKey' => 'privateKey',
            'pubKey'  => 'publicKey',
            'ssl'     => true,
            'xhtml'   => true,
        );
        $captcha = new ReCaptcha($options);
        $test    = $captcha->getOptions();
        $compare = array('privKey' => $options['privKey'], 'pubKey' => $options['pubKey']);
        $this->assertEquals($compare, $test);

        $service = $captcha->getService();
        $test = $service->getParams();
        $compare = array('ssl' => $options['ssl'], 'xhtml' => $options['xhtml']);
        foreach ($compare as $key => $value) {
            $this->assertTrue(array_key_exists($key, $test));
            $this->assertSame($value, $test[$key]);
        }
    }

    public function testShouldAllowSpecifyingServiceObject()
    {
        $captcha = new ReCaptcha();
        $try     = new ReCaptchaService();
        $this->assertNotSame($captcha->getService(), $try);
        $captcha->setService($try);
        $this->assertSame($captcha->getService(), $try);
    }

    public function testSetAndGetPublicAndPrivateKeys()
    {
        $captcha = new ReCaptcha();
        $pubKey = 'pubKey';
        $privKey = 'privKey';
        $captcha->setPubkey($pubKey)
                ->setPrivkey($privKey);

        $this->assertSame($pubKey, $captcha->getPubkey());
        $this->assertSame($privKey, $captcha->getPrivkey());

        $this->assertSame($pubKey, $captcha->getService()->getPublicKey());
        $this->assertSame($privKey, $captcha->getService()->getPrivateKey());
    }

    /** @group ZF-7654 */
    public function testConstructorShouldAllowSettingLangOptionOnServiceObject()
    {
        $options = array('lang'=>'fr');
        $captcha = new ReCaptcha($options);
        $this->assertEquals('fr', $captcha->getService()->getOption('lang'));
    }

    /** @group ZF-7654 */
    public function testConstructorShouldAllowSettingThemeOptionOnServiceObject()
    {
        $options = array('theme'=>'black');
        $captcha = new ReCaptcha($options);
        $this->assertEquals('black', $captcha->getService()->getOption('theme'));
    }

    /** @group ZF-7654 */
    public function testAllowsSettingLangOptionOnServiceObject()
    {
        $captcha = new ReCaptcha;
        $captcha->setOption('lang', 'fr');
        $this->assertEquals('fr', $captcha->getService()->getOption('lang'));
    }

    /** @group ZF-7654 */
    public function testAllowsSettingThemeOptionOnServiceObject()
    {
        $captcha = new ReCaptcha;
        $captcha->setOption('theme', 'black');
        $this->assertEquals('black', $captcha->getService()->getOption('theme'));
    }

    public function testUsesReCaptchaHelper()
    {
        $captcha = new ReCaptcha;
        $this->assertEquals('captcha/recaptcha', $captcha->getHelperName());   
    }
}
