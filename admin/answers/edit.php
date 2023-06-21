<?php
// Include config file
include_once('../../Database.php');
$db = new Database();
$db->connectDB();

$success = false;
$message = "";

if($_SERVER["REQUEST_METHOD"] == "POST")
{
    // Validate question_id
    $input_question_id = trim($_POST["question_id"]);
    if(empty($input_question_id)){
        $question_id_err = "Please enter a question.";
        $message .= $question_id_err;
    } else{
        $stmt = $db->getDb()->prepare("SELECT q.id FROM questions q WHERE q.id = ?");
        $stmt->bind_param("i", $_POST['question_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        $numRows = $result->num_rows;

        if ($numRows > 0) {
            $question_id = $input_question_id;
        } else {
            $question_id_err = "Please enter a valid question_id.";
        }

    }

    // Validate answer
    $input_answer = trim($_POST["answer"]);
    if(empty($input_answer)){
        $answer_err = "Please enter an answer.";
        $message .= $answer_err;
    } else{
        $answer = $input_answer;
    }

    // Validate response
    $input_response = trim($_POST["response"]);
    if(empty($input_response)){
        $response_err = "Please enter a response.";
        $message .= $response_err;
    } else{
        if($input_response == "true"){
            $response = 1;
        }else{
            $response = 0;
        }
        //$response = $input_response;
    }


    if(empty($question_id_err) && empty($answer_err) && empty($response_err)){
        $stmt = $db->getDb()->prepare("UPDATE answers a SET a.question_id = ?, a.answer = ?,a.response = ? WHERE a.id = ?");
        $stmt->bind_param("ssis", $question_id, $answer, $response,$_GET['id']);
        if($stmt->execute()) {
            $success = true;
        }
    }
}
echo json_encode(["success" => $success, "message" => $message]);

