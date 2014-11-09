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

class FactoryTest extends \Everon\TestCase
{

    public function testConstructor()
    {
        $FactoryInstance = new \Everon\Application\Factory(new \Everon\Application\Dependency\Container());
        $this->assertInstanceOf('Everon\Application\Interfaces\Factory', $FactoryInstance);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testBuildConfigShouldSilentlyFallBackToDefaultConfigWhenClassNotFound(Interfaces\Factory $Factory)
    {
        $Compiler = function(){};
        $Item = new \Everon\Config\Loader\Item('wrong_filename', [], false);
        $Config = $Factory->buildConfig('test', $Item, $Compiler);
        $this->assertInstanceOf('Everon\Interfaces\Config', $Config);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testBuildConfig(Interfaces\Factory $Factory)
    {
        $Compiler = function(){};
        $Item = new \Everon\Config\Loader\Item('wrong_filename', [], false);
        $Config = $Factory->buildConfig('test', $Item, $Compiler);
        $this->assertInstanceOf('Everon\Interfaces\Config', $Config);
    }
    
    /**
     * @dataProvider dataProvider
     */
    public function testBuildConsoleCore(Interfaces\Factory $Factory)
    {
        $Core = $Factory->buildConsole();
        $this->assertInstanceOf('Everon\Interfaces\Core', $Core);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testBuildMvcCore(Interfaces\Factory $Factory)
    {
        $Core = $Factory->buildMvc();
        $this->assertInstanceOf('Everon\Interfaces\Core', $Core);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testBuildRestServerCore(Interfaces\Factory $Factory)
    {
        $Core = $Factory->buildRestServer();
        $this->assertInstanceOf('Everon\Interfaces\Core', $Core);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testBuildConfigManager(Interfaces\Factory $Factory)
    {
        $Loader = $this->getMock('Everon\Config\Interfaces\Loader');
        $LoaderCache = $this->getMock('Everon\Config\Interfaces\LoaderCache');
        $ConfigManager = $Factory->buildConfigManager($Loader, $LoaderCache);
        $this->assertInstanceOf('Everon\Config\Interfaces\Manager', $ConfigManager);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testBuildMvcController(Interfaces\Factory $Factory)
    {
        $RequestMock = $this->getMock('Everon\Interfaces\Request');
        $Factory->getDependencyContainer()->register('Request', function() use ($RequestMock) {
            return $RequestMock;
        });

        $RouterMock = $this->getMock('Everon\Interfaces\Router');
        $Factory->getDependencyContainer()->register('Router', function() use ($RouterMock) {
            return $RouterMock;
        });
        
        $ViewManagerMock = $this->getMock('Everon\View\Interfaces\Manager');
        $Factory->getDependencyContainer()->register('ViewManager', function() use ($ViewManagerMock) {
            return $ViewManagerMock;
        });

        $DomainManagerMock = $this->getMock('Everon\Domain\Interfaces\Manager', [],[],'', false);
        $Factory->getDependencyContainer()->register('DomainManager', function() use ($DomainManagerMock) {
            return $DomainManagerMock;
        });

        $ModuleMock = $this->getMock('Everon\Module\Interfaces\Module');
        $Controller = $Factory->buildController('MyController', $ModuleMock, 'Everon\Test');
        $this->assertInstanceOf('Everon\Interfaces\Controller', $Controller);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testBuildView(Interfaces\Factory $Factory)
    {
        $View = $Factory->buildView('MyView', $this->getTemplateDirectory(), '.htm','Everon\Test');
        $this->assertInstanceOf('Everon\View\Interfaces\View', $View);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testBuildModel(Interfaces\Factory $Factory)
    {
        $ConfigManager = $this->getMock('Everon\Config\Interfaces\Manager');
        $Factory->getDependencyContainer()->register('ConfigManager', function() use ($ConfigManager) {
            return $ConfigManager;
        });
        
        $RequestMock = $this->getMock('Everon\Interfaces\Request');
        $Factory->getDependencyContainer()->register('Request', function() use ($RequestMock) {
            return $RequestMock;
        });

        $ConnectionManagerMock = $this->getMock('Everon\DataMapper\Interfaces\ConnectionManager');
        $Factory->getDependencyContainer()->register('ConnectionManager', function() use ($ConnectionManagerMock) {
            return $ConnectionManagerMock;
        });
        
        $DataMapperManagerMock = $this->getMock('Everon\DataMapper\Interfaces\Manager');
        $Factory->getDependencyContainer()->register('DataMapperManager', function() use ($DataMapperManagerMock) {
            return $DataMapperManagerMock;
        });
        
        $Model = $Factory->buildDomainModel('Foo', 'Everon\Domain');
        $this->assertInstanceOf('Everon\Domain\Interfaces\Model', $Model);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testBuildDomainManager(Interfaces\Factory $Factory)
    {
        $this->markTestSkipped('Not sure about the DomainManager\'s dependencies');
        $ConfigManager = $this->getMock('Everon\Config\Interfaces\Manager');
        $Factory->getDependencyContainer()->register('ConfigManager', function() use ($ConfigManager) {
            return $ConfigManager;
        });
        
        $DomainManager = $Factory->buildDomainManager('MyDomainManager', 'Everon\Test');
        $this->assertInstanceOf('Everon\Domain\Interfaces\Manager', $DomainManager);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testBuildRouter(Interfaces\Factory $Factory)
    {
        $RequestMock = $this->getMock('Everon\Interfaces\Request');
        $Factory->getDependencyContainer()->register('Request', function() use ($RequestMock) {
            return $RequestMock;
        });
        $RouterConfig = $this->getMock('Everon\Config\Router', [], [], '', false);

        $Router = $Factory->buildRouter($RouterConfig, $Factory->buildRequestValidator());
        $this->assertInstanceOf('Everon\Interfaces\Router', $Router);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testBuildRouteItem(Interfaces\Factory $Factory)
    {
        $RouteItem = $Factory->buildConfigItem('test', [
            \Everon\Config\Item::PROPERTY_NAME => 'test',
            \Everon\Config\Item\Router::PROPERTY_MODULE => 'test',
            'url' => '/',
            'controller' => 'Test',
            'action' => 'testMe',
            'get' => [],
            'post' => [],
            \Everon\Config\Item::PROPERTY_DEFAULT => true,
        ], 'Everon\Config\Item\Router');

        $this->assertInstanceOf('Everon\Config\Interfaces\ItemRouter', $RouteItem);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testBuildTemplate(Interfaces\Factory $Factory)
    {
        $Template = $Factory->buildTemplate('', []);
        $this->assertInstanceOf('Everon\View\Interfaces\TemplateContainer', $Template);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testBuildTemplateContainer(Interfaces\Factory $Factory)
    {
        $TemplateContainer = $Factory->buildTemplateContainer('', []);
        $this->assertInstanceOf('Everon\View\Interfaces\TemplateContainer', $TemplateContainer);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testBuildLogger(Interfaces\Factory $Factory)
    {
        $Logger = $Factory->buildLogger($this->getFrameworkBootstrap()->getEnvironment()->getLog(), true);
        $this->assertInstanceOf('Everon\Interfaces\Logger', $Logger);
    }
    
    /**
     * @dataProvider dataProvider
     */
    public function testBuildResponse(Interfaces\Factory $Factory)
    {
        $Response = $Factory->buildResponse('guid');
        $this->assertInstanceOf('Everon\Interfaces\Response', $Response);
    }
    
    /**
     * @dataProvider dataProvider
     */
    public function testBuildHttpResponse(Interfaces\Factory $Factory)
    {
        $HeadersMock = $this->getMock('Everon\Http\Interfaces\HeaderCollection');
        $CookiesMock = $this->getMock('Everon\Http\Interfaces\CookieCollection');
        $Response = $Factory->buildHttpResponse('guid', $HeadersMock, $CookiesMock);
        $this->assertInstanceOf('Everon\Http\Interfaces\Response', $Response);
    }
    
    /**
     * @dataProvider dataProvider
     */
    public function testBuildHttpHeaderCollection(Interfaces\Factory $Factory)
    {
        $HeaderCollection = $Factory->buildHttpHeaderCollection();
        $this->assertInstanceOf('Everon\Interfaces\Collection', $HeaderCollection);
    }
    
    /**
     * @dataProvider dataProvider
     */
    public function testBuildRequest(Interfaces\Factory $Factory)
    {
        $server = $this->getServerDataForRequest([
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI' => '/',
            'QUERY_STRING' => '',
        ]);
        
        $Request = $Factory->buildHttpRequest($server, [], [], []);
        $this->assertInstanceOf('Everon\Interfaces\Request', $Request);
    }

    /**
     * @dataProvider dataProvider
     * @expectedException \Everon\Exception\DependencyContainer
     * @expectedExceptionMessage Error injecting dependency: "Wrong"
     */    
    public function testDependencyToObjectShouldThrowExceptionWhenWrongDependency(Interfaces\Factory $Factory)
    {
        $Wrong = new \stdClass();
        $Wrong = $Factory->getDependencyContainer()->inject('Wrong', $Wrong);
    }

    /**
     * @dataProvider dataProviderForExceptions
     * @expectedException \Everon\Exception\Factory
     * @expectedExceptionMessage Core: "Everon\Console\Core" initialization error.
     */
    public function testBuildCoreConsoleShouldThrowExceptionWhenWrongClass(Interfaces\Factory $Factory)
    {
        $Factory->buildConsole();
    }

    /**
     * @dataProvider dataProviderForExceptions
     * @expectedException \Everon\Exception\Factory
     * @expectedExceptionMessage Core: "Everon\Mvc\Core" initialization error.
     */
    public function testBuildCoreMvcShouldThrowExceptionWhenWrongClass(Interfaces\Factory $Factory)
    {
        $Factory->buildMvc();
    }

    /**
     * @dataProvider dataProviderForExceptions
     * @expectedException \Everon\Exception\Factory
     * @expectedExceptionMessage Core: "Everon\Rest\Server" initialization error.
     */
    public function testBuildCoreRestServerShouldThrowExceptionWhenWrongClass(Interfaces\Factory $Factory)
    {
        $Factory->buildRestServer();
    }
    
    /**
     * @dataProvider dataProviderForExceptions
     * @expectedException \Everon\Exception\Factory
     * @expectedExceptionMessage Config: "test_config" initialization error
     */
    public function testBuildConfigShouldThrowExceptionWhenWrongClass(Interfaces\Factory $Factory)
    {
        $Compiler = function(){};
        $Item = new \Everon\Config\Loader\Item('wrong_filename', [], false);
        $Factory->buildConfig('test_config', $Item, $Compiler);
    }
    
    /**
     * @dataProvider dataProviderForExceptions
     * @expectedException \Everon\Exception\Factory
     * @expectedExceptionMessage ConfigManager initialization error
     */
    public function testBuildConfigManagerShouldThrowExceptionWhenWrongClass(Interfaces\Factory $Factory)
    {
        $Loader = $this->getMock('Everon\Config\Interfaces\Loader');
        $LoaderCache = $this->getMock('Everon\Config\Interfaces\LoaderCache');
        $Factory->buildConfigManager($Loader, $LoaderCache);
    }
    
    /**
     * @dataProvider dataProviderForExceptions
     * @expectedException \Everon\Exception\Factory
     * @expectedExceptionMessage Controller: "Everon\Controller\Test" initialization error.
     * File for class: "Everon\Controller\Test" could not be found
     */
    public function testBuildControllerShouldThrowExceptionWhenWrongClass(Interfaces\Factory $Factory)
    {
        $ViewManager = $this->getMock('Everon\View\Interfaces\Manager');
        $DomainManager = $this->getMock('Everon\Domain\Interfaces\Manager');
        $ModuleMock = $this->getMock('Everon\Module\Interfaces\Module');
        $Factory->buildController('Test', $ModuleMock);
    }
    
    /**
     * @dataProvider dataProvider
     * @expectedException \Everon\Exception\Factory
     * @expectedExceptionMessage View: "Everon\Test\Wrong" initialization error.
     * TemplateCompiler: "Everon\View\Template\Compiler\NonExisting" initialization error.
     * File for class: "Everon\View\Template\Compiler\NonExisting" could not be found
     */
    public function testBuildViewShouldThrowExceptionWhenWrongClass(Interfaces\Factory $Factory)
    {
        $Factory->buildView('Wrong', $this->getTemplateDirectory(), '.htm', 'Everon\Test');
    }
    
    /**
     * @dataProvider dataProvider
     * @expectedException \Everon\Exception\Factory
     * @expectedExceptionMessage Model: "Everon\Model\DummyNotExisting" initialization error.
     * File for class: "Everon\Model\DummyNotExisting" could not be found
     */
    public function testBuildModelShouldThrowExceptionWhenWrongClass(Interfaces\Factory $Factory)
    {
        $this->markTestSkipped('Domain under construction');
        $Factory->buildDomainModel('DummyNotExisting');
    }
    
    /**
     * @dataProvider dataProvider
     * @expectedException \Everon\Exception\Factory
     * @expectedExceptionMessage DomainManager: "Everon\Model\Handler\Test" initialization error.
     * File for class: "Everon\Model\Handler\Test" could not be found
     */
    public function testBuildDomainManagerShouldThrowExceptionWhenWrongClass(Interfaces\Factory $Factory)
    {
        $this->markTestSkipped('Domain under construction');
        $Factory->buildDomainManager('Test');
    }
    
    /**
     * @dataProvider dataProviderForExceptions
     * @expectedException \Everon\Exception\Factory
     * @expectedExceptionMessage Router initialization error
     */
    public function testBuildRouterShouldThrowExceptionWhenWrongClass(Interfaces\Factory $Factory)
    {
        $Config = $this->getMock('Everon\Interfaces\Config');
        $Validator = $this->getMock('Everon\Interfaces\RequestValidator');
        $Factory->buildRouter($Config, $Validator);
    }
    
    /**
     * @dataProvider dataProviderForExceptions
     * @expectedException \Everon\Exception\Factory
     * @expectedExceptionMessage ConfigItem: "Everon\Config\Item\Router[test]" initialization error
     */
    public function testBuildRouteItemThrowExceptionWhenWrongClass(Interfaces\Factory $Factory)
    {
        $Factory->buildConfigItem('test', [], 'Everon\Config\Item\Router');
    }
    
    /**
     * @dataProvider dataProviderForExceptions
     * @expectedException \Everon\Exception\Factory
     * @expectedExceptionMessage Template initialization error
     */
    public function testBuildTemplateThrowExceptionWhenWrongClass(Interfaces\Factory $Factory)
    {
        $View = $this->getMock('Everon\View\Interfaces\View');
        $Factory->buildTemplate('', []);
    }
    
    /**
     * @dataProvider dataProviderForExceptions
     * @expectedException \Everon\Exception\Factory
     * @expectedExceptionMessage TemplateContainer initialization error
     */
    public function testBuildTemplateContainerThrowExceptionWhenWrongClass(Interfaces\Factory $Factory)
    {
        $Factory->buildTemplateContainer('', []);
    }
    
    /**
     * @dataProvider dataProvider
     * @expectedException \Everon\Exception\Factory
     * @expectedExceptionMessage TemplateCompiler: "Everon\View\Template\Compiler\Test" initialization error.
     * File for class: "Everon\View\Template\Compiler\Test" could not be found
     */
    public function testBuildTemplateCompilerThrowExceptionWhenWrongClass(Interfaces\Factory $Factory)
    {
        $Factory->buildTemplateCompiler('Test');
    }
    
    /**
     * @dataProvider dataProviderForExceptions
     * @expectedException \Everon\Exception\Factory
     * @expectedExceptionMessage Logger initialization error
     */
    public function testBuildLoggerThrowExceptionWhenWrongClass(Interfaces\Factory $Factory)
    {
        $Factory->buildLogger($this->getFrameworkBootstrap()->getEnvironment()->getLog(), false);
    }

    /**
     * @dataProvider dataProviderForExceptions
     * @expectedException \Everon\Exception\Factory
     * @expectedExceptionMessage Response initialization error
     */
    public function testBuildResponseShouldThrowExceptionWhenWrongClass(Interfaces\Factory $Factory)
    {
        $Factory->buildResponse('guid');
    }
    
    /**
     * @dataProvider dataProviderForExceptions
     * @expectedException \Everon\Exception\Factory
     * @expectedExceptionMessage HttpResponse initialization error
     */
    public function testBuildHttpResponseShouldThrowExceptionWhenWrongClass(Interfaces\Factory $Factory)
    {
        $HeadersMock = $this->getMock('Everon\Http\Interfaces\HeaderCollection');
        $CookiesMock = $this->getMock('Everon\Http\Interfaces\CookieCollection');
        $Response = $Factory->buildHttpResponse('guid', $HeadersMock, $CookiesMock);
    }
    
    /**
     * @dataProvider dataProviderForExceptions
     * @expectedException \Everon\Exception\Factory
     * @expectedExceptionMessage HttpHeaderCollection initialization error
     */
    public function testBuildHttpHeaderCollectionShouldThrowExceptionWhenWrongClass(Interfaces\Factory $Factory)
    {
        $Factory->buildHttpHeaderCollection();
    }
    
    /**
     * @dataProvider dataProviderForExceptions
     * @expectedException \Everon\Exception\Factory
     * @expectedExceptionMessage Request initialization error
     */
    public function testBuildRequestShouldThrowExceptionWhenWrongClass(Interfaces\Factory $Factory)
    {
        $Factory->buildHttpRequest([], [], [], []);
    }
    
    public function getTemplateDirectory()
    {
        return $this->getFixtureDirectory().'templates'.DIRECTORY_SEPARATOR;
    }

    public function dataProvider()
    {
        $Factory = $this->buildFactory();
        $Container = $Factory->getDependencyContainer();
        $ViewManagerMock = $this->getMock('Everon\View\Interfaces\Manager');
        $HttpSessionMock = $this->getMock('Everon\Http\Interfaces\Session');
        
        $Container->register('ViewManager', function() use ($ViewManagerMock) {
            return $ViewManagerMock;
        });

        $Container->register('HttpSession', function() use ($HttpSessionMock) {
            return $HttpSessionMock;
        });
        
        return [
            [$Factory]
        ];
    }
    
    public function dataProviderForExceptions()
    {
        $DiMock = $this->getMock('Everon\Application\Interfaces\DependencyContainer');

        $DiMock->expects($this->once())
            ->method('inject')
            ->will($this->throwException(new \Exception));

        $Factory = $this->buildFactory();
        $Factory->setDependencyContainer($DiMock);
        
        return [
            [$Factory]
        ];
    }



}