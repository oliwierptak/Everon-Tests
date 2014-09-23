<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Test\DataMapper;

use Everon\DataMapper\Criteria;
use Everon\Interfaces;
use Everon\Helper;

class CriteriaTest extends \Everon\TestCase
{
    function testConstructor()
    {
        $Criteria = new \Everon\DataMapper\Criteria();
        $this->assertInstanceOf('Everon\DataMapper\Interfaces\Criteria', $Criteria);
    }

    function dataProvider()
    {
        $Criteria = new \Everon\DataMapper\Criteria();
        
        return [
            [$Criteria]
        ];
    }

}
