<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Test;

use Everon\Interfaces;
use Everon\Email;

class AddressTest extends \Everon\TestCase
{
    public function testConstructor()
    {
        $Recipient = new Email\Address('test@grofas.com', 'Test');
        $this->assertInstanceOf('Everon\Email\Interfaces\Address', $Recipient);
    }
    
    public function testToArray()
    {
        $Address = new Email\Address('test@grofas.com', 'Test');

        $data = $Address->toArray();

        $this->assertInternalType('array', $data);
        $this->assertNotEmpty($data);
    }
}