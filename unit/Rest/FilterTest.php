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
        'WHERE' => [
            ['id', '=', 1, 'AND'],
            ['type', '=', 'INFO']
        ],
        'AND' => [
            ['status', '=', 'ACTIVE', 'OR'],
            ['created', '>=', '2010-10-10'],
        ],
        'OR' => [
            ['status', '=', 'INACTIVE', 'AND'],
            ['created', '=', null],
        ]
    ];
    

    public function testConstructor()
    {
        $Filter = new \Everon\Rest\Filter();
        $this->assertInstanceOf('Everon\Rest\Interfaces\Filter', $Filter);

        //$Request = \Mockery::mock('Everon\Rest\Interfaces\Request');
        //$Request->shouldReceive('getGetParameter')->once()->with('fields', [])->andReturn('id,name,date_added');
    }

    /**
     * @dataProvider dataProvider
     */
    public function testToCriteria(\Everon\Rest\Interfaces\Filter $Filter)
    {
        //$Request = \Mockery::mock('Everon\Rest\Interfaces\Request');
        //$Request->shouldReceive('getGetParameter')->once()->with('fields', [])->andReturn('id,name,date_added');

        $CriteriaBuilder = $Filter->toCriteria($this->json_data);
        sd($CriteriaBuilder->toSqlPart());
        
        $this->assertInstanceOf('Everon\DataMapper\Interfaces\Criteria\Builder', $CriteriaBuilder);
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
