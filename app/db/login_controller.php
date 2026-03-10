<?php

// header("Content-Type: application/json; charset=UTF-8");

require_once 'connection.php';
require_once __DIR__.'/../authentication_code.php';

function login_handler($username, $password, $key_hash) {
    // global $connection;
    $stmt = null;

    if(!$username || !$password) {
        http_response_code(400);
        return["success"=>false, "status"=>"error", "message"=>"Bad request"];
        exit;
    }

    $connection = database_connection();
    
    if(!$connection) {
        return ["success"=>false, "status"=>"failed", "message"=>"Unable to prepare database query."];
        exit;
    }

    try {

        $stmt = $connection->prepare("SELECT u.`password_hash`, m.`key_hash`, u.`id`, m.`id` FROM `users` u INNER JOIN `master_key` m ON u.id = m.user_id WHERE u.`username_hash` = ?");
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($hashPass, $keyHash, $id, $m_id);
        $stmt->fetch();

        // Use this fallback if didn't wrap this function "register_shutdown_function" from inside sendEmail function from send-email.php

        if($stmt->num_rows > 0) {
            if(password_verify($password, $hashPass)) {

                if($key_hash !== "") {
                    if($key_hash !== $keyHash) {
                        return["success"=>false, "status"=>"failed", "message"=>"Invalid master key"];
                        exit;
                    }
                }

                if(sendCode("Login")) {
                    // Use for validation for auth.php after successfully match the code. It will navigate whether dashboard, sign_up or forgot page.
                    // For checking if changing password or logging in.
                    // In v2.1 it was named "process".
                    // See also auth.php and auth.js
                    $_SESSION["page_view"] = "dashboard_login";

                    return["success"=>true, "status"=>"success", "message"=>"Authentication code sent successfully!", "key_hash"=>$keyHash, "id"=>$id, "m_id"=>$m_id];
                    exit;
                }
                
                return["success"=>false, "status"=>"failed", "message"=>"Failed to send the authentication code. Please try again later."];

            } else {
                return["success"=>false, "status"=>"failed", "message"=>"Wrong password"];
            }
            
            exit;
        }

        return["success"=>false, "status"=>"failed", "message"=>"Wrong username or password"];

    } catch(Exception $e) {
        return["success"=>false, "status"=>"error", "message"=>"".$e];
    }

    finally {
        if($stmt) {
            $stmt->close();
        }

        if($connection) {
            $connection->close();
        }
    }
}