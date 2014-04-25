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
    function testRegisterBefore(Interfaces\Manager $Manager, Interfaces\Context $Context)
    {
        $Manager->registerBefore('test.event', $Context);
        $Manager->registerBefore('test.event', $Context);
        
        $Manager->registerAfter('test.event', $Context);
        $Manager->registerAfter('test.event', $Context);
        
        $callbacks = $Manager->getEvents();

        $this->assertArrayHasKey('test.event', $callbacks);
        $this->assertArrayHasKey(\Everon\Event\Manager::DISPATCH_BEFORE, $callbacks['test.event']);
        $this->assertArrayHasKey(\Everon\Event\Manager::DISPATCH_AFTER, $callbacks['test.event']);
        
        $this->assertCount(4, $callbacks['test.event'][\Everon\Event\Manager::DISPATCH_BEFORE]);
        $this->assertCount(4, $callbacks['test.event'][\Everon\Event\Manager::DISPATCH_AFTER]);
        
        $result = $callbacks['test.event'][\Everon\Event\Manager::DISPATCH_BEFORE][1]();
        $this->assertFalse($result);
    }

    
    function dataProvider()
    {
        $Factory = $this->buildFactory();
        $Manager = $Factory->buildEventManager();
        
        $Callback = function() {
            return false;
        };
        
        $Context = $Factory->buildEventContext($Callback);

        return [
            [$Manager, $Context]
        ];
    }
}