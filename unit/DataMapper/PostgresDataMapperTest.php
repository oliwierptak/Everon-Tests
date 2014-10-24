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
        $data_to_save = $this->entity_data;
        $data_to_save['id_854230835'] = $this->entity_id;
        unset($data_to_save['id']);

        $PdoAdapter->shouldReceive('update')->once()->with(
            'UPDATE bar.foo t SET first_name = :first_name,last_name = :last_name WHERE (id = :id_854230835) LIMIT 1',
            $data_to_save
        )->andReturn(1);

        $IdColumnMock = \Mockery::mock('Everon\DataMapper\Interfaces\Schema\Column');
        $IdColumnMock->shouldReceive('isPk')->times(1)->with()->andReturn(true);

        $FirstNameColumnMock = \Mockery::mock('Everon\DataMapper\Interfaces\Schema\Column');
        $FirstNameColumnMock->shouldReceive('isPk')->times(1)->with()->andReturn(false);

        $LastNameColumnMock = \Mockery::mock('Everon\DataMapper\Interfaces\Schema\Column');
        $LastNameColumnMock->shouldReceive('isPk')->times(1)->with()->andReturn(false);

        $Schema = $Mapper->getSchema();
        $Schema->shouldReceive('getPdoAdapterByName')->once()->with('write')->andReturn($PdoAdapter);

        $Table = $Mapper->getTable();
        $Table->shouldReceive('validateId')->once()->with($this->entity_id)->andReturn($this->entity_id);
        $Table->shouldReceive('getPk')->once()->with()->andReturn($this->table_pk_column_name);
        $Table->shouldReceive('getSchema')->once()->with()->andReturn($this->schema_name);
        $Table->shouldReceive('getName')->once()->with()->andReturn($this->table_name);
        $Table->shouldReceive('getIdFromData')->once()->with($this->entity_data)->andReturn($this->entity_id);
        $Table->shouldReceive('prepareDataForSql')->once()->with($this->entity_data, true)->andReturn($data_to_save);
        $Table->shouldReceive('getColumns')->once()->with()->andReturn(['id'=>$IdColumnMock, 'first_name' => $FirstNameColumnMock, 'last_name' => $LastNameColumnMock]);

        $SqlPart = \Mockery::mock('Everon\DataMapper\Interfaces\SqlPart');
        $SqlPart->shouldReceive('getSql')->once()->with()->andReturn('WHERE (id = :id_854230835) LIMIT 1');
        $SqlPart->shouldReceive('getParameters')->once()->with()->andReturn(['id_854230835' => $this->entity_id]);

        $CriteriaBuilder = \Mockery::mock('Everon\DataMapper\Interfaces\Criteria\Builder');
        $CriteriaBuilder->shouldReceive('where')->once()->with($this->table_pk_column_name, '=', $this->entity_id)->andReturn($CriteriaBuilder);
        $CriteriaBuilder->shouldReceive('toSqlPart')->once()->with()->andReturn($SqlPart);
        $CriteriaBuilder->shouldReceive('setLimit')->once()->with(1);
        
        $Factory = $Mapper->getFactory();
        $Factory->shouldReceive('buildCriteriaBuilder')->zeroOrMoreTimes()->with()->andReturn($CriteriaBuilder);

        $result = $Mapper->save($this->entity_data);

        $this->assertEquals(1, $result);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testDelete(\Everon\Interfaces\DataMapper $Mapper, $PdoAdapter)
    {
        $PdoAdapter->shouldReceive('insert')->once()->with(
            'DELETE FROM bar.foo t WHERE (id = :id_854230835) LIMIT 1',
            ['id_854230835' => $this->entity_id]
        )->andReturn($this->entity_id);

        $PdoAdapter->shouldReceive('delete')->once()->andReturn($this->entity_id);

        $Schema = $Mapper->getSchema();
        $Schema->shouldReceive('getPdoAdapterByName')->once()->with('write')->andReturn($PdoAdapter);

        $Table = $Mapper->getTable();
        $Table->shouldReceive('validateId')->once()->with($this->entity_id)->andReturn($this->entity_id);
        $Table->shouldReceive('getPk')->once()->with()->andReturn($this->table_pk_column_name);
        $Table->shouldReceive('getSchema')->once()->with()->andReturn($this->schema_name);
        $Table->shouldReceive('getName')->once()->with()->andReturn($this->table_name);

        $SqlPart = \Mockery::mock('Everon\DataMapper\Interfaces\SqlPart');
        $SqlPart->shouldReceive('getSql')->once()->with()->andReturn('WHERE (id = :id_854230835) LIMIT 1');
        $SqlPart->shouldReceive('getParameters')->once()->with()->andReturn(['id_854230835' => $this->entity_id]);

        $CriteriaBuilder = \Mockery::mock('Everon\DataMapper\Interfaces\Criteria\Builder');
        $CriteriaBuilder->shouldReceive('where')->once()->with($this->table_pk_column_name, '=', $this->entity_id)->andReturn($CriteriaBuilder);
        $CriteriaBuilder->shouldReceive('toSqlPart')->once()->with()->andReturn($SqlPart);

        $Factory = $Mapper->getFactory();
        $Factory->shouldReceive('buildCriteriaBuilder')->zeroOrMoreTimes()->with()->andReturn($CriteriaBuilder);


        $result = $Mapper->delete($this->entity_id);
        $this->assertEquals(1, $result);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testDeleteByCriteria(\Everon\Interfaces\DataMapper $Mapper, $PdoAdapter)
    {
        $PdoAdapter->shouldReceive('delete')->once()->andReturn($this->entity_id);

        $PdoAdapter->shouldReceive('delete')->once()->with(
            'DELETE FROM bar.foo t WHERE (id = :id_854230835)',
            ['id_854230835' => $this->entity_id]
        )->andReturn($this->entity_id);

        $SqlPart = \Mockery::mock('Everon\DataMapper\Interfaces\SqlPart');
        $SqlPart->shouldReceive('getSql')->once()->with()->andReturn('WHERE (id = :id_854230835)');
        $SqlPart->shouldReceive('getParameters')->twice()->with()->andReturn(['id_854230835' => $this->entity_id]);
        
        $CriteriaBuilder = \Mockery::mock('Everon\DataMapper\Interfaces\Criteria\Builder');
        $CriteriaBuilder->shouldReceive('toSqlPart')->once()->with()->andReturn($SqlPart);

        $Schema = $Mapper->getSchema();
        $Schema->shouldReceive('getPdoAdapterByName')->once()->with('write')->andReturn($PdoAdapter);

        $Table = $Mapper->getTable();
        $Table->shouldReceive('getSchema')->once()->with()->andReturn($this->schema_name);
        $Table->shouldReceive('getName')->once()->with()->andReturn($this->table_name);

        $result = $Mapper->deleteByCriteria($CriteriaBuilder);
        $this->assertEquals(1, $result);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testCount(\Everon\Interfaces\DataMapper $Mapper, $PdoAdapter)
    {
        $PdoStatement = \Mockery::mock('\PDOStatement');
        $PdoStatement->shouldReceive('fetchColumn')->once()->with()->andReturn(123);

        $PdoAdapter->shouldReceive('execute')->once()->with(
            'SELECT COUNT(t.id) FROM bar.foo t',
            []
        )->andReturn($PdoStatement);

        $SqlPart = \Mockery::mock('Everon\DataMapper\Interfaces\SqlPart');
        $SqlPart->shouldReceive('getSql')->once()->with()->andReturn('');
        $SqlPart->shouldReceive('getParameters')->once()->with()->andReturn([]);

        $CriteriaBuilder = \Mockery::mock('Everon\DataMapper\Interfaces\Criteria\Builder');
        $CriteriaBuilder->shouldReceive('setOrderBy')->once()->with([]);
        $CriteriaBuilder->shouldReceive('setOffset')->once()->with(null);
        $CriteriaBuilder->shouldReceive('setLimit')->once()->with(null);
        $CriteriaBuilder->shouldReceive('toSqlPart')->once()->with()->andReturn($SqlPart);

        $Schema = $Mapper->getSchema();
        $Schema->shouldReceive('getPdoAdapterByName')->once()->with('read')->andReturn($PdoAdapter);

        $Table = $Mapper->getTable();
        $Table->shouldReceive('getSchema')->once()->with()->andReturn($this->schema_name);
        $Table->shouldReceive('getName')->once()->with()->andReturn($this->table_name);
        $Table->shouldReceive('getPk')->once()->with()->andReturn($this->table_pk_column_name);

        $result = $Mapper->count($CriteriaBuilder);
        $this->assertEquals(123, $result);
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

        $PdoAdapter->shouldReceive('execute')->once()->with(
            'SELECT * FROM bar.foo t WHERE (id = :id_854230835) LIMIT 1',
            ['id_854230835' => $this->entity_id]
        )->andReturn($PdoStatement);

        $SqlPart = \Mockery::mock('Everon\DataMapper\Interfaces\SqlPart');
        $SqlPart->shouldReceive('getSql')->once()->with()->andReturn('WHERE (id = :id_854230835) LIMIT 1');
        $SqlPart->shouldReceive('getParameters')->once()->with()->andReturn(['id_854230835' => $this->entity_id]);

        $CriteriaBuilder = \Mockery::mock('Everon\DataMapper\Interfaces\Criteria\Builder');
        $CriteriaBuilder->shouldReceive('where')->once()->with($this->table_pk_column_name, '=', $this->entity_id)->andReturn($CriteriaBuilder);
        $CriteriaBuilder->shouldReceive('toSqlPart')->once()->with()->andReturn($SqlPart);
        $CriteriaBuilder->shouldReceive('setLimit')->once()->with(1);
        
        $Factory = $Mapper->getFactory();
        $Factory->shouldReceive('buildCriteriaBuilder')->once()->with()->andReturn($CriteriaBuilder);

        $result = $Mapper->fetchOneById($this->entity_id);

        $this->assertInternalType('array', $result);
        $this->assertEquals($this->entity_data, $result);
    }

    public function dataProvider()
    {
        $PdoAdapter = \Mockery::mock('Everon\Interfaces\PdoAdapter');
        
        $Schema = \Mockery::mock('Everon\DataMapper\Interfaces\Schema');
        $Schema->shouldReceive('getAdapterName')->once()->with()->andReturn('PostgreSql');

        $Table = \Mockery::mock('Everon\DataMapper\Schema\Table'); //no interface cause PHP and traits
            
        $Factory = $this->buildFactory();
        $Mapper = $Factory->buildDataMapper('Foo', $Table, $Schema, 'Everon\DataMapper');

        $FactoryMock = \Mockery::mock('Everon\Application\Interfaces\Factory'); //concrete class cause of interface inheritance and mocks problems
        $Mapper->setFactory($FactoryMock);
            
        return [
            [$Mapper, $PdoAdapter]
        ];
    }
}