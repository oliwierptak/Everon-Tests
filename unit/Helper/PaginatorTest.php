<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Test\Helper;

use Everon\Interfaces;

class PaginatorTest extends \Everon\TestCase
{
    public function testConstructor()
    {
        $Paginator = new \Everon\Helper\Paginator(30, 0, 10);
        $this->assertInstanceOf('Everon\Interfaces\Paginator', $Paginator);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testGetCurrentPageShouldReturnValidPage(Interfaces\Paginator $Paginator)
    {
        $this->assertEquals(1, $Paginator->getCurrentPage());
        
        $Paginator->setOffset(5);
        $Paginator->setLimit(5);

        $this->assertEquals(2, $Paginator->getCurrentPage());
    }

    /**
     * @dataProvider dataProvider
     */
    public function testGetPageCountShouldReturnValidPageCount(Interfaces\Paginator $Paginator)
    {
        $this->assertEquals(3, $Paginator->getPageCount());

        $Paginator->setOffset(5);
        $Paginator->setLimit(5);

        $this->assertEquals(6, $Paginator->getPageCount());
    }

    /**
     * @dataProvider dataProvider
     */
    public function testSetCurrentPageShouldUpdateOffset(Interfaces\Paginator $Paginator)
    {
        $this->assertEquals(1, $Paginator->getCurrentPage());
        
        $Paginator->setCurrentPage(2);

        $this->assertEquals(10, $Paginator->getOffset());
        $this->assertEquals(2, $Paginator->getCurrentPage());
    }

    /**
     * @dataProvider dataProvider
     */
    public function testGetLimitShouldFallbackToDefaultWhenInvalid(Interfaces\Paginator $Paginator)
    {
        $this->assertEquals(3, $Paginator->getPageCount());

        $Paginator->setLimit(-1);

        $this->assertEquals(3, $Paginator->getPageCount());
        $this->assertEquals(1, $Paginator->getCurrentPage());
    }

    /**
     * @dataProvider dataProvider
     */
    public function testSetOffsetShouldNotBeBiggerThenTotal(Interfaces\Paginator $Paginator)
    {
        $this->assertEquals(3, $Paginator->getPageCount());

        $Paginator->setOffset(100);

        $this->assertEquals(3, $Paginator->getPageCount());
        $this->assertEquals(3, $Paginator->getCurrentPage());
    }

    /**
     * @dataProvider dataProvider
     */
    public function testSetOffsetShouldNotBeLessThenZero(Interfaces\Paginator $Paginator)
    {
        $this->assertEquals(3, $Paginator->getPageCount());

        $Paginator->setOffset(-100);

        $this->assertEquals(3, $Paginator->getPageCount());
        $this->assertEquals(1, $Paginator->getCurrentPage());
    }

    public function dataProvider()
    {
        $Paginator = new \Everon\Helper\Paginator(30, 0, 10);

        return [
            [$Paginator]
        ];
    }

}