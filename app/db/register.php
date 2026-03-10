<?php
/**
 * @author jimBoYz Ni ChOy!!!
 * Dec. 03, 2025 Wed. 2:50 PM
 */

require_once 'connection.php';

function register_handler() {
    
    $raw = file_get_contents("php://input");
    $data = json_decode($raw, true);

    $firstname_hash = $data["firstname_hash"];
    $lastname_hash = $data["lastname_hash"];
    $firstname = $data["firstname"];
    $lastname = $data["lastname"];

    $username = $data["username"];
    $username_hash = $data["username_hash"];
    $password = $data["password"]; // Fron-end hashed pass
    $confirm = $data["confirm"];

    $key_hash = $data["key_hash"];

    $ip = $_SERVER["REMOTE_ADDR"] ?? "UNKNOWN";
    $hostname_by_addr = gethostbyaddr($ip);
    $hostname = gethostname();
    $device_info = $hostname." | ".$hostname_by_addr;

    $dt = new DateTime('now', new DateTimeZone('Asia/Manila'));
    $dateAdded = $dt->format('Y-m-d H:i:s T');
    $errors = [];

    if(!$username_hash) {
        $errors["username"] = "Username is empty";
        return["status"=>"failed", "message"=>"Failed to create account", "success"=>false, "errors"=>$errors];
    }

    if(!$password) {
        $errors["password"] = "Password is empty";
        return["success"=>false, "status"=>"failed", "message"=>"Failed to create account", "errors"=>$errors];
    }

    if(!$confirm) {
        $errors["confirm-password"] = "Required";
        return["status"=>"failed", "message"=>"Failed to create account", "success"=>false, "errors"=>$errors];
        // exit;
    }

    if($password !== $confirm) {
        return["status"=>"error", "message"=>"Password doesn't match!", "success"=>false];
        exit;
    }

    $pass = password_hash($password, PASSWORD_DEFAULT);

    $connection = database_connection();
    
    if(!$connection) {
        return ["success"=>false, "status"=>"failed", "message"=>"Unable to prepare database query."];
        exit;
    }

    $stmt = null;
    $stmt2 = null;

    try {

        $connection->begin_transaction();

        $query = "INSERT INTO `users` (`firstname_hash`, `lastname_hash`, `username_hash`, `password_hash`, `firstname`, `lastname`, `username`, `device_terminal`, `ip`, `dateRegistered`) VALUES (?,?,?,?,?,?,?,?,?,?)";
        $stmt = $connection->prepare($query);
        $stmt->bind_param("ssssssssss", $firstname_hash, $lastname_hash, $username_hash, $pass, $firstname, $lastname, $username, $device_info, $ip, $dateAdded);
        $stmt->execute();

        $user_id = $connection->insert_id;

        $stmt2 = $connection->prepare("INSERT INTO `master_key`(`user_id`, `key_hash`) VALUES(?, ?)");
        $stmt2->bind_param("is", $user_id, $key_hash);
        $stmt2->execute();

        $connection->commit();

        if($stmt->affected_rows > 0 && $stmt2->affected_rows > 0) {
            return["status"=>"success", "message"=>"New account created successfully!", "success"=>true];

        } else {
            return["status"=>"failed", "message"=>"Failed to create account", "success"=>false, "errors"=>$errors];
        }
        
    } catch(Throwable $e) {
        $connection->rollback();
        // MySQL Duplicate entry error code
        $code = $e->getCode(); //1062 (MySQL)
        $msg = $e->getMessage(); // Contains "Duplicate entry"
        if($code === 1062 || str_contains($msg, 'Duplicate entry')) {
            return["status"=>"failed", "message"=>"Username is already taken.", "success"=>false];
        }
        return["status"=>"failed", "message"=>"Failed to create account: ".$msg, "success"=>false];
    }

    finally {
        
        if($stmt) {
            $stmt->close();
        }

        if($stmt2) {
            $stmt2->close();
        }

        if($connection) {
            $connection->close();
        }
    }
}