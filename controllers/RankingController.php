<?php
class RankingController{
    private $conn;
    public function __construct($db){
        $this->conn = $db;
    }

    public function processRequest(){
        switch ($_SERVER["REQUEST_METHOD"]){
            case 'GET':
                return $this->getClasament();
                break;
            default:
                echo("Unsupported request method!");
                break;
        }
    }

    public function getClasament(){

        try {
            $statement = $this->conn->prepare("SELECT u.name AS username, COALESCE(MAX(g.score), 0) AS mScore, COALESCE(MAX(g.data), '-') as data
                                                FROM users u
                                                LEFT JOIN games g ON u.id = g.user_id
                                                GROUP BY u.id
                                                ORDER BY mScore DESC;
                                                ");
            $statement->execute();
            $result = $statement->get_result();

            $usersScores = array();
            while ($row = $result->fetch_assoc()) {
                $usersScores[] = $row;
            }
            return json_encode($usersScores);

        }
        catch (Exception $e) {
            trigger_error("Error in " . __METHOD__ . ": " . $e->getMessage());
        }

    }
}
