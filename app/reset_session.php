<?php
/**
 * @author jimBoYz Ni ChOy!!!
 * This PHP Script to reset a session. This is optional because logout.php can handle this.
 * Updated script: Dec. 04, 2025 THU. 1:12 PM
 */
include_once __DIR__.'/../config.php';

function reset_session_handler() {
    if($_SERVER["REQUEST_METHOD"] === "POST") {

        // To avoid duplicate
        // Session exists
        if(session_status() !== PHP_SESSION_NONE) {
            session_unset();
            session_destroy();
            return["status"=>"success", "message"=>"Session expired due to inactivity.", "success"=> true];
        } else {
            return["status"=>"error", "message"=>"Session already reset.", "success"=> false];
        }
    }

    return["status"=>"error", "message"=>"Invalid request", "success"=> false];
}