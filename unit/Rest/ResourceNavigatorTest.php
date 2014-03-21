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

    public function testState()
    {
        $RequestMock = $this->getMock('Everon\Rest\Interfaces\Request');
        $RequestMock->expects($this->at(0))
            ->method('getQueryParameter')
            ->with('fields')
            ->will($this->returnValue('id,name,date_added'));
        $RequestMock->expects($this->at(1))
            ->method('getQueryParameter')
            ->with('expand')
            ->will($this->returnValue('test,me'));
        $RequestMock->expects($this->at(2))
            ->method('getQueryParameter')
            ->with('order_by')
            ->will($this->returnValue('id,-name'));
        $RequestMock->expects($this->at(3))
            ->method('getQueryParameter')
            ->with('limit')
            ->will($this->returnValue(null));
        $RequestMock->expects($this->at(4))
            ->method('getQueryParameter')
            ->with('offset')
            ->will($this->returnValue(null));
        $RequestMock->expects($this->at(5))
            ->method('getQueryParameter')
            ->with('collection')
            ->will($this->returnValue(null));
        
        $Navigator = new \Everon\Rest\Resource\Navigator($RequestMock);
        
        $this->assertInstanceOf('Everon\Rest\Interfaces\ResourceNavigator', $Navigator);
        $this->assertEquals(['test', 'me'], $Navigator->getExpand());
        $this->assertEquals(['id','name', 'date_added'], $Navigator->getFields());
        $this->assertEquals(['id','name'], $Navigator->getOrderBy());
        $this->assertEquals(['id'=>'ASC', 'name'=>'DESC'], $Navigator->getSort());
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
