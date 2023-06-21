<?php

// Include config file
include_once('../../Database.php');

$db = new Database();
$db->connectDB();

$success = false;
$message = "";

if($_SERVER["REQUEST_METHOD"] == "POST")
{
    // Validate the new name
    $input = trim($_POST["new_name"]);
    if(empty($input)){
        $err = "Please enter a name.";
    } else{
        $newName = $input;
    }


    if(empty($err)){
        $stmt = $db->getDb()->prepare("UPDATE fruits f SET f.name = ? WHERE f.id = ?");
        $stmt->bind_param("si", $newName, $_GET['id']);
        if($stmt->execute()) {
            $success = true;
        }
    }
}
echo json_encode(["success" => $success, "message" => $message]);

