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
 * @package    Zend_Crypt
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Crypt\Password;

use Zend\Crypt\Password\Bcrypt;
use Zend\Config\Config;
use Zend\Crypt\Password\Exception;

/**
 * @category   Zend
 * @package    Zend_Crypt
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Crypt
 */
class BcryptTest extends \PHPUnit_Framework_TestCase
{
    /** @var Bcrypt */
    public $bcrypt;
    /** @var string */
    public $salt;
    /** @var string */
    public $bcryptPassword;
    /** @var string */
    public $password;

    public function setUp()
    {
        $this->bcrypt   = new Bcrypt();
        $this->salt     = '1234567890123456';
        $this->password = 'test';
        if (version_compare(PHP_VERSION, '5.3.7') >= 0) {
            $this->prefix = '$2y$';
        } else {
            $this->prefix = '$2a$';
        }
        $this->bcryptPassword = $this->prefix . '14$MTIzNDU2Nzg5MDEyMzQ1NeWUUefVlefsTbFhsbqKFv/vPSZBrSFVm';    
    }

    public function testConstructByOptions()
    {
        $options = array(
            'cost'       => '15',
            'salt'       => $this->salt
        );
        $bcrypt  = new Bcrypt($options);
        $this->assertTrue($bcrypt instanceof Bcrypt);
        $this->assertEquals('15', $bcrypt->getCost());
        $this->assertEquals($this->salt, $bcrypt->getSalt());
    }

    public function testConstructByConfig()
    {
        $options = array(
            'cost'       => '15',
            'salt'       => $this->salt
        );
        $config  = new Config($options);
        $bcrypt  = new Bcrypt($config);
        $this->assertTrue($bcrypt instanceof Bcrypt);
        $this->assertEquals('15', $bcrypt->getCost());
        $this->assertEquals($this->salt, $bcrypt->getSalt());
    }

    public function testWrongConstruct()
    {
        $this->setExpectedException('Zend\Crypt\Password\Exception\InvalidArgumentException',
                                    'The options parameter must be an array, a Zend\Config\Config object or a Traversable');
        $bcrypt = new Bcrypt('test');
    }

    public function testSetCost()
    {
        $this->bcrypt->setCost('16');
        $this->assertEquals('16', $this->bcrypt->getCost());
    }

    public function testSetWrongCost()
    {
        $this->setExpectedException('Zend\Crypt\Password\Exception\InvalidArgumentException',
                                    'The cost parameter of bcrypt must be in range 04-31');
        $this->bcrypt->setCost('3');
    }

    public function testSetSalt()
    {
        $this->bcrypt->setSalt($this->salt);
        $this->assertEquals($this->salt, $this->bcrypt->getSalt());
    }

    public function testSetSmallSalt()
    {
        $this->setExpectedException('Zend\Crypt\Password\Exception\InvalidArgumentException',
                                    'The length of the salt must be at lest ' . Bcrypt::MIN_SALT_SIZE . ' bytes');
        $this->bcrypt->setSalt('small salt');
    }

    public function testCreateWithRandomSalt()
    {
        $password = $this->bcrypt->create('test');
        $this->assertTrue(!empty($password));
        $this->assertTrue(strlen($password) === 60);
    }

    public function testCreateWithSalt()
    {
        $this->bcrypt->setSalt($this->salt);
        $password = $this->bcrypt->create($this->password);
        $this->assertEquals($password, $this->bcryptPassword);
    }

    public function testVerify()
    {
        $this->assertTrue($this->bcrypt->verify($this->password, $this->bcryptPassword));
        $this->assertFalse($this->bcrypt->verify(substr($this->password, -1), $this->bcryptPassword));
    }
    
    public function testPasswordWith8bitCharacter()
    {
        $password = 'test' . chr(128);
        $this->bcrypt->setSalt($this->salt);
        
        if (version_compare(PHP_VERSION, '5.3.7') >= 0) {
            $this->assertEquals('$2y$14$MTIzNDU2Nzg5MDEyMzQ1NexAbOIUHkG6Ra.TK9QxHOVUhDxOe4dkW', $this->bcrypt->create($password));
        } else {
            $this->setExpectedException('Zend\Crypt\Password\Exception\RuntimeException',
                'The bcrypt implementation used by PHP can contains a security flaw using password with 8-bit character. ' .
                'We suggest to upgrade to PHP 5.3.7+ or use passwords with only 7-bit characters'
            );
            $output = $this->bcrypt->create($password);
        }
    }
}
