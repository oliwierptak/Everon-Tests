<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Test;

use Everon\Interfaces;
use Everon\Exception;

class ManagerTest extends \Everon\TestCase
{
    
    function testConstructor()
    {
        $ModuleManager = new \Everon\Module\Manager();
        $this->assertInstanceOf('Everon\Interfaces\ModuleManager', $ModuleManager);
    }

    /**
     * @dataProvider dataProvider
     */
    function testGetModuleShouldReturnModule(Interfaces\ModuleManager $ModuleManager)
    {
        $Module = $ModuleManager->getModule('_Core');
        $this->assertInstanceOf('Everon\Interfaces\Module', $Module);
    }
    
    function dataProvider()
    {
        $Factory = $this->buildFactory();
        $Container = $Factory->getDependencyContainer();
        $FileSystem = $Factory->buildFileSystem($this->FrameworkEnvironment->getRoot());
        
        $Container->register('FileSystem', function() use ($FileSystem) {
            return $FileSystem;
        });

        $ViewManagerMock = $this->getMock('Everon\Interfaces\ViewManager');
        $Container->register('ViewManager', function() use ($ViewManagerMock) {
            return $ViewManagerMock;
        });
        
        $ModuleManager = $Factory->buildModuleManager();
        
        return [
            [$ModuleManager]
        ];
    }

}