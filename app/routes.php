<?php
use FastRoute\RouteCollector;

return function (RouteCollector $r) {

    // Define routes

    $r->addRoute('GET', '/', function() {
        include __DIR__ . '/includes/home.php';
    });
   
    $r->addRoute('GET', '/about-us', function() {
        include __DIR__ . '/includes/about-us.php';
    });

   
};
