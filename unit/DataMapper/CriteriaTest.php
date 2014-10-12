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

class CriteriaTest extends \Everon\TestCase
{
    public function testConstructor()
    {
        $Criteria = new \Everon\DataMapper\Criteria();
        $this->assertInstanceOf('Everon\DataMapper\Interfaces\Criteria', $Criteria);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testWhereShouldResetGlueOnCriterium(\Everon\DataMapper\Interfaces\Criteria $Criteria)
    {
        $Criterium = \Mockery::mock('Everon\DataMapper\Criteria\Criterium');
        $Criterium->shouldReceive('resetGlue')->once()->with(null);
        
        $Criteria->where($Criterium);
        
        $this->assertInstanceOf('Everon\Interfaces\Collection', $Criteria->getCriteriumCollection());
        $this->assertCount(1, $Criteria->toArray());
    }

    /**
     * @dataProvider dataProvider
     */
    public function testAndWhereShouldGlueAndOnCriterium(\Everon\DataMapper\Interfaces\Criteria $Criteria)
    {
        $Criterium = \Mockery::mock('Everon\DataMapper\Criteria\Criterium');
        $Criterium->shouldReceive('resetGlue')->once()->with(null);
        $Criterium->shouldReceive('glueByAnd')->once();
        
        $Criteria->where($Criterium);
        $Criteria->andWhere($Criterium);

        $this->assertInstanceOf('Everon\Interfaces\Collection', $Criteria->getCriteriumCollection());
        $this->assertCount(2, $Criteria->toArray());
    }

    /**
     * @dataProvider dataProvider
     */
    public function testOrWhereShouldGlueOrOnCriterium(\Everon\DataMapper\Interfaces\Criteria $Criteria)
    {
        $Criterium = \Mockery::mock('Everon\DataMapper\Criteria\Criterium');
        $Criterium->shouldReceive('resetGlue')->once()->with(null);
        $Criterium->shouldReceive('glueByOr')->once();

        $Criteria->where($Criterium);
        $Criteria->orWhere($Criterium);

        $this->assertInstanceOf('Everon\Interfaces\Collection', $Criteria->getCriteriumCollection());
        $this->assertCount(2, $Criteria->toArray());
    }

    /**
     * @dataProvider dataProvider
     * @expectedException \Everon\DataMapper\Exception\Criteria
     * @expectedExceptionMessage No subquery found, use where() to start new subqury
     */
    public function testAndWhereShouldThrowExceptionWhenSubquerEmpty(\Everon\DataMapper\Interfaces\Criteria $Criteria)
    {
        $Criterium = \Mockery::mock('Everon\DataMapper\Criteria\Criterium');

        $Criteria->andWhere($Criterium);
    }

    /**
     * @dataProvider dataProvider
     * @expectedException \Everon\DataMapper\Exception\Criteria
     * @expectedExceptionMessage No subquery found, use where() to start new subqury
     */
    public function testOrWhereShouldThrowExceptionWhenSubquerEmpty(\Everon\DataMapper\Interfaces\Criteria $Criteria)
    {
        $Criterium = \Mockery::mock('Everon\DataMapper\Criteria\Criterium');

        $Criteria->orWhere($Criterium);
    }

    public function dataProvider()
    {
        $Criteria = new \Everon\DataMapper\Criteria();
        
        return [
            [$Criteria]
        ];
    }

}
