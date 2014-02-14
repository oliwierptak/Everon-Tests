<?php
namespace Everon;

$CustomExceptionHandler = function(){}; //disable default exception handler

require_once(
    implode(DIRECTORY_SEPARATOR,
        [dirname(__FILE__), '..', '..', 'Config', 'Bootstrap', 'mvc.php'])  
);

//cleanup global state after bootstrap, otherwise phpunit will complain, and $backupGlobalsBlacklist does not work
unset($CustomExceptionHandler);
unset($nesting);
unset($Factory);
unset($Container);
unset($Environment);