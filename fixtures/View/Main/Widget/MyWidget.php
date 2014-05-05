<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Test\View\Main\Widget;

class MyWidget extends \Everon\View\Widget
{
    public function getData()
    {
        return ['templateParameter' => 'Template data'];
    }
}