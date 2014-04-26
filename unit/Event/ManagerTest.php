<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Test\Event;

use Everon\Event\Interfaces;

class ManagerTest extends \Everon\TestCase
{
    function testConstructor()
    {
        $Manager = new \Everon\Event\Manager();
        $this->assertInstanceOf('Everon\Event\Interfaces\Manager', $Manager);
    }

    /**
     * @dataProvider dataProvider
     */
    function testRegister(Interfaces\Manager $Manager, Interfaces\Context $Context)
    {
        $Manager->registerBefore('test.event', $Context);
        $Manager->registerBefore('test.event', $Context);
        
        $Manager->registerAfter('test.event', $Context);
        $Manager->registerAfter('test.event', $Context);
        
        $events = $Manager->getEvents();

        $this->assertArrayHasKey('test.event', $events);
        $this->assertArrayHasKey(\Everon\Event\Manager::DISPATCH_BEFORE, $events['test.event']);
        $this->assertArrayHasKey(\Everon\Event\Manager::DISPATCH_AFTER, $events['test.event']);
        
        $this->assertCount(4, $events['test.event'][\Everon\Event\Manager::DISPATCH_BEFORE]);
        $this->assertCount(4, $events['test.event'][\Everon\Event\Manager::DISPATCH_AFTER]);
        
        $result = $events['test.event'][\Everon\Event\Manager::DISPATCH_BEFORE][1]();
        $this->assertTrue($result);
    }

    /**
     * @dataProvider dataProvider
     */
    function testDispatch(Interfaces\Manager $Manager, Interfaces\Context $Context)
    {
        $Manager->registerBefore('test.event', $Context);
        $Manager->registerAfter('test.event', $Context);
        
        $result_before = $Manager->dispatchBefore('test.event', $Context);
        $result_after = $Manager->dispatchAfter('test.event', $Context);
        
        $this->assertTrue($result_before);
        $this->assertTrue($result_after);
    }

    /**
     * @dataProvider dataProvider
     * @expectedException \Everon\Event\Exception\EventManager
     * @expectedExceptionMessage I was called first
     */
    function testDispatchBeforeFalseShouldStopPropagation(Interfaces\Manager $Manager, Interfaces\Context $Context)
    {
        $Manager->registerBefore('test.event', $Context);

        $ContextNew = clone $Context;
        $ContextNew->setCallback(function() {
            return false;
        });
        $Manager->registerBefore('test.event', $ContextNew, 10);

        $ContextNew = clone $Context;
        $ContextNew->setCallback(function() {
            throw new \Everon\Event\Exception\EventManager('I was called first');
        });
        $Manager->registerBefore('test.event', $ContextNew, 1000);
        
        $Manager->dispatchBefore('test.event');
    }

    
    function dataProvider()
    {
        $Factory = $this->buildFactory();
        $Manager = $Factory->buildEventManager();
        
        $Callback = function() {
            return true;
        };
        
        $Context = $Factory->buildEventContext($Callback);

        return [
            [$Manager, $Context]
        ];
    }
}