<?php

header("Content-Type: application/json");

require_once '../../app/db/save-entry.php';

echo json_encode(save_entry());