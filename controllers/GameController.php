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

            case 'POST':
                return $this->stopGame();

            default:
                echo("Unsupported request method!");
                break;
        }
    }

    public function stopGame(){
        $bearer = AuthController::getAuthorizationHeader() ?? "";
        $header = AuthController::parseBearerToken($bearer) ?? "";
        $user = AuthController::getUserFomToken($header);

        //get the vars form the $POST
        if(isset($_POST['score']))
            $score = $_POST['score'];

        if($score == 0)
            return json_encode("No need to save");

        $userId = $user->getId();
        $date = date("Y-m-d");

        //insert in games
        $stmt = $this->conn->prepare("INSERT INTO games (user_id, score, data) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $userId, $score, $date);
        if($stmt->execute()) {
            return json_encode("Score has been saved and rank updated");
        }
        return json_encode("Error when updating the database");
    }

    public function getAllGames(){

        try {
            $bearer = AuthController::getAuthorizationHeader() ?? "";
            $header = AuthController::parseBearerToken($bearer) ?? "";
            $user = AuthController::getUserFomToken($header);
            $userId = $user->getId();

            $statement = $this->conn->prepare("SELECT g.* FROM games g  WHERE g.user_id = ? ORDER BY g.id DESC");
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



