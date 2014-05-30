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

class MessageTest extends \Everon\TestCase
{
    public function testConstructor()
    {
        $FromMock = $this->getMock('Everon\Email\Interfaces\Address', [], [], '', false);
        $RecipientMock = $this->getMock('Everon\Email\Interfaces\Recipient', [], [], '', false);
        $Message = new Email\Message($RecipientMock, $FromMock, 'subject', 'html body');
        $this->assertInstanceOf('Everon\Email\Interfaces\Message', $Message);
    }
    
    public function testToArray()
    {
        $Address = new Email\Address('test@grofas.com', 'Test');
        $Recipient = new Email\Recipient([$Address]);
        $Message = new Email\Message($Recipient, $Address, 'subject', 'html body');
        
        $data = $Message->toArray();
        
        $this->assertInternalType('array', $data);
        $this->assertNotEmpty($data);
    }
}