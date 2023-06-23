<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/admin/auth.php');
// Include config file
include_once('../../Database.php');
$db = new Database();
$db->connectDB();

$questions = [];
// Attempt select query execution
$sql = "SELECT f.id as id FROM fruits f ORDER BY f.id";
if($result = $db->getDb()->query($sql)) {
    $fruitsIds = $result->fetch_all(MYSQLI_ASSOC);
}

echo json_encode($fruitsIds);
