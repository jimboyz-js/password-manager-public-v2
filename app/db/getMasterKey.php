<?php
require_once 'connection.php';

function get_master_key() {
    $data = json_decode(file_get_contents("php://input"), true);

    $key_hash = $data["key"];
    $id = $data["id"];
    $username_hash = $data["username_hash"];
    // if(!$id) { to be review
    //     http_response_code(400);
    //     respond("failed", "Invalid ID: ".$id);
    //     exit;
    // }

    $connection = database_connection();
    
    if(!$connection) {
        return ["success"=>false, "status"=>"failed", "message"=>"Unable to prepare database query."];
        exit;
    }

    $stmt = null;

    try {
        $stmt = $connection->prepare("SELECT m.`key_hash` FROM `master_key` m INNER JOIN users u ON m.user_id = u.id WHERE m.key_hash = ? AND u.username_hash = ?");
        $stmt->bind_param("ss", $key_hash, $username_hash);
        $stmt->execute();
        $stmt->store_result();

        if($stmt->num_rows > 0) {
            return["status"=>"success", "message"=>"Success", "success"=>true];
            exit;
        }

        return["status"=>"failed", "message"=>"Unknown master key!", "success"=>false];

    } catch (Throwable $e) {
        return["status"=>"failed", "message"=>$e->getMessage(), "success"=>false];
    }

    finally {
        if($stmt !== null) {
            $stmt->close();
        }

        if($connection !== null) {
            $connection->close();
        }
    }
}

