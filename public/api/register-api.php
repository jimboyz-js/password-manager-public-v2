<?php
header("Content-Type: application/json");

require_once '../../app/db/register.php';

echo json_encode(register_handler());