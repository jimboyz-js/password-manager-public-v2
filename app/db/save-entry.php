<?php

require_once 'connection.php';

function save_entry() {
    $raw = file_get_contents('php://input');
    $data = json_decode($raw, true);

    $title = $data['title'];
    $title_hash = $data['title_hash'];
    $addedBy = $data['added_by'];
    $password = $data['password'];
    $password_hash = $data['password_hash'];
    // $confirm = $data['confirm'];
    $username = $data['username'];
    $username_hash = $data['username_hash'];
    $note = $data['note'];

    $dt = new DateTime('now', new DateTimeZone('Asia/Manila'));
    // To show PST in the output, include the T format character:
    $dateAdded = $dt->format('Y-m-d H:i:s T');


    // Confirm was removed in version 2.2
    // if($password_hash !== $confirm) {
    //     respond("failed", "Password doesn't match!", ["success"=>false]);
    //     exit;
    // }

    $connection = database_connection();
    
    if(!$connection) {
        return ["success"=>false, "status"=>"failed", "message"=>"Unable to prepare database query."];
        exit;
    }

    $stmt = null;

    try {
        // insert entry
        $stmt = $connection->prepare('INSERT INTO accounts (addedBy, dateAdded, account_for, title_hash, username, username_hash, password, note) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->bind_param('ssssssss', $addedBy, $dateAdded, $title, $title_hash, $username, $username_hash, $password, $note);
        if ($stmt->execute()) {
            return['status'=>'success', 'message'=>'New entry added successfully!', 'id'=>$stmt->insert_id, 'success'=>true];
        } else {
            return['status'=>'failed', 'message'=>'Failed to add data: '.$stmt->error, "success"=>false];
        }
        
    } catch(Exception $e) {
        http_response_code(500);
        return["status"=>"error", "message"=>"Failed: ".$e, "success"=>false];
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