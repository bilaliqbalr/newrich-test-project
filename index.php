<?php

$rootDir = __DIR__;
$srcDir = $rootDir . '/src';

require $srcDir . '/Helper/functions.php';
require $srcDir . '/App.php';
require $srcDir . '/Database.php';
require $srcDir . '/Form/Form.php';
require $srcDir . '/Controller/Router.php';

try {
    App::setTemplateDir($srcDir . '/template');
    App::setConfig(require $srcDir . '/config.php');

    $router = new Router();

    // All requests
    $router->addRoute('GET', '/', 'index');
    $router->addRoute('GET', '/submissions', 'formSubmissions');
    $router->addRoute('GET', '/new', 'createForm');
    $router->addRoute('POST', '/submit', 'submitForm');

    // API requests
    $router->addRoute('POST', '/create-form', 'createNewFormFromApi');

    $router->handleRequest();

} catch (Exception $e) {
    App::view('error', ['message' => $e->getMessage()]);
}
