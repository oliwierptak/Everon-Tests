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

use Everon\Http\Interfaces\Cookie;

class CookieTest extends \Everon\TestCase
{
    public function testConstructor()
    {
        $Cookie = new \Everon\Http\Cookie('testCookie', 'abc', '+15 minutes');
        $this->assertInstanceOf('Everon\Http\Interfaces\Cookie', $Cookie);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testCookieHasExpired(Cookie $Cookie)
    {
        $Cookie->setExpireDateFromString('-15 hours');
        $this->assertTrue($Cookie->isExpired());

        $Cookie->setExpireDateFromString('+15 seconds');
        $this->assertFalse($Cookie->isExpired());
    }

    /**
     * @dataProvider dataProvider
     */
    public function testNeverExpire(Cookie $Cookie)
    {
        $Cookie->delete();
        $this->assertTrue($Cookie->isExpired());

        $Cookie->neverExpire();
        $this->assertFalse($Cookie->isExpired());
    }
    
    /**
     * @dataProvider dataProvider
     */
    public function testGetValue(Cookie $Cookie)
    {
        $value = $Cookie->getValue();
        $this->assertEquals('abc', $value);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testGetExpire(Cookie $Cookie)
    {
        $expires = $Cookie->getExpire();
        $this->assertNotNull($expires);
    }
  
    public function dataProvider()
    {
        /**
         * @var \Everon\Application\Interfaces\Factory $Factory
         */
        $Factory = $this->buildFactory();
        $Cookie = $Factory->buildHttpCookie('testCookie', 'abc', '+15 minutes');

        return [
            [$Cookie]
        ];
    }

}