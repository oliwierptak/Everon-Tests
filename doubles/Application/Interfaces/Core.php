<?php
namespace Everon\Application\Interfaces;


interface Core extends \Everon\Interfaces\Core
{
    /**
     * @return string
     */
    function getLocaleCode();

    /**
     * @return string
     */
    function getCountryCode();

    /**
     * @return string
     */
    function getCountryName();
}
