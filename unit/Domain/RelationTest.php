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

use Everon\Domain;

class RelationTest extends \Everon\TestCase
{
    protected $owning_domain_name = 'Foo';
    protected $domain_name = 'Bar';

    /*
     * For ONE TO ONE and MANY TO MANY 
     * mapped_by = null means it's owning side
     * inverted_by = null means it's belonging to side
     * 
     * ONE TO MANY
     * Foo is owning side
     * Foo has many Bars
     * Bar has one Foo
     * 
     * Foo->id = Bar.foo_id
     * 
     * MANY TO ONE
     * Foo is owning side
     * Foo has many Bars
     * Bar has one Foo
     * 
     * Bar->foo_id = Foo.id 
     * 
     * MANY TO MANY
     * FooBarLog is another table where we record when particular foo and bar were linked together; only one of those links
     * can be marked as primary, hence the is_primary column.
     * 
     * The owning side is picked by convenience.
     * 
     * Foo has many Bars
     * Bar has many Foos
     * 
     * FooBarLog.id = autoincrement
     * FooBarLog.date_created = timestamp
     * FooBarLog.is_primary = bool
     * FooBarLog->foo_id = Foo.id
     * FooBarLog->bar_id = Bar.id
     * 
     */
    
    /*
    In Foo Entity (parent):
        'Bar' => [
            'type' => Domain\Relation::MANY_TO_ONE, //Many Bars belongs to one Foo
            'mapped_by' => 'id',
            'inversed_by' => 'foo_id'
        ],
        'BarOneToOneBelonging' => [
            'type' => Domain\Relation::ONE_TO_ONE, //One Bar belongs to one Foo
            'mapped_by' => null,
            'inversed_by' => 'foo_id',
            'column' => 'id',
        ],
        'FooBarLog' => [
            'type' => Domain\Relation::MANY_TO_MANY, //Many Bars belong to many Foos
            'mapped_by' => null,
            'inversed_by' => 'foo_id',
            'column' => 'id',
        ]
    
    In Bar Entity (child):
        'Foo' => [
            'type' => Domain\Relation::ONE_TO_MANY, //One Foo has many Bars
            'mapped_by' => 'foo_id',
            'inversed_by' => 'id'
        ],
        'FooOneToOneOwning' => [
            'type' => Domain\Relation::ONE_TO_ONE, //One Foo has one Bar
            'mapped_by' => 'foo_id',
            'inversed_by' => null,
            'column' => 'id'
        ],
        'FooBarLog' => [
            'type' => Domain\Relation::MANY_TO_MANY, //Many Foos have many Bars
            'mapped_by' => 'foo_id',
            'inversed_by' => null,
            'column' => 'id'
        ]    
     */

    public function testConstructor()
    {
        $OwnerEntity = \Mockery::mock('Everon\Domain\Interfaces\Entity');
        $RelationMapper = \Mockery::mock('Everon\Domain\Interfaces\RelationMapper');
        
        $OneToOne = new Domain\Relation\OneToOne($OwnerEntity, $RelationMapper);
        $OneToMany = new Domain\Relation\OneToMany($OwnerEntity, $RelationMapper);
        $ManyToOne = new Domain\Relation\OneToMany($OwnerEntity, $RelationMapper);
        $ManyToMany = new Domain\Relation\OneToMany($OwnerEntity, $RelationMapper);
        
        $this->assertInstanceOf('Everon\Domain\Interfaces\Relation', $OneToOne);
        $this->assertInstanceOf('Everon\Domain\Interfaces\Relation', $OneToMany);
        $this->assertInstanceOf('Everon\Domain\Interfaces\Relation', $ManyToOne);
        $this->assertInstanceOf('Everon\Domain\Interfaces\Relation', $ManyToMany);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testGetDataInManyToOne(\Everon\Interfaces\Factory $Factory)
    {
        //USER
        $UserColumn = \Mockery::mock('Everon\DataMapper\Schema\Column');
        $UserColumn->shouldReceive('isNullable')->once()->andReturn(true);
        
        $UserTable = \Mockery::mock('Everon\DataMapper\Schema\Table');
        //$FooTable->shouldReceive('getName')->once()->andReturn('foo');
        //$FooTable->shouldReceive('getFullName')->once()->andReturn('foo');
        //$FooTable->shouldReceive('getForeignKeys')->once()->andReturn([]);
        $UserTable->shouldReceive('validateId')->with(1)->once();

        $UserDataMapper = \Mockery::mock('Everon\Interfaces\DataMapper');
        //$FooDataMapper->shouldReceive('getTable')->once()->andReturn($FooTable);
        
        $UserRepository = \Mockery::mock('Everon\Domain\Interfaces\Repository');
        $UserRepository->shouldReceive('getMapper')->once()->andReturn($UserDataMapper);
        
        $UserForeignKey = \Mockery::mock('Everon\DataMapper\Schema\ForeignKey');
        $UserForeignKey->shouldReceive('getFullTableName')->once()->andReturn('s_users.user');

        //TICKET
        $TicketTable = \Mockery::mock('Everon\DataMapper\Schema\Table');
        //$TicketTable->shouldReceive('getName')->once()->andReturn('bar');
        $TicketTable->shouldReceive('getFullName')->once()->andReturn('s_misc.ticket');
        $TicketTable->shouldReceive('getForeignKeys')->twice()->andReturn(['user_id' => $UserForeignKey]);
        $TicketTable->shouldReceive('getColumnByName')->with('user_id')->once()->andReturn($UserColumn);

        $TicketDataMapper = \Mockery::mock('Everon\Interfaces\DataMapper');
        $TicketDataMapper->shouldReceive('getTable')->times(3)->andReturn($TicketTable);

        $TicketRepository = \Mockery::mock('Everon\Domain\Interfaces\Repository');
        $TicketRepository->shouldReceive('getMapper')->times(4)->andReturn($TicketDataMapper);

        //SCHEMA
        $SchemaMock = \Mockery::mock('Everon\DataMapper\Interfaces\Schema');
        $SchemaMock->shouldReceive('getTableByName')->with('s_users.user')->twice()->andReturn($UserTable);
        $SchemaMock->shouldReceive('getTableByName')->with('s_misc.ticket')->twice()->andReturn($TicketTable);

        //DATA MAPPERS
        $UserDataMapper->shouldReceive('getSchema')->once()->andReturn($SchemaMock);
        $TicketDataMapper->shouldReceive('getSchema')->once()->andReturn($SchemaMock);

        //OWNER ENTITY
        $OwnerEntity = \Mockery::mock('Everon\Domain\Interfaces\Entity');
        $OwnerEntity->shouldReceive('getDomainName')->twice()->andReturn('User');
        //$OwnerEntity->shouldReceive('getValueByName')->with('foo_id')->once()->andReturn(1);
        $OwnerEntity->shouldReceive('getValueByName')->with('id')->once()->andReturn(1);


        //FACTORY
        $FactoryMock = \Mockery::mock('Everon\Application\Interfaces\Factory');
        $FactoryMock->shouldReceive('buildDataMapper')->with('User', $UserTable, $SchemaMock)->once()->andReturn($UserDataMapper);
        $FactoryMock->shouldReceive('buildDataMapper')->with('Ticket', $TicketTable, $SchemaMock)->once()->andReturn($TicketDataMapper);
        $FactoryMock->shouldReceive('buildDomainRepository')->with('User', $UserDataMapper)->once()->andReturn($UserRepository);
        $FactoryMock->shouldReceive('buildDomainRepository')->with('Ticket', $TicketDataMapper)->once()->andReturn($TicketRepository);

        $TicketMapper = $Factory->buildDomainRelationMapper(Domain\Relation::MANY_TO_ONE, 'Ticket', null, 'id', 'user_id');

        $TicketRelationForUser = $Factory->buildDomainRelation('Ticket', $OwnerEntity, $TicketMapper);
        $TicketRelationForUser->getDomainManager()->getDataMapperManager()->setSchema($SchemaMock);
        $TicketRelationForUser->getDomainManager()->setFactory($FactoryMock);
        
        
        $this->assertInstanceOf('Everon\Domain\Interfaces\Relation', $TicketRelationForUser);
        
        $RelationData = $TicketRelationForUser->getData();
        $this->assertInstanceOf('Everon\Interfaces\Collection', $RelationData);
    }


    public function dataProvider()
    {
        $Factory = $this->buildFactory();
                    
        return [
            [$Factory]
        ];
    }
}