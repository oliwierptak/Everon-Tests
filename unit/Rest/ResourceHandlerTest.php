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

class ResourceHandlerTest extends \Everon\TestCase
{

    public function testConstructor()
    {
        $Handler = new \Everon\Rest\Resource\Handler('some_url', ['v1'], 'url', []);
        $this->assertInstanceOf('Everon\Rest\Interfaces\ResourceHandler', $Handler);
    }

    public function dataProvider()
    {
        return [
            []
        ];
    }

}
