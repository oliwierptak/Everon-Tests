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

        $CriteriaBuilder->where('modified', 'IS', null)->andWhere('name', '!=', null)->orWhere('id', '=', 55);
        $CriteriaBuilder->glueByOr();
        $this->assertInstanceOf('Everon\DataMapper\Interfaces\Criteria', $CriteriaBuilder->getCurrentCriteria());
        $this->assertCount(3, $CriteriaBuilder->getCurrentCriteria()->toArray());

        $SqlPart = $CriteriaBuilder->toSqlPart();
        
        preg_match_all('@:([a-zA-Z]+)_(\d+)@', $SqlPart->getSql(), $sql_parameters);
        $sql_parameters = $sql_parameters[0];
        
        //strips : in front
        array_walk($sql_parameters, function(&$item){
            $item = substr($item, 1, strlen($item));
        });
        
        foreach ($sql_parameters as $key) {
            $this->assertTrue(array_key_exists($key, $SqlPart->getParameters()));
        }
        
        $this->assertEquals(count($SqlPart->getParameters()), count($sql_parameters));
        /*
            sql: (id IN (:id_1081915057,:id_1052744513,:id_1359948893) AND id NOT IN (:id_349904367,:id_880096498,:id_1251203133) AND name = :name_1369439063) OR (modified IS NULL AND name IS NOT NULL AND id = :id_176496691)
            parameters -> array(8) [
            'name_1369439063' => string (3) "foo"
            'id_349904367' => integer 4
            'id_880096498' => integer 5
            'id_1251203133' => integer 6
            'id_1081915057' => integer 1
            'id_1052744513' => integer 2
            'id_1359948893' => integer 3
            'id_176496691' => integer 55
        */
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
