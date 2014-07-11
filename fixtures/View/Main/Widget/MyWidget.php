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

class MyAbstractWidget extends \Everon\View\AbstractWidget
{
    protected function populate()
    {
        $this->data = ['templateParameter' => 'Template data'];
    }
}