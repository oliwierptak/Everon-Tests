<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Test\DataMapper;

use Everon\DataMapper\Criteria;
use Everon\Interfaces;
use Everon\Helper;

class CriteriaBuilderTest extends \Everon\TestCase
{
    function testConstructor()
    {
        $Criteria = new \Everon\DataMapper\Criteria\Builder();
        $this->assertInstanceOf('Everon\DataMapper\Interfaces\Criteria\Builder', $Criteria);
    }

    /**
     * @dataProvider dataProvider
     */
    function testWhere(\Everon\DataMapper\Interfaces\Criteria\Builder $CriteriaBuilder)
    {
        $CriteriaBuilder->_or('id', 'in', [1,2,3])->_and('name', '!=', 'john');
        $CriteriaBuilder->_or('name', 'ilike', 'Neth')->_or('code', 'ilike', 'Neht');

        $CriteriaBuilder->_and(function($Criteria){
            $Criteria->_or('id', 'in', [1,2,3])->_and('name', '!=', 'john');
        });

        $CriteriaBuilder->_or(function($Criteria){
            $Criteria->_or('id', 'in', [1,2,3])->_and('name', '!=', 'john');
        });
    }

    function dataProvider()
    {
        $filter = [
            'where' => [],
            'limit' => 10,
            'offset' => 0,
        ];

        $CriteriaBuilder = new \Everon\DataMapper\Criteria\Builder();
        
        return [
            [$CriteriaBuilder, $filter]
        ];
    }

}
