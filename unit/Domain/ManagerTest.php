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
        $Manager = new \Everon\Domain\Manager($DataMapperManagerMock);
        $this->assertInstanceOf('Everon\Domain\Interfaces\Handler', $Manager);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testGetModelShouldReturnModel(\Everon\Domain\Interfaces\Manager $DomainManager)
    {
        $ModelMock = $this->getMock('Everon\Domain\Interfaces\Model');
        $FactoryMock = $this->getMock('Everon\Application\Interfaces\Factory');
        $FactoryMock->expects($this->once())
            ->method('buildDomainModel')
            ->will($this->returnValue($ModelMock));

        $DomainManager->setFactory($FactoryMock);
        $Model = $DomainManager->getModelByName('User');
        
        $this->assertInstanceOf('Everon\Domain\Interfaces\Model', $Model);
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
        $RepositoryMock = $this->getMock('Everon\Domain\Interfaces\Repository', [], [], '', false);
        $FactoryMock = $this->getMock('Everon\Application\Interfaces\Factory');

        $SchemaMock->expects($this->once())
            ->method('getTableByName')
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
        
        $Repository = $DomainManager->getRepositoryByName('User');
        
        $this->assertInstanceOf('Everon\Domain\Interfaces\Repository', $Repository);
    }
    
    public function dataProvider()
    {
        $DataMapperManagerMock = $this->getMock('Everon\DataMapper\Interfaces\Manager');
        $DomainMapperMock = $this->getMock('Everon\Domain\Interfaces\Mapper');
        
        $DomainMapperMock->expects($this->once())
            ->method('getTableName')
            ->will($this->returnValue('users'));
        
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
