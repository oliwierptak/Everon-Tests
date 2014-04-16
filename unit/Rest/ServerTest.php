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

class ServerTest extends \Everon\TestCase
{

    public function testConstructor()
    {
        $Server = new \Everon\Rest\Server();
        $this->assertInstanceOf('Everon\Rest\Interfaces\Server', $Server);
    }

    public function dataProvider()
    {
        return [
            []
        ];
    }

}
