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

use Everon\Http\Interfaces\Session;

class SessionTest extends \Everon\TestCase
{
    public function testConstructor()
    {
        $Session = new \Everon\Http\Session('RequestIdentifier');
        $this->assertInstanceOf('Everon\Http\Interfaces\Session', $Session);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testSettersAndGetters(Session $Session)
    {
        $Session->set('this_is_a_test', 123);
        
        $this->assertEquals('guid', $Session->getGuid());
        $this->assertEquals(123, $Session->get('this_is_a_test'));
    }
  
    public function dataProvider()
    {
        /**
         * @var \Everon\Interfaces\Factory $Factory
         */
        $Factory = $this->buildFactory();
        $Session = $Factory->buildHttpSession('guid');

        return [
            [$Session]
        ];
    }

}