<?php
require "db_connection.php";
header("Content-Type: application/json");

$email = $_POST["email"] ?? "";

$check = $con->prepare("SELECT id FROM users WHERE email=?");
$check->bind_param("s", $email);
$check->execute();
$res = $check->get_result();

if ($res->num_rows == 0) {
    http_response_code(404);
    echo json_encode(["error" => "User not found"]);
    exit;
}

$stmt = $con->prepare("DELETE FROM users WHERE email=?");
$stmt->bind_param("s", $email);

if ($stmt->execute()) {
    http_response_code(200);
    echo json_encode(["message" => "User deleted successfully"]);
} else {
    http_response_code(500);
    echo json_encode(["error" => "Delete failed"]);
}

$stmt->close();
$con->close();
?>