<?php
// Include config file
include_once('../../Database.php');
$db = new Database();
$db->connectDB();

$questions = [];
// Attempt select query execution
$sql = "SELECT a.*, q.question as question FROM answers a  left JOIN questions q on q.id=a.question_id";
if($result = $db->getDb()->query($sql)) {
    $questions = $result->fetch_all(MYSQLI_ASSOC);
}

echo json_encode($questions);
