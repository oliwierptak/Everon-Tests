<?php
namespace Everon\Application\Dependency;


trait ApplicationCore
{
    /**
     * @var \Everon\Application\Interfaces\Core
     */
    protected $ApplicationCore = null;


    /**
     * @return \Everon\Application\Interfaces\Core
     */
    public function getApplicationCore()
    {
        return $this->ApplicationCore;
    }

    /**
     * @param \Everon\Application\Interfaces\Core $ApplicationCore
     */
    public function setApplicationCore(\Everon\Application\Interfaces\Core $ApplicationCore)
    {
        $this->ApplicationCore = $ApplicationCore;
    }

}
