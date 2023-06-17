<?php

include_once('AuthController.php');

require_once ($_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php');

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode( '/', $uri );

$requestMethod = $_SERVER["REQUEST_METHOD"];
$authController = new AuthController($requestMethod);

switch ($uri[1]) {
    case 'login':
        $authController->processRequest();
        break;

    default:
        header('Location: /home/index.html');
        exit();
}