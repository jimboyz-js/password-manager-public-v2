<?php

header("Content-Type: application/json");

require_once '../../app/db/getMasterKey.php';

echo json_encode(get_master_key());