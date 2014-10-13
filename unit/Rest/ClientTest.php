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

use Everon\Rest\Interfaces\Client;

class ClientTest extends \Everon\TestCase
{
    
    public function testConstructor()
    {
        $Href = new \Everon\Rest\Resource\Href('http://api.nova/', 'v1', 'url');
        $CurlAdapter = $this->getMock('Everon\Rest\Interfaces\CurlAdapter');
        $RestClient = new \Everon\Rest\Client($Href, $CurlAdapter);
        $this->assertInstanceOf('Everon\Rest\Interfaces\Client', $RestClient);
        $this->assertInstanceOf('Everon\Rest\Interfaces\CurlAdapter', $CurlAdapter);
    }    

    /**
     * @dataProvider dataProvider
     */
    public function testGetRequest(Client $Client)
    {
        $json = '{"data": {
            "href": "http:\/\/api.nova:80\/v1\/users\/1",
            "id": 1,
            "email": "test@test.com",
            "password": "easy",
            "userPermissions": {
                "href": "http:\/\/api.nova:80\/v1\/users\/1\/userPermissions"
            },
            "userGroups": {
                "href": "http:\/\/api.nova:80\/v1\/users\/1\/userGroups"
            },
            "userRoles": {
                "href": "http:\/\/api.nova:80\/v1\/users\/1\/userRoles"
            }
        }}';
        $expected = json_decode($json, true);
        $CurlAdapterMock = $Client->getCurlAdapter();
        $CurlAdapterMock->expects($this->once())
            ->method('get')
            ->will($this->returnValue($json));

        $result = $Client->get('users', 1);
        
        $this->assertEquals($expected, $result);
    }


    /**
     * @dataProvider dataProvider
     */
    public function testGetUrl(Client $Client)
    {
        $url = $Client->getUrl('users');
        $this->assertEquals('http://api.nova/v1/users', $url);
        
        $url = $Client->getUrl('users', '1');
        $this->assertEquals('http://api.nova/v1/users/1', $url);
        
        $url = $Client->getUrl('users', '1','userPermissions');
        $this->assertEquals('http://api.nova/v1/users/1/userPermissions', $url);
    }
    
    public function dataProvider()
    {
        $Factory = $this->buildFactory();
        $Container = $Factory->getDependencyContainer();

        $ResourceManager = \Mockery::mock('Everon\Rest\Interfaces\ResourceManager');

        $Container->register('ResourceManager', function() use ($ResourceManager) {
            return $ResourceManager;
        });
        
        $Href = new \Everon\Rest\Resource\Href('http://api.nova/', 'v1', 'url');
        $CurlAdapter = $this->getMock('Everon\Rest\Interfaces\CurlAdapter');
        
        $RestClient = $Factory->buildRestClient($Href, $CurlAdapter);

        return [
            [$RestClient]
        ]; 
    }

}
