<?php

header("Content-Type: application/json");

require_once '../../app/auth.php';

echo json_encode(auth_app_handler());