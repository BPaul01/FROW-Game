<?php
// Include config file
include_once('../../Database.php');
$db = new Database();
$db->connectDB();

$success = false;
$message = "";

if($_SERVER["REQUEST_METHOD"] == "POST")
{
    
    // Validate name
    $input = trim($_POST["fruit_name"]);
    if(empty($input)){
        $err = "Please enter an question.";
    } else{
        $name = $input;
    }
    
    if(empty($err)){
        $stmt = $db->getDb()->prepare("INSERT INTO fruits (`name`) VALUES (?)");
        $stmt->bind_param("s", $name);
        if($stmt->execute()) {
            $success = true;
        }
    }
}

echo json_encode(["success" => $success, "message" => $message]);

