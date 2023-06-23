<?php

class AnswerController
{
    private $conn;
    private $requestMethod;

    public function __construct($db, $req){
        $this->conn = $db;
        $this->requestMethod = $req;
    }

    public function processRequest(){
        switch ($this->requestMethod) {
            case 'GET':
                //find the value of the params
                $questionId = $_GET['questionId'];
                $receivedAnswer = $_GET['receivedAnswer'];

                if ($this->validateAnswer($questionId, $receivedAnswer)) {
                    return json_encode(["correct" => 'true']);
                }
                else {
                    return json_encode(["correct" => 'false']);
                }

            default:
                echo("Unsupported request method!");
                break;
        }
    }

    public function validateAnswer($question_id, $received_answer){
        try{
            $statement = $this->conn->prepare("SELECT * FROM answers a  WHERE a.response = true AND a.question_id = ?");
            $statement->bind_param("i",$question_id);
            $statement->execute();
            $result = $statement->get_result();
            //if($result->num_rows === 0) exit('No rows');

            while($row = $result->fetch_assoc()) {
                if($received_answer == $row['answer']) {
                    return true;
                } else {
                    return false;
                }
            }

        }
        catch(PDOException $e){
            trigger_error("Error in " . __METHOD__ . ": " . $e->getMessage(), E_USER_ERROR);
        }
        return false;
    }

    /**
     * @param $question_id
     * @return array containing all the answers of the question
     */
    public function getQuestionAnswers($question_id){
        $statement = $this->conn->prepare("SELECT * FROM answers a  WHERE a.question_id = ?");
        $statement->bind_param("i",$question_id);
        $statement->execute();

        $answers = [];

        $result = $statement->get_result();
        //if($result->num_rows === 0) exit('No rows');

        while($row = $result->fetch_assoc()) {
            $answers = array_merge($answers, [$row['answer']]);
        }

        return $answers;
    }
}