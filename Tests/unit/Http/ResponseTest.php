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
        $Response = new \Everon\Http\Response('RequestIdentifier', $HeadersMock);
        $this->assertInstanceOf('Everon\Http\Interfaces\Response', $Response);
    }

    /**
     * @dataProvider dataProvider
     * @runInSeparateProcess
     */
    public function testToHtml(\Everon\Http\Interfaces\Response $Response)
    {
        $Response->setData('<b>test</b>');
        $html = $Response->toHtml();
        $headers = xdebug_get_headers();
        
        $this->assertEquals($headers[0], 'EVRID:RequestIdentifier');
        $this->assertEquals($headers[1], 'content-type: text/html; charset="utf-8"');
        $this->assertInternalType('string', $html);
        $this->assertEquals('<b>test</b>', $html);
        $this->assertEquals('text/html', $Response->getContentType());
    }
    
    /**
     * @dataProvider dataProvider
     * @runInSeparateProcess
     */
    public function testToJson(\Everon\Http\Interfaces\Response $Response)
    {
        $Response->setData(['test'=>'yes']);
        $json = $Response->toJson();
        $headers = xdebug_get_headers();
        
        $this->assertEquals($headers[0], 'EVRID:RequestIdentifier');
        $this->assertEquals($headers[1], 'content-type: application/json');
        $this->assertInternalType('string', $json);
        $this->assertEquals('{"data":{"test":"yes"}}', $json);
        $this->assertEquals('application/json', $Response->getContentType());
    }

    /**
     * @dataProvider dataProvider
     * @runInSeparateProcess
     */
    public function testToText(\Everon\Http\Interfaces\Response $Response)
    {
        $Response->setData('test');
        $text = $Response->toText();
        $headers = xdebug_get_headers();
        
        $this->assertEquals($headers[0], 'EVRID:RequestIdentifier');
        $this->assertEquals($headers[1], 'content-type: text/plain; charset="utf-8"');
        $this->assertInternalType('string', $text);
        $this->assertEquals('test', $text);
        $this->assertEquals('text/plain', $Response->getContentType());
    }
    
    public function dataProvider()
    {
        /**
         * @var \Everon\Interfaces\Factory $Factory
         */
        $Factory = $this->buildFactory();
        $Headers = $Factory->buildHttpHeaderCollection([]); //cant use mock, phpunit complains about file not found
        $Response = $Factory->buildHttpResponse('RequestIdentifier', $Headers);

        return [
            [$Response]
        ];
    }

}