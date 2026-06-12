<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );
    })->create();

$storagePath = getenv('APP_STORAGE_PATH')
    ?: ($_SERVER['APP_STORAGE_PATH'] ?? null)
    ?: ($_ENV['APP_STORAGE_PATH'] ?? null)
    ?: (ini_get('flexi.storage') ?: null);

// Fallback: derive from Windows APPDATA (always set, doesn't depend on env passing)
if (!$storagePath) {
    $appData = getenv('APPDATA') ?: ($_SERVER['APPDATA'] ?? null);
    if ($appData) {
        $storagePath = $appData . DIRECTORY_SEPARATOR . 'flexi-quotation' . DIRECTORY_SEPARATOR . 'storage';
    }
}

if ($storagePath) {
    // Ensure critical dirs exist even if Electron's ensureStorage didn't run
    foreach (['framework/views', 'framework/cache/data', 'framework/sessions', 'logs', 'fonts', 'app'] as $dir) {
        @mkdir($storagePath . DIRECTORY_SEPARATOR . $dir, 0755, true);
    }
    $app->useStoragePath($storagePath);
}

return $app;
