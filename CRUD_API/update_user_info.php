<?php
require "db_connection.php";

$email = $_POST["email"] ?? "";
$new_password = $_POST["new_password"] ?? "";

if ($email == "" || $new_password == "") {
    http_response_code(400);
    echo json_encode(["error" => "email and new_password are required"]);
    exit;
}

$hashed_password = md5($new_password);

$check = $con->prepare("SELECT id FROM users WHERE email=?");
$check->bind_param("s", $email);
$check->execute();
$result = $check->get_result();

if ($result->num_rows == 0) {
    http_response_code(404);
    echo json_encode(["error" => "User not found"]);
    exit;
}

$stmt = $con->prepare("UPDATE users SET password=? WHERE email=?");
$stmt->bind_param("ss", $hashed_password, $email);

if ($stmt->execute()) {
    http_response_code(200);
    echo json_encode(["message" => "Password updated successfully"]);
} else {
    http_response_code(500);
    echo json_encode(["error" => "Update failed"]);
}

$stmt->close();
$con->close();
?>