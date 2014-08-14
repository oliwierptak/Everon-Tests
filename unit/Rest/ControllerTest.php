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

class ControllerTest extends \Everon\TestCase
{

    public function testConstructor()
    {
        $ModuleMock = $this->getMock('Everon\Module\Interfaces\Module');
        $Controller = new \Everon\Test\Rest\Controller($ModuleMock);
        $this->assertInstanceOf('Everon\Rest\Interfaces\Controller', $Controller);
    }

    public function dataProvider()
    {
        return [
            []
        ];
    }

}
