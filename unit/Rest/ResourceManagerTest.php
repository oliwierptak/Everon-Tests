<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Test\Rest;

use Everon\Rest;

class ResourceManagerTest extends \Everon\TestCase
{
    /**
     * @var int
     */
    protected $entity_id = 1;

    /**
     * @var string
     */
    protected $current_version = 'v2';

    /**
     * @var string
     */
    protected $resource_id = '1';

    /**
     * @var string
     */
    protected $resource_name = 'foos';

    /**
     * @var string
     */
    protected $collection_name = null;

    /**
     * @var string
     */
    protected $domain_name = 'Foo';

    /**
     * @var string
     */
    protected $request_path = '';

    /**
     * @var string
     */
    protected $url = 'http://api.localhost:80/';

    /**
     * @var array
     */
    protected $supported_versions = ['v1', 'v2'];

    /**
     * @var string
     */
    protected $versioning = Rest\Resource\Handler::VERSIONING_URL;

    /**
     * @var array
     */
    protected $mappings = [
        'foos' => 'Foo',
        'bars' => 'Bars'
    ];
        
    public function testConstructor()
    {
        $Handler = new Rest\Resource\Manager($this->url, $this->supported_versions, $this->versioning, $this->mappings);
        $this->assertInstanceOf('Everon\Rest\Interfaces\ResourceManager', $Handler);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testBuildResourceFromEntity(Rest\Interfaces\ResourceManager $ResourceManager)
    {
        $RelationCollection = \Mockery::mock('Everon\Interfaces\Collection');
        
        $Entity = \Mockery::mock('Everon\Domain\Interfaces\Entity');
        $Entity->shouldReceive('getId')->once()->with()->andReturn($this->entity_id);
        $Entity->shouldReceive('getRelationCollection')->once()->with()->andReturn($RelationCollection);
        
        $Href = \Mockery::mock('Everon\Rest\Interfaces\ResourceHref');
        $Href->shouldReceive('setCollectionName')->once()->with($this->collection_name);
        $Href->shouldReceive('setResourceName')->once()->with($this->resource_name);
        $Href->shouldReceive('setResourceId')->once()->with($this->resource_id);
        $Href->shouldReceive('setRequestPath')->once()->with($this->request_path);

        //$RelationCollection = ;
        $Resource = \Mockery::mock('Everon\Rest\Interfaces\Resource');
        $Resource->shouldReceive('getDomainEntity')->once()->with()->andReturn($Entity); 
        //$Resource->shouldReceive('getHref')->once()->with()->andReturn($Href); 
        $Resource->shouldReceive('setRelationCollection')->once(); 
        
        $Factory = \Mockery::mock('Everon\Application\Interfaces\Factory');
        $Factory->shouldReceive('buildRestResourceHref')->once()
            ->with($this->url, $this->current_version, $this->versioning)
            ->andReturn($Href);
        $Factory->shouldReceive('buildRestResource')->once()
            ->with($this->domain_name, $this->current_version, $Href, $this->resource_name, $Entity)
            ->andReturn($Resource);

        $ResourceManager->setFactory($Factory);
        $ResourceManager->buildResourceFromEntity($Entity, $this->current_version, $this->resource_name);
    }

    public function dataProvider()
    {
        $Factory = $this->buildFactory();
        $Manager = $Factory->buildRestResourceManager($this->url, $this->supported_versions, $this->versioning, $this->mappings);
        
        return [
            [$Manager]
        ];
    }

}
