<?php
namespace Everon\Application;

use Everon\Exception;

class Factory extends \Everon\Factory implements Interfaces\Factory
{
    /**
     * @inheritdoc
     */
    public function buildTestApp()
    {
        $Application = parent::buildRestServer('Everon\Application');
        $this->getDependencyContainer()->register('ApplicationCore', function() use ($Application) {
            return $Application;
        });
        return $Application;
    }  
}