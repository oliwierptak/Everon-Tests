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

use Everon\Interfaces;

class ManagerTest extends \Everon\TestCase
{
    public function testConstructor()
    {
        $DataMapperManagerMock = $this->getMock('Everon\DataMapper\Interfaces\Manager');
        $Manager = new \Everon\Test\Domain\Manager($DataMapperManagerMock);
        $this->assertInstanceOf('Everon\Domain\Interfaces\Handler', $Manager);
    }

    /**
     * @dataProvider dataProvider
     */
    public function SKIP_testBuildEntityShouldReturnEntity(\Everon\Domain\Interfaces\Manager $DomainManager)
    {
        $TableMock = $this->getMock('Everon\DataMapper\Interfaces\Schema\Table');
        $TableMock->expects($this->once())
            ->method('getPk')
            ->will($this->returnValue('id'));
        
        $MapperMock = $this->getMock('Everon\Interfaces\DataMapper');
        $MapperMock->expects($this->exactly(0))
            ->method('getAndValidateId')
            ->will($this->returnValue(1));
        $MapperMock->expects($this->once())
            ->method('getTable')
            ->will($this->returnValue($TableMock));
        
        $RepositoryMock = $this->getMock('Everon\Domain\Interfaces\Repository');
        $RepositoryMock->expects($this->once())
            ->method('getName')
            ->will($this->returnValue('User'));
        $RepositoryMock->expects($this->exactly(1))
            ->method('getMapper')
            ->will($this->returnValue($MapperMock));
        $RepositoryMock->expects($this->once())
            ->method('buildEntityRelations');
        
        $EntityMock = $this->getMock('Everon\Domain\Interfaces\Entity');
        
        $FactoryMock = $this->getMock('Everon\Application\Interfaces\Factory');
        $FactoryMock->expects($this->once())
            ->method('buildDomainEntity')
            ->will($this->returnValue($EntityMock));
        
        $DomainManager->setFactory($FactoryMock);
        $Entity = $DomainManager->buildEntity($RepositoryMock, 1, []);
        
        $this->assertInstanceOf('Everon\Domain\Interfaces\Entity', $Entity);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testGetModelShouldReturnModel(\Everon\Domain\Interfaces\Manager $DomainManager)
    {
        $ModelMock = $this->getMock('Everon\Test\Domain\User\Model');
        $FactoryMock = $this->getMock('Everon\Application\Interfaces\Factory');
        $FactoryMock->expects($this->once())
            ->method('buildDomainModel')
            ->will($this->returnValue($ModelMock));

        $DomainManager->setFactory($FactoryMock);
        $Model = $DomainManager->getModel('User');
        
        $this->assertInstanceOf('Everon\Test\Domain\User\Model', $Model);
    }
    
    /**
     * @dataProvider dataProvider
     */
    public function testGetRepositoryShouldReturnRepository(\Everon\Domain\Interfaces\Manager $DomainManager)
    {
        $DataMapperManagerMock = $DomainManager->getDataMapperManager();
        $DataMapperMock = $this->getMock('Everon\Interfaces\DataMapper');
        $SchemaTableMock = $this->getMock('Everon\DataMapper\Interfaces\Schema\Table');
        $SchemaMock = $this->getMock('Everon\DataMapper\Interfaces\Schema');
        $RepositoryMock = $this->getMock('Everon\Test\Domain\User\Repository', [], [], '', false);
        $FactoryMock = $this->getMock('Everon\Application\Interfaces\Factory');

        $SchemaMock->expects($this->once())
            ->method('getTable')
            ->will($this->returnValue($SchemaTableMock));
        $DataMapperManagerMock->expects($this->once())
            ->method('getSchema')
            ->will($this->returnValue($SchemaMock));

        $FactoryMock->expects($this->once())
            ->method('buildDataMapper')
            ->will($this->returnValue($DataMapperMock));
        $FactoryMock->expects($this->once())
            ->method('buildDomainRepository')
            ->will($this->returnValue($RepositoryMock));

        $DomainManager->setFactory($FactoryMock);
        $DomainManager->setDataMapperManager($DataMapperManagerMock);
        
        $Repository = $DomainManager->getRepository('User');
        
        $this->assertInstanceOf('Everon\Test\Domain\User\Repository', $Repository);
    }
    
    public function dataProvider()
    {
        $DataMapperManagerMock = $this->getMock('Everon\DataMapper\Interfaces\Manager');
        $DomainMapperMock = $this->getMock('Everon\Domain\Interfaces\Mapper');
        
        $DomainMapperMock->expects($this->once())
            ->method('getDataMapperNameByDomain')
            ->will($this->returnValue('User'));
        
        $DataMapperManagerMock->expects($this->once())
            ->method('getDomainMapper')
            ->will($this->returnValue($DomainMapperMock));

        $DataMapperManagerMock->setDomainMapper($DomainMapperMock);
        
        $DomainManager = $this->buildFactory()->buildDomainManager($DataMapperManagerMock);
        
        return [
            [$DomainManager]
        ];
    }

}
