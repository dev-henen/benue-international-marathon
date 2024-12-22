<?php
define('ROOT_PATH', dirname(__DIR__));

require ROOT_PATH . '/vendor/autoload.php';

use FastRoute\RouteCollector;
use FastRoute\Dispatcher;
use function FastRoute\simpleDispatcher;
use Twig\Loader\FilesystemLoader;
use Twig\Environment;

// Set the path to your templates globally
$templateDir = ROOT_PATH . '/app/templates'; // Adjust path as needed
$loader = new FilesystemLoader($templateDir);

// Initialize Twig
$GLOBALS['twig'] = new Environment($loader, [
    'cache' => ROOT_PATH . '/cache', // Optional: Enable caching for better performance
    'debug' => true,               // Optional: Enable debug mode
]);

// Load routes
$dispatcher = simpleDispatcher(require ROOT_PATH . '/app/routes.php');

// Get the current HTTP method and URI
$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

// Remove query string (?key=value) from the URI
if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

// Dispatch the route
$routeInfo = $dispatcher->dispatch($httpMethod, $uri);
switch ($routeInfo[0]) {
    case Dispatcher::NOT_FOUND:
        http_response_code(404);
        echo "404 Not Found";
        break;

    case Dispatcher::METHOD_NOT_ALLOWED:
        http_response_code(405);
        echo "405 Method Not Allowed";
        break;

    case Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2]; // Route parameters, if any
        call_user_func_array($handler, $vars);
        break;
}
