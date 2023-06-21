<?php
// Include config file
include_once('../../Database.php');
$db = new Database();
$db->connectDB();

$questions = [];
// Attempt select query execution
$sql = "SELECT q.id as id FROM questions q ORDER BY q.id";
if($result = $db->getDb()->query($sql)) {
    $questionsIds = $result->fetch_all(MYSQLI_ASSOC);
}

echo json_encode($questionsIds);
