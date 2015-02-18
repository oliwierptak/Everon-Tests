<?php
namespace Everon;

if (defined('EVERON_ENVIRONMENT') === false) {
    define('EVERON_ENVIRONMENT', 'development');
}

$CUSTOM_EXCEPTION_HANDLER = function(){}; //disable default exception handler

$nesting = implode('..', array_fill(0, 3, DIRECTORY_SEPARATOR));
$EVERON_ROOT =  realpath(dirname(__FILE__).$nesting).DIRECTORY_SEPARATOR;
$EVERON_SOURCE_ROOT = implode(DIRECTORY_SEPARATOR, [$EVERON_ROOT, 'vendor', 'everon', 'everon', 'src', 'Everon']).DIRECTORY_SEPARATOR;

$EVERON_CUSTOM_PATHS = [
    'application' => getcwd().'/doubles/Application/',
    'config' => getcwd().'/doubles/Config/',
    'data_mapper' => getcwd().'/doubles/DataMapper/',
    'domain' => getcwd().'/doubles/Domain/',
    'domain_config' => getcwd().'/doubles/Domain/',
    'module' => getcwd().'/doubles/Module/',
    'rest' => getcwd().'/doubles/Rest/',
    'web' => getcwd().'/doubles/Web/',
    'view' => getcwd().'/doubles/View/',
    'tmp' => getcwd().'/doubles/Tmp/',
    'log' => getcwd().'/doubles/Tmp/logs/',
    'cache' => getcwd().'/doubles/Tmp/cache/',
    'cache_config' => getcwd().'/doubles/Tmp/cache/',
    'cache_view' => getcwd().'/doubles/Tmp/cache/view/',
];

require_once(
    implode(DIRECTORY_SEPARATOR,
        [$EVERON_SOURCE_ROOT, 'Config', '_bootstrap.php'])
);




