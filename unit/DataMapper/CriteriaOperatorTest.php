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

class CriteriaOperatorTest extends \Everon\TestCase
{
    function testConstructor()
    {
        $Operator = new \Everon\DataMapper\Criteria\Operator\Equal();
        $this->assertInstanceOf('Everon\DataMapper\Interfaces\Criteria\Operator', $Operator);
    }

    /**
     * @dataProvider dataProvider
     */
    function testWhereOrAndShouldBuildCriteria(\Everon\DataMapper\Interfaces\Criteria\Operator $Operator)
    {
    }

    function dataProvider()
    {
        $Factory = $this->buildFactory();
        $CriteriaBuilder = $Factory->buildCriteriaOperator('Equal');


        /*        $CriteriaBuilder->_and(function($Builder){
                    $Builder->_or('id', 'in', [1,2,3])->_and('name', '!=', 'john');
                });
        
                $CriteriaBuilder->_or(function($Builder){
                    $Builder->_or('id', 'in', [1,2,3])->_and('name', '!=', 'john');
                });*/
        
        return [
            [$CriteriaBuilder]
        ];
    }

}
