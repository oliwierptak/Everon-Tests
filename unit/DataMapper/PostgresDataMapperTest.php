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

use Everon\Domain;
use Everon\Domain\Interfaces;
use Everon\Helper;

class PostgresDataMapperTest extends \Everon\TestCase
{
    use Helper\Arrays;
    
    protected $entity_data = [
        'id' => 1,
        'first_name' => 'John',
        'last_name' => 'Doe'
    ];
    
    protected $entity_id = 1;
    
    protected $schema_name = 'bar';
    
    protected $table_name = 'foo';
    
    protected $table_pk_column_name = 'id';


    public function testConstructor()
    {
        $SchemaMock = $this->getMock('Everon\DataMapper\Interfaces\Schema');
        $TableMock = $this->getMock('Everon\DataMapper\Interfaces\Schema\Table', [], [],'', false);
        $DataMapper = new \Everon\DataMapper\PostgreSql\Foo($TableMock, $SchemaMock);
        $this->assertInstanceOf('Everon\Interfaces\DataMapper', $DataMapper);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testAdd(\Everon\Interfaces\DataMapper $Mapper, $PdoAdapter)
    {
        $data_to_add = $this->entity_data;
        unset($data_to_add['id']);

        $PdoAdapter->shouldReceive('insert')->once()->with(
            'INSERT INTO bar.foo ("first_name","last_name") VALUES (:first_name,:last_name) RETURNING id',
            $data_to_add
        )->andReturn($this->entity_id);

        $IdColumnMock = \Mockery::mock('Everon\DataMapper\Interfaces\Schema\Column');
        $IdColumnMock->shouldReceive('isPk')->times(3)->with()->andReturn(true);

        $FirstNameColumnMock = \Mockery::mock('Everon\DataMapper\Interfaces\Schema\Column');
        $FirstNameColumnMock->shouldReceive('isPk')->times(3)->with()->andReturn(false);

        $LastNameColumnMock = \Mockery::mock('Everon\DataMapper\Interfaces\Schema\Column');
        $LastNameColumnMock->shouldReceive('isPk')->times(3)->with()->andReturn(false);
        
        $Schema = $Mapper->getSchema();
        $Schema->shouldReceive('getPdoAdapterByName')->once()->with('write')->andReturn($PdoAdapter);

        $Table = $Mapper->getTable();
        $Table->shouldReceive('validateId')->once()->with($this->entity_id)->andReturn($this->entity_id);
        $Table->shouldReceive('getPk')->once()->with()->andReturn($this->table_pk_column_name);
        $Table->shouldReceive('getSchema')->once()->with()->andReturn($this->schema_name);
        $Table->shouldReceive('getName')->once()->with()->andReturn($this->table_name);
        $Table->shouldReceive('prepareDataForSql')->once()->with($data_to_add, false)->andReturn($data_to_add);
        $Table->shouldReceive('getColumns')->once()->with()->andReturn(['id'=>$IdColumnMock, 'first_name' => $FirstNameColumnMock, 'last_name' => $LastNameColumnMock]);
        
        $result = $Mapper->add($data_to_add);

        $this->assertInternalType('array', $result);
        $this->assertEquals($this->entity_data, $result);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testSave(\Everon\Interfaces\DataMapper $Mapper, $PdoAdapter)
    {
        $PdoAdapter->shouldReceive('update')->once()->with(
            'UPDATE bar.foo t SET first_name = :first_name,last_name = :last_name WHERE id = :id',
            $this->entity_data
        )->andReturn($this->entity_id);

        $IdColumnMock = \Mockery::mock('Everon\DataMapper\Interfaces\Schema\Column');
        $IdColumnMock->shouldReceive('isPk')->times(2)->with()->andReturn(true);

        $FirstNameColumnMock = \Mockery::mock('Everon\DataMapper\Interfaces\Schema\Column');
        $FirstNameColumnMock->shouldReceive('isPk')->times(2)->with()->andReturn(false);

        $LastNameColumnMock = \Mockery::mock('Everon\DataMapper\Interfaces\Schema\Column');
        $LastNameColumnMock->shouldReceive('isPk')->times(2)->with()->andReturn(false);

        $Schema = $Mapper->getSchema();
        $Schema->shouldReceive('getPdoAdapterByName')->once()->with('write')->andReturn($PdoAdapter);

        $Table = $Mapper->getTable();
        $Table->shouldReceive('validateId')->once()->with($this->entity_id)->andReturn($this->entity_id);
        $Table->shouldReceive('getPk')->once()->with()->andReturn($this->table_pk_column_name);
        $Table->shouldReceive('getSchema')->once()->with()->andReturn($this->schema_name);
        $Table->shouldReceive('getName')->once()->with()->andReturn($this->table_name);
        $Table->shouldReceive('getIdFromData')->once()->with($this->entity_data)->andReturn($this->entity_id);
        $Table->shouldReceive('prepareDataForSql')->once()->with($this->entity_data, true)->andReturn($this->entity_data);
        $Table->shouldReceive('getColumns')->once()->with()->andReturn(['id'=>$IdColumnMock, 'first_name' => $FirstNameColumnMock, 'last_name' => $LastNameColumnMock]);

        $result = $Mapper->save($this->entity_data);

        $this->assertEquals($this->entity_id, $result);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testDelete(\Everon\Interfaces\DataMapper $Mapper, $PdoAdapter)
    {
        $PdoAdapter->shouldReceive('delete')->once()->with(
            'DELETE FROM bar.foo t WHERE id = :id',
            ['id' => $this->entity_id]
        )->andReturn($this->entity_id);

        $Schema = $Mapper->getSchema();
        $Schema->shouldReceive('getPdoAdapterByName')->once()->with('write')->andReturn($PdoAdapter);

        $Table = $Mapper->getTable();
        $Table->shouldReceive('validateId')->once()->with($this->entity_id)->andReturn($this->entity_id);
        $Table->shouldReceive('getPk')->once()->with()->andReturn($this->table_pk_column_name);
        $Table->shouldReceive('getSchema')->once()->with()->andReturn($this->schema_name);
        $Table->shouldReceive('getName')->once()->with()->andReturn($this->table_name);

        $result = $Mapper->delete($this->entity_id);
        $this->assertEquals(1, $result);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testDeleteByCriteria(\Everon\Interfaces\DataMapper $Mapper, $PdoAdapter)
    {
        $PdoAdapter->shouldReceive('delete')->once()->with(
            'DELETE FROM bar.foo t WHERE (1=1 AND id = :id)',
            ['id' => $this->entity_id]
        )->andReturn($this->entity_id);
        
        $Criteria = \Mockery::mock('Everon\DataMapper\Interfaces\CriteriaOLD');
        $Criteria->shouldReceive('getWhereSql')->once()->with()->andReturn('WHERE (1=1 AND id = :id)');
        $Criteria->shouldReceive('getWhere')->once()->with()->andReturn([
            'id' => $this->entity_id
        ]);

        $Schema = $Mapper->getSchema();
        $Schema->shouldReceive('getPdoAdapterByName')->once()->with('write')->andReturn($PdoAdapter);

        $Table = $Mapper->getTable();
        $Table->shouldReceive('getSchema')->once()->with()->andReturn($this->schema_name);
        $Table->shouldReceive('getName')->once()->with()->andReturn($this->table_name);

        $result = $Mapper->deleteByCriteria($Criteria);
        $this->assertEquals(1, $result);
    }
    
    /**
     * @dataProvider dataProvider
     */
    public function testFetchOneShouldReturnArray(\Everon\Interfaces\DataMapper $Mapper, $PdoAdapter)
    {
        $Schema = $Mapper->getSchema();
        $Schema->shouldReceive('getPdoAdapterByName')->once()->with('read')->andReturn($PdoAdapter);

        $Table = $Mapper->getTable();
        $Table->shouldReceive('validateId')->once()->with($this->entity_id)->andReturn($this->entity_id);
        $Table->shouldReceive('getPk')->once()->with()->andReturn($this->table_pk_column_name);
        $Table->shouldReceive('getSchema')->once()->with()->andReturn($this->schema_name);
        $Table->shouldReceive('getName')->once()->with()->andReturn($this->table_name);
        
        $PdoStatement = \Mockery::mock('\PDOStatement');
        $PdoStatement->shouldReceive('fetch')->once()->with()->andReturn($this->entity_data);

        $PdoAdapter->shouldReceive('execute')->once()->with('
            SELECT * 
            FROM bar.foo t
            WHERE (1=1 AND id = :id)
            
            
            LIMIT 1
            ', ['id' => $this->entity_id])->andReturn($PdoStatement);

        $result = $Mapper->fetchOneById($this->entity_id);

        $this->assertInternalType('array', $result);
        $this->assertEquals($this->entity_data, $result);
    }
    

    public function dataProvider()
    {
        $PdoAdapterMock = \Mockery::mock('Everon\Interfaces\PdoAdapter');
        
        $SchemaMock = \Mockery::mock('Everon\DataMapper\Interfaces\Schema');
        $SchemaMock->shouldReceive('getAdapterName')->once()->with()->andReturn('PostgreSql');
        /* 
               $SchemaMock->shouldReceive('getPdoAdapterByName')->once()->with()->andReturn($PdoAdapterMock);
                $SchemaMock->shouldReceive('getDatabase')->once()->with()->andReturn('everon_test');
                $SchemaMock->shouldReceive('getDriver')->once()->with()->andReturn('PostgreSql');
                $SchemaMock->shouldReceive('getAdapterName')->once()->with()->andReturn('PostgreSql');
        */


        $TableMock = \Mockery::mock('Everon\DataMapper\Schema\Table'); //no interface cause PHP and traits
/*
        $TableMock->shouldReceive('getName')->once()->with()->andReturn('foo');
        $TableMock->shouldReceive('validateId')->once()->with()->andReturn(1);
        $TableMock->shouldReceive('getColumns')->once()->with()->andReturn(['id'=>$IdColumnMock, 'first_name' => $NameColumnMock]);
        $TableMock->shouldReceive('getPk')->once()->with()->andReturn('id');
*/
            
        $Factory = $this->buildFactory();
        $Mapper = $Factory->buildDataMapper('Foo', $TableMock, $SchemaMock, 'Everon\DataMapper');
        
        return [
            [$Mapper, $PdoAdapterMock]
        ];
    }
}