<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Test\Config;

use Everon\Environment;

class ManagerTest extends \Everon\TestCase
{
    public function testConstructor()
    {
        $Loader = $this->getMock('Everon\Config\Interfaces\Loader');
        $Manager = new \Everon\Config\Manager($Loader);
        $this->assertInstanceOf('Everon\Config\Interfaces\Manager', $Manager);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testRegister(\Everon\Config\Interfaces\Manager $ConfigManager, \Everon\Interfaces\Config $Expected)
    {
        $count = count($ConfigManager->getConfigs());
        $ConfigManager->unRegister($Expected->getName());
        $this->assertCount($count - 1, $ConfigManager->getConfigs());
        
        $ConfigManager->register($Expected);
        $this->assertCount($count, $ConfigManager->getConfigs());
    }

    /**
     * @dataProvider dataProvider
     */
    public function testUnRegister(\Everon\Config\Interfaces\Manager $ConfigManager, \Everon\Interfaces\Config $Expected)
    {
        $count = count($ConfigManager->getConfigs());
        $ConfigManager->unRegister($Expected->getName());

        $this->assertCount($count - 1, $ConfigManager->getConfigs());
    }

    /**
     * @dataProvider dataProvider
     * @expectedException \Everon\Exception\Config
     * @expectedExceptionMessage Config with name: "test" already registered
     */
    public function testRegisterShouldThrowExceptionWhenConfigAlreadyExists(\Everon\Config\Interfaces\Manager $ConfigManager, \Everon\Interfaces\Config $Expected)
    {
        $ConfigManager->register($Expected);
        $ConfigManager->register($Expected);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testLoadAndRegisterConfigs(\Everon\Config\Interfaces\Manager $ConfigManager, \Everon\Interfaces\Config $Expected)
    {
        $Config = $ConfigManager->getConfigByName('application');
        $this->assertInstanceOf('Everon\Config', $Config);

        $Config = $ConfigManager->getConfigByName('router');
        $this->assertInstanceOf('Everon\Config\Router', $Config);

        $Config = $ConfigManager->getConfigByName('test');
        $this->assertInstanceOf('Everon\Interfaces\Config', $Config);
        $this->assertEquals($Expected->toArray(), $Config->toArray());
    }

    /**
     * @dataProvider dataProvider
     */
    public function testSettersAndGetters(\Everon\Config\Interfaces\Manager $ConfigManager, \Everon\Interfaces\Config $Expected)
    {
        $Config = $ConfigManager->getConfigByName('application');
        $this->assertInstanceOf('Everon\Config', $Config);
        
        $Config = $ConfigManager->getConfigByName('router');
        $this->assertInstanceOf('Everon\Config\Router', $Config);
        
        $Config = $ConfigManager->getConfigByName('test');
        $this->assertInstanceOf('Everon\Config', $Config);
    }

    /**
     * @dataProvider dataProvider
     * @expectedException \Everon\Exception\Config
     * @expectedExceptionMessage Invalid config name: wrong
     */
    public function testGetConfigByNameShouldThrowExceptionWhenConfigFileNotFound(\Everon\Config\Interfaces\Manager $ConfigManager, \Everon\Interfaces\Config $Expected)
    {
        $Config = $ConfigManager->getConfigByName('wrong');
    }

    /**
     * @dataProvider dataProvider
     */
    public function testRegisterWithCache(\Everon\Config\Interfaces\Manager $ConfigManager, \Everon\Interfaces\Config $Expected)
    {
        $ConfigManager->setIsCachingEnabled(true);
        $ConfigManager->unRegister($Expected->getName());
        $ConfigManager->register($Expected);
        $Config = $ConfigManager->getConfigByName($Expected->getName());
        $this->assertInstanceOf('Everon\Interfaces\Config', $Config);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testLoadAndRegisterConfigsWithCache(\Everon\Config\Interfaces\Manager $ConfigManager, \Everon\Interfaces\Config $Expected)
    {
        $ConfigManager->setIsCachingEnabled(true);

        $Property = $this->getProtectedProperty('Everon\Config\Manager', 'configs');
        $Property->setValue($ConfigManager, null);
        $Config = $ConfigManager->getConfigByName('application');

        $this->assertInstanceOf('Everon\Interfaces\Config', $Config);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testGetConfigsWithCache(\Everon\Config\Interfaces\Manager $ConfigManager, \Everon\Interfaces\Config $Expected)
    {
        $ConfigManager->setIsCachingEnabled(true);

        $Property = $this->getProtectedProperty('Everon\Config\Manager', 'configs');
        $Property->setValue($ConfigManager, null);

        $configs = $ConfigManager->getConfigs();
        $this->assertNotEmpty($configs);
    }

    public function dataProvider()
    {
        /**
         * @var \Everon\Application\Interfaces\Factory $Factory
         */
        $Factory = $this->buildFactory();

        //$name, Interfaces\ConfigLoaderItem $ConfigLoaderItem, \Closure $Compiler
        $Compiler = function(&$data) {};

        
        
        $filename = $this->getConfigDirectory().'test.ini';
        $ConfigLoaderItem = $Factory->buildConfigLoaderItem($filename, parse_ini_file($filename, true));
        $Expected = $Factory->buildConfig(
            'test',
            $ConfigLoaderItem,
            $Compiler
        );

        $Environment = new Environment($this->FrameworkEnvironment->getRoot(), $this->FrameworkEnvironment->getEveronRoot());
        $Environment->setConfig($this->getConfigDirectory());
        $Environment->setCacheConfig($this->getConfigCacheDirectory());
        
        $FileSystem = $Factory->buildFileSystem($this->getDoublesDirectory());
        
        $ConfigLoader = $Factory->buildConfigLoader($Environment->getConfig(), $Environment->getCacheConfig());
        $ConfigLoader->setFactory($Factory);
        
        $ConfigManager = $Factory->buildConfigManager($ConfigLoader);
        $ConfigManager->setFactory($Factory);
        $ConfigManager->setEnvironment($Environment);
        $ConfigManager->setFileSystem($FileSystem);
        
        return [
            [$ConfigManager, $Expected]
        ];
    }

}
