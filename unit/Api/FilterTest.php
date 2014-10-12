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
    protected $json_data = null;
    
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
        $this->assertInstanceOf('Everon\Interfaces\Collection', $Filter->getFilterCollection());
        $this->assertTrue($Filter->getFilterCollection()->isEmpty());
    }


    /**
     * @dataProvider dataProvider
     */
    public function testApplyToCriteriaShouldNotReturnEmptyCollection(\Everon\Rest\Interfaces\Filter $Filter)
    {
        $Criteria = new \Everon\DataMapper\CriteriaOLD();
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
            ],
            [
                'column'=>'datetime',
                'operator'=>'NOT IN',
                'value'=>[new \DateTime(),new \DateTime()],
                'column_glue'=>'AND',

            ]
        ]));

        $Filter->assignToCriteria($Criteria);
        $this->assertFalse($Criteria->getWhereSql()->isEmpty());
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
