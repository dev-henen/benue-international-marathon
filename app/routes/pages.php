<?php

use FastRoute\RouteCollector;

return function (RouteCollector $r) {
    $r->addRoute('GET', '/', ['App\Controllers\Pages\HomeController', 'index']);
    $r->addRoute('GET', '/about-us', ['App\Controllers\Pages\AboutController', 'index']);
    $r->addRoute('GET', '/register', ['App\Controllers\Pages\RegisterController', 'index']);
    $r->addRoute('GET', '/register/get-slip', ['App\Controllers\Pages\GetSlipController', 'index']);
    $r->addRoute('GET', '/getSlip', ['App\Controllers\Pages\GetSlipController', 'download']);
    $r->addRoute('GET', '/register/verify-slip', ['App\Controllers\Pages\GetSlipController', 'verify']);
};
