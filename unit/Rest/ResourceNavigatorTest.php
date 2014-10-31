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

use Everon\Rest;

class ResourceNavigatorTest extends \Everon\TestCase
{
    protected $limit = 10;
    protected $offset = 0;
    protected $order_by = null;
    protected $order_by_result = ['id'=>'ASC', 'name'=>'DESC'];
    
    
    public function testGetExpandGetFieldsGetOrderBy()
    {
        $Request = \Mockery::mock('Everon\Rest\Interfaces\Request');
        $Request->shouldReceive('getGetParameter')->once()->with('fields', [])->andReturn('id,name,date_added');
        $Request->shouldReceive('getGetParameter')->once()->with('expand', [])->andReturn('test,me');
        $Request->shouldReceive('getGetParameter')->once()->with('limit', $this->limit)->andReturn($this->limit);
        $Request->shouldReceive('getGetParameter')->once()->with('offset', $this->offset)->andReturn($this->offset);
        $Request->shouldReceive('getGetParameter')->once()->with('filters')->andReturn(null);
        $Request->shouldReceive('getGetParameter')->once()->with('order_by', [])->andReturn('id,-name');
        $Request->shouldReceive('getQueryParameter')->once()->with('collection', null)->andReturn(null);
        
        $Navigator = new \Everon\Rest\Resource\Navigator($Request);
        
        $this->assertInstanceOf('Everon\Rest\Interfaces\ResourceNavigator', $Navigator);
        $this->assertEquals(['test', 'me'], $Navigator->getExpand());
        $this->assertEquals(['id','name', 'date_added'], $Navigator->getFields());
        $this->assertEquals($this->order_by_result, $Navigator->getOrderBy());
    }

    /**
     * @dataProvider dataProvider
     */
    public function testGetCriteriaShouldReturnCriteria(Rest\Interfaces\ResourceNavigator $Navigator)
    {
        $Request = $Navigator->getRequest();
        $Request->shouldReceive('getGetParameter')->once()->with('fields', [])->andReturn('id,name,date_added');
        $Request->shouldReceive('getGetParameter')->once()->with('expand', [])->andReturn('test,me');
        $Request->shouldReceive('getGetParameter')->once()->with('limit', $this->limit)->andReturn($this->limit);
        $Request->shouldReceive('getGetParameter')->once()->with('offset', $this->offset)->andReturn($this->offset);
        $Request->shouldReceive('getGetParameter')->once()->with('filters')->andReturn(null);
        $Request->shouldReceive('getGetParameter')->once()->with('order_by', [])->andReturn('id,-name');
        $Request->shouldReceive('getQueryParameter')->once()->with('collection', null)->andReturn(null);

        $CriteriaBuilder = \Mockery::mock('Everon\DataMapper\Interfaces\Criteria\Builder');
        $CriteriaBuilder->shouldReceive('setLimit')->once()->with($this->limit)->andReturn($CriteriaBuilder);
        $CriteriaBuilder->shouldReceive('setOffset')->once()->with($this->offset)->andReturn($CriteriaBuilder);
        $CriteriaBuilder->shouldReceive('setOrderBy')->once()->with($this->order_by_result)->andReturn($CriteriaBuilder);

        $Factory = $Navigator->getFactory();
        $Factory->shouldReceive('buildCriteriaBuilder')->once()->with()->andReturn($CriteriaBuilder);
        
        $CriteriaBuilder = $Navigator->toCriteria();

        $this->assertInstanceOf('Everon\Rest\Interfaces\ResourceNavigator', $Navigator);
        $this->assertInstanceOf('Everon\DataMapper\Interfaces\Criteria\Builder', $CriteriaBuilder);
    }

    public function dataProvider()
    {
        $Factory = $this->buildFactory();
        
        $Request = \Mockery::mock('Everon\Rest\Interfaces\Request');
        $Navigator = $Factory->buildRestResourceNavigator($Request);

        $FactoryMock = \Mockery::mock('Everon\Application\Interfaces\Factory');
        $Navigator->setFactory($FactoryMock);
        
        return [
            [$Navigator]
        ];
    }

}
