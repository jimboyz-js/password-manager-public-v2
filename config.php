<?php
/**
 * @author jimBoYz Ni ChOy!!!
 * Date: Dec. 01, 2025 MON. 6:33 PM
 */
// ==========================
// CONFIG.PHP
// Secure session management
// ==========================

// Automatically detect if connection is HTTPS
$isSecure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
         || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443);


// Start session safely
if(session_status() === PHP_SESSION_NONE) {
    
    // Optional: set session configuration
    // ini_set('session.gc_maxlifetime', 3600); // 1-hour session lifetime

    // Set secure session cookie parameters
    session_set_cookie_params([
        'lifetime' => 0,        // 0 = expires when browser closes or you can put 3600
        'path' => '/',
        'secure' => $isSecure,  // Only send cookie over HTTPS if available
        'httponly' => true,     // Prevent JS from accessing the cookie
        'samesite' => 'Lax'     // Helps protect against CSRF
    ]);

    // Enforce extra session security options
    ini_set('session.use_strict_mode', 1);  // Prevent reuse of old session IDs
    ini_set('session.cookie_httponly', 1);  
    ini_set('session.cookie_secure', $isSecure);

    // Start the session
    session_start();
}

// This prevents session fixation attacks — the old session ID becomes invalid.
// session_regenerate_id(true);

// ------------------------------------------
// 1️⃣ Session Hijacking Protection
// ------------------------------------------
if (!isset($_SESSION['fingerprint'])) {
    // Create a browser fingerprint
    $_SESSION['fingerprint'] = md5($_SERVER['HTTP_USER_AGENT']);
} elseif ($_SESSION['fingerprint'] !== md5($_SERVER['HTTP_USER_AGENT'])) {
    // If fingerprint doesn't match → destroy session
    session_unset();
    session_destroy();
    
    header("Location: login.php?error=session_hijack");
    header("Location: ${dirname(__DIR__)} login.php?error=session_hijack");
    respond("logged out", "Unauthorized access! Please login.");
    exit;
}

// ------------------------------------------
// 2️⃣ Inactivity Timeout (auto logout)
// ------------------------------------------
// $timeout = 1800; // 30 minutes

// if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeout) {
//     session_unset();
//     session_destroy();
//     header("Location: login.php?timeout=1");
//     exit;
// }
// $_SESSION['last_activity'] = time();

// ------------------------------------------
// 3️⃣ Optional: Force user to be logged in
// ------------------------------------------
// Example: block pages if user not logged in
/*
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}
*/
?>
