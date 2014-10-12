<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Test\Module;

use Everon\Exception;

class ManagerTest extends \Everon\TestCase
{
    
    function testConstructor()
    {
        $ModuleManager = new \Everon\Test\Module\Manager();
        $this->assertInstanceOf('Everon\Module\Interfaces\Manager', $ModuleManager);
    }

    /**
     * @dataProvider dataProvider
     */
    function testGetModuleShouldReturnModule(\Everon\Module\Interfaces\Handler $ModuleManager)
    {
        $Module = $ModuleManager->getModuleByName('Foo');
        $this->assertInstanceOf('Everon\Module\Interfaces\Module', $Module);
    }
    
    function dataProvider()
    {
        $Factory = $this->buildFactory();
        $Container = $Factory->getDependencyContainer();
        $FileSystem = $Factory->buildFileSystem($this->getDoublesDirectory());

        $FactoryWorkerMock = $this->getMock('Everon\Interfaces\FactoryWorker');
        $ModuleMock = $this->getMock('Everon\Module\Interfaces\Module');
        $ModuleMock->expects($this->once())
            ->method('setFactoryWorker')
            ->will($this->returnValue(null));
        
        $FactoryMock = $this->getMock('Everon\Application\Interfaces\Factory');
        $FactoryMock->expects($this->once())
            ->method('buildModule')
            ->will($this->returnValue($ModuleMock));
        $FactoryMock->expects($this->once())
            ->method('buildFactoryWorker')
            ->will($this->returnValue($FactoryWorkerMock));

        $ViewManagerMock = $this->getMock('Everon\View\Interfaces\Manager');
        $Container->register('ViewManager', function() use ($ViewManagerMock) {
            return $ViewManagerMock;
        });

        $RouterConfigMock = $this->getMock('Everon\Config\Router', [], [], '', false);
        $ModuleConfigMock = $this->getMock('Everon\Interfaces\Config', [], [], '', false);

        $ConfigManagerMock = $this->getMock('Everon\Config\Interfaces\Manager');
        $ConfigManagerMock->expects($this->once())
            ->method('getConfigValue')
            ->will($this->returnValue(['Foo']));

        $ConfigManagerMock->expects($this->at(1))
            ->method('getConfigByName')
            ->with('Foo@module')
            ->will($this->returnValue($ModuleConfigMock));

        $ConfigManagerMock->expects($this->at(2))
            ->method('getConfigByName')
            ->with('Foo@router')
            ->will($this->returnValue($RouterConfigMock));

        $ModuleManager = $Factory->buildModuleManager();
        $ModuleManager->setFactory($FactoryMock);
        $ModuleManager->setFileSystem($FileSystem);
        $ModuleManager->setConfigManager($ConfigManagerMock);
          
        
        return [
            [$ModuleManager]
        ];
    }

}