<?php
if(session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../../app/db/getData.php';
header('Content-Type: application/json');

echo json_encode(get_data_handler());

