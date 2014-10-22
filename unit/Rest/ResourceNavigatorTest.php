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

class ResourceNavigatorTest extends \Everon\TestCase
{

    public function testConstructor()
    {
        $RequestMock = $this->getMock('Everon\Rest\Interfaces\Request');
        $Navigator = new \Everon\Rest\Resource\Navigator($RequestMock);
        $this->assertInstanceOf('Everon\Rest\Interfaces\ResourceNavigator', $Navigator);
    }

    public function testStateMockery()
    {
        $Request = \Mockery::mock('Everon\Rest\Interfaces\Request');
        $Request->shouldReceive('getGetParameter')->once()->with('fields', [])->andReturn('id,name,date_added');
        $Request->shouldReceive('getGetParameter')->once()->with('expand', [])->andReturn('test,me');
        $Request->shouldReceive('getGetParameter')->once()->with('limit', 10)->andReturn(null);
        $Request->shouldReceive('getGetParameter')->once()->with('offset', 0)->andReturn(null);
        $Request->shouldReceive('getGetParameter')->once()->with('filters')->andReturn(null);
        $Request->shouldReceive('getGetParameter')->once()->with('order_by', [])->andReturn('id,-name');
        $Request->shouldReceive('getQueryParameter')->once()->with('collection', null)->andReturn(null);
        
        $Navigator = new \Everon\Rest\Resource\Navigator($Request);
        
        $this->assertInstanceOf('Everon\Rest\Interfaces\ResourceNavigator', $Navigator);
        $this->assertEquals(['test', 'me'], $Navigator->getExpand());
        $this->assertEquals(['id','name', 'date_added'], $Navigator->getFields());
        $this->assertEquals(['id'=>'ASC', 'name'=>'DESC'], $Navigator->getOrderBy());
    }

    public function dataProvider()
    {
        $RequestMock = $this->getMock('Everon\Rest\Interfaces\Request');
        $Factory = $this->buildFactory();
        
        $Navigator = $Factory->buildRestResourceNavigator($RequestMock);
        
        return [
            [$Navigator]
        ];
    }

}
