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
        $this->assertInstanceOf('Everon\DataMapper\Interfaces\Criteria\Container', $CriteriaBuilder->getCurrentContainer());
        $this->assertInstanceOf('Everon\DataMapper\Interfaces\Criteria', $CriteriaBuilder->getCurrentContainer()->getCriteria());
        $this->assertCount(2, $CriteriaBuilder->getCurrentContainer()->getCriteria()->toArray());

        $CriteriaBuilder->where('name', '!=', 'foo')->andWhere('name', '!=', 'bar');
        $this->assertInstanceOf('Everon\DataMapper\Interfaces\Criteria\Container', $CriteriaBuilder->getCurrentContainer());
        $this->assertInstanceOf('Everon\DataMapper\Interfaces\Criteria', $CriteriaBuilder->getCurrentContainer()->getCriteria());
        $this->assertCount(2, $CriteriaBuilder->getCurrentContainer()->getCriteria()->toArray());
    }

    /**
     * @dataProvider dataProvider
     */
    function testWhereRaw(\Everon\DataMapper\Interfaces\Criteria\Builder $CriteriaBuilder)
    {
        $CriteriaBuilder->whereRaw('foo + bar')->andWhereRaw('1=1')->orWhereRaw('foo::bar()');
        $SqlPart = $CriteriaBuilder->toSqlPart();
        
        $this->assertEquals('WHERE (foo + bar AND 1=1 OR foo::bar())', $SqlPart->getSql());
        $this->assertEmpty($SqlPart->getParameters());
    }

    /**
     * @dataProvider dataProvider
     */
    function testGlue(\Everon\DataMapper\Interfaces\Criteria\Builder $CriteriaBuilder)
    {
        $CriteriaBuilder->where('id', 'IN', [1,2,3])->orWhere('id', 'NOT IN', [4,5,6]);
        $CriteriaBuilder->glueByOr();
        $CriteriaBuilder->where('name', '!=', 'foo')->andWhere('name', '!=', 'bar');
        $CriteriaBuilder->glueByAnd();
        $CriteriaBuilder->where('bar', '=', 'foo')->andWhere('name', '=', 'Doe');

        $SqlPart = $CriteriaBuilder->toSqlPart();
        
        preg_match_all('@:([a-zA-Z]+)_(\d+)(_(\d+))?@', $SqlPart->getSql(), $sql_parameters);
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
         (id IN (:id_1263450107,:id_1088910886,:id_404821955) OR id NOT IN (:id_470739703,:id_562547487,:id_230395754)) OR
        (name != :name_1409254675 AND name != :name_190021050) AND
        (bar = :bar_1337676982 AND name = :name_391340793)"
            protected parameters -> array(10) [
                'id_470739703' => integer 4
                'id_562547487' => integer 5
                'id_230395754' => integer 6
                'id_1263450107' => integer 1
                'id_1088910886' => integer 2
                'id_404821955' => integer 3
                'name_190021050' => string (3) "bar"
                'name_1409254675' => string (3) "foo"
                'name_391340793' => string (3) "Doe"
                'bar_1337676982' => string (3) "foo"
            ]
         */
    }

    /**
     * @dataProvider dataProvider
     */
    function testToSqlPartShouldReturnValidSqlPart(\Everon\DataMapper\Interfaces\Criteria\Builder $CriteriaBuilder)
    {
        $CriteriaBuilder->where('id', 'IN', [1,2,3])->orWhere('id', 'NOT IN', [4,5,6])->andWhere('name', '=', 'foo');
        $this->assertInstanceOf('Everon\DataMapper\Interfaces\Criteria\Container', $CriteriaBuilder->getCurrentContainer());
        $this->assertInstanceOf('Everon\DataMapper\Interfaces\Criteria', $CriteriaBuilder->getCurrentContainer()->getCriteria());
        $this->assertCount(3, $CriteriaBuilder->getCurrentContainer()->getCriteria()->toArray());

        $CriteriaBuilder->where('modified', 'IS', null)->andWhere('name', '!=', null)->orWhere('id', '=', 55);
        $this->assertInstanceOf('Everon\DataMapper\Interfaces\Criteria\Container', $CriteriaBuilder->getCurrentContainer());
        $this->assertInstanceOf('Everon\DataMapper\Interfaces\Criteria', $CriteriaBuilder->getCurrentContainer()->getCriteria());
        $this->assertCount(3, $CriteriaBuilder->getCurrentContainer()->getCriteria()->toArray());

        $SqlPart = $CriteriaBuilder->toSqlPart();
        
        preg_match_all('@:([a-zA-Z]+)_(\d+)(_(\d+))?@', $SqlPart->getSql(), $sql_parameters);
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
            sql: WHERE (id IN (:id_843451778,:id_897328169,:id_1377365551) OR id NOT IN (:id_1260952006,:id_519145813,:id_1367241593) AND name = :name_1178871152) OR (modified IS NULL AND name IS NOT NULL OR id = :id_895877163)
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

    /**
     * @dataProvider dataProvider
     */
    function testToString(\Everon\DataMapper\Interfaces\Criteria\Builder $CriteriaBuilder)
    {
        $CriteriaBuilder->whereRaw('foo + bar')->andWhereRaw('1=1')->orWhereRaw('foo::bar()');
        $this->assertEquals('WHERE (foo + bar AND 1=1 OR foo::bar())', (string) $CriteriaBuilder);
    }

    /**
     * @dataProvider dataProvider
     */
    function testToArray(\Everon\DataMapper\Interfaces\Criteria\Builder $CriteriaBuilder)
    {
        $CriteriaBuilder->whereRaw('foo + bar')->andWhereRaw('1=1')->orWhereRaw('foo::bar()')->orWhere('id', '=', 55);
        $this->assertCount(1, $CriteriaBuilder->toArray());
    }

    /**
     * @dataProvider dataProvider
     */
    function testLimitOffsetGroupBy(\Everon\DataMapper\Interfaces\Criteria\Builder $CriteriaBuilder)
    {
        $CriteriaBuilder->whereRaw('foo + bar')->andWhereRaw('1=1')->orWhereRaw('foo::bar()');
        $CriteriaBuilder->glueByAnd();
        $CriteriaBuilder->whereRaw('1=1');
        $CriteriaBuilder->setLimit(10);
        $CriteriaBuilder->setOffset(5);
        $CriteriaBuilder->setGroupBy('name,id');
        $CriteriaBuilder->setOrderBy(['name' => 'DESC', 'id' => 'ASC']);
        $SqlPart = $CriteriaBuilder->toSqlPart();
        
        $this->assertEquals('WHERE (foo + bar AND 1=1 OR foo::bar())
AND (1=1) GROUP BY name,id ORDER BY name DESC,id ASC LIMIT 10 OFFSET 5', $SqlPart->getSql());
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
