<?php
namespace Everon;

$CustomExceptionHandler = function(){}; //disable default exception handler

$nesting = implode('..', array_fill(0, 3, DIRECTORY_SEPARATOR));
$EVERON_ROOT =  realpath(dirname(__FILE__).$nesting).DIRECTORY_SEPARATOR;
$EVERON_SOURCE_ROOT = implode(DIRECTORY_SEPARATOR, [$EVERON_ROOT, 'vendor', 'everon', 'everon', 'src', 'Everon']).DIRECTORY_SEPARATOR;

@require_once($EVERON_ROOT.'vendor/autoload.php');

require_once(
    implode(DIRECTORY_SEPARATOR,
        [$EVERON_SOURCE_ROOT, 'Config', 'Bootstrap.php'])
);

/**
 * @var Bootstrap $Bootstrap
 * @var Interfaces\Environment $Environment
 * @var Interfaces\DependencyContainer $Container
 * @var Interfaces\Factory $Factory
 */
if ($Bootstrap->useEveronAutoload()) {
    $Bootstrap->getClassLoader()->add('Everon\DataMapper', $Environment->getDataMapper());
    $Bootstrap->getClassLoader()->add('Everon\Domain', $Environment->getDomain());
    $Bootstrap->getClassLoader()->add('Everon\Module', $Environment->getModule());
    $Bootstrap->getClassLoader()->add('Everon\Rest', $Environment->getRest());
}

//cleanup global state after bootstrap, otherwise phpunit will complain, and $backupGlobalsBlacklist does not work
unset($CustomExceptionHandler);
unset($nesting);
unset($Factory);
unset($Container);
unset($Environment);
