<?php

header("Content-Type: application/json");

require_once '../../app/auth_check_session.php';
require_once __DIR__.'/../../config.php';

// Protected by session
if (isset($_SESSION['auth_code'])) {

    if (!isset($_SESSION['resend_timer_expire_at'])) {
        $_SESSION['resend_timer_expire_at'] = time() + 300; // 5 minutes = 300 seconds

    }

    if (!isset($_SESSION['is_resend_available'])) {
        $_SESSION['is_resend_available'] = true;
    }

    $resendTimer = $_SESSION['resend_timer_expire_at'] ?? '';
    $isResendAvailable = $_SESSION['is_resend_available'] ?? false;

    echo json_encode([
        "authenticated" => true,
        "email"=>obfuscate_email($_ENV["app.smtp.email"]),
        "expire_time" => expireTime(),
        "resend_timer" => $resendTimer,
        "resend_available" => $isResendAvailable
    ]);
} else {
    http_response_code(401);
    echo json_encode(["authenticated" => false, "status"=>"error", "message"=>"Unauthorized access!"]);
}