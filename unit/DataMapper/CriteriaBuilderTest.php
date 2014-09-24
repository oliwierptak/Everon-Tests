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

class CriteriaBuilderTest extends \Everon\TestCase
{
    function testConstructor()
    {
        $Criteria = new \Everon\DataMapper\Criteria\Builder();
        $this->assertInstanceOf('Everon\DataMapper\Interfaces\Criteria\Builder', $Criteria);
    }

    /**
     * @dataProvider dataProvider
     */
    function testWhereOrAndShouldBuildCriteria(\Everon\DataMapper\Interfaces\Criteria\Builder $CriteriaBuilder)
    {
        $CriteriaBuilder->_or('id', 'IN', [1,2,3])->_or('id', 'NOT IN', [4,5,6]);
        $CriteriaBuilder->_and('name', '!=', 'foo')->_and('name', '!=', 'bar');
        
        $this->assertInstanceOf('Everon\DataMapper\Interfaces\Criteria', $CriteriaBuilder->getCriteriaAnd());
        $this->assertInstanceOf('Everon\DataMapper\Interfaces\Criteria', $CriteriaBuilder->getCriteriaOr());
        
        $this->assertCount(2, $CriteriaBuilder->getCriteriaAnd()->toArray());
        $this->assertCount(2, $CriteriaBuilder->getCriteriaOr()->toArray());
    }

    function dataProvider()
    {
        $Factory = $this->buildFactory();
        $CriteriaBuilder = $Factory->buildCriteriaBuilder();


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
