<?php
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Sprawdź czy ścieżka do vendor jest poprawna (zależy od struktury)
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';

$request = Request::capture();
$response = $app->handle($request);
$response->send();
$kernel->terminate($request, $response);