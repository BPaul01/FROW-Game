<?php
include_once('entities/QuestionEntity.php');

class QuestionController
{
    private $database;
    private static $maxQuestions = 3;

    public function __construct($database){
        $this->database = $database;
    }

    public function processRequest(){
        switch ($_SERVER["REQUEST_METHOD"]){
            case 'GET':

                //find the value of the param
                $difficulty = $_GET['difficulty'];

                //read a question from the database with the same difficulty
                $question = $this->getRandomQuestion($difficulty);

                //find the answers
                $answers = (new AnswerController($this->database, 'GET'))->getQuestionAnswers($question->getId());

                $response = [
                    "id" => $question->getId(),
                    "fruit_id" => $question->getFruitId(),
                    "level" => $question->getLevel(),
                    "question" => $question->getQuestion(),
                    "photo" => $question->getPhoto(),
                    "answers" => $answers
                ];

                echo json_encode($response);

                break;

            default:
                $response = $this->notFoundResponse();
                break;
        }
    }

    /**
     * @param $difficulty
     * @return QuestionEntity
     */
    public function getRandomQuestion($difficulty)
    {
        $statement = $this->database->prepare("SELECT * FROM questions WHERE level = ? ORDER BY RAND() LIMIT 1");
        $statement->bind_param("s", $difficulty);
        $statement->execute();

        $result = $statement->get_result();
        //if($result->num_rows === 0) exit('No rows');

        if($row = $result->fetch_assoc()) {
            $id = $row['id'];
            $fruitId = $row['fruit_id'];
            $level = $row['level'];
            $text = $row['question'];
            $photo = $row['photo'];

            $question = new QuestionEntity($id, $fruitId, $level, $text, $photo);

            return $question;
        }

        //$this->notFoundResponse();
        return null;
    }

    /**
     * @param $difficulty
     * @return QuestionEntity
     */
    public function getRandomBatchQuestions()
    {
        $questionsResponse = [];
        $difficulty = $_GET['difficulty'];

        while(count($questionsResponse) <= static::$maxQuestions) {
            //read a question from the database with the same difficulty
            $question = $this->getRandomQuestion($difficulty);

            if(!isset($questionsResponse[$question->getId()])) {
                $answers = (new AnswerController($this->database, 'GET'))->getQuestionAnswers($question->getId());

                $questionsResponse[$question->getId()] = [
                    "id" => $question->getId(),
                    "fruit_id" => $question->getFruitId(),
                    "level" => $question->getLevel(),
                    "question" => $question->getQuestion(),
                    "photo" => $question->getPhoto(),
                    "answers" => $answers
                ];
            }
        }

        return $questionsResponse;
    }

    private function notFoundResponse()
    {
        $response['status_code_header'] = 'HTTP/1.1 404 Not Found';
        $response['body'] = null;
        return $response;
    }
}



