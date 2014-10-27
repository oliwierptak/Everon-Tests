<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Test\Domain;

use Everon\Domain\Foo\Repository;
use Everon\Domain\Interfaces;

class RepositoryTest extends \Everon\TestCase
{
    protected $entity_data = [
        'id' => 1,
        'first_name' => 'John',
        'last_name' => 'Doe'
    ];
    
    protected $entity_id = 1;
    
    protected $user_id = 1;
    
    public function testConstructor()
    {
        $DataMapperMock = $this->getMock('Everon\Interfaces\DataMapper');
        $Repository = new Repository('Foo', $DataMapperMock);
        $this->assertInstanceOf('Everon\Domain\Interfaces\Repository', $Repository);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testPersistShouldAddNewEntityAndMarkEntityAsPersisted(Repository $Repository)
    {
        $data_to_add = $this->entity_data;
        $data_to_add['id'] = null;
        
        $Entity = \Mockery::mock('Everon\Domain\Interfaces\Entity');
        $Entity->shouldReceive('isDeleted')->once()->with()->andReturn(false);
        $Entity->shouldReceive('toArray')->once()->with()->andReturn($data_to_add);
        $Entity->shouldReceive('isNew')->twice()->with()->andReturn(true);
        $Entity->shouldReceive('persist')->with($this->entity_data)->once();
        $Entity->shouldReceive('isPersisted')->once()->with()->andReturn(true);

        $Table = \Mockery::mock('Everon\DataMapper\Schema\Table\Foo');
        $Table->shouldReceive('prepareDataForSql')->once()->with($data_to_add, false)->andReturn($data_to_add);
        
        $Mapper = $Repository->getMapper();
        $Mapper->shouldReceive('add')->once()->with($data_to_add, $this->user_id)->andReturn($this->entity_data);
        $Mapper->shouldReceive('getTable')->once()->with()->andReturn($Table);
        
        $Repository->persist($Entity, $this->user_id);
        
        $this->assertTrue($Entity->isPersisted());
    }

    /**
     * @dataProvider dataProvider
     */
    public function testPersistShouldSaveNewEntityAndMarkEntityAsPersisted(Repository $Repository)
    {
        $Entity = \Mockery::mock('Everon\Domain\Interfaces\Entity');
        $Entity->shouldReceive('isDeleted')->once()->with()->andReturn(false);
        $Entity->shouldReceive('toArray')->once()->with()->andReturn($this->entity_data);
        $Entity->shouldReceive('isNew')->twice()->with()->andReturn(false);
        $Entity->shouldReceive('persist')->with($this->entity_data)->once();
        $Entity->shouldReceive('isPersisted')->once()->with()->andReturn(true);

        $Table = \Mockery::mock('Everon\DataMapper\Schema\Table\Foo');
        $Table->shouldReceive('prepareDataForSql')->with($this->entity_data, true)->once()->andReturn($this->entity_data);

        $Mapper = $Repository->getMapper();
        $Mapper->shouldReceive('save')->once()->with($this->entity_data, $this->user_id);
        $Mapper->shouldReceive('getTable')->once()->with()->andReturn($Table);

        $Repository->persist($Entity, $this->user_id);

        $this->assertTrue($Entity->isPersisted());
    }

    /**
     * @dataProvider dataProvider
     */
    public function testRemoveShouldDeleteEntityAndMarkEntityAsDeleted(Repository $Repository)
    {
        $Entity = \Mockery::mock('Everon\Domain\Interfaces\Entity');
        $Entity->shouldReceive('isNew')->once()->with()->andReturn(false);
        $Entity->shouldReceive('isDeleted')->once()->with()->andReturn(false);
        $Entity->shouldReceive('delete')->once()->with();
        $Entity->shouldReceive('isDeleted')->once()->with()->andReturn(true);
        $Entity->shouldReceive('getId')->once()->with()->andReturn($this->entity_id);

        $Mapper = $Repository->getMapper();
        $Mapper->shouldReceive('delete')->once()->with($this->entity_id, $this->user_id);

        $Repository->remove($Entity, $this->user_id);

        $this->assertTrue($Entity->isDeleted());
    }

    /**
     * @dataProvider dataProvider
     */
    public function testGetEntityByIdShouldReturnEntity(Repository $Repository)
    {
        //$RelationCollection = new \Everon\Helper\Collection([]);
        
        $Entity = \Mockery::mock('Everon\Domain\Interfaces\Entity');
        $Entity->shouldReceive('getRelationDefinition')->once()->with()->andReturn([]);
        $Entity->shouldReceive('setRelationCollection')->once(); //->with($RelationCollection); can't get it to work

        $CriteriaBuilder = \Mockery::mock('Everon\DataMapper\Interfaces\Criteria\Builder');
        $CriteriaBuilder->shouldReceive('where')->once()->with('id', '=', 1)->andReturn([]);
        $CriteriaBuilder->shouldReceive('setLimit')->once()->with(1);
        $CriteriaBuilder->shouldReceive('setOffset')->once()->with(0);

        $Factory = \Mockery::mock('Everon\Application\Interfaces\Factory');
        $Factory->shouldReceive('buildDomainEntity')->once()->with("Foo", "id", $this->entity_data)->andReturn($Entity);
        $Factory->shouldReceive('buildCriteriaBuilder')->twice()->with()->andReturn($CriteriaBuilder);

        $IdColumnMock = \Mockery::mock('Everon\DataMapper\Interfaces\Schema\Column');
        $IdColumnMock->shouldReceive('isPk')->times(1)->with()->andReturn(true);
        $IdColumnMock->shouldReceive('getColumnDataForEntity')->once()->with($this->entity_id)->andReturn($this->entity_id);

        $FirstNameColumnMock = \Mockery::mock('Everon\DataMapper\Interfaces\Schema\Column');
        $FirstNameColumnMock->shouldReceive('isPk')->times(1)->with()->andReturn(false);
        $FirstNameColumnMock->shouldReceive('getColumnDataForEntity')->once()->with($this->entity_data['first_name'])->andReturn($this->entity_data['first_name']);

        $LastNameColumnMock = \Mockery::mock('Everon\DataMapper\Interfaces\Schema\Column');
        $LastNameColumnMock->shouldReceive('isPk')->times(1)->with()->andReturn(false);
        $LastNameColumnMock->shouldReceive('getColumnDataForEntity')->once()->with($this->entity_data['last_name'])->andReturn($this->entity_data['last_name']);

        $Table = \Mockery::mock('Everon\DataMapper\Interfaces\Schema\Table\Foo');
        $Table->shouldReceive('getPk')->once()->with()->andReturn('id');
        $Table->shouldReceive('getPk')->once()->with()->andReturn('id');
        $Table->shouldReceive('getColumns')->twice()->with()->andReturn(['id'=>$IdColumnMock, 'first_name' => $FirstNameColumnMock, 'last_name' => $LastNameColumnMock]);

        $Mapper = $Repository->getMapper();
        $Mapper->shouldReceive('getTable')->once()->with()->andReturn($Table);
        $Mapper->shouldReceive('fetchOneByCriteria')->once()->with($CriteriaBuilder)->andReturn($this->entity_data);

        $Repository->setFactory($Factory);

        $result = $Repository->getEntityById($this->entity_id);
        
        $this->assertInstanceOf('Everon\Domain\Interfaces\Entity', $result);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testBuildFromArrayShouldReturnEntity(Repository $Repository)
    {
        $Entity = \Mockery::mock('Everon\Domain\Interfaces\Entity');
        $Entity->shouldReceive('getRelationDefinition')->once()->with()->andReturn([]);
        $Entity->shouldReceive('setRelationCollection')->once();

        $CriteriaBuilder = \Mockery::mock('Everon\DataMapper\Interfaces\Criteria\Builder');

        $Factory = \Mockery::mock('Everon\Application\Interfaces\Factory');
        $Factory->shouldReceive('buildDomainEntity')->once()->with("Foo", "id", $this->entity_data)->andReturn($Entity);
        $Factory->shouldReceive('buildCriteriaBuilder')->once()->with()->andReturn($CriteriaBuilder);

        $IdColumnMock = \Mockery::mock('Everon\DataMapper\Interfaces\Schema\Column');
        $IdColumnMock->shouldReceive('isPk')->times(1)->with()->andReturn(true);
        $IdColumnMock->shouldReceive('getColumnDataForEntity')->once()->with($this->entity_id)->andReturn($this->entity_id);

        $FirstNameColumnMock = \Mockery::mock('Everon\DataMapper\Interfaces\Schema\Column');
        $FirstNameColumnMock->shouldReceive('isPk')->times(1)->with()->andReturn(false);
        $FirstNameColumnMock->shouldReceive('getColumnDataForEntity')->once()->with($this->entity_data['first_name'])->andReturn($this->entity_data['first_name']);

        $LastNameColumnMock = \Mockery::mock('Everon\DataMapper\Interfaces\Schema\Column');
        $LastNameColumnMock->shouldReceive('isPk')->times(1)->with()->andReturn(false);
        $LastNameColumnMock->shouldReceive('getColumnDataForEntity')->once()->with($this->entity_data['last_name'])->andReturn($this->entity_data['last_name']);

        $Table = \Mockery::mock('Everon\DataMapper\Interfaces\Schema\Table\Foo');
        $Table->shouldReceive('getPk')->once()->with()->andReturn('id');
        $Table->shouldReceive('getColumns')->twice()->with()->andReturn(['id'=>$IdColumnMock, 'first_name' => $FirstNameColumnMock, 'last_name' => $LastNameColumnMock]);

        $Mapper = $Repository->getMapper();
        $Mapper->shouldReceive('getTable')->once()->with()->andReturn($Table);
        $Mapper->shouldReceive('fetchOneByCriteria')->once()->with($CriteriaBuilder)->andReturn($this->entity_data);

        $Repository->setFactory($Factory);

        $result = $Repository->buildFromArray($this->entity_data);

        $this->assertInstanceOf('Everon\Domain\Interfaces\Entity', $result);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testPersistFromArrayShouldPersistEntity(Repository $Repository)
    {
        $Entity = \Mockery::mock('Everon\Domain\Interfaces\Entity');
        $Entity->shouldReceive('getRelationDefinition')->once()->with()->andReturn([]);
        $Entity->shouldReceive('setRelationCollection')->once();
        $Entity->shouldReceive('isDeleted')->once()->with()->andReturn(false);
        $Entity->shouldReceive('toArray')->once()->with()->andReturn($this->entity_data);
        $Entity->shouldReceive('isNew')->twice()->with()->andReturn(false);
        $Entity->shouldReceive('persist')->with($this->entity_data)->once();
        $Entity->shouldReceive('isPersisted')->once()->with()->andReturn(true);

        $CriteriaBuilder = \Mockery::mock('Everon\DataMapper\Interfaces\Criteria\Builder');

        $Factory = \Mockery::mock('Everon\Application\Interfaces\Factory');
        $Factory->shouldReceive('buildDomainEntity')->once()->with("Foo", "id", $this->entity_data)->andReturn($Entity);
        $Factory->shouldReceive('buildCriteriaBuilder')->once()->with()->andReturn($CriteriaBuilder);

        $IdColumnMock = \Mockery::mock('Everon\DataMapper\Interfaces\Schema\Column');
        $IdColumnMock->shouldReceive('isPk')->times(1)->with()->andReturn(true);
        $IdColumnMock->shouldReceive('getColumnDataForEntity')->once()->with($this->entity_id)->andReturn($this->entity_id);

        $FirstNameColumnMock = \Mockery::mock('Everon\DataMapper\Interfaces\Schema\Column');
        $FirstNameColumnMock->shouldReceive('isPk')->times(1)->with()->andReturn(false);
        $FirstNameColumnMock->shouldReceive('getColumnDataForEntity')->once()->with($this->entity_data['first_name'])->andReturn($this->entity_data['first_name']);

        $LastNameColumnMock = \Mockery::mock('Everon\DataMapper\Interfaces\Schema\Column');
        $LastNameColumnMock->shouldReceive('isPk')->times(1)->with()->andReturn(false);
        $LastNameColumnMock->shouldReceive('getColumnDataForEntity')->once()->with($this->entity_data['last_name'])->andReturn($this->entity_data['last_name']);

        $Table = \Mockery::mock('Everon\DataMapper\Interfaces\Schema\Table\Foo');
        $Table->shouldReceive('getPk')->once()->with()->andReturn('id');
        $Table->shouldReceive('getColumns')->twice()->with()->andReturn(['id'=>$IdColumnMock, 'first_name' => $FirstNameColumnMock, 'last_name' => $LastNameColumnMock]);
        $Table->shouldReceive('prepareDataForSql')->with($this->entity_data, true)->once()->andReturn($this->entity_data);
        
        $Mapper = $Repository->getMapper();
        $Mapper->shouldReceive('save')->once()->with($this->entity_data, $this->user_id);
        $Mapper->shouldReceive('getTable')->once()->with()->andReturn($Table);
        $Mapper->shouldReceive('fetchOneByCriteria')->once()->with($CriteriaBuilder)->andReturn($this->entity_data);

        $Repository->setFactory($Factory);

        $result = $Repository->persistFromArray($this->entity_data, $this->user_id);

        $this->assertInstanceOf('Everon\Domain\Interfaces\Entity', $result);
        $this->assertTrue($Entity->isPersisted());
    }
    
    /**
     * @dataProvider dataProvider
     */
    public function testGetNameShouldReturnClassName(Repository $Repository)
    {
        $this->assertEquals('Foo', $Repository->getName());
    }

   
    public function dataProvider()
    {
        $DataMapperMock = \Mockery::mock('Everon\Interfaces\DataMapper');

        $Factory = $this->buildFactory();
        $Container = $Factory->getDependencyContainer();
        $DomainManagerMock = \Mockery::mock('Everon\Domain\Interfaces\Manager');
        $Container->register('DomainManager', function() use ($DomainManagerMock) {
            return $DomainManagerMock;
        });
        
        $Repository = $Factory->buildDomainRepository('Foo', $DataMapperMock, 'Everon\Domain');
        
        return [
            [$Repository]
        ];
    }
}