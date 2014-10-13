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
use Everon\Config;

class RequestValidatorTest extends \Everon\TestCase
{

    public function testConstructor()
    {
        $Validator = new \Everon\RequestValidator();
        $this->assertInstanceOf('\Everon\Interfaces\RequestValidator', $Validator);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testValidate(Interfaces\RequestValidator $Validator, Config\Interfaces\ItemRouter $RouteItem, Interfaces\Request $Request)
    {
        $result = $Validator->validate($RouteItem, $Request);
        $this->assertInternalType('array', $result);
    }
    
    /**
     * @dataProvider dataProvider
     */
    public function testValidateShouldThrowExceptionWhenError(Interfaces\RequestValidator $Validator, Config\Interfaces\ItemRouter $RouteItem, Interfaces\Request $Request)
    {
        $PostCollection = $Request->getPostCollection();
        $PostCollection->set('password', '');
        $Request->setPostCollection($PostCollection->toArray());
        
        $result = $Validator->validate($RouteItem, $Request);
        $errors = $Validator->getErrors();
        
        $this->assertInternalType('array', $result);
        $this->assertEquals([
            [],
            [],
            [
                'token' => 3,
                'username' => 'test'
            ]
        ], $result);
        
        $this->assertEquals([
            'password' => 'Invalid parameter: "password" for route: "test_complex"'
        ], $errors);
    }
    
    /**
     * @dataProvider dataProvider
     * @expectedException \Everon\Exception\RequestValidator
     */
    public function testValidateQueryShouldThrowExceptionWhenError(Interfaces\RequestValidator $Validator, Config\Interfaces\ItemRouter $RouteItem, Interfaces\Request $Request)
    {
        $RouteItemMock = $this->getMockBuilder('Everon\Config\Item\Router')
            ->disableOriginalConstructor()
            ->getMock();
        
        $RouteItemMock->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('anitem'));
        
        $RouteItemMock->expects($this->once())
            ->method('getCleanUrl')
            ->will($this->throwException(new \Exception('exception')));
        
        $result = $Validator->validate($RouteItemMock, $Request);
        $this->assertInternalType('array', $result);
    }
    
    /**
     * @dataProvider dataProvider
     * @expectedException \Everon\Exception\RequestValidator
     */
    public function testValidatePostShouldThrowExceptionWhenError(Interfaces\RequestValidator $Validator, Config\Interfaces\ItemRouter $RouteItem, Interfaces\Request $Request)
    {
        $RouteItemMock = $this->getMock('Everon\Config\Item\Router', [], [], '', false);
        $RouteItemMock->expects($this->once())
            ->method('filterQueryKeys')
            ->will($this->returnValue([]));
        
        $RouteItemMock->expects($this->any())
            ->method('getPostRegex')
            ->will($this->throwException(new \Exception('getPostRegex')));
        
        $result = $Validator->validate($RouteItemMock, $Request);
        $this->assertInternalType('array', $result);
    }
    
    public function dataProvider()
    {
        $Factory = $this->buildFactory();

        $server_data = $this->getServerDataForRequest([
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI' => '/login/submit/session/adf24ds34/redirect/account%5Csummary?and=something&else=2457',
            'QUERY_STRING' => 'and=something&else=2457',
        ]);
        $Request = $Factory->buildHttpRequest(
            $server_data, [
                'and'=>'something',
                'else'=>2457
            ],[
                'token' => 3,
                'username' => 'test',
                'password' => 'aaa'
            ],
            []
        );

        $RouteItem = $Factory->buildConfigItem('test_complex', [
            \Everon\Config\Item::PROPERTY_NAME => 'test',
            \Everon\Config\Item\Router::PROPERTY_MODULE => 'test',
            'url' => '/',
            'controller' => 'Test',
            'action' => 'testMe',
            'get' => [],
            'post' => [
                'password' => '[[:alnum:]]{3,22}'
            ],
            \Everon\Config\Item::PROPERTY_DEFAULT => true,
        ], 'Everon\Config\Item\Router');
        $Validator = new \Everon\RequestValidator();
        
        return [
            [$Validator, $RouteItem, $Request]
        ];
    }

}
