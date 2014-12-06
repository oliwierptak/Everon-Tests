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

class ManagerTest extends \Everon\TestCase
{
    public function testConstructor()
    {
        $Loader = $this->getMock('Everon\Config\Interfaces\Loader');
        $LoaderCache = $this->getMock('Everon\FileSystem\Interfaces\CacheLoader');
        $Manager = new \Everon\Config\Manager($Loader, $LoaderCache);
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
        $this->markTestSkipped();
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
        $this->markTestSkipped();
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

        $filename = $this->getFrameworkBootstrap()->getEnvironment()->getConfig().'test.ini';
        $data = parse_ini_file($filename, true);
        $Expected = $Factory->buildConfig(
            'test',
            $filename,
            $data
        );

        //$FileSystem = $Factory->buildFileSystem($this->getFrameworkBootstrap()->getEnvironment()->getRoot());
        
        $ConfigLoader = $Factory->buildConfigLoader($this->getFrameworkBootstrap()->getEnvironment()->getConfig());
        $ConfigLoader->setFactory($Factory);

        $ConfigLoaderCache = $Factory->buildConfigCacheLoader($this->getFrameworkBootstrap()->getEnvironment()->getCacheConfig());
        $ConfigLoaderCache->setFactory($Factory);
        
        $ConfigManager = $Factory->buildConfigManager($ConfigLoader, $ConfigLoaderCache);
        $ConfigManager->setFactory($Factory);
        //$ConfigManager->setFileSystem($FileSystem);
        
        return [
            [$ConfigManager, $Expected]
        ];
    }

}
