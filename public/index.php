<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// 1. Rejestracja Autoloadera Composer
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

require __DIR__.'/../vendor/autoload.php';

// 2. Uruchomienie aplikacji (Bootstrap)
$app = require_once __DIR__.'/../bootstrap/app.php';

// 3. Obsługa żądania i wysłanie odpowiedzi
$handle = $app->handle(Request::capture());

$handle->send();

$app->terminate();