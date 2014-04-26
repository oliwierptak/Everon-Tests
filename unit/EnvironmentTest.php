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

class EnvironmentTest extends \Everon\TestCase
{
   
    public function testConstructor()
    {
        $Environment = new \Everon\Environment('app_root', 'src_root');
        $this->assertInstanceOf('Everon\Interfaces\Environment', $Environment);
    }

    public function testGetters()
    {
        $root = $this->FrameworkBootstrap->getEnvironment()->getRoot();
        $root_source = $this->FrameworkBootstrap->getEnvironment()->getEveronRoot();
        $Environment = new \Everon\Environment($root, $root_source);

        $this->assertEquals($root, $Environment->getRoot());

        $this->assertEquals($root.'Config'.DIRECTORY_SEPARATOR, $Environment->getConfig());
        $this->assertEquals($root.'DataMapper'.DIRECTORY_SEPARATOR, $Environment->getDataMapper());
        $this->assertEquals($root.'Domain'.DIRECTORY_SEPARATOR, $Environment->getDomain());
        $this->assertEquals($root.'View'.DIRECTORY_SEPARATOR, $Environment->getView());
        $this->assertEquals($root.'Controller'.DIRECTORY_SEPARATOR, $Environment->getController());

        $this->assertEquals($root.'Tests'.DIRECTORY_SEPARATOR, $Environment->getTest());

        $this->assertEquals($root.'Tmp'.DIRECTORY_SEPARATOR, $Environment->getTmp());
        $this->assertEquals($root.'Tmp'.DIRECTORY_SEPARATOR.'logs'.DIRECTORY_SEPARATOR, $Environment->getLog());
        $this->assertEquals($root.'Tmp'.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR, $Environment->getCache());
        $this->assertEquals($root.'Tmp'.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR, $Environment->getCacheConfig());
        $this->assertEquals($root.'Tmp'.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'view'.DIRECTORY_SEPARATOR, $Environment->getCacheView());
    }
    
    public function testSetters()
    {
        $Environment = new \Everon\Environment('app_root', 'src_root');
        
        $Environment->setRoot('test');
        $this->assertEquals('test', $Environment->getRoot());
        
        $Environment->setConfig('test');
        $this->assertEquals('test', $Environment->getConfig());
        
        $Environment->setDomain('test');
        $this->assertEquals('test', $Environment->getDomain());
        
        $Environment->setView('test');
        $this->assertEquals('test', $Environment->getView());
        
        $Environment->setController('test');
        $this->assertEquals('test', $Environment->getController());

        $Environment->setTest('test');
        $this->assertEquals('test', $Environment->getTest());

        $Environment->setEveronInterface('test');
        $this->assertEquals('test', $Environment->getEveronInterface());
        
        $Environment->setEveronLib('test');
        $this->assertEquals('test', $Environment->getEveronConfig());

        $Environment->setTmp('test');
        $this->assertEquals('test', $Environment->getTmp());
        
        $Environment->setLog('test');
        $this->assertEquals('test', $Environment->getLog());
        
        $Environment->setCache('test');
        $this->assertEquals('test', $Environment->getCache());
        
        $Environment->setCacheConfig('test');
        $this->assertEquals('test', $Environment->getCacheConfig());
    }

}