<?php
require_once "db_connection.php";

$sql = "SELECT id, name, email FROM users ORDER BY id DESC";
$result = $con->query($sql);

$users = [];
while ($row = $result->fetch_assoc()) {
    $users[] = $row;
}

http_response_code(200);
echo json_encode([
    "count" => count($users),
    "users" => $users
]);

$con->close();
?>