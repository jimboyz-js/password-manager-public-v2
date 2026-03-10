<?php
/**
 * @author jimBoYz Ni ChOy!!!
 */

require_once 'authentication_code.php';
require_once 'db/handle_attempt_tracker.php';
require_once __DIR__.'/../config.php';

/**
 * Updated: Nov. 27, 2025 (v2.2.1) Thu. 3:02 PM
 * You can also call the attemt_tracker() function outside sendCode(...) "if statement block".
 * If the admin user want to capture or track forgot/sign-up attempt before sending (sent) the authentication code.
 */

function user_auth_handler() {
    // Call the method attemt_tracker() from handle_attempt_tracker.php
    // Capture first the attempt to forgot the password before the authentication code send.
    if(!attempt_tracker()) {
        // return["status"=>"failed", "message"=>"Failed to send the authentication code. Please try again later.", "success"=>false];
        return["status"=>"failed", "message"=>"Too many attempts. Please try again tomorrow.", "success"=>false];
        exit;
    }

    $data = json_decode(file_get_contents("php://input"), true);

    // Version 2.2.2 $mode the script change to POST from GET
    // $mode = $_GET["mode"] ?? null;
    $mode = $data["mode"] ?? null;
    if($mode === "reset") {
        $mode = "Change Pass";
    }

    if(sendCode($mode)) {

        // Updated: unused this block of code in version 2.2.1. It was used since 2.2.0. [updated: 11-27-2025 3:11 PM Thu.]
        // Call the method attemt_tracker() from handle_attempt_tracker.php
        // Track the IP and forgot/sign-up attempts after the auth code send.
        // if(!attempt_tracker()) {
        //     http_response_code(500);
        //     respond("failed", "Something went wrong. Please try again later.", ["success"=>false]);
        //     exit;
        // }

        // Get the element by sent from user_auth.js
        // Use to page_view session that will be validate later by the auth.php
        // $page_view_id default is "change_password" view.

        // Version 2.2.2 FROM GET change to POST see user-auth.js
        // $page_view_id = isset($_GET["id"]) ? $_GET["id"] : "change_password";
        $page_view_id = isset($data["id"]) ? $data["id"] : "change_password";

        // Use for validation for auth.php after successfully match the code. It will navigate whether dashboard or forgot and sign-up page.
        // Use for change_password.php
        // For checking if changing password or signing up.
        $_SESSION["page_view"] = $page_view_id;
        
        return["status"=>"success", "message"=>"Authentication code sent successfully!", "success"=>true];
    } else {
        return["status"=>"failed", "message"=>"Failed to send the authentication code. Please try again later.", "success"=>false];
    }
}
