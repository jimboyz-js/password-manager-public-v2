<?php
/**
 * @author jimBoYz Ni ChOy!!!
 * @Date Updated: Dec. 04, 2025 THU. 3:57 PM
 */
include_once __DIR__.'/../config.php';

function logout_handler() {
    if(isset($_SESSION["is_login_success"])) {

        if($_SERVER["REQUEST_METHOD"] === "POST") {
            session_unset();
            session_destroy();
            return["status"=>"success", "message"=>"Logout successfully from current session!", "success"=>true];
        } else {
            http_response_code(403); //Forbidden access
            return["status"=>"failed", "message"=>"Invalid request", "success"=>false];
        }
        exit;
    }

    http_response_code(401);
    return["status"=>"failed", "message"=>"Unauthorized request", "success"=>false];
    exit;
}