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
    protected $module_name = 'Rest';
    protected $resource_data = [
        'href' => 'http://api.localhost/v1/foo'
    ];

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
        $this->expectOutputString('{"href":"http:\/\/api.localhost\/v1\/foo"}');
        
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

        $Controller->execute('foo');
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

        $Module = \Mockery::mock('Everon\Module\Interfaces\Module');
        
        $Controller = $Factory->buildController('RestControllerDouble', $Module, 'Everon\Rest');
        
        return [
            [$Controller]
        ];
    }

}
