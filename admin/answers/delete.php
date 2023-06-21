<?php
// Include config file
include_once('../../Database.php');
$db = new Database();
$db->connectDB();

if($_SERVER["REQUEST_METHOD"] != "DELETE" || empty($_GET['id'])) {
    echo json_encode(['success' => false]);
    exit;
}

$stmt = $db->getDb()->prepare("DELETE FROM answers WHERE id = ?");
$stmt->bind_param("s", $_GET['id']);
$stmt->execute();
$result = $stmt->get_result();
echo json_encode(['success' => true]);