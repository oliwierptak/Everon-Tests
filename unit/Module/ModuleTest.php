<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Test\Module;

use Everon\Interfaces;
use Everon\Exception;

class ModuleTest extends \Everon\TestCase
{
    
    public function testConstructor()
    {
        $ConfigMock = $this->getMock('Everon\Interfaces\Config');
        $Module = new \Everon\Module\Foo\Module('Foo', 'directory', $ConfigMock, $ConfigMock);
        $this->assertInstanceOf('Everon\Module\Interfaces\Module', $Module);
    }
    
    public function dataProvider()
    {
        $Factory = $this->buildFactory();

        return [
            [$Factory]
        ];
    }

}