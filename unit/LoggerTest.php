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

//todo: use chain of command to store log in different places: disk, memory, email, etc
class LoggerTest extends \Everon\TestCase
{

    public function testConstructor()
    {
        $Logger = new \Everon\Logger($this->getLogDirectory(), true);
        $this->assertInstanceOf('\Everon\Interfaces\Logger', $Logger);
    }


    protected function getLogDirectory()
    {
        return $this->getTmpDirectory().'logs'.DIRECTORY_SEPARATOR;
    }

    protected function setUp()
    {
        $Logger = new \Everon\Logger($this->getLogDirectory(), true);
        
        foreach ($Logger->getLogFiles() as $level => $filename) {
            $log_file = $this->getLogDirectory().$filename;
            if (is_file($log_file)) {
                unlink($log_file);
            }
        }
    }
    /**
     * @dataProvider dataProvider
     */
    public function testSetGetFiles(\Everon\Interfaces\Logger $Logger)
    {
        $files = [
            'error' => 'test-everon-error.log',
        ];
        
        $Logger->setLogFiles($files);
        $this->assertCount(1, $Logger->getLogFiles());
    }

    /**
     * @dataProvider dataProvider
     */
    public function testWriting(\Everon\Interfaces\Logger $Logger)
    {
        $dates = [
            'warning' => $Logger->warn('warning'),
            'error' => $Logger->error('error'),
            'debug' => $Logger->debug('debug'),
            'trace' => $Logger->trace(new \Exception('trace')),
            'critical' => $Logger->critical('critical'),
            'notFound' => $Logger->notFound('notFound')
        ];
        
        foreach ($dates as $log_time) {
            $this->assertInstanceOf('DateTime', $log_time);
        }
    }
    
    public function dataProvider()
    {
        $Factory = $this->buildFactory();
        $Logger = $Factory->buildLogger($this->getLogDirectory(), true);
        
        return [
            [$Logger]
        ];
    }

}
