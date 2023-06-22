<?php
include_once('controllers/AuthController.php');

function requestIsValid()
{
    $bearer = AuthController::getAuthorizationHeader();
    if($bearer == null) {
        return false;
    }

    $header = AuthController::parseBearerToken($bearer);
    if($header == null) {
        return false;
    }

    $user = AuthController::getUserFomToken($header);
    if($user::class != "UserEntity") {
        return false;
    }

    return true;
}
