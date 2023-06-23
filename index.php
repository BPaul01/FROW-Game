<?php

require_once('controllers/QuestionController.php');
require_once('controllers/AnswerController.php');
require_once('controllers/AuthController.php');
require_once('controllers/GameController.php');
require_once('controllers/RankingController.php');
require_once('controllers/UserController.php');
require_once('Database.php');
require_once('helpers.php');

require_once ($_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php');

$db = new Database();
$db->connectDB();

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode( '/', $uri );

$requestMethod = $_SERVER["REQUEST_METHOD"];
$authController = new AuthController($requestMethod);

switch ($uri[1]) {
    case 'login':
        echo $authController->processLoginRequest();
        break;

    case 'signup':
        echo $authController->signUpRequest();
        break;

    case 'questions':
        $controller = new QuestionController($db->getDb());
        $controller->processRequest();
        break;

    case 'randomBatchQuestions':
        $controller = new QuestionController($db->getDb());
        echo json_encode($controller->getRandomBatchQuestions());
        break;

    case 'answers':
        $controller = new AnswerController($db->getDb(),$requestMethod);
        echo $controller->processRequest();
        break;

    case 'feed':
        header('Location: /feed/rss.php');
        break;

    case 'validate':
        echo requestIsValid();
        break;
    case 'myGames':
        $controller = new GameController($db->getDb(),$requestMethod);
        $response = $controller->processRequest();
        echo($response);
        break;
    case 'username':
        $controller = new UserController($db->getDb());
        $response = $controller->processRequest();
        echo($response);
        break;
    case 'clasament':
        $controller = new RankingController($db->getDb());
        $response = $controller->processRequest();
        echo($response);
        break;

    default:
        header('Location: /home/index.html');
        exit();
}