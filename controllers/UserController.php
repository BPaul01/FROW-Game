<?php
class UserController{
    private $conn;
    public function __construct($db){
        $this->conn = $db;
    }

    public function processRequest(){
        switch ($_SERVER["REQUEST_METHOD"]){
            case 'GET':
                return $this->getCurrentUsername();
            default:
                echo("Unsupported request method!");
                break;
        }
    }

    public function getCurrentUsername(){

        try {
            $bearer = AuthController::getAuthorizationHeader() ?? "";
            $header = AuthController::parseBearerToken($bearer) ?? "";
            $user = AuthController::getUserFomToken($header);

            return $user->getUserName();
        }
        catch (Exception $e) {
            trigger_error("Error in " . __METHOD__ . ": " . $e->getMessage());
        }

    }
}



