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

use Everon\Domain;
use Everon\Domain\Interfaces;
use Everon\Helper;

class DataMapperTest extends \Everon\TestCase
{
    use Helper\Arrays;
    
    public function testConstructor()
    {
        $SchemaMock = $this->getMock('Everon\DataMapper\Interfaces\Schema');
        $TableMock = $this->getMock('Everon\DataMapper\Interfaces\Schema\Table', [], [],'', false);
        $DataMapper = new DataMapper\PostgreSql\User($TableMock, $SchemaMock);
        $this->assertInstanceOf('Everon\Interfaces\DataMapper', $DataMapper);
    }

    /**
     * @22dataProvider dataProvider
     */
    public function SKIPtestWithRealDatabase()
    {
        $Factory = $this->buildFactory();
        $DatabaseConfig = $Factory->getDependencyContainer()->resolve('ConfigManager')->getConfigByName('database');
        $ConnectionManager = $Factory->buildConnectionManager($DatabaseConfig);

        $Connection = $ConnectionManager->getConnectionByName('schema');
        list($dsn, $username, $password, $options) = $Connection->toPdo();
        $Pdo = $Factory->buildPdo($dsn, $username, $password, $options);
        $PdoAdapter = $Factory->buildPdoAdapter($Pdo, $Connection);
        $Reader = $Factory->buildSchemaReader($PdoAdapter);
        $Schema = $Factory->buildSchema($Reader, $ConnectionManager);
        $Table = $Schema->getTable('user');
        $Mapper = $Factory->buildDataMapper('User', $Table, $Schema);
        
        $entity_data = [
            'first_name' => 'John',
            'last_name' => 'Doe'
        ];
  
        $Entity = new \Everon\Test\Domain\User\Entity(null, $entity_data);
        $this->assertTrue($Entity->isNew());
        
        $id = $Mapper->add($Entity);
        $this->assertInstanceOf('Everon\Test\Domain\User\Entity', $Entity);
        
        $data = $Entity->toArray();
        $Entity->persist($id, $data);
        $Mapper->save($Entity);
        
        $fetched_data = $Mapper->fetchOne($id);
        $this->assertInternalType('array', $fetched_data);

        $Mapper->delete($Entity);
        $this->assertInstanceOf('Everon\Domain\Interfaces\Entity', $Entity);
        $this->assertNull($Entity->getId());
        
        $Criteria = new \Everon\DataMapper\CriteriaOLD([1=>1]);
        $all = $Mapper->fetchAll($Criteria);
        $this->assertInternalType('array', $all);
        $this->assertCount(10, $all);
    } 
    
    /**
     * @dataProvider dataProvider
     */
    public function testFetchOneShouldReturnArray(\Everon\Interfaces\DataMapper $Mapper, $PdoAdapterMock)
    {
        $entity_data = [
            'id' => 1,
            'first_name' => 'John',
            'last_name' => 'Doe'
        ];
        
        $PdoStatementMock = $this->getMock('\PDOStatement');
        $PdoStatementMock->expects($this->once())
            ->method('fetch')
            ->will($this->returnValue($entity_data));

        $PdoAdapterMock->expects($this->once())
            ->method('execute')
            ->will($this->returnValue($PdoStatementMock));

        $result = $Mapper->fetchOneById(1);

        $this->assertInternalType('array', $result);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testFetchAllShouldReturnArray(\Everon\Interfaces\DataMapper $Mapper, $PdoAdapterMock)
    {
        $PdoStatementMock = $this->getMock('\PDOStatement');
        $PdoStatementMock->expects($this->once())
            ->method('fetchAll')
            ->will($this->returnValue([]));

        $PdoAdapterMock->expects($this->once())
            ->method('execute')
            ->will($this->returnValue($PdoStatementMock));

        $Criteria = new \Everon\DataMapper\CriteriaOLD();
        
        $result = $Mapper->fetchAll($Criteria);
        
        $this->assertInternalType('array', $result);
    }

    public function dataProvider()
    {
        $PdoAdapterMock = $this->getMock('Everon\Interfaces\PdoAdapter', [], [], '', false);
        
        $IdColumnMock = $this->getMock('Everon\DataMapper\Interfaces\Schema\Column');
        $IdColumnMock->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('id'));
        $IdColumnMock->expects($this->any())
            ->method('isPk')
            ->will($this->returnValue(true));

        $NameColumnMock = $this->getMock('Everon\DataMapper\Interfaces\Schema\Column');
        $NameColumnMock->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('name'));
        $NameColumnMock->expects($this->any())
            ->method('isPk')
            ->will($this->returnValue(false));
        
        $SchemaMock = $this->getMock('Everon\DataMapper\Interfaces\Schema', [], [], '', false);
        $SchemaMock->expects($this->once())
            ->method('getDatabase')
            ->will($this->returnValue('everon_test'));
        $SchemaMock->expects($this->once())
            ->method('getDriver')
            ->will($this->returnValue('PostgreSql'));
        $SchemaMock->expects($this->once())
            ->method('getAdapterName')
            ->will($this->returnValue('PostgreSql'));
        $SchemaMock->expects($this->once())
            ->method('getPdoAdapterByName')
            ->will($this->returnValue($PdoAdapterMock));

        $TableMock = $this->getMock('Everon\DataMapper\Interfaces\Schema\Table', [],[], '', false);
        $TableMock->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('user'));
        $TableMock->expects($this->once())
            ->method('validateId')
            ->will($this->returnValue(1));
        $TableMock->expects($this->any())
            ->method('getColumns')
            ->will($this->returnValue(['id'=>$IdColumnMock, 'first_name' => $NameColumnMock]));
        $TableMock->expects($this->any())
            ->method('getPk')
            ->will($this->returnValue('id'));

        $Factory = $this->buildFactory();
        $Mapper = $Factory->buildDataMapper('User', $TableMock, $SchemaMock, 'Everon\Test\DataMapper');
        
        return [
            [$Mapper, $PdoAdapterMock]
        ];
    }
}