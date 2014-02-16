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

class RequestTest extends \Everon\TestCase
{

    public function testConstructor()
    {
        $Request = new \Everon\Rest\Request();
        $this->assertInstanceOf('Everon\Rest\Interfaces\Request', $Request);
    }

    public function dataProvider()
    {
        $filename = $this->getConfigDirectory().'test.ini';
        $ConfigLoaderItem = new \Everon\Config\Loader\Item($filename, parse_ini_file($filename, true));
        $Compiler = function(&$item) {};        
        $Config = new \Everon\Config('test', $ConfigLoaderItem, $Compiler);
        $Config->setFactory($this->buildFactory());
        
        return [
            [$Config]
        ];
    }

}
