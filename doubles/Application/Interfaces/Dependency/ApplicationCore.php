<?php
namespace Everon\Application\Interfaces\Dependency;

use Everon\Application\Interfaces;

interface ApplicationCore
{
    /**
     * @return Interfaces\Core
     */
    function getApplicationCore();

    /**
     * @param Interfaces\Core $Core
     */
    function setApplicationCore(Interfaces\Core $Core);
}