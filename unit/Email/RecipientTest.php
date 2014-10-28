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

class RecipientTest extends \Everon\TestCase
{
    public function testConstructor()
    {
        $AddressMock = $this->getMock('Everon\Email\Interfaces\Recipient', [], [], '', false);
        $Recipient = new Email\Recipient([$AddressMock]);
        $this->assertInstanceOf('Everon\Email\Interfaces\Recipient', $Recipient);
    }
    
    public function testToArray()
    {
        $Address = new Email\Address('test@grofas.com', 'Test');
        $Recipient = new Email\Recipient([$Address]);
        
        $data = $Recipient->toArray();
        
        $this->assertInternalType('array', $data);
        $this->assertNotEmpty($data);
    }
}