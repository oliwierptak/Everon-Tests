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

class RequestTest extends \Everon\TestCase
{

    public function testConstructor()
    {
        $Request = new \Everon\Rest\Request([
            'SERVER_PROTOCOL'=> 'HTTP/1.1',
            'REQUEST_METHOD'=> 'GET',
            'REQUEST_URI'=> '/v1/books',
            'QUERY_STRING'=> '?foo=bar',
            'SERVER_NAME'=> 'everon.nova',
            'SERVER_PORT'=> 80,
            'SERVER_ADDR'=> '127.0.0.1',
            'REMOTE_ADDR'=> '127.0.0.1',
            'HTTPS'=> 'off',
        ],[
            'foo' => 'bar'
        ],[],[], 'url');

        $this->assertEquals('v1', $Request->getVersion());
        $this->assertInstanceOf('\Everon\Rest\Interfaces\Request', $Request);
        $this->assertInternalType('array', $Request->getGetCollection()->toArray());
        $this->assertInternalType('array', $Request->getPostCollection()->toArray());
        $this->assertInternalType('array', $Request->getFileCollection()->toArray());
    }

    public function dataProvider()
    {

    }

}
