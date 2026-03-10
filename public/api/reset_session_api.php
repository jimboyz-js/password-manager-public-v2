<?php

header("Content-Type: application/json");

require_once '../../app/reset_session.php';

echo json_encode(reset_session_handler());