<?php

header("Content-Type: application/json");

require_once '../../app/user_auth.php';
require_once 'helpers.php';

try {
    echo json_encode(user_auth_handler());
} catch(Exception $e) {
    respond("failed", $e->getMessage(), ["success"=>false]);
}