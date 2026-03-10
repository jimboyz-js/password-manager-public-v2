<?php
/**
 * Attempt Tracker
 * @author jimBoYz Ni ChOy!!!
 * --------------------------------
 * This script handles user "Forgot Password" and "Sign up" requests.
 * It tracks the number of attempts made from the same IP address
 * to prevent brute-force or abuse. Only 5 attempts are allowed per IP
 * within a certain time window.
 */

// [Initialization] Include database connection
require_once 'connection.php';
require_once dirname(__DIR__ ) . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__) . '../../');
$dotenv->load();

function attempt_tracker() {
    
    $connection = database_connection();
    
    if(!$connection) {
        return ["success"=>false, "status"=>"failed", "message"=>"Unable to prepare database query."];
        exit;
    }

    $stmt = null;
    try {
        // [Security] Capture the user's IP address
        $ip = $_SERVER['REMOTE_ADDR'];

        // [Attempt Tracking] Check if the IP already exists in the database
        $stmt = $connection->prepare("SELECT attempts, last_attempt FROM password_reset_attempts WHERE ip_address = ?");
        $stmt->bind_param("s", $ip);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($attempts, $last_attempt);
        $stmt->fetch();

        $now = time();

        // [Logic] If record exists, validate attempt count
        if ($stmt->num_rows > 0) {
            $last = strtotime($last_attempt);

            $limit = $_ENV["app.limit.attempt"] ?? 5; 
            // [Security] Limit user to default 5 attempts
            // 86400 = ONE DAY
            if ($attempts >= $limit && ($now - $last) < 86400) {
                // Too many attempts
                // respond("failed", "Too many attempts. Please try again tomorrow.", ["success"=>false]);
                // exit;
                return false;

            } elseif (($now - $last) >= 86400) {
                // Reset counter after 24h
                $stmt = $connection->prepare("UPDATE password_reset_attempts SET attempts = 1, last_attempt = NOW() WHERE ip_address = ?");
                $stmt->bind_param("s", $ip);
                $stmt->execute();
            } else {
                // [Database] Update attempt count
                // Increment attempt
                $stmt = $connection->prepare("UPDATE password_reset_attempts SET attempts = attempts + 1, last_attempt = NOW() WHERE ip_address = ?");
                $stmt->bind_param("s", $ip);
                $stmt->execute();
            }
        } else {
            // [Database] Create new attempt record for IP
            // First attempt
            $stmt = $connection->prepare("INSERT INTO password_reset_attempts (ip_address, attempts) VALUES (?, 1)");
            $stmt->bind_param("s", $ip);
            $stmt->execute();
        }

        return true;

    } catch(Throwable $e) {
        // respond("error", "".$e, ["success"=>false]);
        return false;
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