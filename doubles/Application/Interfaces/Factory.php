<?php
namespace Everon\Application\Interfaces;


interface Factory extends \Everon\Interfaces\Factory
{
    /**
     * @return \Everon\Interfaces\Core
     */
    function buildTestApp();
}
