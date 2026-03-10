<?php

header("Content-Type: application/json");

require_once '../../app/db/getUserAccountData.php';

echo json_encode(get_user_account_data());