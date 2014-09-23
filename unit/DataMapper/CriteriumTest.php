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

class CriteriumTest extends \Everon\TestCase
{
    protected $column = 'foo';
    
    protected $operator = '=';
    
    protected $value = 'bar';
    
    protected $glue = 'AND';
    
    
    function testConstructor()
    {
        $Criterium = new \Everon\DataMapper\Criteria\Criterium($this->column, $this->operator, $this->value);
        $this->assertInstanceOf('Everon\DataMapper\Interfaces\Criteria\Criterium', $Criterium);
    }

    function dataProvider()
    {
        $Criteria = new \Everon\DataMapper\Criteria\Criterium($this->column, $this->operator, $this->value);
        
        return [
            [$Criteria]
        ];
    }

}
