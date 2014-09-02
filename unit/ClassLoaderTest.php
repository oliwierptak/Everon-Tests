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

class ClassLoaderTest extends \Everon\TestCase
{
    public function setUp()
    {
        $Loader = new \Everon\ClassLoader(true);
        $Loader->unRegister();
    }
    
    public function testConstructor()
    {
        $ClassMap = new \Everon\ClassMap('');
        $Loader = new \Everon\ClassLoader(true, $ClassMap);
        $this->assertInstanceOf('Everon\Interfaces\ClassLoader', $Loader);
    }

    /**
     * @dataProvider dataProvider
     */    
    public function testLoadShouldIncludeFile(\Everon\Interfaces\ClassLoader $Loader, \Everon\Interfaces\Environment $Environment)
    {
        $Loader->add('Everon', $Environment->getEveronRoot());
        $Loader->load('Everon\Core');
    }

    /**
     * @dataProvider dataProvider
     * @expectedException \RuntimeException
     * @expectedExceptionMessage File for class: "test_wrong_class" could not be found
     */
    public function testLoadShouldThrowExceptionWhenFileWasNotFound(\Everon\Interfaces\ClassLoader $Loader, \Everon\Interfaces\Environment $Environment)
    {
        $Loader->load('test_wrong_class');
    }

    public function dataProvider()
    {
        $Loader = new \Everon\ClassLoader(true);
        $Environment = $this->FrameworkBootstrap->getEnvironment();
        
        return [
            [$Loader, $Environment]
        ];
    }

}

