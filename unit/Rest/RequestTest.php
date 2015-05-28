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

class RequestTest extends \Everon\TestCase
{
    /**
     * @var array
     */
    protected $server = [
        'SERVER_PROTOCOL'=> 'HTTP/1.1',
        'REQUEST_METHOD'=> 'GET',
        'REQUEST_URI'=> '/v1/foobars',
        'QUERY_STRING'=> '?foo=bar',
        'SERVER_NAME'=> 'api.localhost',
        'SERVER_PORT'=> 80,
        'SERVER_ADDR'=> '127.0.0.1',
        'REMOTE_ADDR'=> '127.0.0.1',
        'HTTPS'=> 'off',
    ];

    /**
     * @var array
     */
    protected $get = [
        'foo' => 'bar'
    ];

    /**
     * @var array
     */
    protected $post = [
        'id' => null,
        'email' => 'test@grofas.com',
        'password' => 'foobar'
    ];

    /**
     * @var array
     */
    protected $files = [];

    /**
     * @var string
     */
    protected $versioning = Rest\Resource\Handler::VERSIONING_URL;

    /**
     * @var string
     */
    protected $version = 'v1';
    
    protected $url = 'http://api.localhost/v1/foobars';
    

    public function testConstructor()
    {
        $Request = new Rest\Request($this->server, $this->get, $this->post, $this->files, $this->versioning); 

        $this->assertInstanceOf('\Everon\Rest\Interfaces\Request', $Request);
        $this->assertInternalType('array', $Request->getGetCollection()->toArray());
        $this->assertInternalType('array', $Request->getPostCollection()->toArray());
        $this->assertInternalType('array', $Request->getQueryCollection()->toArray());
        $this->assertInternalType('array', $Request->getFileCollection()->toArray());
    }

    public function testGetVersion()
    {
        $Request = new Rest\Request($this->server, $this->get, $this->post, $this->files, $this->versioning);
        $this->assertEquals($this->version, $Request->getVersion());
        
        $this->server['REQUEST_URI'] = '/v2/foobars';
        $Request = new Rest\Request($this->server, $this->get, $this->post, $this->files, $this->versioning);
        $this->assertEquals('v2', $Request->getVersion());
    }

    /**
     * @dataProvider dataProvider
     */
    public function testGetFullPath(Rest\Interfaces\Request $Request)
    {
        $path = $Request->getFullPath();
        
        $this->assertEquals('v1/foobars', $path);
    }

    public function dataProvider()
    {
        $Factory = $this->buildFactory();
        
        $Request = $Factory->buildRestRequest($this->server, $this->get, $this->post, $this->files, $this->versioning);

        return [
            [$Request]
        ];
    }

}
