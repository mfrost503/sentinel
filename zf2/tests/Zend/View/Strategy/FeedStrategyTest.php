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
 * @package    Zend_View
 * @subpackage UnitTest
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\View\Strategy;

use PHPUnit_Framework_TestCase as TestCase,
    Zend\EventManager\EventManager,
    Zend\Feed\Writer\FeedFactory,
    Zend\Http\Request as HttpRequest,
    Zend\Http\Response as HttpResponse,
    Zend\View\Model\ModelInterface as Model,
    Zend\View\Model\FeedModel,
    Zend\View\Model\ViewModel,
    Zend\View\Renderer\FeedRenderer,
    Zend\View\Strategy\FeedStrategy,
    Zend\View\ViewEvent;

/**
 * @category   Zend
 * @package    Zend_View
 * @subpackage UnitTest
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class FeedStrategyTest extends TestCase
{
    public function setUp()
    {
        $this->renderer = new FeedRenderer;
        $this->strategy = new FeedStrategy($this->renderer);
        $this->event    = new ViewEvent();
        $this->response = new HttpResponse();
    }

    public function testFeedModelSelectsFeedStrategy()
    {
        $this->event->setModel(new FeedModel());
        $result = $this->strategy->selectRenderer($this->event);
        $this->assertSame($this->renderer, $result);
    }

    public function testRssAcceptHeaderSelectsFeedStrategy()
    {
        $request = new HttpRequest();
        $request->getHeaders()->addHeaderLine('Accept', 'application/rss+xml');
        $this->event->setRequest($request);
        $result = $this->strategy->selectRenderer($this->event);
        $this->assertSame($this->renderer, $result);
    }

    public function testAtomAcceptHeaderSelectsFeedStrategy()
    {
        $request = new HttpRequest();
        $request->getHeaders()->addHeaderLine('Accept', 'application/atom+xml');
        $this->event->setRequest($request);
        $result = $this->strategy->selectRenderer($this->event);
        $this->assertSame($this->renderer, $result);
    }

    public function testLackOfFeedModelOrAcceptHeaderDoesNotSelectFeedStrategy()
    {
        $result = $this->strategy->selectRenderer($this->event);
        $this->assertNotSame($this->renderer, $result);
        $this->assertNull($result);
    }

    protected function assertResponseNotInjected()
    {
        $content = $this->response->getContent();
        $headers = $this->response->getHeaders();
        $this->assertTrue(empty($content));
        $this->assertFalse($headers->has('content-type'));
    }

    public function testNonMatchingRendererDoesNotInjectResponse()
    {
        $this->event->setResponse($this->response);

        // test empty renderer
        $this->strategy->injectResponse($this->event);
        $this->assertResponseNotInjected();

        // test non-matching renderer
        $renderer = new FeedRenderer();
        $this->event->setRenderer($renderer);
        $this->strategy->injectResponse($this->event);
        $this->assertResponseNotInjected();
    }

    public function testNonStringOrFeedResultDoesNotInjectResponse()
    {
        $this->event->setResponse($this->response);
        $this->event->setRenderer($this->renderer);
        $this->event->setResult($this->response);

        $this->strategy->injectResponse($this->event);
        $this->assertResponseNotInjected();
    }

    public function testMatchingRendererAndStringResultInjectsResponse()
    {
        $this->renderer->setFeedType('atom');
        $expected = '<?xml version="1.0"><root><content>content</content></root>';
        $this->event->setResponse($this->response);
        $this->event->setRenderer($this->renderer);
        $this->event->setResult($expected);

        $this->strategy->injectResponse($this->event);
        $content = $this->response->getContent();
        $headers = $this->response->getHeaders();
        $this->assertEquals($expected, $content);
        $this->assertTrue($headers->has('content-type'));
        $this->assertEquals('application/atom+xml', $headers->get('content-type')->getFieldValue());
    }
    
    protected function getFeedData($type)
    {
        return array(
            'copyright' => date('Y'),
            'date_created' => time(),
            'date_modified' => time(),
            'last_build_date' => time(),
            'description' => __CLASS__,
            'id' => 'http://framework.zend.com/',
            'language' => 'en_US',
            'feed_link' => array(
                'link' => 'http://framework.zend.com/feed.xml',
                'type' => $type,
            ),
            'link' => 'http://framework.zend.com/feed.xml',
            'title' => 'Testing',
            'encoding' => 'UTF-8',
            'base_url' => 'http://framework.zend.com/',
            'entries' => array(
                array(
                    'content' => 'test content',
                    'date_created' => time(),
                    'date_modified' => time(),
                    'description' => __CLASS__,
                    'id' => 'http://framework.zend.com/1',
                    'link' => 'http://framework.zend.com/1',
                    'title' => 'Test 1',
                ),
                array(
                    'content' => 'test content',
                    'date_created' => time(),
                    'date_modified' => time(),
                    'description' => __CLASS__,
                    'id' => 'http://framework.zend.com/2',
                    'link' => 'http://framework.zend.com/2',
                    'title' => 'Test 2',
                ),
            ),
        );
    }

    public function testMatchingRendererAndFeedResultInjectsResponse()
    {
        $this->renderer->setFeedType('atom');
        $expected = FeedFactory::factory($this->getFeedData('atom'));
        $this->event->setResponse($this->response);
        $this->event->setRenderer($this->renderer);
        $this->event->setResult($expected);

        $this->strategy->injectResponse($this->event);
        $content = $this->response->getContent();
        $headers = $this->response->getHeaders();
        $this->assertEquals($expected->export('atom'), $content);
        $this->assertTrue($headers->has('content-type'));
        $this->assertEquals('application/atom+xml', $headers->get('content-type')->getFieldValue());
    }

    public function testResponseContentTypeIsBasedOnFeedType()
    {
        $this->renderer->setFeedType('rss');
        $expected = FeedFactory::factory($this->getFeedData('rss'));
        $this->event->setResponse($this->response);
        $this->event->setRenderer($this->renderer);
        $this->event->setResult($expected);

        $this->strategy->injectResponse($this->event);
        $content = $this->response->getContent();
        $headers = $this->response->getHeaders();
        $this->assertEquals($expected->export('rss'), $content);
        $this->assertTrue($headers->has('content-type'));
        $this->assertEquals('application/rss+xml', $headers->get('content-type')->getFieldValue());
    }

    public function testReturnsNullWhenUnableToSelectRenderer()
    {
        $model   = new ViewModel();
        $request = new HttpRequest();
        $this->event->setModel($model);
        $this->event->setRequest($request);
        $this->assertNull($this->strategy->selectRenderer($this->event));
    }

    public function testAttachesListenersAtExpectedPriorities()
    {
        $events = new EventManager();
        $events->attachAggregate($this->strategy);

        foreach (array('renderer' => 'selectRenderer', 'response' => 'injectResponse') as $event => $method) {
            $listeners        = $events->getListeners($event);
            $expectedCallback = array($this->strategy, $method);
            $expectedPriority = 1;
            $found            = false;
            foreach ($listeners as $listener) {
                $callback = $listener->getCallback();
                if ($callback === $expectedCallback) {
                    if ($listener->getMetadatum('priority') == $expectedPriority) {
                        $found = true;
                        break;
                    }
                }
            }
            $this->assertTrue($found, 'Listener not found');
        }
    }

    public function testCanAttachListenersAtSpecifiedPriority()
    {
        $events = new EventManager();
        $events->attachAggregate($this->strategy, 100);

        foreach (array('renderer' => 'selectRenderer', 'response' => 'injectResponse') as $event => $method) {
            $listeners        = $events->getListeners($event);
            $expectedCallback = array($this->strategy, $method);
            $expectedPriority = 100;
            $found            = false;
            foreach ($listeners as $listener) {
                $callback = $listener->getCallback();
                if ($callback === $expectedCallback) {
                    if ($listener->getMetadatum('priority') == $expectedPriority) {
                        $found = true;
                        break;
                    }
                }
            }
            $this->assertTrue($found, 'Listener not found');
        }
    }

    public function testDetachesListeners()
    {
        $events = new EventManager();
        $events->attachAggregate($this->strategy);
        $listeners = $events->getListeners('renderer');
        $this->assertEquals(1, count($listeners));
        $listeners = $events->getListeners('response');
        $this->assertEquals(1, count($listeners));
        $events->detachAggregate($this->strategy);
        $listeners = $events->getListeners('renderer');
        $this->assertEquals(0, count($listeners));
        $listeners = $events->getListeners('response');
        $this->assertEquals(0, count($listeners));
    }
}
