<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/admin/auth.php');

// Include config file
include_once('../../Database.php');

$db = new Database();
$db->connectDB();

$success = false;
$message = "";

if($_SERVER["REQUEST_METHOD"] == "POST")
{
    // Validate fruit_id
    $input_fruit_id = trim($_POST["fruit_id"]);
    if(empty($input_fruit_id)){
        $fruit_id_err = "Please enter an fruit.";
        $message .= $fruit_id_err;
    } else{
        $fruit_id = $input_fruit_id;
    }

    // Validate level
    $input_level = trim($_POST["level"]);
    if(empty($input_level)){
        $level_err = "Please enter a level.";
        $message .= $level_err;
    } else{
        $level = $input_level;
    }
    
    // Validate question
    $input_question = trim($_POST["question"]);
    if(empty($input_question)){
        $question_err = "Please enter an question."; 
        $message .= $question_err;    
    } else{
        $question = $input_question;
    }

    if(empty($_FILES['photo']['name'])) {
        $photo_err = "Please enter an photo."; 
        $message .= $photo_err;  
    }

    if(empty($fruit_id_err) && empty($level_err) && empty($question_err) && empty($photo_err))
    {
        $imagename = $_FILES['photo']['name'];
        $imagetype = $_FILES['photo']['type'];
        $imageerror = $_FILES['photo']['error'];
        $imagetemp = $_FILES['photo']['tmp_name'];

        $stmt = $db->getDb()->prepare("UPDATE questions q SET q.fruit_id = ?, q.level = ?, q.question = ?, photo = ? WHERE q.id = ?");
        $stmt->bind_param("isssi", $fruit_id, $level, $question, $imagename, $_GET['id']);
        if($stmt->execute()) {
            $success = true;
            $imagePath = $_SERVER["DOCUMENT_ROOT"] . "/assets/images/questions/{$_GET['id']}/";

            if(!is_dir($imagePath)) {
                mkdir($imagePath);
            }

            if(is_uploaded_file($imagetemp)) {
                $success = move_uploaded_file($imagetemp, $imagePath . $imagename);
            }
        }
    }
}

echo json_encode(["success" => $success, "message" => $message]);