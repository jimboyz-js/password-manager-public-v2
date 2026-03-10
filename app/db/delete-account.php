<?php

require_once 'connection.php';
include_once __DIR__.'/../../config.php';

function delete_data_handler() {
    // Protected by session
    if(!isset($_SESSION["is_login_success"])) {
        return["failed", "message"=>"Unauthorized access!", "success" => false];
        exit;
    }

    $connection = database_connection();
    
    if(!$connection) {
        return ["success"=>false, "status"=>"failed", "message"=>"Unable to prepare database query."];
        exit;
    }

    $stmt = null;

    try {

        if($_SERVER["REQUEST_METHOD"] === "POST") {
            $id = $_POST["id"];
            $table = $_POST["table_name"];
            
            /**
             * 12-18-2025 THU. 4:43 PM
             * Security check: allow deletion only from explicitly approved tables.
             * This prevents table-name manipulation via crafted requests.
             * Tools: burp suite, postmane and etc.
             * I successfully manipulates the table using burp and delete data from the other table.
             * Allowed tables: users, accounts.
             */

            /**
             * This version is for multiple table and have a role
             *
             * $allowedTables = [
             *   'dashboard' => ['posts', 'comments'],
             *   'admin'     => ['users', 'posts', 'comments', 'logs']
             * ];
             * $role = $_SESSION['role']; // dashboard or admin
             * if (!in_array($table, $allowedTables[$role], true)) {
             *   http_response_code(403);
             *   exit('Unauthorized table access');
             * }
             * 
             */

            /**
             * Filter the table
             * 
             * if($table === "users") {
             *   $table = "users";
             * } else if($table === "accounts") {
             *   $table = "accounts";
             * } else {
             *   http_response_code(403);
             *   exit('Unauthorized table access');
             * }
             */

            // Cleaner version
            // I have two options (minimal configuration in the server side) whether I put a SESSION role in the dashboard.php and admin.php or filter only the JS,
            if ($table !== 'users' && $table !== 'accounts') {
                http_response_code(403);
                return["status"=>"failed", "message"=>"Unknown table `$table`.", "success" => false];
                exit('Unauthorized table access');
            }
            
            $stmt = $connection->prepare("DELETE FROM `$table` WHERE `id` = ?");
            $stmt->bind_param('i', $id);
            $stmt->execute();

            if($stmt->affected_rows > 0) {
                return["status"=>"success", "message"=>"ID: $id was deleted successfully!", "success" => true];
            } else {
                return["status"=>"failed", "message"=>"Failed to delete the data — ID: $id", "success" => false];
            }

            exit;
        }

        return["status"=>"failed", "message"=>"Invalid request", "success" => false];

    } catch(Exception $e) {
        return["status"=>"failed", "message"=>$e->getMessage(), "success" => false];
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