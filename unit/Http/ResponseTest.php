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
    public function AAtestAddCookie(\Everon\Http\Interfaces\Response $Response)
    {
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

    /**
     * @dataProvider dataProvider
     * @runInSeparateProcess
     */
    public function testDeleteCookie(\Everon\Http\Interfaces\Response $Response)
    {
        $Cookie = new \Everon\Http\Cookie('test', 'test it', '+1 year');

        $Response->addCookie($Cookie);
        $this->assertInstanceOf('Everon\Http\Interfaces\Cookie', $Response->getCookie($Cookie->getName()));
        
        $Response->deleteCookie($Cookie);

        $Response->setData('test');
        $text = $Response->toText();

        $headers = xdebug_get_headers();

        $D = new \DateTime('@'.$Cookie->getExpire());
        $date = $D->format('D, d-M-Y H:i:s').' GMT';
        $value = urlencode($Cookie->getJsonValue());
        
        $this->assertEquals('Set-Cookie: test='.$value.'; expires='.$date.'; Max-Age=-31536000; path=/; httponly', $headers[0]);
        $this->assertEquals('content-type: text/plain; charset="utf-8"', $headers[1]);
        $this->assertEquals('EVRID: RequestIdentifier', $headers[2]);
        $this->assertInternalType('string', $text);
        $this->assertEquals('test', $text);
        $this->assertEquals('text/plain', $Response->getContentType());
    }

    /**
     * @dataProvider dataProvider
     * @runInSeparateProcess
     */
    public function testGetCookie(\Everon\Http\Interfaces\Response $Response)
    {
        $Factory = $this->buildFactory();
        $CookieCollection = $Factory->buildHttpCookieCollection(['test_me' => 'tests it']);
        $Response->setCookieCollection($CookieCollection);
        
        $Cookie = $Response->getCookie('test_me');
        $this->assertInstanceOf('Everon\Http\Interfaces\Cookie', $Cookie);

        $Response->setData('test');
        $text = $Response->toText();

        $headers = xdebug_get_headers();

        $value = urlencode($Cookie->getJsonValue());

        $this->assertEquals('Set-Cookie: test_me='.$value.'; path=/; httponly', $headers[0]);
        $this->assertEquals('content-type: text/plain; charset="utf-8"', $headers[1]);
        $this->assertEquals('EVRID: RequestIdentifier', $headers[2]);
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

        $Cookie = new \Everon\Http\Cookie('test_me', 'test it', 0);
        $FactoryMock = $this->getMock('Everon\Application\Interfaces\Factory', [], [], '', false);
        $FactoryMock->expects($this->once())
            ->method('buildHttpCookie')
            ->will($this->returnValue($Cookie));
            
        $Response->setFactory($FactoryMock);

        return [
            [$Response]
        ];
    }

}