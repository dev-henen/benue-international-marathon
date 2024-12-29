<?php

use FastRoute\RouteCollector;

return function (RouteCollector $r) {
    $r->addRoute('POST', '/api/register', ['App\Controllers\API\RegisterController', 'register']);
};
