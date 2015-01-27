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
        $Criterium->shouldReceive('getColumn')->once()->andReturn($this->column);
        $Criterium->shouldReceive('getValue')->once()->andReturn([$this->value]);
        $Criterium->shouldReceive('getPlaceholderAsParameter')->once()->andReturn($this->placeholder_as_parameter);

        list($sql, $parameters) = $Operator->toSqlPartData($Criterium);

        
        $matches = [];
        $this->assertTrue(
            preg_match('@^foo IN \(\:(foo_(\d+)(_(\d+))?)\)$@', $sql, $matches) === 1
        );
        $key = $matches[1];
        $this->assertEquals([$key => 'bar'], $parameters);
    }

    public function testToSqlPartDataShouldReturnSqlPartAndParametersNotIn()
    {
        $Factory = $this->buildFactory();
        $Operator = $Factory->buildCriteriaOperator('NotIn');

        $Criterium = \Mockery::mock('Everon\DataMapper\Criteria\Criterium');
        $Criterium->shouldReceive('getColumn')->once()->andReturn($this->column);
        $Criterium->shouldReceive('getValue')->once()->andReturn([$this->value]);
        $Criterium->shouldReceive('getPlaceholderAsParameter')->once()->andReturn($this->placeholder_as_parameter);

        list($sql, $parameters) = $Operator->toSqlPartData($Criterium);

        $matches = [];
        $this->assertTrue(
            preg_match('@^foo NOT IN \(\:(foo_(\d+)(_(\d+))?)\)$@', $sql, $matches) === 1
        );

        $key = $matches[1];
        $this->assertEquals([$key => 'bar'], $parameters);
    }

    public function testToSqlPartDataShouldReturnSqlPartAndParametersGreaterThen()
    {
        $Factory = $this->buildFactory();
        $Operator = $Factory->buildCriteriaOperator('GreaterThen');

        $Criterium = \Mockery::mock('Everon\DataMapper\Criteria\Criterium');
        $Criterium->shouldReceive('getColumn')->once()->andReturn($this->column);
        $Criterium->shouldReceive('getPlaceholder')->once()->andReturn($this->placeholder);
        $Criterium->shouldReceive('getPlaceholderAsParameter')->once()->andReturn($this->placeholder_as_parameter);
        $Criterium->shouldReceive('getValue')->twice()->andReturn($this->value);

        list($sql, $parameters) = $Operator->toSqlPartData($Criterium);

        $this->assertEquals('foo > :foo_76548', $sql);
        $this->assertEquals(['foo_76548' => 'bar'], $parameters);
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

    public function testToSqlPartDataShouldReturnSqlPartAndParametersSmallerThen()
    {
        $Factory = $this->buildFactory();
        $Operator = $Factory->buildCriteriaOperator('SmallerThen');

        $Criterium = \Mockery::mock('Everon\DataMapper\Criteria\Criterium');
        $Criterium->shouldReceive('getColumn')->once()->andReturn($this->column);
        $Criterium->shouldReceive('getPlaceholder')->once()->andReturn($this->placeholder);
        $Criterium->shouldReceive('getPlaceholderAsParameter')->once()->andReturn($this->placeholder_as_parameter);
        $Criterium->shouldReceive('getValue')->twice()->andReturn($this->value);

        list($sql, $parameters) = $Operator->toSqlPartData($Criterium);

        $this->assertEquals('foo < :foo_76548', $sql);
        $this->assertEquals(['foo_76548' => 'bar'], $parameters);
    }

    public function testToSqlPartDataShouldReturnSqlPartAndParametersSmallerOrEqual()
    {
        $Factory = $this->buildFactory();
        $Operator = $Factory->buildCriteriaOperator('SmallerOrEqual');

        $Criterium = \Mockery::mock('Everon\DataMapper\Criteria\Criterium');
        $Criterium->shouldReceive('getColumn')->once()->andReturn($this->column);
        $Criterium->shouldReceive('getPlaceholder')->once()->andReturn($this->placeholder);
        $Criterium->shouldReceive('getPlaceholderAsParameter')->once()->andReturn($this->placeholder_as_parameter);
        $Criterium->shouldReceive('getValue')->twice()->andReturn($this->value);

        list($sql, $parameters) = $Operator->toSqlPartData($Criterium);

        $this->assertEquals('foo <= :foo_76548', $sql);
        $this->assertEquals(['foo_76548' => 'bar'], $parameters);
    }

    public function testToSqlPartDataShouldReturnSqlPartAndParametersBetween()
    {
        $Factory = $this->buildFactory();
        $Operator = $Factory->buildCriteriaOperator('Between');

        $Criterium = \Mockery::mock('Everon\DataMapper\Criteria\Criterium');
        $Criterium->shouldReceive('getColumn')->once()->andReturn($this->column);
        $Criterium->shouldReceive('getValue')->once()->andReturn(['2000-01-01', '2000-01-31']);
        $Criterium->shouldReceive('getPlaceholderAsParameter')->twice()->andReturn($this->placeholder_as_parameter);

        list($sql, $parameters) = $Operator->toSqlPartData($Criterium);

        preg_match_all('@:([a-zA-Z]+)_(\d+)(_(\d+))?@', $sql, $sql_parameters);
        $sql_parameters = $sql_parameters[0];

        $sql_to_compare = 'foo BETWEEN '.trim(implode(' AND ', array_values($sql_parameters))); //BETWEEN :foo_414533573 AND :foo_1406630365

        //strips : in front
        array_walk($sql_parameters, function(&$item){
            $item = substr($item, 1, strlen($item));
        });
        
        $parameters_to_compare = array_combine(array_values($sql_parameters), ['2000-01-01', '2000-01-31']);

        $this->assertEquals($sql_to_compare, $sql);
        $this->assertEquals($parameters_to_compare, $parameters);
    }

    public function testToSqlPartDataShouldReturnSqlPartAndParametersNotBetween()
    {
        $Factory = $this->buildFactory();
        $Operator = $Factory->buildCriteriaOperator('NotBetween');

        $Criterium = \Mockery::mock('Everon\DataMapper\Criteria\Criterium');
        $Criterium->shouldReceive('getColumn')->once()->andReturn($this->column);
        $Criterium->shouldReceive('getValue')->once()->andReturn(['2000-01-01', '2000-01-31']);
        $Criterium->shouldReceive('getPlaceholderAsParameter')->twice()->andReturn($this->placeholder_as_parameter);

        list($sql, $parameters) = $Operator->toSqlPartData($Criterium);

        preg_match_all('@:([a-zA-Z]+)_(\d+)(_(\d+))?@', $sql, $sql_parameters);
        $sql_parameters = $sql_parameters[0];

        $sql_to_compare = 'foo NOT BETWEEN '.trim(implode(' AND ', array_values($sql_parameters))); //NOT BETWEEN :foo_414533573 AND :foo_1406630365

        //strips : in front
        array_walk($sql_parameters, function(&$item){
            $item = substr($item, 1, strlen($item));
        });

        $parameters_to_compare = array_combine(array_values($sql_parameters), ['2000-01-01', '2000-01-31']);

        $this->assertEquals($sql_to_compare, $sql);
        $this->assertEquals($parameters_to_compare, $parameters);
    }

    public function testToSqlPartDataShouldReturnSqlPartAndParametersRawWithParameters()
    {
        $parameters = ['foo' => 'foo_value', 'bar' => 'bar_value'];
        $column_value = 'foo = :foo AND bar = :bar';
        
        $Factory = $this->buildFactory();
        $Operator = $Factory->buildCriteriaOperator('Raw');

        $Criterium = \Mockery::mock('Everon\DataMapper\Criteria\Criterium');
        $Criterium->shouldReceive('getColumn')->once()->andReturn($column_value);
        $Criterium->shouldReceive('getValue')->once()->andReturn($parameters);

        list($sql, $parameters) = $Operator->toSqlPartData($Criterium);
        
        $this->assertEquals($sql, $column_value);
        $this->assertEquals($parameters, $parameters);
    }
    
    public function testToSqlPartDataShouldReturnSqlPartAndParametersRawNoParameters()
    {
        $parameters = null;
        $column_value = "foo = 'foo'";

        $Factory = $this->buildFactory();
        $Operator = $Factory->buildCriteriaOperator('Raw');

        $Criterium = \Mockery::mock('Everon\DataMapper\Criteria\Criterium');
        $Criterium->shouldReceive('getColumn')->once()->andReturn($column_value);
        $Criterium->shouldReceive('getValue')->once()->andReturn($parameters);

        list($sql, $parameters) = $Operator->toSqlPartData($Criterium);

        $this->assertEquals($sql, $column_value);
        $this->assertEquals($parameters, $parameters);
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
