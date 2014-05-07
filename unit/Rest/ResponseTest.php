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

class ResponseTest extends \Everon\TestCase
{

    public function testConstructor()
    {
        $HeadersMock = $this->getMock('Everon\Http\HeaderCollection', [], [], '', false);
        $CookiesMock = $this->getMock('Everon\Http\Interfaces\CookieCollection');
        $Response = new \Everon\Rest\Response('RequestIdentifier', $HeadersMock, $CookiesMock);
        $this->assertInstanceOf('Everon\Rest\Interfaces\Response', $Response);
    }

    public function dataProvider()
    {
        return [
            []
        ];
    }

}
