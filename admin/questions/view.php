<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/admin/auth.php');
// Include config file
include_once('../../Database.php');
$db = new Database();
$db->connectDB();

$questions = [];
// Attempt select query execution
$sql = "SELECT q.*, f.name as fruit_name 
        FROM questions q
        Left JOIN fruits f on q.fruit_id=f.id";
if($result = $db->getDb()->query($sql)) {
    $questions = $result->fetch_all(MYSQLI_ASSOC);
    // if($result->num_rows > 0) {
    //         while($row = $result->fetch_assoc()){
    //             echo "<tr>";
    //                 echo "<td>" . $row['id'] . "</td>";
    //                 echo "<td>" . $row['fruit_name'] . "</td>";
    //                 echo "<td>" . $row['level'] . "</td>";
    //                 echo "<td>" . $row['question'] . "</td>";
    //                 echo "<td>" . $row['photo'] . "</td>";
    //                 echo '<td><a href="/admin/questions/update.php?id='. $row['id'] .'">Edit</a></td>';
    //                 echo '<td><a href="/admin/questions/delete.php?id='. $row['id'] .'">Delete</a></td>';
    //             echo "</tr>";
    //         }
    // }
}

echo json_encode($questions);
