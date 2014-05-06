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
        $CookiesMock = $this->getMock('Everon\Http\Interfaces\CookieCollection', [], [], '', false);
        $Response = new \Everon\Http\Response('RequestIdentifier', $HeadersMock, $CookiesMock);
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
        
        $this->assertEquals($headers[0], 'content-type: text/html; charset="utf-8"');
        $this->assertEquals($headers[1], 'EVRID: RequestIdentifier');
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

        $this->assertEquals($headers[0], 'content-type: application/json');
        $this->assertEquals($headers[1], 'EVRID: RequestIdentifier');
        $this->assertInternalType('string', $json);
        $this->assertEquals('{"test":"yes"}', $json);
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

        $this->assertEquals($headers[0], 'content-type: text/plain; charset="utf-8"');
        $this->assertEquals($headers[1], 'EVRID: RequestIdentifier');
        $this->assertInternalType('string', $text);
        $this->assertEquals('test', $text);
        $this->assertEquals('text/plain', $Response->getContentType());
    }

    /**
     * @dataProvider dataProvider
     * @runInSeparateProcess
     */
    public function testAddCookie(\Everon\Http\Interfaces\Response $Response)
    {
        /*$CookieMock = $this->getMock('Everon\Http\Interfaces\Cookie', [], [], '', false);
        $CookieMock->expects($this->once())
            ->method('getName')
            ->will($this->returnValue('test'));
        $CookieMock->expects($this->once())
            ->method('getValue')
            ->will($this->returnValue('test it'));
        $CookieMock->expects($this->once())
            ->method('getPath')
            ->will($this->returnValue('/'));
        $CookieMock->expects($this->once())
            ->method('getDomain')
            ->will($this->returnValue('www.example.com'));
        $CookieMock->expects($this->once())
            ->method('isSecure')
            ->will($this->returnValue(false));
        $CookieMock->expects($this->once())
            ->method('isHttpOnly')
            ->will($this->returnValue(false));*/
        
        //$Cookie = new \Everon\Http\Cookie('test', 'test it', date('2012-12-01 12:00:00', time()));
        $Cookie = new \Everon\Http\Cookie('test', 'test it', 0);
        
        $Response->addCookie($Cookie);

        $Response->setData('test');
        $text = $Response->toText();
        
        $headers = xdebug_get_headers();

        $this->assertEquals($headers[0], 'Set-Cookie: test=test+it; path=/; httponly');
        $this->assertEquals($headers[1], 'content-type: text/plain; charset="utf-8"');
        $this->assertEquals($headers[2], 'EVRID: RequestIdentifier');
        $this->assertInternalType('string', $text);
        $this->assertEquals('test', $text);
        $this->assertEquals('text/plain', $Response->getContentType());
    }
    
    public function dataProvider()
    {
        /**
         * @var \Everon\Application\Interfaces\Factory $Factory
         */
        $Factory = $this->buildFactory();
        $Headers = $Factory->buildHttpHeaderCollection([]); //cant use mock, phpunit complains about file not found
        $Cookies = $Factory->buildHttpCookieCollection([]);
        $Response = $Factory->buildHttpResponse('RequestIdentifier', $Headers, $Cookies);

        return [
            [$Response]
        ];
    }

}