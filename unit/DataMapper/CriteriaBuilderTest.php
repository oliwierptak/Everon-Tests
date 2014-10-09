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
        $CriteriaBuilder->where('id', 'IN', [1,2,3])->orWhere('id', 'NOT IN', [4,5,6]);
        $this->assertInstanceOf('Everon\DataMapper\Interfaces\Criteria', $CriteriaBuilder->getCurrentCriteria());
        $this->assertCount(2, $CriteriaBuilder->getCurrentCriteria()->toArray());

        $CriteriaBuilder->where('name', '!=', 'foo')->andWhere('name', '!=', 'bar');
        $this->assertInstanceOf('Everon\DataMapper\Interfaces\Criteria', $CriteriaBuilder->getCurrentCriteria());
        $this->assertCount(2, $CriteriaBuilder->getCurrentCriteria()->toArray());

    }

    /**
     * @dataProvider dataProvider
     */
    function testToSqlShouldReturnValidSqlPart(\Everon\DataMapper\Interfaces\Criteria\Builder $CriteriaBuilder)
    {
        $CriteriaBuilder->where('id', 'IN', [1,2,3])->orWhere('id', 'NOT IN', [4,5,6])->andWhere('name', '=', 'foo');
        $this->assertInstanceOf('Everon\DataMapper\Interfaces\Criteria', $CriteriaBuilder->getCurrentCriteria());
        $this->assertCount(3, $CriteriaBuilder->getCurrentCriteria()->toArray());

        $CriteriaBuilder->where('modified', 'IS', null);
        $CriteriaBuilder->glueByOr();
        $this->assertInstanceOf('Everon\DataMapper\Interfaces\Criteria', $CriteriaBuilder->getCurrentCriteria());
        $this->assertCount(1, $CriteriaBuilder->getCurrentCriteria()->toArray());

        $sql = $CriteriaBuilder->toSql();
        $this->assertEquals('((id IN (1,2,3)) OR (id NOT IN (4,5,6)) AND (name = :name)) OR (modified IS NULL)', $sql);
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
