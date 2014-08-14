<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Test;

use Everon\DataMapper\Interfaces\Criteria;
use Everon\Helper;

class FilterTest extends \Everon\TestCase
{
    protected function setUp()
    {
        $this->json_data = '%5B%7B"column"%3A"customer_id"%2C"value"%3A1%2C"operator"%3A"%3D"%7D%2C%7B"column"%3A"customer_id"%2C"value"%3A1%2C"operator"%3A"%3D"%7D%5D';
    }

    public function testConstructor()
    {
        $json = urldecode($this->json_data);
        $json = json_decode($json);

        $Filter = new \Everon\Rest\Filter(new Helper\Collection($json));
        $this->assertInstanceOf('\Everon\Rest\Interfaces\Filter', $Filter);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testGetFilterCollectionShouldReturnEmptyFilterCollection(\Everon\Rest\Interfaces\Filter $Filter)
    {
//        $this->assertInstanceOf('Everon\Interfaces\Collection', $Filter->getFilterCollection());
//        $this->assertTrue($Filter->getFilterCollection()->isEmpty());
    }

    /**
     * @dataProvider dataProvider
     */
    public function testGetFilterCollectionShouldInitFilterCollection(\Everon\Rest\Interfaces\Filter $Filter)
    {
        $this->assertInstanceOf('Everon\Interfaces\Collection', $Filter->getFilterCollection());
        $this->assertTrue($Filter->getFilterCollection()->isEmpty());


        $Filter->setFilterDefinition(new Helper\Collection([
            [
                'column'=>'test',
                'operator'=>'=',
                'value'=>'%stst%',
            ],
            [
                'column'=>'datetime',
                'operator'=>'NOT IN',
                'value'=>[new \DateTime(),new \DateTime(),new \DateTime()],
               // 'glue'=>'OR'
            ],
            [
                'column'=>'datetime',
                'operator'=>'BETWEEN',
                'value'=>[new \DateTime(),new \DateTime()],
                'glue'=>'AND'
            ]
        ]));


        $this->assertFalse($Filter->getFilterCollection()->isEmpty());
    }

    /**
     * @dataProvider dataProvider
     */
    public function testTotallyWrongOperatorType(\Everon\Rest\Interfaces\Filter $Filter)
    {

//        $Filter->setFilterDefinition(new Helper\Collection([
//            [
//                'column'=>'test',
//                'operator'=>'NOT IS',
//                'value'=>null,
//            ]
//        ]));


//        $this->assertInstanceOf('Everon\Interfaces\Collection', $Filter->getFilterCollection());
       // $this->assertTrue($Filter->getFilterCollection()->isEmpty());
    }

    /**
     * @dataProvider dataProvider
     */
    public function testApplyToCriteriaShouldNotReturnEmptyCollection(\Everon\Rest\Interfaces\Filter $Filter)
    {
        $Criteria = new \Everon\DataMapper\Criteria();
        $Filter->setFilterDefinition(new Helper\Collection([
            [
                'column'=>'user.kolom',
                'operator'=>'NOT BETWEEN',
                'value'=>['a','b']
            ],
            [
                'column'=>'datetime',
                'operator'=>'IN',
                'value'=>[new \DateTime(),new \DateTime(),new \DateTime()],
                 'glue'=>'OR'
            ],
            [
                'column'=>'datetime',
                'operator'=>'NOT IN',
                'value'=>[new \DateTime(),new \DateTime()],
                'glue'=>'AND'
            ]
        ]));
        $Filter->assignToCriteria($Criteria);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testNullValuesPerOperator(\Everon\Rest\Interfaces\Filter $Filter)
    {

        $Filter->setFilterDefinition(new Helper\Collection([
            [
                'column'=>'userId',
                'operator'=>'IS NOT',
                'value'=>null,
                'glue'=>'AND'
            ]
        ]));
        $list = $Filter->convertToCriteriaCollection();

        $this->assertFalse($list->isEmpty());
    }


    public function dataProvider()
    {
        $Factory = $this->buildFactory();
        $Filter = $Factory->buildRestFilter(new Helper\Collection([]));
        return [
            [$Filter]
        ];

    }

}
