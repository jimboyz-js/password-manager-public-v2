<?php

if(session_status() === PHP_SESSION_NONE) {
    session_start();
}

header("Content-Type: application/json");

require_once '../../app/db/login_controller.php';

$username = $_POST["username"];
$password = $_POST["password"];
$key_hash = $_POST["master-key"];

$response = login_handler($username, $password, $key_hash);
echo json_encode($response);