<?php
if(session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'connection.php';

function change_password_handler() {

    $data = json_decode(file_get_contents("php://input"), true);

    $username = $data["username"];
    $password = $data["password"];
    $confirm = $data["confirm"];

    $errors = [];

    if($password === "" || $confirm === "") {
        return["status"=>"failed", "message"=>"Empty fields", "success"=>false, "errors"=>$errors];
        exit;
    }

    if($password !== $confirm) {
        $errors["confirm-password"] = "Password doesn't match!";
        return["status"=>"failed", "message"=>"Password doesn't match!", "success"=>false, "errors"=>$errors];
        exit;
    }

    $password_hashed = password_hash($password, PASSWORD_DEFAULT);

    $connection = database_connection();
    
    if(!$connection) {
        return ["success"=>false, "status"=>"failed", "message"=>"Unable to prepare database query."];
        exit;
    }

    $stmt = null;

    try {

        $stmt = $connection->prepare("SELECT `id` FROM `users` WHERE `username_hash` = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if($stmt->num_rows > 0) {
            $stmt = $connection->prepare("UPDATE `users` SET `password_hash` = ? WHERE `username_hash` = ?");
            $stmt->bind_param("ss", $password_hashed, $username);
            $stmt->execute();

            if($stmt->affected_rows > 0) {
                
                // Unset session...
                unset($_SESSION["forgot_pass_page"]);

                return["status"=>"success", "message"=>"Password change successfully!", "success"=>true];
            }

            return["status"=>"failed", "message"=>"Something went wrong! Please try again later.", "success"=>false];
            
        } else {
            $errors["username"] = "Username does not exist.";
            return["status"=>"failed", "message"=>"Username does not exist.", "success" => false, "errors"=>$errors];
        }

    } catch(Exception $e) {
        return['status'=>'failed', "message"=>$e->getMessage(), "success"=>false];
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