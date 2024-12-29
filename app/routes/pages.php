<?php

use FastRoute\RouteCollector;

return function (RouteCollector $r) {
    $r->addRoute('GET', '/', ['App\Controllers\Pages\HomeController', 'index']);
    $r->addRoute('GET', '/about-us', ['App\Controllers\Pages\AboutController', 'index']);
    $r->addRoute('GET', '/register', ['App\Controllers\Pages\RegisterController', 'index']);
};
