<?php
namespace Everon;

$CUSTOM_EXCEPTION_HANDLER = function(){}; //disable default exception handler

$nesting = implode('..', array_fill(0, 3, DIRECTORY_SEPARATOR));
$EVERON_ROOT =  realpath(dirname(__FILE__).$nesting).DIRECTORY_SEPARATOR;
$EVERON_SOURCE_ROOT = implode(DIRECTORY_SEPARATOR, [$EVERON_ROOT, 'vendor', 'everon', 'everon', 'src', 'Everon']).DIRECTORY_SEPARATOR;

require_once(
    implode(DIRECTORY_SEPARATOR,
        [$EVERON_SOURCE_ROOT, 'Config', '_bootstrap.php'])
);

/**
 * @var Bootstrap $Bootstrap
 * @var Interfaces\Environment $Environment
 * @var Interfaces\DependencyContainer $Container
 * @var Interfaces\Factory $Factory
 */

//cleanup global state after bootstrap, otherwise phpunit will complain, and $backupGlobalsBlacklist does not work
unset($CUSTOM_EXCEPTION_HANDLER);
unset($nesting);
unset($Factory);
unset($Container);
unset($Environment);
