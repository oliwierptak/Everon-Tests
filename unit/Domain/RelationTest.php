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
    protected $owning_domain_name = 'User';
    protected $domain_name = 'Account';

    /*
     * For ONE TO ONE and MANY TO MANY 
     * mapped_by = null means it's owning side
     * inverted_by = null means it's belonging to side
     * 
     * bidirectional/unidirectional aspect is defined by either lack or presence of the relation definition between
     * owning and belonging to sides.
     * 
     * ONE TO ONE (bidirectional)
     * User is owning side
     * One User has one Account
     * 
     * User->id = Account.user_id
     * 
     * ONE TO ONE (bidirectional)
     * User is owning side
     * One Account belongs to one User
     * 
     * Account.user_id = User->id 
     * 
     * 
     * ONE TO MANY (bidirectional)
     * User is owning side
     * One User has many Accounts
     * 
     * User->id = Account.user_id
     * 
     * 
     * MANY TO ONE (bidirectional)
     * User is owning side
     * Many Accounts belong to one User
     * 
     * Account->user_id = User.id 
     * 
     * 
     * MANY TO MANY
     * Student is the owning side
     * Many Students have many Courses
     * Many Courses belong to many Students
     * 
     * Owning side is configured by mapped_by / inversed_by properties.
     * mapped_by set to null means it's the owning side, inversed_by set to null means it's the belonging to side.
     * The owning side is picked by convenience / requirement or it can be set by using unidirectional 
     * relation definition.
     * 
     * Example of join table:
     * 
     * StudentCourseLog.id = autoincrement
     * StudentCourseLog->student_id = Student.id
     * StudentCourseLog->course_id = Course.id
     * StudentCourseLog.date_attended = timestamp
     * StudentCourseLog.grade = int
     * 
     */
    
    /*
    In User Entity (parent):
        'Account' => [
            'type' => Domain\Relation::MANY_TO_ONE, //Many Accounts belongs to one User
            'mapped_by' => 'id',
            'inversed_by' => 'user_id'
        ],
        'AccountOneToOneBelonging' => [
            'type' => Domain\Relation::ONE_TO_ONE, //One Account belongs to one User
            'mapped_by' => null,
            'inversed_by' => 'user_id',
            'column' => 'id',
        ],
        'AccountManyToManyBelonging' => [
            'type' => Domain\Relation::MANY_TO_MANY, //Many Accounts belong to many Users
            'mapped_by' => null,
            'inversed_by' => 'user_id',
            'column' => 'id',
        ],
        'AccountManyToManyWithJoinTableOwning' => [
            'type' => Domain\Relation::MANY_TO_MANY, //Many Accounts belong to many Users
            'mapped_by' => null,
            'inversed_by' => 'user_id',
            'column' => 'id',
            'join_tables' => [
                [ 'name' => '' ] 
            ]
        ]
    
    In Account Entity (child):
        'User' => [
            'type' => Domain\Relation::ONE_TO_MANY, //One User has many Accounts
            'mapped_by' => 'user_id',
            'inversed_by' => 'id'
        ],
        'UserOneToOneOwning' => [
            'type' => Domain\Relation::ONE_TO_ONE, //One User has one Account
            'mapped_by' => 'user_id',
            'inversed_by' => null,
            'column' => 'id'
        ],
        'UserManyToManyOwning' => [
            'type' => Domain\Relation::MANY_TO_MANY, //Many Users have many Accounts
            'mapped_by' => 'user_id',
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
        //$UserTable->shouldReceive('getName')->once()->andReturn('foo');
        //$UserTable->shouldReceive('getFullName')->once()->andReturn('foo');
        //$UserTable->shouldReceive('getForeignKeys')->once()->andReturn([]);
        $UserTable->shouldReceive('validateId')->with(1)->once();

        $UserDataMapper = \Mockery::mock('Everon\Interfaces\DataMapper');
        //$UserDataMapper->shouldReceive('getTable')->once()->andReturn($UserTable);
        
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
        //$OwnerEntity->shouldReceive('getValueByName')->with('user_id')->once()->andReturn(1);
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