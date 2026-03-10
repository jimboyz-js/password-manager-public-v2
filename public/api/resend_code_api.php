<?php
/**
 * @author jimBoYz Ni ChOy!!!
 */
if(session_status() === PHP_SESSION_NONE) {
    session_start();
}
header("Content-Type: application/json");

require_once '../../app/resend_code.php';
$response = resend_code_handler();
echo json_encode($response);

?>