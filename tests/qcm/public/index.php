<?php
declare(strict_types=1);

// Autoloader simple : App\Core\Router -> app/Core/Router.php
spl_autoload_register(function (string $class): void {
    $prefix = 'App\\';
    if (strncmp($class, $prefix, strlen($prefix)) !== 0) {
        return;
    }
    $file = __DIR__ . '/../app/' . str_replace('\\', '/', substr($class, strlen($prefix))) . '.php';
    if (is_file($file)) {
        require $file;
    }
});

require __DIR__ . '/../app/Core/helpers.php';

use App\Core\Router;
use App\Security\Auth;

$config = require __DIR__ . '/../config/config.php';
date_default_timezone_set($config['timezone']);
ini_set('log_errors', '1');
ini_set('error_log', $config['logs']);   // les erreurs vont dans storage/logs/app.log
ini_set('display_errors', '0');          // jamais d'erreur détaillée à l'écran

Auth::start();

$router = new Router();
require __DIR__ . '/../config/routes.php';

try {
    $router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
} catch (Throwable $e) {
    http_response_code(500);
    error_log($e::class . ': ' . $e->getMessage() . ' (' . $e->getFile() . ':' . $e->getLine() . ')');
    echo 'Erreur interne.';
}
