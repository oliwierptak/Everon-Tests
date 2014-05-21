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

use Everon\Http\Interfaces\CookieCollection;

class CookieCollectionTest extends \Everon\TestCase
{
    public function testConstructor()
    {
        $CookieMock = $this->getMock('Everon\Http\Interfaces\Cookie');
        $CookieCollection = new \Everon\Http\CookieCollection([$CookieMock]);
        $this->assertInstanceOf('Everon\Http\Interfaces\CookieCollection', $CookieCollection);
    }

    /**
     * @expectedException \Everon\Http\Exception\CookieCollection
     * @expectedExceptionMessage Only Cookies allowed in Collection
     */
    public function testConstructorShouldThrowExceptionWhenWrongItemData()
    {
        $CookieCollection = new \Everon\Http\CookieCollection(['abc']);
    }
  
    public function dataProvider()
    {
        /**
         * @var \Everon\Application\Interfaces\Factory $Factory
         */
        $Factory = $this->buildFactory();
        $CookieMock = $this->getMock('Everon\Http\Interfaces\Cookie');
        $CookieCollection = $Factory->buildHttpCookieCollection([$CookieMock]);

        return [
            [$CookieCollection]
        ];
    }

}