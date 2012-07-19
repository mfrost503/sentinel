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
 * @package    Zend_Navigation
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Navigation;

use Zend\Config;
use Zend\Mvc\Router\RouteMatch;
use Zend\Mvc\Service\ServiceManagerConfiguration;
use Zend\Navigation;
use Zend\Navigation\Page\Mvc as MvcPage;
use Zend\Navigation\Service\ConstructedNavigationFactory;
use Zend\Navigation\Service\DefaultNavigationFactory;
use Zend\ServiceManager\ServiceManager;

/**
 * Tests the class Zend\Navigation\MvcNavigationFactory
 *
 * @category   Zend
 * @package    Zend_Navigation
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Navigation
 */
class ServiceFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Zend\ServiceManager\ServiceManager
     */
    protected $serviceManager;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        $config = array(
            'modules'                 => array(),
            'module_listener_options' => array(
                'config_cache_enabled' => false,
                'cache_dir'            => 'data/cache',
                'module_paths'         => array(),
                'extra_config'         => array(
                    'service_manager' => array(
                        'factories' => array(
                            'Configuration' => function() {
                                return array(
                                    'navigation' => array(
                                        'file'    => __DIR__ . '/_files/navigation.xml',
                                        'default' => array(
                                            array(
                                                'label' => 'Page 1',
                                                'uri'   => 'page1.html'
                                            ),
                                            array(
                                                'label' => 'MVC Page',
                                                'route' => 'foo',
                                                'pages' => array(
                                                    array(
                                                        'label' => 'Sub MVC Page',
                                                        'route' => 'foo'
                                                    )
                                                )
                                            ),
                                            array(
                                                'label' => 'Page 3',
                                                'uri'   => 'page3.html'
                                            )
                                        )
                                    )
                                );
                            }
                        )
                    ),
                )
            ),
        );

        $sm = $this->serviceManager = new ServiceManager(new ServiceManagerConfiguration);
        $sm->setService('ApplicationConfiguration', $config);
        $sm->get('ModuleManager')->loadModules();
        $sm->get('Application')->bootstrap();

        $app = $this->serviceManager->get('Application');
        $app->getMvcEvent()->setRouteMatch(new RouteMatch(array(
            'controller' => 'post',
            'action'     => 'view',
            'id'         => '1337',
        )));
    }

    /**
     * Tear down the environment after running a test
     */
    protected function tearDown()
    {

    }

    /**
     * @covers \Zend\Navigation\MvcNavigationFactory
     */
    public function testDefaultFactoryAcceptsFileString()
    {
        $this->serviceManager->setFactory('Navigation', 'ZendTest\Navigation\TestAsset\FileNavigationFactory');
        $container = $this->serviceManager->get('Navigation');
    }

    /**
     * @covers \Zend\Navigation\MvcNavigationFactory
     */
    public function testMvcPagesGetInjectedWithComponents()
    {
        $this->serviceManager->setFactory('Navigation', 'Zend\Navigation\Service\DefaultNavigationFactory');
        $container = $this->serviceManager->get('Navigation');

        $recursive = function($that, $pages) use (&$recursive) {
            foreach($pages as $page) {
                if ($page instanceof MvcPage) {
                    $that->assertInstanceOf('Zend\Mvc\Router\RouteStackInterface', $page->getRouter());
                    $that->assertInstanceOf('Zend\Mvc\Router\RouteMatch', $page->getRouteMatch());
                }

                $recursive($that, $page->getPages());
            }
        };
        $recursive($this, $container->getPages());
    }

    /**
     * @covers \Zend\Navigation\MvcNavigationFactory
     */
    public function testDefaultFactory()
    {
        $this->serviceManager->setFactory('Navigation', 'Zend\Navigation\Service\DefaultNavigationFactory');

        $container = $this->serviceManager->get('Navigation');
        $this->assertEquals(3, $container->count());
    }

    /**
     * @covers \Zend\Navigation\MvcNavigationFactory
     */
    public function testConstructedFromArray()
    {
        $argument = array(
            array(
                'label' => 'Page 1',
                'uri'   => 'page1.html'
            ),
            array(
                'label' => 'Page 2',
                'uri'   => 'page2.html'
            ),
            array(
                'label' => 'Page 3',
                'uri'   => 'page3.html'
            )
        );

        $factory = new ConstructedNavigationFactory($argument);
        $this->serviceManager->setFactory('Navigation', $factory);

        $container = $this->serviceManager->get('Navigation');
        $this->assertEquals(3, $container->count());
    }

    /**
     * @covers \Zend\Navigation\MvcNavigationFactory
     */
    public function testConstructedFromFileString()
    {
        $argument = __DIR__ . '/_files/navigation.xml';
        $factory  = new ConstructedNavigationFactory($argument);
        $this->serviceManager->setFactory('Navigation', $factory);

        $container = $this->serviceManager->get('Navigation');
        $this->assertEquals(3, $container->count());
    }

    /**
     * @covers \Zend\Navigation\MvcNavigationFactory
     */
    public function testConstructedFromConfig()
    {
        $argument = new Config\Config(array(
            array(
                'label' => 'Page 1',
                'uri'   => 'page1.html'
            ),
            array(
                'label' => 'Page 2',
                'uri'   => 'page2.html'
            ),
            array(
                'label' => 'Page 3',
                'uri'   => 'page3.html'
            )
        ));

        $factory = new ConstructedNavigationFactory($argument);
        $this->serviceManager->setFactory('Navigation', $factory);

        $container = $this->serviceManager->get('Navigation');
        $this->assertEquals(3, $container->count());
    }
}
