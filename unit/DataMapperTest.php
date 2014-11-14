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
        $DataMapper = new \Everon\DataMapper\PostgreSql\Foo($TableMock, $SchemaMock);
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
        $Schema = $Factory->buildSchema($Reader, $ConnectionManager,);
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
}