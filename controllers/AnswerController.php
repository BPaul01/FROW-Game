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
        switch ($this->requestMethod){
            case 'GET':
                $body = file_get_contents('php://input');
                $data = json_decode($body, true);

                if ($data && isset($data['question_id']) && isset($data['received_answer'])) {
                    $questionId = $data['question_id'];
                    $receivedAnswer = $data['received_answer'];

                    if($this->validateAnswer($questionId,$receivedAnswer)){
                        return "correct";
                    }
                    else {return "incorrect";}
                }
                else{
                    echo("Incorrect parameters!");
                }

                break;
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
            if($result->num_rows === 0) exit('No rows');

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

    }

    public function getQuestionAnswers($question_id){
        $statement = $this->conn->prepare("SELECT * FROM answers a  WHERE a.question_id = ?");
        $statement->bind_param("i",$question_id);
        $statement->execute();

        $answers = [];

        $result = $statement->get_result();
        if($result->num_rows === 0) exit('No rows');

        while($row = $result->fetch_assoc()) {
            $answers = array_merge($answers, [$row['answer']]);
        }

        return $answers;
    }

}