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
    public function testMakeRequest(Client $Client)
    {
        $json = json_decode('{"data": {
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
        }}', true);
        
        $CurlAdapterMock = $Client->getCurlAdapter();
        $CurlAdapterMock->expects($this->once())
            ->method('get')
            ->will($this->returnValue($json));

        $Factory = $this->buildFactory();
        $CurlAdapterMock = $Factory->buildRestCurlAdapter();
        $Client->setCurlAdapter($CurlAdapterMock);
        $result = $Client->get('users', 1);

        $this->assertEquals($json, $result);
        
        $User = $Client->getDomainManager()->buildEntityFromArray('User', $result['data']);
        $Resource = $Client->getResourceManager()->buildResourceFromEntity($User, 'users', 'v1');
        $Resource->getDomainEntity()->setEmail('foobar');

        $data = $Resource->toArray();
        $Client->put('users', 1, $data);
        $result = $Client->get('users', 1);
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

        $Container->register('ResourceManager', function() use ($Factory) {
            $Factory->getDependencyContainer()->monitor('ResourceManager', ['Everon\Config\Manager']);
            $ConfigManager = $Factory->getDependencyContainer()->resolve('ConfigManager');

            $rest = $ConfigManager->getConfigValue('rest.rest');
            $versioning = $ConfigManager->getConfigValue('rest.versioning');
            $mapping = $ConfigManager->getConfigValue('rest.mapping', []);
            $rest_server_url = $rest['protocol'].$rest['host'].':'.$rest['port'].$rest['url'];
            return $Factory->buildRestResourceManager($rest_server_url, $versioning['supported_versions'], $versioning['type'], $mapping);
        });
        
        $Href = new \Everon\Rest\Resource\Href('http://api.nova/', 'v1', 'url');
        $CurlAdapter = $this->getMock('Everon\Rest\Interfaces\CurlAdapter');
        
        $RestClient = $Factory->buildRestClient($Href, $CurlAdapter);

        return [
            [$RestClient]
        ]; 
    }

}
