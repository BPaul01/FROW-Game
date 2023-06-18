<?php

include_once('AuthController.php');
include_once('controllers/QuestionController.php');
include_once('controllers/AnswerController.php');
include_once('Database.php');

require_once ($_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php');

$db = new Database();
$db->connectDB();

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode( '/', $uri );

$requestMethod = $_SERVER["REQUEST_METHOD"];
$authController = new AuthController($requestMethod);

switch ($uri[1]) {
    case 'login':
        $authController->processRequest();
        break;

    case 'questions':
        $controller = new QuestionController($db->getDb());
        $controller->processRequest();
        break;

    case 'answers':
        $controller = new AnswerController($db->getDb(),$requestMethod);
        $response = $controller->processRequest();

        echo ($response);
        break;

    default:
        header('Location: /home/index.html');
        exit();
}