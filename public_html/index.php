<?php
define('ROOT_PATH', dirname(__DIR__));

ini_set('display_errors', 1);
error_reporting(E_ALL);

require ROOT_PATH . '/vendor/autoload.php';

use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use Twig\Loader\FilesystemLoader;
use Twig\Environment;

// Set up Twig
$templateDir = ROOT_PATH . '/app/templates';
$loader = new FilesystemLoader($templateDir);
$GLOBALS['twig'] = new Environment($loader, [
    'cache' => ROOT_PATH . '/cache',
    'debug' => true,
    'auto_reload' => true, // Enable this for development
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
