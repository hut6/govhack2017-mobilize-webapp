<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use Symfony\Component\HttpFoundation\Request;

require __DIR__.'/../vendor/autoload.php';
if (PHP_VERSION_ID < 70000) {
    include_once __DIR__.'/../var/bootstrap.php.cache';
}

$server = $_SERVER['SERVER_NAME'];

switch ($server) {
    case 'mobilize.team':
    case '0.0.0.0':
        $kernel = new AppKernel('prod', false);
        break;
    case 'volunteers-backend.dw.gg':
    case '2017-volunteers-govhack.dw.gg':
        $kernel = new AppKernel('dev', true);
        break;
    default:
        die('No site configured at ' . $server);
}

if (PHP_VERSION_ID < 70000) {
    $kernel->loadClassCache();
}
//$kernel = new AppCache($kernel);

// When using the HttpCache, you need to call the method in your front controller instead of relying on the configuration parameter
//Request::enableHttpMethodParameterOverride();
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->headers->set('Access-Control-Allow-Origin', '*');
$response->send();
$kernel->terminate($request, $response);
