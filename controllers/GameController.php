<?php
class GameController{
    private $conn;
    private $requestMethod;
    public function __construct($db, $req){
        $this->conn = $db;
        $this->requestMethod = $req;
    }

    public function processRequest(){
        switch ($this->requestMethod){
            case 'GET':
                return $this->getAllGames();
                break;
            default:
                echo("Unsupported request method!");
                break;
        }
    }

    public function getAllGames(){

        try {
            $bearer = AuthController::getAuthorizationHeader() ?? "";
            $header = AuthController::parseBearerToken($bearer) ?? "";
            $user = AuthController::getUserFomToken($header);
            $userId = $user->getId();

            $statement = $this->conn->prepare("SELECT g.* FROM games g  WHERE g.user_id = ? ORDER BY  g.data DESC");
            $statement->bind_param("i",$userId);
            $statement->execute();
            $result = $statement->get_result();

            /*$games = $result->fetchALL();
            $jsonGames = json_encode($games);
            return $jsonGames;*/

            $games = array();
            while ($row = $result->fetch_assoc()) {
                $games[] = $row;
            }
            return json_encode($games);

        }
        catch (Exception $e) {
            trigger_error("Error in " . __METHOD__ . ": " . $e->getMessage());
        }

    }
}



