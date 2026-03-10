<?php
if(session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'connection.php';

function master_key_conf() {

    if(!isset($_SESSION["is_login_success"])) {
        http_response_code(403);
        respond("failed", "Forbidden", ["success"=>false]);
        exit;
    }

    $data = json_decode(file_get_contents("php://input"), true);
    $master_key = $data["key"];
    $id = $data["id"];

    $stmt = null;
    try {

        $stmt = $connection->prepare("INSERT INTO `master_key`(`key_hash`, `user_id`) VALUES(?,?)");
        $stmt->bind_param("si", $master_key, $id);
        $stmt->execute();

        if($stmt->affected_rows > 0) {
            // respond("success", "New master key created successfully!", ["success"=>true]);
            return["status"=>"success", "message"=>"New master key created successfully!", "success"=>true];
        } else {
            return["status"=>"failed", "message"=>"Failed to create key!", "success"=>false];
        }

    } catch(Exception $e) {
        return["status"=>"failed", "message"=>$e->getMessage(), "success"=>false];
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
