<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Test\Rest;

class FilterTest extends \Everon\TestCase
{
    protected $json_data = [
        ['id', '=', 1],
        ['type', '=', 'INFO', 'OR'],
        ['status', '=', 'INACTIVE', 'AND'],
        ['created', '=', null]
    ];
    

    public function testConstructor()
    {
        $Filter = new \Everon\Rest\Filter();
        $this->assertInstanceOf('Everon\Rest\Interfaces\Filter', $Filter);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testToCriteria(\Everon\Rest\Interfaces\Filter $Filter)
    {
        $CriteriaBuilder = $Filter->toCriteria($this->json_data);
        $SqlPart = $CriteriaBuilder->toSqlPart();
        
        $this->assertInstanceOf('Everon\DataMapper\Interfaces\Criteria\Builder', $CriteriaBuilder);
        //$this->assertEquals('WHERE (id = :id_1243257904 OR type = :type_1052619708 AND status = :status_410253891 AND created IS NULL)', $CriteriaBuilder);
        $this->assertCount(3, $SqlPart->getParameters());
    }

    public function dataProvider()
    {
        $Factory = $this->buildFactory();
        
        $Filter = $Factory->buildRestFilter();
        
        return [
            [$Filter]
        ];
    }

}
