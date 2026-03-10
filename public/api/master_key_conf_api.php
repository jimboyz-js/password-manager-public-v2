<?php

header("Content-Type: application/json");

require_once '../../app/db/masterKeyConfiguration.php';

echo json_encode(master_key_conf());