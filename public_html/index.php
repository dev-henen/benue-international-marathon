<?php

use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use Twig\Loader\FilesystemLoader;
use Twig\Environment;
use Illuminate\Database\Capsule\Manager as Capsule;
use Dotenv\Dotenv;

define('ROOT_PATH', dirname(__DIR__));

ini_set('error_log', ROOT_PATH . '/error.log');
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1);
ini_set('session.cookie_samesite', 'Strict');
ini_set('session.use_strict_mode', 1);

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: private, max-age=0, must-revalidate");
header("Pragma: no-cache");

require ROOT_PATH . '/vendor/autoload.php';

// Load .env file
$dotenv = Dotenv::createImmutable(ROOT_PATH);
$dotenv->load();

// Set error display based on environment
if ($_ENV['APP_ENV'] === 'development') {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
    error_reporting(0);
}

// Set up Twig
$templateDir = ROOT_PATH . '/app/templates';
$loader = new FilesystemLoader($templateDir);
$GLOBALS['twig'] = new Environment($loader, [
    'cache' => ROOT_PATH . '/cache',
    'debug' => filter_var($_ENV['TWIG_DEBUG'], FILTER_VALIDATE_BOOLEAN),
    'auto_reload' => filter_var($_ENV['TWIG_AUTO_RELOAD'], FILTER_VALIDATE_BOOLEAN),
]);

define('ENCRYPTION_KEY', $_ENV['ENCRYPTION_KEY']);
define('REGISTRATION_DETAILS', [
    'year' => 2025,
    'is_open' => true
]);

// Determine whether the request is for the API or pages
$uri = $_SERVER['REQUEST_URI'];
if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

$prefix = '/api/';
$isApiRequest = strpos($uri, $prefix) === 0;

// Load the appropriate routes file
$routesFile = $isApiRequest ? ROOT_PATH . '/app/routes/api.php' : ROOT_PATH . '/app/routes/pages.php';

// Create the dispatcher
$dispatcher = FastRoute\simpleDispatcher(require $routesFile);

// Dispatch the route
$httpMethod = $_SERVER['REQUEST_METHOD'];
$routeInfo = $dispatcher->dispatch($httpMethod, $uri);

switch ($routeInfo[0]) {
    case Dispatcher::NOT_FOUND:
        http_response_code(404);
        echo $GLOBALS['twig']->render('/404.twig'); // Custom 404 page
        break;

    case Dispatcher::METHOD_NOT_ALLOWED:
        http_response_code(405);
        echo $GLOBALS['twig']->render('/405.twig'); // Custom 405 page
        break;

    case Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];

        if (is_array($handler)) {
            // Instantiate the controller if the handler is a class-method pair
            $controller = new $handler[0](); // Create an instance of the controller
            call_user_func_array([$controller, $handler[1]], $vars);
        } else {
            // Handle other callable types (e.g., closures)
            call_user_func_array($handler, $vars);
        }
        break;
}
