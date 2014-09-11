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
    For ONE TO ONE and MANY TO MANY 
    mapped_by = null means it's owning side
    inverted_by = null means it's belonging to side
    
    bidirectional/unidirectional aspect is defined by either lack or presence of the relation definition between
    owning and belonging to sides.
    
    The column property is used to change the name of the column the relation is referenced by.
    By default the primary key is used.
    
    In this example User is referenced by its id (primary key) in the Account table. The Account remembers its users
    in its user_id column.
    
    ONE TO ONE (User)
    User is owning side
    One User has one Account
    
    User->id = Account.user_id
    
    ONE TO ONE (Account)
    User is owning side
    One Account belongs to one User
    
    Account.user_id = User->id 
    
    
    ONE TO MANY (User)
    User is owning side
    One User has many Accounts
    
    User->id = Account.user_id
    
    
    MANY TO ONE (Account)
    User is owning side
    Many Accounts belong to one User
    
    Account->user_id = User.id 
    
    
    MANY TO MANY
    Student is the owning side
    Many Students have many Courses
    Many Courses belong to many Students
    
    Owning side is configured by mapped_by / inversed_by properties.
    mapped_by set to null means it's the owning side, inversed_by set to null means it's the belonging to side.
    The owning side is picked by convenience / requirement or it can be set by using unidirectional 
    relation definition.
    
    EXAMPLES:
    
    ONE TO ONE
    --------------------------------------------------------------------------------------------------------------------
    TABLES:
    
    User.id = int
    
    Account.user_id = User.id
 

    = One User has One Account 
     
    In User Entity:
        'Account' => [
            'type' => Domain\Relation::ONE_TO_ONE,
            'mapped_by' => null, //foreign key in User
            'inversed_by' => 'user_id', //Column in Account
            'column' => 'id', //Column in User (optional)
        ]
    
    
    = One Account belongs to One User (Account.user_id points to User.id)
    
    In Account Entity:
        'User' => [
            'type' => Domain\Relation::ONE_TO_ONE,
            'mapped_by' => 'user_id', //foreign key in Account
            'inversed_by' => 'id', //Column in User (optional)
        ]
    
    
   
    ONE TO MANY and MANY TO ONE
    --------------------------------------------------------------------------------------------------------------------
    TABLES:
    
    User.id = int
    
    Account.user_id = User.id
    
    
    = One User has Many Accounts 
     
    In User Entity:
        'Account' => [
            'type' => Domain\Relation::ONE_TO_MANY,
            'mapped_by' => null, //foreign key in User
            'inversed_by' => 'user_id', //Column in Account
            'column' => 'id' //Column in User
        ]
    
    
    = Many Accounts belongs to One User
    
    In Account Entity:
        'User' => [
            'type' => Domain\Relation::MANY_TO_ONE,
            'mapped_by' => 'user_id', //foreign key in Account
            'inversed_by' => 'id', //Column in User
        ]
    
    
        
    MANY TO MANY
    --------------------------------------------------------------------------------------------------------------------
    TABLES:
    
    Student.id = int
    
    Course.id = int
    
    StudentCourseLog.id = int
    StudentCourseLog->student_id = Student.id
    StudentCourseLog->course_id = Course.id
    StudentCourseLog.date_attended = timestamp
    StudentCourseLog.grade = int
    
    
    
    = Many Students have Many Courses 
     
    In Student Entity:
        'Course' => [
            'type' => Domain\Relation::MANY_TO_MANY,
            'mapped_by' => 'course_id', //Column in join_table
            'inversed_by' => 'id', //Column in Course
            'join_table' => 'StudentCourseLog' //SELECT * FROM Course WHERE Course.id IN (SELECT course_id FROM StudentCourseLog WHERE student_id = Student.id),
            'join_on' => 'student_id',
            'column' => 'id' //Column in Student
        ]
    
    
    = Many Courses belongs to Many Students
    
    In Course Entity:
        'Student' => [
            'type' => Domain\Relation::MANY_TO_MANY,
            'mapped_by' => 'student_id', //Column in join_table
            'inversed_by' => 'id', //Column in Student
            'join_table' => 'StudentCourseLog' //SELECT * FROM Student WHERE Student.id IN (SELECT student_id FROM StudentCourseLog WHERE course_id = Course.id),
             'join_on' => 'course_id',
            'column' => 'id' //Column in Course
        ]
    
    //$this->sql = $this->getDataMapper()->getJoinSql('t.*', 's_customers.customer', 's_customers.customer_user_rel cur', 't.id', 'cur.customer_id');

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

        //ACCOUNT
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