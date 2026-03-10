<?php
/**
 * @author jimBoYz Ni ChOy!!!
 * Finalize: 12-18-2025-4-51-pm
 */

include_once __DIR__.'/../config.php';
require_once('send-email.php');
require_once 'get-url.php';

function sendCode($mode, $subject = 'Your Verification Code'):bool {

    $code = str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT); // e.g., "0777"
    $html = null;
    $app_link = $_ENV["app.site.link"];
    $url = "https://jspasswordmanager.com";
    if(strtolower($mode) === "login") {
        $html = file_get_contents(__DIR__."/email-template/login-email-template.html");
    } else if (strtolower($mode) === "change pass") {
        $html = file_get_contents(__DIR__."/email-template/change-pass-email-template.html");
        $url = $app_link.'/forgot.php';
    } else if (strtolower($mode) === "sign_up") {
        $html = file_get_contents(__DIR__."/email-template/create-account-email-template.html");
        $url = $app_link.'/sign-up.php';
    }
    
    // $username = ""; // Instead of username, I prefer to use firstname in an email template. Disable this part, to use username either pass from JS or in an .env file.
    $firstname = $_ENV["app.firstname"];
    $link = $app_link ?? "https://jspasswordmanager.com";
    $url = $url ?? getFullURL();
    
    $html = str_replace(
        ["{{USERNAME}}", "{{CODE}}", "{{LINK}}", "{{URL}}"],
        [$firstname, $code, $link, $url],
        $html
    );
    if(sendEmail($subject, $html, "<p>Your verification code is <strong>{$code}</strong>. It will expire in 5 minutes.</p>")) {
        
        // Set expiration time 5 minutes from now
        $expireTime = new DateTime('now', new DateTimeZone('Asia/Manila')); // Change to "America/Los_Angeles" if you want
        $expireTime->modify('+5 minutes'); // "+2 minutes" for 2 mins. 10 for mins.

        // Format time as e.g., 1:47 PM PST
        $formattedTime = $expireTime->format('g:i A T');

        // Login mode: specific session for login.
        // $login_mode = strtolower($mode) === "login" ? strtolower($mode) : ""; // Updated: auth-page handle this see auth-page.php:18 (if block)
        // $code = $login_mode !== "" ? $code.$login_mode : $code;
        // $_SESSION['auth_code'.$login_mode] = hash('sha512', $code);

        $_SESSION['formatted-time'] = $formattedTime;
        $_SESSION['auth_code'] = hash('sha512', $code);// Optional: The code is stored via cookie is hash using the algo 'sha512'
        $_SESSION['auth_expires'] = time() + 300; // 600 10 minutes from now. Set 120 for 2 minutes. Version: 2.2.2 I changed to 5 mins.
        
        return true;

    } else {
        return false;
    }

}

function _sendCode($subject, $body):bool {

    $code = str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT); // e.g., "0777"

    if(sendEmail($subject, $body, "<p>Your authentication code is <strong>{$code}</strong>. It will expire in 5 minutes.</p>")) {

        // Set expiration time 5 minutes from now
        $expireTime = new DateTime('now', new DateTimeZone('Asia/Manila')); // Change to "America/Los_Angeles" if you are in America
        $expireTime->modify('+5 minutes'); // "+2 minutes" for 2 mins.

        // Format time as e.g., 1:47 PM PST
        $formattedTime = $expireTime->format('g:i A T');

        $_SESSION['formatted-time'] = $formattedTime;

        $_SESSION['auth_code'] = hash('sha512', $code);// Optional: The code is stored via cookie is hash using the algo 'sha512'
        $_SESSION['auth_expires'] = time() + 300; // 600 10 minutes from now. Set 120 for 2 minutes.

        return true;

    } else {
        return false;
    }

}