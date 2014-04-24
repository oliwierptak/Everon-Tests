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
    function testRegisterBefore(\Everon\Event\Interfaces\Manager $Manager)
    {
        $Callback = function() {
            return false;
        };
        
        $Manager->registerBefore('test.event', $Callback);
        $Manager->registerBefore('test.event', $Callback);
        $Manager->registerAfter('test.event', $Callback);
        $Manager->registerAfter('test.event', $Callback);
        
        $Property = $this->getProtectedProperty('Everon\Event\Manager', 'listeners');
        $listeners = $Property->getValue($Manager);
        
        s($listeners);
        $this->assertArrayHasKey('test.event', $listeners);
        $this->assertArrayHasKey(\Everon\Event\Manager::WHEN_BEFORE, $listeners['test.event']);
        $this->assertArrayHasKey(\Everon\Event\Manager::WHEN_AFTER, $listeners['test.event']);
        
        $this->assertCount(2, $listeners['test.event'][\Everon\Event\Manager::WHEN_BEFORE][1]);
        $this->assertCount(2, $listeners['test.event'][\Everon\Event\Manager::WHEN_AFTER][1]);
    }

    
    function dataProvider()
    {
        $Factory = $this->buildFactory();
        $Manager = $Factory->buildEventManager();

        return [
            [$Manager]
        ];
    }
}