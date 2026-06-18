<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Root front controller (XAMPP / sub-folder friendly)
|--------------------------------------------------------------------------
| Laravel's real front controller lives in public/index.php. When the app
| is served from a sub-folder of htdocs (e.g. /Projects/Phone Tracking
| System) Apache can't be pointed straight at public/, so this thin copy
| boots the framework from the project root and keeps URLs clean (no
| "/public" segment). Static assets are mapped into public/ by .htaccess.
*/

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/bootstrap/app.php';

$app->handleRequest(Request::capture());
