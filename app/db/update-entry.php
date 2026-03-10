<?php
require_once 'connection.php';
include_once __DIR__.'/../../config.php';

function update_entry_handler() {
    // Session secure
    if(!isset($_SESSION["is_login_success"])) {
        http_response_code(401);
        return["status"=>"error", "message"=>"Unauthorize access", "success"=>false];
        exit;
    }

    $raw = file_get_contents('php://input');
    $data = json_decode($raw, true);

    $password = $data['password']; //password hashed by ui
    $confirm = $data['confirm'];
    $username_hash = $data['username_hash'];
    $page = $data["page"];

    $dt = new DateTime('now', new DateTimeZone('Asia/Manila'));
    // To show PST in the output, include the T format character:
    $dateAdded = $dt->format('Y-m-d H:i:s T');

    // Confirm field was removed from version 2.2 (add or register)
    // Nov. 17, 2025 Mon.
    // See save-entry.php
    // In save-entry.php was first gave description regarding removing the confirm field.
    // if($password_hash !== $confirm) {
    //     respond("failed", "Password doesn't match!", ["success"=>false]);
    //     exit;
    // }

    // As of version 2.2, username cannot be update

    $connection = database_connection();
    
    if(!$connection) {
        return ["success"=>false, "status"=>"failed", "message"=>"Unable to prepare database query."];
        exit;
    }

    $stmt = null;
    try {
        // udpate entry
        if($page === "dashboard") {
            $title = $data['title'];
            $title_hash = $data['title_hash'];
            $updateBy = $data['update_by'];
            $username = $data['username'];
            $note = $data['note'];
            $id = $data["id"];

            $stmt = $connection->prepare('UPDATE `accounts` SET addedBy = ?, account_for = ?, title_hash = ?, username = ?, username_hash = ?, password = ?, note = ? WHERE id = ?');
            $stmt->bind_param('ssssssss', $updateBy, $title, $title_hash, $username, $username_hash, $password, $note, $id);
            $stmt->execute();
            
            if ($stmt->affected_rows > 0) {
                return['status'=>'success', 'message'=>'Update successfully!', 'id'=>$stmt->insert_id, 'success'=>true];
            } else {
                return['status'=>'failed', 'message'=>'Failed to update data: '.$stmt->error, "success"=>false];
            }

        } else if ($page === "admin") {

            $lastname = $data["lastname"];
            $firstname = $data["firstname"]; //encrypted firstname (ui)
            $firstname_hash = $data["firstname_hash"]; //firstname hashed (ui)
            $lastname_hash = $data["lastname_hash"]; // These hashes (fname and lname) use for search. It update also when the user edit the data.
            $isPass = $data["isPass"];
            $oldPass = $data["old_password"];
            $pass = password_hash($password, PASSWORD_DEFAULT); // $password arg is encrypted password from the ui, then it assign to a var $pass as hashed password.
            $ip = $_SERVER["REMOTE_ADDR"] ?? "UNKNOWN";
            $hostname_by_addr = gethostbyaddr($ip);
            $hostname = gethostname();
            $deviceTerminal = $hostname." | ".$hostname_by_addr;
            
            if($isPass) {

                if($password !== $confirm) {
                    return["status"=>"failed", "message"=>"Password doesn't match!", "success"=>false];
                    exit;
                }

                $stmt = $connection->prepare("SELECT `password_hash` FROM `users` WHERE `username_hash` = ?");
                $stmt->bind_param('s', $username_hash);
                $stmt->execute();
                $stmt->store_result();
                $stmt->bind_result($old);
                $stmt->fetch();

                if($stmt->num_rows > 0) {

                    if(password_verify($oldPass, $old)) {

                        $stmt = $connection->prepare('UPDATE `users` SET lastname = ?, firstname = ?, firstname_hash = ?, lastname_hash = ?, device_terminal = ?, ip = ?, password_hash = ?, dateRegistered = ? WHERE username_hash = ?');
                        $stmt->bind_param('sssssssss', $lastname, $firstname, $firstname_hash, $lastname_hash, $deviceTerminal, $ip, $pass, $dateAdded, $username_hash);   
                    } else {
                        return['status'=>'failed', 'message'=>'Unknown old password.', "success"=>false];
                        exit;
                    }
                }

            } else {
                $stmt = $connection->prepare('UPDATE `users` SET lastname = ?, firstname = ?, firstname_hash = ?, lastname_hash = ?, device_terminal = ?, ip = ?, dateRegistered = ? WHERE username_hash = ?');
                $stmt->bind_param('ssssssss', $lastname, $firstname, $firstname_hash, $lastname_hash, $deviceTerminal, $ip, $dateAdded, $username_hash);
                // $stmt->execute();
            }

            $stmt->execute();
            if ($stmt->affected_rows > 0) {
                return['status'=>'success', 'message'=>'Update successfully!', 'id'=>$stmt->insert_id, 'success'=>true];
            } else {
                return['status'=>'failed', 'message'=>'Failed to update data: '.$stmt->error, "success"=>false];
            }

        } else {
            return['status'=>'failed', 'message'=>'Something went wrong!', "success"=>false];
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