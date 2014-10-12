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

class CriteriumTest extends \Everon\TestCase
{
    protected $column = 'foo';
    protected $operator_type = '=';
    protected $value = 'bar';
    
    protected $sql_part_data = [
        'foo = :foo_76548',
        ['foo_76548' => 'bar']
    ];
    
    protected $sql_part_data_is = [
        'foo IS NULL',
        []
    ];
    
    protected $sql_part_data_not_is = [
        'foo IS NOT NULL',
        []
    ];
    
    
    public function testConstructor()
    {
        $Criterium = new \Everon\DataMapper\Criteria\Criterium($this->column, $this->operator_type, $this->value);
        $this->assertInstanceOf('Everon\DataMapper\Interfaces\Criteria\Criterium', $Criterium);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testToSqlPartDataShouldReturnSqlPartInstance(\Everon\DataMapper\Interfaces\Criteria\Criterium $Criterium)
    {
        //$class_is = Criteria\Builder::getOperatorClassName(Criteria\Operator::TYPE_IS);
        //$class_not_is = Criteria\Builder::getOperatorClassName(Criteria\Operator::TYPE_NOT_IS);

        //$CriteriaOperatorIs = \Mockery::mock('Everon\DataMapper\Interfaces\Criteria\Operator');
        //$CriteriaOperatorNotIs = \Mockery::mock('Everon\DataMapper\Interfaces\Criteria\Operator');
        $CriteriaOperatorEqual = \Mockery::mock('Everon\DataMapper\Interfaces\Criteria\Operator');
        $CriteriaOperatorEqual->shouldReceive('toSqlPartData')->once()->andReturn($this->sql_part_data);

        $SqlPart = \Mockery::mock('Everon\DataMapper\Interfaces\SqlPart');
        
        $Factory = \Mockery::mock('Everon\Application\Interfaces\Factory');
        $Factory->shouldReceive('buildDataMapperSqlPart')->once()->andReturn($SqlPart);
        //$Factory->shouldReceive('buildCriteriaOperator')->once()->with($class_is)->andReturn($CriteriaOperatorIs);
        //$Factory->shouldReceive('buildCriteriaOperator')->once()->with($class_not_is)->andReturn($CriteriaOperatorNotIs);
        $Factory->shouldReceive('buildCriteriaOperator')->once()->with(Criteria\Operator::TYPE_EQUAL)->andReturn($CriteriaOperatorEqual);
        
        $Criterium->setFactory($Factory);
        
        $SqlPart = $Criterium->toSqlPart();
        $this->assertInstanceOf('Everon\DataMapper\Interfaces\SqlPart', $SqlPart);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testBuildOperatorShouldReturnIsNullWhenValueIsNull(\Everon\DataMapper\Interfaces\Criteria\Criterium $Criterium)
    {
        $Criterium->setValue(null);

        $CriteriaOperatorIs = \Mockery::mock('Everon\DataMapper\Interfaces\Criteria\Operator');
        $CriteriaOperatorIs->shouldReceive('toSqlPartData')->once()->andReturn($this->sql_part_data_is);
        
        $CriteriaOperatorEqual = \Mockery::mock('Everon\DataMapper\Interfaces\Criteria\Operator');
        $CriteriaOperatorEqual->shouldReceive('getType')->once()->andReturn(Criteria\Operator::TYPE_EQUAL);

        $SqlPart = \Mockery::mock('Everon\DataMapper\Interfaces\SqlPart');

        $Factory = \Mockery::mock('Everon\Application\Interfaces\Factory');
        $Factory->shouldReceive('buildDataMapperSqlPart')->once()->andReturn($SqlPart);
        $Factory->shouldReceive('buildCriteriaOperator')->once()->with(Criteria\Operator::TYPE_IS)->andReturn($CriteriaOperatorIs);
        $Factory->shouldReceive('buildCriteriaOperator')->once()->with(Criteria\Operator::TYPE_EQUAL)->andReturn($CriteriaOperatorEqual);

        $Criterium->setFactory($Factory);

        $SqlPart = $Criterium->toSqlPart();
        $this->assertInstanceOf('Everon\DataMapper\Interfaces\SqlPart', $SqlPart);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testBuildOperatorShouldReturnIsNotNullWhenValueIsNull(\Everon\DataMapper\Interfaces\Criteria\Criterium $Criterium)
    {
        $Criterium->setValue(null);
        
        $CriteriaOperatorNotIs = \Mockery::mock('Everon\DataMapper\Interfaces\Criteria\Operator');
        $CriteriaOperatorNotIs->shouldReceive('toSqlPartData')->once()->andReturn($this->sql_part_data_not_is);
        
        $CriteriaOperatorEqual = \Mockery::mock('Everon\DataMapper\Interfaces\Criteria\Operator');
        $CriteriaOperatorEqual->shouldReceive('getType')->twice()->andReturn(Criteria\Operator::TYPE_NOT_EQUAL);

        $SqlPart = \Mockery::mock('Everon\DataMapper\Interfaces\SqlPart');

        $Factory = \Mockery::mock('Everon\Application\Interfaces\Factory');
        $Factory->shouldReceive('buildDataMapperSqlPart')->once()->andReturn($SqlPart);
        $Factory->shouldReceive('buildCriteriaOperator')->once()->with(Criteria\Operator::TYPE_NOT_IS)->andReturn($CriteriaOperatorNotIs);
        $Factory->shouldReceive('buildCriteriaOperator')->once()->with(Criteria\Operator::TYPE_EQUAL)->andReturn($CriteriaOperatorEqual);

        $Criterium->setFactory($Factory);

        $SqlPart = $Criterium->toSqlPart();
        $this->assertInstanceOf('Everon\DataMapper\Interfaces\SqlPart', $SqlPart);
    }
    
    
    public function dataProvider()
    {
        $Criteria = new \Everon\DataMapper\Criteria\Criterium($this->column, $this->operator_type, $this->value);
        
        return [
            [$Criteria]
        ];
    }

}
