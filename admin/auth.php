<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/helpers.php');

$isAdmin = false;
$bearer = AuthController::getAuthorizationHeader() ?? "";
$header = AuthController::parseBearerToken($bearer) ?? "";
$user = AuthController::getUserFomToken($header);

if($user::class != "UserEntity" || !$user->getIsAdmin()) {
    header('HTTP/1.1 401 Unauthorized');
    header('Location: /home/index.html');
    exit();
}