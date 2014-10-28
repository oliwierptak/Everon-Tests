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
        $View = new \Everon\Test\MyView($this->getTemplateDirectory());
        $this->assertInstanceOf('Everon\View\Interfaces\View', $View);
    }


    public function dataProvider()
    {
        $Factory = $this->buildFactory();
        $Container = $Factory->getDependencyContainer();

        $template_directory = $this->getFixtureDirectory().'templates'.DIRECTORY_SEPARATOR.'Main'.DIRECTORY_SEPARATOR;
        $ViewManager = $Factory->buildViewManager(['php' => '.php'], $template_directory, $this->getViewCacheDirectory());
        $Container->register('ViewManager', function() use ($ViewManager) {
            return $ViewManager;
        });

        $View = $Factory->buildView('MyView', $this->getTemplateDirectory(), '.htm', 'Everon\Test');
        
        return [
            [$View]
        ];
    }

}
