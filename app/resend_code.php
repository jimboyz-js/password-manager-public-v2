<?php
/**
 * @author jimBoYz Ni ChOy!!!
 * Finalize: 12-18-2025-4-53-pm (all script)
 */
include_once __DIR__.'/../config.php';
include_once 'get-url.php';
require_once('send-email.php');

function resend_code_handler() {
    
    if(isset($_SESSION["auth_code"])) {
        $code = str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT); // e.g., "0777"
        
        $mode = $_SESSION["page_view"];

        $html = null;
        $app_link = $_ENV["app.site.link"];
        $url = "https://jspasswordmanager.com";

        if(strtolower($mode) === "dashboard_login") {
            $html = file_get_contents(__DIR__."/email-template/login-email-template.html");
        } else if (strtolower($mode) === "change_password") {
            $html = file_get_contents(__DIR__."/email-template/change-pass-email-template.html");
            $url = $app_link.'/forgot.php';
        } else if (strtolower($mode) === "create_account") {
            $html = file_get_contents(__DIR__."/email-template/create-account-email-template.html");
            $url = $app_link.'/sign-up.php';
        }
        
        // $username = ""; // Instead of username, I prefer to use firstname in an email template. Disable this part, to use username either pass from JS or in an .env file.
        $firstname = $_ENV["app.firstname"] ?? "JS";
        $link = $app_link ?? "https://jspasswordmanager.com";
        $url = $url ?? getFullURL();
        
        $html = str_replace(
            ["{{USERNAME}}", "{{CODE}}", "{{LINK}}", "{{URL}}"],
            [$firstname, $code, $link, $url],
            $html
        );

        if(sendEmail('Your Verification Code', $html, "<p>Your authentication code is <strong>{$code}</strong>. It will expire in 5 minutes.</p>")) {
            
            if(isset($_SESSION['resend_timer_expire_at'])) {
                unset($_SESSION['resend_timer_expire_at']);
            }
            
            // Set expiration time 5 minutes from now
            $expireTime = new DateTime('now', new DateTimeZone('Asia/Manila')); // Change to "America/Los_Angeles" if you are in America
            $expireTime->modify('+5 minutes');

            // Format time as e.g., 1:47 PM PST
            $formattedTime = $expireTime->format('g:i A T');

            $_SESSION['formatted-time'] = $formattedTime;

            // temp 120
            $_SESSION['auth_code'] = hash('sha512', $code);// Optional: The code is stored via cookie is hash using the algo 'sha512'
            $_SESSION['auth_expires'] = time() + 300; // 600 10 minutes from now. Set 120 for 2 minutes.
            $_SESSION['is_resend_available'] = false;

            // Set the attempts to 0
            $_SESSION["attempts"] = 0;
            
            // echo json_encode(["email_sent"=>true, "message"=>"Verification code send successfully!"]);
            return["email_sent"=>true, "message"=>"Verification code send successfully!"];

        } else {
            // echo json_encode(["email_sent"=>false, "message"=>"Failed to resend the authentication code. Please try again later."]);
            return["email_sent"=>false, "message"=>"Failed to resend the authentication code. Please try again later."];
        }

    } else {
        http_response_code(401); // Unauthorized
        // echo json_encode(["status"=>"error", "message"=>"Unauthorized access!", "email_sent" => false]);
        return["status"=>"error", "message"=>"Unauthorized access!", "email_sent" => false];
    }
}

?>