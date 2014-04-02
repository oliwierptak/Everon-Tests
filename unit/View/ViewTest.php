<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Test\View;

use Everon\Helper;

class ViewTest extends \Everon\TestCase
{
    use Helper\Arrays;

    public function testConstructor()
    {
        $IndexTemplateMock = $this->getMock('Everon\Interfaces\Template', [], [], '', false);
        $View = new \Everon\Test\MyView($this->getTemplateDirectory(), [], $IndexTemplateMock, '.htm');
        $this->assertInstanceOf('Everon\View', $View);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testSetGet(\Everon\Interfaces\View $View)
    {
        $View->set('test', 'me');
        $this->assertEquals('me', $View->get('test'));
    }

    /**
     * @dataProvider dataProvider
     */
    public function testSetContainer(\Everon\Interfaces\View $View)
    {
        $View->setContainer('');
        $this->assertInstanceOf('Everon\Interfaces\TemplateContainer', $View->getContainer());

        $View->setContainer([]);
        $this->assertInstanceOf('Everon\Interfaces\TemplateContainer', $View->getContainer());
        
        $View->setContainer(new \Everon\View\Template\Container('', []));
        $this->assertInstanceOf('Everon\Interfaces\TemplateContainer', $View->getContainer());
    }

    /**
     * @dataProvider dataProvider
     * @expectedException \Everon\Exception\Template
     * @expectedExceptionMessage Invalid container type
     */
    public function testSetContainerShouldThrowExceptionWhenWrongInputIsSet(\Everon\Interfaces\View $View)
    {
        $View->setContainer(null);
        $this->assertInstanceOf('Everon\Interfaces\TemplateContainer', $View->getContainer());
    }
    
    /**
     * @dataProvider dataProvider
     */
    public function testGetContainerShouldReturnEmptyStringWhenContainerIsNull(\Everon\Interfaces\View $View)
    {
        $PropertyContainer = $this->getProtectedProperty('Everon\View', 'Container');
        $PropertyContainer->setValue($View, null);
        
        $Output = $View->getContainer();
        $this->assertEquals('', (string) $Output);
    }

    public function dataProvider()
    {
        $Factory = $this->buildFactory();
        $View = $Factory->buildView('MyView', $this->getTemplateDirectory(), [], '.htm', 'Everon\Test');
        
        return [
            [$View]
        ];
    }

}
