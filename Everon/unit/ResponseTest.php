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

class ResponseTest extends \Everon\TestCase
{
    
    public function testConstructor()
    {
        $HeadersMock = $this->getMock('Everon\Http\HeaderCollection', [], [], '', false);
        $Response = new \Everon\Response('Guid', $HeadersMock);
        $this->assertInstanceOf('\Everon\Interfaces\Response', $Response);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testToJson(\Everon\Interfaces\Response $Response)
    {
        $Response->setData(['test'=>'yes']);
        $json = $Response->toJson();
        
        $this->assertInternalType('string', $json);
        $this->assertEquals('{"data":{"test":"yes"}}', $json);
        $this->assertEquals('application/json', $Response->getContentType());
    }

    /**
     * @dataProvider dataProvider
     */
    public function testToText(\Everon\Interfaces\Response $Response)
    {
        $Response->setData('this is a test');
        $text = $Response->toText();

        $this->assertInternalType('string', $text);
        $this->assertEquals('this is a test', $text);
        $this->assertEquals('text/plain', $Response->getContentType());
    }
    
    public function dataProvider()
    {
        /**
         * @var \Everon\Interfaces\Factory $Factory
         */
        $HeadersMock = $this->getMock('Everon\Http\HeaderCollection', [], [], '', false);
        $Factory = $this->buildFactory();
        $Response = $Factory->buildResponse('guid', $HeadersMock);
        
        return [
            [$Response]
        ];
    }
}