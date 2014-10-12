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
    function testToSqlPartShouldReturnValidSqlPart(\Everon\DataMapper\Interfaces\Criteria\Builder $CriteriaBuilder)
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
            sql: (id IN (:id_843451778,:id_897328169,:id_1377365551) OR id NOT IN (:id_1260952006,:id_519145813,:id_1367241593) AND name = :name_1178871152) OR (modified IS NULL AND name IS NOT NULL OR id = :id_895877163)
            parameters -> array(8) [
                'name_1178871152' => string (3) "foo"
                'id_1260952006' => integer 4
                'id_519145813' => integer 5
                'id_1367241593' => integer 6
                'id_843451778' => integer 1
                'id_897328169' => integer 2
                'id_1377365551' => integer 3
                'id_895877163' => integer 55
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
