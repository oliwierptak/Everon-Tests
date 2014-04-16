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

class ApiKeyTest extends \Everon\TestCase
{

    public function testConstructor()
    {
        $ApiKey = new \Everon\Rest\ApiKey('id', 'secret');
        $this->assertInstanceOf('Everon\Rest\Interfaces\ApiKey', $ApiKey);
    }

    public function dataProvider()
    {
        return [
            []
        ];
    }

}
