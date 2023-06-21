<?php
// Include config file
include_once('../../Database.php');
$db = new Database();
$db->connectDB();

$fruits = [];
// Attempt select query execution

$sql = "SELECT * FROM fruits";

if($result = $db->getDb()->query($sql)) {
    $fruits = $result->fetch_all(MYSQLI_ASSOC);
}

echo json_encode($fruits);
