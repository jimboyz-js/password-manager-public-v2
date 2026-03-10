<?php

header("Content-Type: application/json");

require_once '../../app/logout.php';

echo json_encode(logout_handler());