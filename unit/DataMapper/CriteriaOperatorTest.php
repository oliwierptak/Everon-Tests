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

class CriteriaOperatorTest extends \Everon\TestCase
{
    protected $column = 'foo';
    protected $placeholder = ':foo_76548';
    protected $placeholder_as_paramter = 'foo_76548';
    protected $value = 'bar';
    
    public function testConstructor()
    {
        $Operator = new \Everon\DataMapper\Criteria\Operator\Equal();
        $this->assertInstanceOf('Everon\DataMapper\Interfaces\Criteria\Operator', $Operator);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testToSqlPartDataShouldReturnSqlPartAndParameters(\Everon\DataMapper\Interfaces\Criteria\Operator $Operator)
    {
        $Criterium = \Mockery::mock('Everon\DataMapper\Criteria\Criterium');
        $Criterium->shouldReceive('getColumn')->once()->andReturn($this->column);
        $Criterium->shouldReceive('getPlaceholder')->once()->andReturn($this->placeholder);
        $Criterium->shouldReceive('getPlaceholderAsParameter')->once()->andReturn($this->placeholder_as_paramter);
        $Criterium->shouldReceive('getValue')->twice()->andReturn($this->value);

        list($sql, $parameters) = $Operator->toSqlPartData($Criterium);
        
        $this->assertEquals('foo = :foo_76548', $sql);
        $this->assertEquals(['foo_76548' => 'bar'], $parameters);
    }

    public function dataProvider()
    {
        $Factory = $this->buildFactory();
        $CriteriaBuilder = $Factory->buildCriteriaOperator('Equal');


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
