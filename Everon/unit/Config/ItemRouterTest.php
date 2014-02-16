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

class ItemRouterTest extends \Everon\TestCase
{

    public function testConstructor()
    {
        $data = [
            \Everon\Config\Item::PROPERTY_NAME => 'test',
            \Everon\Config\Item\Router::PROPERTY_MODULE => 'test',
            'url' => '/',
            'controller' => 'Test',
            'action' => 'testMe',
            'get' => [],
            'post' => [],
            \Everon\Config\Item::PROPERTY_DEFAULT => true,
        ];
        
        $Item = new \Everon\Config\Item\Router($data);
        
        $this->assertInstanceOf('Everon\Config\Interfaces\ItemRouter', $Item);
        $this->assertEquals($data['controller'], $Item->getController());
        $this->assertEquals($data['action'], $Item->getAction());
        $this->assertEquals($data['url'], $Item->getUrl());
        $this->assertEquals($data[\Everon\Config\Item::PROPERTY_NAME], $Item->getName());
        $this->assertEquals($data[\Everon\Config\Item::PROPERTY_DEFAULT], $Item->isDefault());
        $this->assertEquals($data[\Everon\Config\Item\Router::PROPERTY_MODULE], $Item->getModule());
    }

}