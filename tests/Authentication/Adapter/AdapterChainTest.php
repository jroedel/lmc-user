<?php

namespace LmcUserTest\Authentication\Adapter;

use Laminas\EventManager\EventInterface;
use Laminas\EventManager\EventManagerInterface;
use Laminas\EventManager\SharedEventManagerInterface;
use LmcUser\Authentication\Adapter\AdapterChain;
use LmcUser\Authentication\Adapter\AdapterChainEvent;
use Laminas\Stdlib\RequestInterface;

class AdapterChainTest extends \PHPUnit_Framework_TestCase
{
    /**
     * The object to be tested.
     *
     * @var AdapterChain
     */
    protected $adapterChain;

    /**
     * Mock event manager.
     *
     * @var \PHPUnit_Framework_MockObject_MockObject|EventManagerInterface
     */
    protected $eventManager;

    /**
     * Mock event manager.
     *
     * @var \PHPUnit_Framework_MockObject_MockObject|SharedEventManagerInterface
     */
    protected $sharedEventManager;

    /**
     * For tests where an event is required.
     *
     * @var \PHPUnit_Framework_MockObject_MockObject|EventInterface
     */
    protected $event;

    /**
     * Used when testing prepareForAuthentication.
     *
     * @var \PHPUnit_Framework_MockObject_MockObject|RequestInterface
     */
    protected $request;

    /**
     * Prepare the objects to be tested.
     */
    protected function setUp()
    {
        $this->event = null;
        $this->request = null;

        $this->adapterChain = new AdapterChain();

        $this->sharedEventManager = $this->getMock('Laminas\EventManager\SharedEventManagerInterface');
        //$this->sharedEventManager->expects($this->any())->method('getListeners')->will($this->returnValue([]));

        $this->eventManager = $this->getMock('Laminas\EventManager\EventManagerInterface');
        $this->eventManager->expects($this->any())->method('getSharedManager')->will($this->returnValue($this->sharedEventManager));
        $this->eventManager->expects($this->any())->method('setIdentifiers');

        $this->adapterChain->setEventManager($this->eventManager);
    }

    /**
     * @covers \LmcUser\Authentication\Adapter\AdapterChain::authenticate
     */
    public function testAuthenticate()
    {
        $event = $this->getMock('LmcUser\Authentication\Adapter\AdapterChainEvent');
        $event->expects($this->once())
              ->method('getCode')
              ->will($this->returnValue(123));
        $event->expects($this->once())
              ->method('getIdentity')
              ->will($this->returnValue('identity'));
        $event->expects($this->once())
              ->method('getMessages')
              ->will($this->returnValue(array()));

        $this->sharedEventManager->expects($this->once())
             ->method('getListeners')
             ->with($this->equalTo(['authenticate']), $this->equalTo('authenticate'))
             ->will($this->returnValue(array()));

        $this->adapterChain->setEvent($event);
        $result = $this->adapterChain->authenticate();

        $this->assertInstanceOf('Laminas\Authentication\Result', $result);
        $this->assertEquals($result->getIdentity(), 'identity');
        $this->assertEquals($result->getMessages(), array());
    }

    /**
     * @covers \LmcUser\Authentication\Adapter\AdapterChain::resetAdapters
     */
    public function testResetAdapters()
    {
        $listeners = array();

        for ($i=1; $i<=3; $i++) {
            $storage = $this->getMock('LmcUser\Authentication\Storage\Db');
            $storage->expects($this->once())
                    ->method('clear');

            $adapter = $this->getMock('LmcUser\Authentication\Adapter\ChainableAdapter');
            $adapter->expects($this->once())
                    ->method('getStorage')
                    ->will($this->returnValue($storage));

            $callback = [$adapter, 'authenticate'];
            $listeners[] = $callback;
        }

        $this->sharedEventManager->expects($this->once())
             ->method('getListeners')
             ->with($this->equalTo(['authenticate']), $this->equalTo('authenticate'))
             ->will($this->returnValue($listeners));

        $result = $this->adapterChain->resetAdapters();

        $this->assertInstanceOf('LmcUser\Authentication\Adapter\AdapterChain', $result);
    }

    /**
     * Get through the first part of SetUpPrepareForAuthentication
     */
    protected function setUpPrepareForAuthentication()
    {
        $this->request = $this->getMock('Laminas\Stdlib\RequestInterface');
        $this->event = $this->getMock('LmcUser\Authentication\Adapter\AdapterChainEvent');

        $this->event->expects($this->once())->method('setRequest')->with($this->request);

        $this->eventManager->expects($this->at(0))->method('trigger')->with('authenticate.pre');

        /**
         * @var $response \Laminas\EventManager\ResponseCollection
         */
        $responses = $this->getMock('Laminas\EventManager\ResponseCollection');

        $this->eventManager->expects($this->at(1))
            ->method('trigger')
            ->with('authenticate', $this->event)
            ->will($this->returnCallback(function ($event, $target, $callback) use ($responses) {
                if (call_user_func($callback, $responses->last())) {
                    $responses->setStopped(true);
                }
                return $responses;
            }));

        $this->adapterChain->setEvent($this->event);

        return $responses;
    }

    /**
     * Provider for testPrepareForAuthentication()
     *
     * @return array
     */
    public function identityProvider()
    {
        return array(
            array(true, true),
            array(false, false),
        );
    }

    /**
     * Tests prepareForAuthentication when falls through events.
     *
     * @param mixed $identity
     * @param bool  $expected
     *
     * @dataProvider identityProvider
     * @covers \LmcUser\Authentication\Adapter\AdapterChain::prepareForAuthentication
     */
    public function testPrepareForAuthentication($identity, $expected)
    {
        $result = $this->setUpPrepareForAuthentication();

        $result->expects($this->once())->method('stopped')->will($this->returnValue(false));

        $this->event->expects($this->once())->method('getIdentity')->will($this->returnValue($identity));

        $this->assertEquals(
            $expected,
            $this->adapterChain->prepareForAuthentication($this->request),
            'Asserting prepareForAuthentication() returns true'
        );
    }

    /**
     * Test prepareForAuthentication() when the returned collection contains stopped.
     *
     * @covers \LmcUser\Authentication\Adapter\AdapterChain::prepareForAuthentication
     */
    public function testPrepareForAuthenticationWithStoppedEvent()
    {
        $result = $this->setUpPrepareForAuthentication();

        $result->expects($this->once())->method('stopped')->will($this->returnValue(true));

        $lastResponse = $this->getMock('Laminas\Stdlib\ResponseInterface');
        $result->expects($this->atLeastOnce())->method('last')->will($this->returnValue($lastResponse));

        $this->assertEquals(
            $lastResponse,
            $this->adapterChain->prepareForAuthentication($this->request),
            'Asserting the Response returned from the event is returned'
        );
    }

    /**
     * Test prepareForAuthentication() when the returned collection contains stopped.
     *
     * @covers \LmcUser\Authentication\Adapter\AdapterChain::prepareForAuthentication
     * @expectedException \LmcUser\Exception\AuthenticationEventException
     */
    public function testPrepareForAuthenticationWithBadEventResult()
    {
        $result = $this->setUpPrepareForAuthentication();

        $result->expects($this->once())->method('stopped')->will($this->returnValue(true));

        $lastResponse = 'random-value';
        $result->expects($this->atLeastOnce())->method('last')->will($this->returnValue($lastResponse));

        $this->adapterChain->prepareForAuthentication($this->request);
    }

    /**
     * Test getEvent() when no event has previously been set.
     *
     * @covers \LmcUser\Authentication\Adapter\AdapterChain::getEvent
     */
    public function testGetEventWithNoEventSet()
    {
        $event = $this->adapterChain->getEvent();

        $this->assertInstanceOf(
            'LmcUser\Authentication\Adapter\AdapterChainEvent',
            $event,
            'Asserting the adapter in an instance of LmcUser\Authentication\Adapter\AdapterChainEvent'
        );
        $this->assertEquals(
            $this->adapterChain,
            $event->getTarget(),
            'Asserting the Event target is the AdapterChain'
        );
    }

    /**
     * Test getEvent() when an event has previously been set.
     *
     * @covers \LmcUser\Authentication\Adapter\AdapterChain::setEvent
     * @covers \LmcUser\Authentication\Adapter\AdapterChain::getEvent
     */
    public function testGetEventWithEventSet()
    {
        $event = new \LmcUser\Authentication\Adapter\AdapterChainEvent();

        $this->adapterChain->setEvent($event);

        $this->assertEquals(
            $event,
            $this->adapterChain->getEvent(),
            'Asserting the event fetched is the same as the event set'
        );
    }

    /**
     * Tests the mechanism for casting one event type to AdapterChainEvent
     *
     * @covers \LmcUser\Authentication\Adapter\AdapterChain::setEvent
     */
    public function testSetEventWithDifferentEventType()
    {
        $testParams = array('testParam' => 'testValue');

        $event = new \Laminas\EventManager\Event;
        $event->setParams($testParams);

        $this->adapterChain->setEvent($event);
        $returnEvent = $this->adapterChain->getEvent();

        $this->assertInstanceOf(
            'LmcUser\Authentication\Adapter\AdapterChainEvent',
            $returnEvent,
            'Asserting the adapter in an instance of LmcUser\Authentication\Adapter\AdapterChainEvent'
        );

        $this->assertEquals(
            $testParams,
            $returnEvent->getParams(),
            'Asserting event parameters match'
        );
    }

    /**
     * Test the logoutAdapters method.
     *
     * @depends testGetEventWithEventSet
     * @covers \LmcUser\Authentication\Adapter\AdapterChain::logoutAdapters
     */
    public function testLogoutAdapters()
    {
        $event = new AdapterChainEvent();

        $this->eventManager
            ->expects($this->once())
            ->method('trigger')
            ->with('logout', $event);

        $this->adapterChain->setEvent($event);
        $this->adapterChain->logoutAdapters();
    }
}
