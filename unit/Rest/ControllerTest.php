<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Test\Rest;

use Everon\Rest\Interfaces;

class ControllerTest extends \Everon\TestCase
{
    protected $resource_data = [
        'href' => 'http://api.localhost/v1/foobars'
    ];
    protected $api_version = 'v1';
    protected $module_name = 'Rest';
    protected $user_id = 1;
    protected $collection_name = 'barbarfoofoos';
    protected $resource_id = 1;
    protected $resource_name = 'foobars';
    protected $resource_url = 'http://api.localhost/v1/foobars/barbarfoofoos';
    

    public function testConstructor()
    {
        $Module = \Mockery::mock('Everon\Module\Interfaces\Module');
        $Controller = new \Everon\Rest\RestControllerDouble($Module);
        $this->assertInstanceOf('Everon\Rest\Interfaces\Controller', $Controller);
    }

    /**
     * @dataProvider dataProvider
     * @expectedException \Everon\Exception\InvalidControllerMethod
     * @expectedExceptionMessage Controller: "Rest@RestControllerDouble" has no action: "does_not_exist" defined
     */
    public function testExecuteShouldThrowExceptionWhenMethodDoesNotExist(Interfaces\Controller $Controller)
    {
        $Module = $Controller->getModule();
        $Module->shouldReceive('getName')->once()->with()->andReturn($this->module_name);
        $Controller->execute('does_not_exist');
    }

    /**
     * @dataProvider dataProvider
     */
    public function testExecuteShouldPrepareResponse(Interfaces\Controller $Controller)
    {
        $this->expectOutputString('{"href":"http:\/\/api.localhost\/v1\/foobars"}');
        
        $ResourceBasic = \Mockery::mock('Everon\Rest\Interfaces\ResourceBasic');
        $ResourceBasic->shouldReceive('toArray')->once()->with()->andReturn($this->resource_data);
        
        $Response = $Controller->getResponse();
        $Response->shouldReceive('setResult')->once()->with(true);
        $Response->shouldReceive('getData')->once()->with()->andReturn($ResourceBasic);
        $Response->shouldReceive('setData')->once()->with($this->resource_data);
        $Response->shouldReceive('wasStatusSet')->once()->with()->andReturn(false);
        $Response->shouldReceive('setStatusCode')->once()->with(200);
        $Response->shouldReceive('setStatusMessage')->once()->with('OK');
        $Response->shouldReceive('toJson')->once()->with()->andReturn(json_encode($this->resource_data));

        $Controller->execute('foobar');
    }

    /**
     * @dataProvider dataProvider
     */
    public function testAddResourceFromRequest(Interfaces\Controller $Controller)
    {
        $resource_data_to_add = [
            'id' => null,
            'email' => 'test@foo.bar',
            'password' => 'foobar'
        ];

        $Href = \Mockery::mock('Everon\Rest\Interfaces\Href');
        $Href->shouldReceive('getUrl')->once()->with()->andReturn($this->resource_url);
        
        $Resource = \Mockery::mock('Everon\Rest\Interfaces\Resource');
        $Resource->shouldReceive('getHref')->once()->with()->andReturn($Href);
        
        $PostCollection = \Mockery::mock('Everon\Interfaces\Collection');
        $PostCollection->shouldReceive('toArray')->once()->with(true)->andReturn($resource_data_to_add);
        
        $Request = $Controller->getRequest();
        $Request->shouldReceive('getVersion')->once()->with()->andReturn($this->api_version);
        $Request->shouldReceive('getPostCollection')->once()->with()->andReturn($PostCollection);
        $Request->shouldReceive('getQueryParameter')->once()->with('resource', null)->andReturn($this->resource_name);

        $Response = $Controller->getResponse();
        $Response->shouldReceive('setData')->once()->with($Resource);
        $Response->shouldReceive('setStatusCode')->once()->with(201);
        $Response->shouldReceive('setHeader')->once()->with('Location', $this->resource_url);
        
        $ResourceManager = $Controller->getResourceManager();
        $ResourceManager->shouldReceive('add')->once()->with("v1", "foobars", $resource_data_to_add, $this->user_id)->andReturn($Resource);

        $Controller->addResourceFromRequest();
    }

    /**
     * @dataProvider dataProvider
     */
    public function testSaveResourceFromRequest(Interfaces\Controller $Controller)
    {
        $resource_data_to_add = [
            'email' => 'test@foo.bar',
            'password' => 'foobar'
        ];

        $Resource = \Mockery::mock('Everon\Rest\Interfaces\Resource');
        
        $PostCollection = \Mockery::mock('Everon\Interfaces\Collection');
        $PostCollection->shouldReceive('toArray')->once()->with(true)->andReturn($resource_data_to_add);

        $Request = $Controller->getRequest();
        $Request->shouldReceive('getVersion')->once()->with()->andReturn($this->api_version);
        $Request->shouldReceive('getPostCollection')->once()->with()->andReturn($PostCollection);
        $Request->shouldReceive('getQueryParameter')->once()->with('resource', null)->andReturn($this->resource_name);
        $Request->shouldReceive('getQueryParameter')->once()->with('resource_id', null)->andReturn($this->resource_id);

        $Response = $Controller->getResponse();
        $Response->shouldReceive('setData')->once()->with($Resource);
        $Response->shouldReceive('setStatusCode')->once()->with(200);

        $ResourceManager = $Controller->getResourceManager();
        $ResourceManager->shouldReceive('save')->once()->with("v1", "foobars", $this->resource_id, $resource_data_to_add, $this->user_id)->andReturn($Resource);

        $Controller->saveResourceFromRequest();
    }

    /**
     * @dataProvider dataProvider
     */
    public function testDeleteResourceFromRequest(Interfaces\Controller $Controller)
    {
        $Resource = \Mockery::mock('Everon\Rest\Interfaces\Resource');

        $Request = $Controller->getRequest();
        $Request->shouldReceive('getVersion')->once()->with()->andReturn($this->api_version);
        $Request->shouldReceive('getQueryParameter')->once()->with('resource', null)->andReturn($this->resource_name);
        $Request->shouldReceive('getQueryParameter')->once()->with('resource_id', null)->andReturn($this->resource_id);

        $Response = $Controller->getResponse();
        $Response->shouldReceive('setData')->once()->with($Resource);
        $Response->shouldReceive('setStatusCode')->once()->with(204);

        $ResourceManager = $Controller->getResourceManager();
        $ResourceManager->shouldReceive('delete')->once()->with("v1", "foobars", $this->resource_id, $this->user_id)->andReturn($Resource);

        $Controller->deleteResourceFromRequest();
    }

    /**
     * @dataProvider dataProvider
     */
    public function testGetResourceFromRequestShouldReturnResource(Interfaces\Controller $Controller)
    {
        $Resource = \Mockery::mock('Everon\Rest\Interfaces\Resource');

        $Navigator = \Mockery::mock('Everon\Rest\Interfaces\ResourceNavigator');
        
        $Factory = $Controller->getFactory();
        $Factory->shouldReceive('buildRestResourceNavigator')->once()->with($Controller->getRequest())->andReturn($Navigator);

        $Request = $Controller->getRequest();
        $Request->shouldReceive('getVersion')->once()->with()->andReturn($this->api_version);
        $Request->shouldReceive('getQueryParameter')->once()->with('resource', null)->andReturn($this->resource_name);
        $Request->shouldReceive('getQueryParameter')->once()->with('resource_id', null)->andReturn($this->resource_id);

        $ResourceManager = $Controller->getResourceManager();
        $ResourceManager->shouldReceive('getResource')->once()->with("v1", "foobars", $this->resource_id, $Navigator)->andReturn($Resource);

        $Controller->getResourceFromRequest();
    }

    /**
     * @dataProvider dataProvider
     */
    public function testGetResourceFromRequestShouldReturnResourceCollection(Interfaces\Controller $Controller)
    {
        $Resource = \Mockery::mock('Everon\Rest\Interfaces\Resource');

        $Navigator = \Mockery::mock('Everon\Rest\Interfaces\ResourceNavigator');

        $Factory = $Controller->getFactory();
        $Factory->shouldReceive('buildRestResourceNavigator')->once()->with($Controller->getRequest())->andReturn($Navigator);

        $Request = $Controller->getRequest();
        $Request->shouldReceive('getVersion')->once()->with()->andReturn($this->api_version);
        $Request->shouldReceive('getQueryParameter')->once()->with('resource', null)->andReturn($this->resource_name);
        $Request->shouldReceive('getQueryParameter')->once()->with('resource_id', null)->andReturn(null);

        $ResourceManager = $Controller->getResourceManager();
        $ResourceManager->shouldReceive('getCollectionResource')->once()->with("v1", "foobars", $Navigator)->andReturn($Resource);

        $Controller->getResourceFromRequest();
    }

    /**
     * @dataProvider dataProvider
     */
    public function testServeResourceFromRequest(Interfaces\Controller $Controller)
    {
        $Resource = \Mockery::mock('Everon\Rest\Interfaces\Resource');

        $Navigator = \Mockery::mock('Everon\Rest\Interfaces\ResourceNavigator');

        $Factory = $Controller->getFactory();
        $Factory->shouldReceive('buildRestResourceNavigator')->once()->with($Controller->getRequest())->andReturn($Navigator);

        $Response = $Controller->getResponse();
        $Response->shouldReceive('setData')->once()->with($Resource);

        $Request = $Controller->getRequest();
        $Request->shouldReceive('getVersion')->once()->with()->andReturn($this->api_version);
        $Request->shouldReceive('getQueryParameter')->once()->with('resource', null)->andReturn($this->resource_name);
        $Request->shouldReceive('getQueryParameter')->once()->with('resource_id', null)->andReturn($this->resource_id);

        $ResourceManager = $Controller->getResourceManager();
        $ResourceManager->shouldReceive('getResource')->once()->with("v1", "foobars", $this->resource_id, $Navigator)->andReturn($Resource);

        $Controller->getResourceFromRequest();
    }

    /**
     * @dataProvider dataProvider
     */
    public function testAddResourceCollectionFromRequest(Interfaces\Controller $Controller)
    {
        $collection_data_to_add = [[
            'id' => null,
            'email' => 'test@foo.bar',
            'password' => 'foobar'
        ],[
            'id' => null,
            'email' => 'bar@test.bar',
            'password' => 'testr'
        ]];

        $Href = \Mockery::mock('Everon\Rest\Interfaces\Href');
        $Href->shouldReceive('getUrl')->once()->with()->andReturn($this->resource_url);

        $Resource = \Mockery::mock('Everon\Rest\Interfaces\Resource');
        $Resource->shouldReceive('getHref')->once()->with()->andReturn($Href);

        $PostCollection = \Mockery::mock('Everon\Interfaces\Collection');
        $PostCollection->shouldReceive('toArray')->once()->with(true)->andReturn($collection_data_to_add);

        $Request = $Controller->getRequest();
        $Request->shouldReceive('getVersion')->once()->with()->andReturn($this->api_version);
        $Request->shouldReceive('getPostCollection')->once()->with()->andReturn($PostCollection);
        $Request->shouldReceive('getQueryParameter')->once()->with('resource', null)->andReturn($this->resource_name);
        $Request->shouldReceive('getQueryParameter')->once()->with('resource_id', null)->andReturn($this->resource_id);
        $Request->shouldReceive('getQueryParameter')->once()->with('collection', null)->andReturn($this->collection_name);

        $Response = $Controller->getResponse();
        $Response->shouldReceive('setData')->once()->with($Resource);
        $Response->shouldReceive('setStatusCode')->once()->with(201);
        $Response->shouldReceive('setHeader')->once()->with('Location', $this->resource_url);

        $ResourceManager = $Controller->getResourceManager();
        $ResourceManager->shouldReceive('addCollection')->once()->with("v1", "foobars", $this->resource_id, $this->collection_name, $collection_data_to_add, $this->user_id)->andReturn($Resource);

        $Controller->addResourceCollectionFromRequest();
    }

    /**
     * @dataProvider dataProvider
     */
    public function testSaveResourceCollectionFromRequest(Interfaces\Controller $Controller)
    {
        $collection_data_to_add = [[
            'id' => 1,
            'email' => 'test@foo.bar',
            'password' => 'foobar'
        ],[
            'id' => 2,
            'email' => 'bar@test.bar',
            'password' => 'testr'
        ]];
        $Resource = \Mockery::mock('Everon\Rest\Interfaces\Resource');

        $PostCollection = \Mockery::mock('Everon\Interfaces\Collection');
        $PostCollection->shouldReceive('toArray')->once()->with(true)->andReturn($collection_data_to_add);

        $Request = $Controller->getRequest();
        $Request->shouldReceive('getVersion')->once()->with()->andReturn($this->api_version);
        $Request->shouldReceive('getPostCollection')->once()->with()->andReturn($PostCollection);
        $Request->shouldReceive('getQueryParameter')->once()->with('resource', null)->andReturn($this->resource_name);
        $Request->shouldReceive('getQueryParameter')->once()->with('resource_id', null)->andReturn($this->resource_id);
        $Request->shouldReceive('getQueryParameter')->once()->with('collection', null)->andReturn($this->collection_name);

        $Response = $Controller->getResponse();
        $Response->shouldReceive('setData')->once()->with($Resource);
        $Response->shouldReceive('setStatusCode')->once()->with(200);
        $Response->shouldReceive('setHeader')->once()->with('Location', $this->resource_url);

        $ResourceManager = $Controller->getResourceManager();
        $ResourceManager->shouldReceive('saveCollection')->once()->with("v1", "foobars", $this->resource_id, $this->collection_name, $collection_data_to_add, $this->user_id)->andReturn($Resource);

        $Controller->saveResourceCollectionFromRequest();
    }

    /**
     * @dataProvider dataProvider
     */
    public function testDeleteResourceCollectionFromRequest(Interfaces\Controller $Controller)
    {
        $collection_data_to_add = [[
            'id' => 1,
            'email' => 'test@foo.bar',
            'password' => 'foobar'
        ],[
            'id' => 2,
            'email' => 'bar@test.bar',
            'password' => 'testr'
        ]];
        $Resource = \Mockery::mock('Everon\Rest\Interfaces\Resource');

        $PostCollection = \Mockery::mock('Everon\Interfaces\Collection');
        $PostCollection->shouldReceive('toArray')->once()->with(true)->andReturn($collection_data_to_add);

        $Request = $Controller->getRequest();
        $Request->shouldReceive('getVersion')->once()->with()->andReturn($this->api_version);
        $Request->shouldReceive('getPostCollection')->once()->with()->andReturn($PostCollection);
        $Request->shouldReceive('getQueryParameter')->once()->with('resource', null)->andReturn($this->resource_name);
        $Request->shouldReceive('getQueryParameter')->once()->with('resource_id', null)->andReturn($this->resource_id);
        $Request->shouldReceive('getQueryParameter')->once()->with('collection', null)->andReturn($this->collection_name);

        $Response = $Controller->getResponse();
        $Response->shouldReceive('setData')->once()->with($Resource);
        $Response->shouldReceive('setStatusCode')->once()->with(204);
        $Response->shouldReceive('setHeader')->once()->with('Location', $this->resource_url);

        $ResourceManager = $Controller->getResourceManager();
        $ResourceManager->shouldReceive('deleteCollection')->once()->with("v1", "foobars", $this->resource_id, $this->collection_name, $collection_data_to_add, $this->user_id)->andReturn($Resource);

        $Controller->deleteResourceCollectionFromRequest();
    }

    /**
     * @dataProvider dataProvider
     */
    public function testShowException(Interfaces\Controller $Controller)
    {
        $this->expectOutputString('{"error":"Moar error info"}');
        
        $code = 500;
        $message = 'Moar error info';

        $Exception = new \Everon\Http\Exception((new \Everon\Http\Message\InternalServerError($message)));
        
        $Response = $Controller->getResponse();
        $Response->shouldReceive('wasStatusSet')->once()->with()->andReturn(true);
        $Response->shouldReceive('setData')->once()->with(['error' => $message]);
        $Response->shouldReceive('setStatusCode')->once()->with($code);
        $Response->shouldReceive('setStatusMessage')->once()->with($message);
        $Response->shouldReceive('toJson')->once()->with()->andReturn(json_encode(['error' => $message]));

        $Controller->showException($Exception);
    }

    public function dataProvider()
    {
        $Factory = $this->buildFactory();
        $Container = $Factory->getDependencyContainer();
        
        $ResourceManager = \Mockery::mock('Everon\Rest\Interfaces\ResourceManager');
        $Container->register('ResourceManager', function() use ($ResourceManager) {
            return $ResourceManager;
        });

        $Response = \Mockery::mock('Everon\Rest\Interfaces\Response');
        $Container->register('Response', function() use ($Response) {
            return $Response;
        });
        
        $Request = \Mockery::mock('Everon\Rest\Interfaces\Request');
        $Container->register('Request', function() use ($Request) {
            return $Request;
        });

        $FactoryMock = \Mockery::mock('Everon\Application\Interfaces\Factory');
        $Container->register('Factory', function() use ($FactoryMock) {
            return $FactoryMock;
        });

        $Module = \Mockery::mock('Everon\Module\Interfaces\Module');
        $Controller = $Factory->buildController('RestControllerDouble', $Module, 'Everon\Rest');
        $Controller->setFactory($FactoryMock);
        
        return [
            [$Controller]
        ];
    }

}
