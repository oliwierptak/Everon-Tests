<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Test\Http;

class ResponseTest extends \Everon\TestCase
{
    public function testConstructor()
    {
        $HeadersMock = $this->getMock('Everon\Http\Interfaces\HeaderCollection', [], [], '', false);
        $Response = new \Everon\Http\Response('Guid', $HeadersMock);
        $this->assertInstanceOf('Everon\Http\Interfaces\Response', $Response);
    }

    /**
     * @runInSeparateProcess
     */
    public function testToJson()
    {
        $H = new \Everon\Http\HeaderCollection([]); //phpunit complains about mock file not found
        $Response = new \Everon\Http\Response('Guid', $H);
        
        $Response->setData(['test'=>'yes']);
        $json = $Response->toJson();
        $headers = xdebug_get_headers();
        
        $this->assertEquals($headers[0], 'EVRID:Guid');
        $this->assertEquals($headers[1], 'content-type: application/json');
        $this->assertInternalType('string', $json);
        $this->assertEquals('{"data":{"test":"yes"}}', $json);
        $this->assertEquals('application/json', $Response->getContentType());
    }
    
    /**
     * @runInSeparateProcess
     */
    public function testToText()
    {
        $H = new \Everon\Http\HeaderCollection([]); //phpunit complains about mock file not found
        $Response = new \Everon\Http\Response('Guid', $H);
        
        $Response->setData('test');
        $text = $Response->toText();
        $headers = xdebug_get_headers();
        
        $this->assertEquals($headers[0], 'EVRID:Guid');
        $this->assertEquals($headers[1], 'content-type: text/plain; charset="utf-8"');
        $this->assertInternalType('string', $text);
        $this->assertEquals('test', $text);
        $this->assertEquals('text/plain', $Response->getContentType());
    }
    
    public function dataProviderSSS()
    {
        /**
         * @var \Everon\Interfaces\Factory $Factory
         */
        $HeadersMock = $this->getMock('\Everon\Http\Interfaces\HeaderCollection', [], [], '', false);
        $Factory = $this->buildFactory();
        $Response = $Factory->buildHttpResponse('guid', $HeadersMock);

        return [
            [$Response]
        ];
    }

}