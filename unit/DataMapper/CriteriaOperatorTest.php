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
    protected $placeholder_as_parameter = 'foo_76548';
    protected $value = 'bar';
    
    public function testConstructor()
    {
        $Operator = new \Everon\DataMapper\Criteria\Operator\Equal();
        $this->assertInstanceOf('Everon\DataMapper\Interfaces\Criteria\Operator', $Operator);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testToSqlPartDataShouldReturnSqlPartAndParametersEqual(\Everon\DataMapper\Interfaces\Criteria\Operator $Operator)
    {
        $Criterium = \Mockery::mock('Everon\DataMapper\Criteria\Criterium');
        $Criterium->shouldReceive('getColumn')->once()->andReturn($this->column);
        $Criterium->shouldReceive('getPlaceholder')->once()->andReturn($this->placeholder);
        $Criterium->shouldReceive('getPlaceholderAsParameter')->once()->andReturn($this->placeholder_as_parameter);
        $Criterium->shouldReceive('getValue')->twice()->andReturn($this->value);

        list($sql, $parameters) = $Operator->toSqlPartData($Criterium);
        
        $this->assertEquals('foo = :foo_76548', $sql);
        $this->assertEquals(['foo_76548' => 'bar'], $parameters);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testToSqlPartDataShouldReturnSqlPartAndParametersNotEqual(\Everon\DataMapper\Interfaces\Criteria\Operator $Operator)
    {
        $Factory = $this->buildFactory();
        $Operator = $Factory->buildCriteriaOperator('NotEqual');
        
        $Criterium = \Mockery::mock('Everon\DataMapper\Criteria\Criterium');
        $Criterium->shouldReceive('getColumn')->once()->andReturn($this->column);
        $Criterium->shouldReceive('getPlaceholder')->once()->andReturn($this->placeholder);
        $Criterium->shouldReceive('getPlaceholderAsParameter')->once()->andReturn($this->placeholder_as_parameter);
        $Criterium->shouldReceive('getValue')->twice()->andReturn($this->value);

        list($sql, $parameters) = $Operator->toSqlPartData($Criterium);

        $this->assertEquals('foo != :foo_76548', $sql);
        $this->assertEquals(['foo_76548' => 'bar'], $parameters);
    }

    public function testToSqlPartDataShouldReturnSqlPartAndParametersIn()
    {
        $Factory = $this->buildFactory();
        $Operator = $Factory->buildCriteriaOperator('In');

        $Criterium = \Mockery::mock('Everon\DataMapper\Criteria\Criterium');
        $Criterium->shouldReceive('getColumn')->twice()->andReturn($this->column);
        $Criterium->shouldReceive('getValue')->once()->andReturn([$this->value]);

        list($sql, $parameters) = $Operator->toSqlPartData($Criterium);

        $matches = [];
        $this->assertTrue(
            preg_match('@^foo IN \(\:(foo_(\d+))\)$@', $sql, $matches) === 1
        );
        
        $key = $matches[1];
        $this->assertEquals([$key => 'bar'], $parameters);
    }

    public function testToSqlPartDataShouldReturnSqlPartAndParametersNotIn()
    {
        $Factory = $this->buildFactory();
        $Operator = $Factory->buildCriteriaOperator('NotIn');

        $Criterium = \Mockery::mock('Everon\DataMapper\Criteria\Criterium');
        $Criterium->shouldReceive('getColumn')->twice()->andReturn($this->column);
        $Criterium->shouldReceive('getValue')->once()->andReturn([$this->value]);

        list($sql, $parameters) = $Operator->toSqlPartData($Criterium);

        $matches = [];
        $this->assertTrue(
            preg_match('@^foo NOT IN \(\:(foo_(\d+))\)$@', $sql, $matches) === 1
        );

        $key = $matches[1];
        $this->assertEquals([$key => 'bar'], $parameters);
    }

    public function testToSqlPartDataShouldReturnSqlPartAndParametersGreaterOrEqual()
    {
        $Factory = $this->buildFactory();
        $Operator = $Factory->buildCriteriaOperator('GreaterOrEqual');

        $Criterium = \Mockery::mock('Everon\DataMapper\Criteria\Criterium');
        $Criterium->shouldReceive('getColumn')->once()->andReturn($this->column);
        $Criterium->shouldReceive('getPlaceholder')->once()->andReturn($this->placeholder);
        $Criterium->shouldReceive('getPlaceholderAsParameter')->once()->andReturn($this->placeholder_as_parameter);
        $Criterium->shouldReceive('getValue')->twice()->andReturn($this->value);

        list($sql, $parameters) = $Operator->toSqlPartData($Criterium);

        $this->assertEquals('foo >= :foo_76548', $sql);
        $this->assertEquals(['foo_76548' => 'bar'], $parameters);
    }

    public function dataProvider()
    {
        $Factory = $this->buildFactory();
        $Operator = $Factory->buildCriteriaOperator('Equal');
        
        return [
            [$Operator]
        ];
    }

}
