<?php
use FastRoute\RouteCollector;

define('BASE_PATH', __DIR__);

return function (RouteCollector $r) {
    // Define routes
    $r->addRoute('GET', '/', function() {
        include __DIR__ . '/includes/home.php';
    });

   
};
