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

use Everon\Module;
use Everon\Exception;

class ManagerTest extends \Everon\TestCase
{
    
    function testConstructor()
    {
        $ModuleManager = new \Everon\Module\Manager();
        $this->assertInstanceOf('Everon\Module\Interfaces\Manager', $ModuleManager);
    }

    /**
     * @dataProvider dataProvider
     */
    function testGetModuleShouldReturnModule(Module\Interfaces\Manager $ModuleManager)
    {
        $Module = $ModuleManager->getModule('Test');
        $this->assertInstanceOf('Everon\Interfaces\Module', $Module);
    }
    
    function dataProvider()
    {
        $Factory = $this->buildFactory();
        $Container = $Factory->getDependencyContainer();
        
        $FactoryWorkerMock = $this->getMock('Everon\Interfaces\FactoryWorker');

        $DirMock = $this->getMock('\SplFileInfo', ['isDot', 'getBasename'], [], '', false);
        $DirMock->expects($this->once())
            ->method('isDot')
            ->will($this->returnValue(false));
        $DirMock->expects($this->any())
            ->method('getBasename')
            ->will($this->returnValue('Test'));
        $DirMock->expects($this->any())
            ->method('getPathname')
            ->will($this->returnValue($this->getDoublesDirectory().'Module'.DIRECTORY_SEPARATOR.'Test'));
        
        
        $FileSystemMock = $this->getMock('Everon\Interfaces\FileSystem');
        $FileSystemMock->expects($this->once())
            ->method('listPathDir')
            ->will($this->returnValue([$DirMock]));
        
        $ModuleMock = $this->getMock('Everon\Interfaces\Module');
        
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
            ->will($this->returnValue(['Test']));

        $ConfigManagerMock->expects($this->at(1))
            ->method('getConfigByName')
            ->with('Test@module')
            ->will($this->returnValue($ModuleConfigMock));

        $ConfigManagerMock->expects($this->at(2))
            ->method('getConfigByName')
            ->with('Test@router')
            ->will($this->returnValue($RouterConfigMock));

        $ModuleManager = $Factory->buildModuleManager();
        $ModuleManager->setFactory($FactoryMock);
        $ModuleManager->setFileSystem($FileSystemMock);
        $ModuleManager->setConfigManager($ConfigManagerMock);
          
        
        return [
            [$ModuleManager]
        ];
    }

}