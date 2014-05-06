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

class WidgetTest extends \Everon\TestCase
{
    use Helper\Arrays;


    public function __construct($name = NULL, array $data=[], $dataName='')
    {
        parent::__construct($name, $data, $dataName);
        require_once($this->getFixtureDirectory().'View'.DIRECTORY_SEPARATOR.'Main'.DIRECTORY_SEPARATOR.'Widget'.DIRECTORY_SEPARATOR.'MyWidget.php');
    }

    public function testConstructor()
    {
        $ViewWidget = new \Everon\Test\View\Main\Widget\MyWidget();
        $this->assertInstanceOf('Everon\Interfaces\ViewWidget', $ViewWidget);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testRender(\Everon\Interfaces\ViewWidget $Widget)
    {
        $result = $Widget->render();
        $this->assertEquals('Template data',$result);
    }

    public function dataProvider()
    {
        $fixture_directory = $this->getFixtureDirectory().'View'.DIRECTORY_SEPARATOR;
        $Factory = $this->buildFactory();
        $Container = $Factory->getDependencyContainer();
        $ViewManager = $Factory->buildViewManager(['php' => '.php'], $fixture_directory, $this->getViewCacheDirectory());
        $Container->register('ViewManager', function() use ($ViewManager) {
            return $ViewManager;
        });

        /**
         * @var \Everon\Interfaces\ViewWidget $Widget
         */
        $Widget = $ViewManager->createWidget('MyWidget', 'Everon\Test\View');
        return [
            [$Widget]
        ];
    }

}
