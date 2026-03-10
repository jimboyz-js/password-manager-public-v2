<?php

header("Content-Type: application/json");

require_once '../../app/db/delete-account.php';

echo json_encode(delete_data_handler());