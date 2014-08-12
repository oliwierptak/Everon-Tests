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

use Everon\Module;
use Everon\Exception;

class ManagerTest extends \Everon\TestCase
{
    
    function testConstructor()
    {
        $ModuleManager = new \Everon\Test\Module\Manager();
        $this->assertInstanceOf('Everon\Module\Interfaces\Handler', $ModuleManager);
    }
}