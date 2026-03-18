<?php
require "db_connection.php";

$name = $_POST["name"] ?? "";
$email = $_POST["email"] ?? "";
$password = $_POST["password"] ?? "";


if ($name == "" || $email == "" || $password == "") {
  http_response_code(400);
  echo json_encode(["error" => "name, email, password are required"]);
  exit;
}



$hashed = md5($password);

// check email exists
$check = $con->prepare("SELECT id FROM users WHERE email=?");
$check->bind_param("s", $email);
$check->execute();
$res = $check->get_result();

if ($res->num_rows > 0) {
  http_response_code(409);
  echo json_encode(["error" => "User already exists"]);
  exit;
}

$stmt = $con->prepare("INSERT INTO users(name,email,password) VALUES(?,?,?)");
$stmt->bind_param("sss", $name, $email, $hashed);

if ($stmt->execute()) {
  http_response_code(201);
  echo json_encode(["message" => "User created", "id" => $con->insert_id]);
} else {
  http_response_code(500);
  echo json_encode(["error" => "Insert failed"]);
}
?>