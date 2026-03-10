<?php

header("Content-Type: application/json");

require_once '../../app/db/update-entry.php';

echo json_encode(update_entry_handler());