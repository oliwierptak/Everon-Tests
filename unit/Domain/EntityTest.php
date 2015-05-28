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
use Everon\Domain\Interfaces;
use Everon\Domain\Foo\Entity;

class EntityTest extends \Everon\TestCase
{
    function testConstructor()
    {
        $data = [
            'id' => 1,
            'first_name' => 'John',
            'last_name' => 'Doe',
            'date_of_birth' => '1990-09-09',
        ];
        $Entity = new \Everon\Domain\Foo\Entity('id', $data);
        
        $this->assertInstanceOf('Everon\Domain\Interfaces\Entity', $Entity);
        $this->assertEquals(1, $Entity->getId());
    }

    function testEntityStateShouldBeNewWhenIdNotSet()
    {
        $data = [
            'id' => null,
            'first_name' => 'John',
            'last_name' => 'Doe',
            'date_of_birth' => '1990-09-09',
        ];
        
        $Factory = $this->buildFactory();
        $Entity = $Factory->buildDomainEntity('Foo', 'id', $data, 'Everon\Domain');
        
        $this->assertNull($Entity->getId());
        $this->assertTrue($Entity->isNew());
        $this->assertFalse($Entity->isDeleted());
        $this->assertFalse($Entity->isModified());
        $this->assertFalse($Entity->isPersisted());
    }

    /**
     * @dataProvider dataProvider
     */
    function testStateShouldBePersistedWhenIdIsSet(Entity $Entity, array $data)
    {
        $this->assertNotNull($Entity->getId());
        $this->assertTrue($Entity->isPersisted());
        $this->assertFalse($Entity->isNew());
        $this->assertFalse($Entity->isDeleted());
        $this->assertFalse($Entity->isModified());
    }
    
    /**
     * @dataProvider dataProvider
     */
    function testShouldReturnValueByGetter(Entity $Entity, array $data)
    {
        $this->assertEquals($data['first_name'], $Entity->getFirstName());
        $this->assertEquals($data['date_of_birth'], $Entity->getDateOfBirth());
    }
    
    /**
     * @dataProvider dataProvider
     */
    function testShouldMarkModifiedProperties(Entity $Entity, array $data)
    {
        $this->assertEquals($data['first_name'], $Entity->getFirstName());
        $this->assertEquals($data['last_name'], $Entity->getLastName());
        
        $Entity->setFirstName('Tom');
        $Entity->setLastName('Smith');

        $this->assertEquals('Tom', $Entity->getFirstName());
        $this->assertEquals('Smith', $Entity->getLastName());
        
        $this->assertNotEmpty($Entity->getModifiedProperties());
        $this->assertCount(2, $Entity->getModifiedProperties());
        
        $this->assertTrue($Entity->isPropertyModified('first_name'));
        $this->assertFalse($Entity->isPropertyModified('date_of_birth'));
    }
    
    /**
     * @dataProvider dataProvider
     */
    function testGetValueByNameShouldReturnValue(Entity $Entity, array $data)
    {
        $this->assertEquals($data['first_name'], $Entity->getValueByName('first_name'));
        $this->assertEquals($data['last_name'], $Entity->getValueByName('last_name'));
    }

    /**
     * @dataProvider dataProvider
     */
    function testSetValueByNameShouldSetValue(Entity $Entity, array $data)
    {
        $Entity->setValueByName('first_name', 'Tom');
        $Entity->setValueByName('last_name', 'Smith');
        
        $this->assertEquals('Tom', $Entity->getFirstName());
        $this->assertEquals('Smith', $Entity->getLastName());
    }
    
    /**
     * @dataProvider dataProvider
     */
    function testDeleteShouldResetStateAndMarkAsDeleted(Entity $Entity, array $data)
    {
        $Entity->delete();
        
        $this->assertNull($Entity->getId());
        $this->assertNull($Entity->getModifiedProperties());
        $this->assertEmpty($Entity->getData());
        $this->assertTrue($Entity->isDeleted());
    }

    /**
     * @dataProvider dataProvider
     */
    function testPersistShouldSetIdAndDataAndMarkAsPersisted(Entity $Entity, array $data)
    {
        $Entity->persist($data);

        $this->assertEquals(1, $Entity->getId());
        $this->assertNull($Entity->getModifiedProperties());
        $this->assertEquals($data, $Entity->getData());
        $this->assertTrue($Entity->isPersisted());
    }

    /**
     * @dataProvider dataProvider
     * @expectedException \Everon\Domain\Exception\Entity
     * @expectedExceptionMessage It's the job of the database to maintain its primary keys.
     */
    function testSetIdShouldThrowException(Entity $Entity, array $data)
    {
        $Entity->setId(1);
    }
    
    /**
     * @dataProvider dataProvider
     * @expectedException \Everon\Domain\Exception\Entity
     * @expectedExceptionMessage Invalid property name: i dont exist
     */
    function testGetValueByNameShouldThrowExceptionWhenKeyDoesNotExist(Entity $Entity, array $data)
    {
        $Entity->getValueByName('i dont exist');
    }
    
    function dataProvider()
    {
        $data = [
            'id' => 1,
            'first_name' => 'John',
            'last_name' => 'Doe',
            'date_of_birth' => '1990-09-09',
        ];
        
        $Factory = $this->buildFactory();
        $Entity = $Factory->buildDomainEntity('Foo', 'id', $data, 'Everon\Domain');
                    
        return [
            [$Entity, $data]
        ];
    }
}