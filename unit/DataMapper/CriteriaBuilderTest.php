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

function mt_rand($min=1, $max=1000)
{
    return 100;
}

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

        $CriteriaBuilder->where('modified', 'IS', null)->andWhere('name', '!=', null)->orWhere('id', '=', 55);
        $CriteriaBuilder->glueByOr();
        $this->assertInstanceOf('Everon\DataMapper\Interfaces\Criteria', $CriteriaBuilder->getCurrentCriteria());
        $this->assertCount(3, $CriteriaBuilder->getCurrentCriteria()->toArray());

        $SqlPart = $CriteriaBuilder->toSqlPart();
        
        preg_match_all('@:([a-zA-Z]+)_(\d+)@', $SqlPart->getSql(), $jesus_fucking_christ);
        $jesus_fucking_christ = $jesus_fucking_christ[0];
        
        //strips : in front
        array_walk($jesus_fucking_christ, function(&$item){
            $item = substr($item, 1, strlen($item));
        });
        
        foreach ($jesus_fucking_christ as $key) {
            $this->assertTrue(array_key_exists($key, $SqlPart->getParameters()));
        }
        
        $this->assertEquals(count($SqlPart->getParameters()), count($jesus_fucking_christ));
        //$this->assertEquals('((id IN (1,2,3)) OR (id NOT IN (4,5,6)) AND (name = :name)) OR (modified IS NULL)', $sql);
    }

    function dataProvider()
    {
        $Factory = $this->buildFactory();
        $CriteriaBuilder = $Factory->buildCriteriaBuilder();

        return [
            [$CriteriaBuilder]
        ];
    }

}
