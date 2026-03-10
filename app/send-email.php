<?php
/**
 * @author jimBoYz Ni ChOy!!!
 * PHP 12-02-2025 6:43PM
 * send-email.php handles SMTP to send verification code for 2FA
 */

// Import PHPMailer classes into the global namespace
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Load Composer's autoloader
require_once 'vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__.'/../');
$dotenv->load();

/**
 * @param subject [Use for subject of an email]
 * @param body [Use for body of an email] 
 */
function sendEmail($subject, $body, $altBody): bool {

    /**
     * Updated docs: Dec. 02, 2025 TUE. 6:54PM
     * @version 2.2.2
     * Remove register_shutdown_function()
     * Converts all standard PHP warnings/notices into catchable exceptions.
     *
     * Purpose:
     * - Ensures that any runtime warning (undefined index, deprecated function, etc.)
     *   can be handled inside try/catch blocks.
     * - Prevents accidental exposure of internal warnings to JSON/API responses.
     * - Useful when this script is included by other PHP files where unwanted
     *   warnings must not leak to the output.
     *
     * Note:
     * - This only handles non-fatal errors (E_WARNING, E_NOTICE, etc.).
     * - Fatal errors (E_ERROR, parse errors, out-of-memory) are NOT caught here;
     *   they require register_shutdown_function() if you want to handle them.
     */

    /**
     * If You Use Only set_error_handler()
     * Converts PHP warnings/notices into exceptions for consistent JSON error handling.
     * Does not catch fatal errors (use register_shutdown_function() for that).
     */

    /**
     * Enable exception-based error handling.
     *
     * Converts PHP warnings/notices into exceptions so errors can be captured inside
     * try/catch blocks and returned as clean JSON responses. This is useful for API
     * endpoints and included scripts to avoid unintentional warning output.
     *
     * Note: Fatal errors are not caught by this handler.
     */

    // Begins capturing all output (including hidden PHPMailer echoes or warnings)
    // Start output buffering
    ob_start();

    set_error_handler(function($severity, $message) {
        throw new \ErrorException($message, 0, $severity);
    });

    $mail = new PHPMailer(true); // remove args if you do not want try/catch
    $mail->SMTPDebug = 0; // (default) force no debug output put 1 for debug
    $smtp_username = $_ENV['app.smtp.username'];
    $smtp_password = $_ENV['app.smtp.password'];
    $smtp_email = $_ENV["app.smtp.email"];
    $smtp_host = $_ENV["app.smtp.host"];

    try {

        // (Optional) Prevent short script timeout — adjust if needed
        // Default is 30
        set_time_limit(60);

        // Server settings
        $mail->isSMTP();                                            // Use SMTP
        $mail->Host       = $smtp_host;                             // Set the SMTP server
        $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
        $mail->Username   = $smtp_username;                         // SMTP username
        $mail->Password   = $smtp_password;                         // SMTP password (use app password if 2FA is on)
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // Enable TLS encryption
        $mail->Port       = 587;                                    // TCP port to connect to

        // Recipients
        // Email settings
        // $mail->setFrom('no-reply@example.com', 'Your App');
        $mail->setFrom($smtp_username, 'Password Manager V2.2.2');// App Name Version
        $mail->addAddress($smtp_email, $smtp_username);

        // Content
        $mail->isHTML(true);                                       // Set email format to HTML
        $mail->Subject = $subject;
        $mail->Body    = $body;
        $mail->AltBody = $altBody;

        $mail->send();
        // Clean up any buffer content before returning JSON
        ob_end_clean();
        restore_error_handler();   // IMPORTANT
        return true;
        
    } catch (\Throwable $e) {
        error_log("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
        restore_error_handler();   // restore default error handling
        // Clean buffer before outputting JSON error
        ob_end_clean();
        return false;
    }
}