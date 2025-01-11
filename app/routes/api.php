<?php

use FastRoute\RouteCollector;

return function (RouteCollector $r) {
    $r->addRoute('POST', '/api/register', ['App\Controllers\API\RegisterController', 'register']);
    $r->addRoute('POST', '/api/register/verify', ['App\Controllers\API\RegisterController', 'verifyRegistration']);
    $r->addRoute('POST', '/api/generate-transaction-reference', ['App\Controllers\API\GenerateTransactionReferenceController', 'registration']);
    $r->addRoute('POST', '/api/get-registration-slip', ['App\Controllers\API\GetSlipController', 'sendLinkToEmail']);
};
