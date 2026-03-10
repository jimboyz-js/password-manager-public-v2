<?php
/**
 * @author jimBoYz Ni ChOy!!!
 */

include_once __DIR__.'/../config.php';

// Protected by session
// See authentication_code.php under sendCode(...)
function auth_app_handler() {
    if(isset($_SESSION['auth_code'])) {

        $is_success = false;
        $message = "";
        $page_view = "dashboard_login"; // default

        if($_SERVER["REQUEST_METHOD"] === "POST") {

            $userCode = $_POST['verification-code'] ?? '';
            if (!isset($_SESSION['auth_code']) || !isset($_SESSION['auth_expires'])) {
                $message = "No code to verify.";
            }

            if(!isset($_SESSION["attempts"])) {
                $_SESSION["attempts"] = 0;
            }

            if(isset($_SESSION["attempts"])) {

                if($_SESSION["attempts"] >= 5) {
                    $message = "Too many attempts. Please request a new code.";
                    $is_success = false;

                } else {

                    if (hash("sha512", $userCode) === $_SESSION['auth_code']) {

                        if (time() > $_SESSION['auth_expires']) {
                            $message = "Code expired. Please request a new one.";

                        } else {

                            // Unset or delete auth_code, formatted-time, auth_expires session and etc.
                            unset($_SESSION['auth_code']);
                            unset($_SESSION['formatted-time']);
                            unset($_SESSION['auth_expires']);
                            unset($_SESSION["attempts"]);

                            $message = "Code match. Success!";
                            $is_success = true;

                            // For validation whether goto dashboard or login page
                            // Check for login.php and forgot.php under sendEmail (auth code)
                            // Validate: auth.js
                            if(isset($_SESSION["page_view"])) {
                                $page_view = $_SESSION["page_view"];

                                if($page_view === "dashboard_login") {
                                    $_SESSION["is_login_success"] = true;
                                }

                                // Use to handle page forgot.php
                                // This session unset by change_password.php
                                if($page_view === "change_password") {
                                    $_SESSION["forgot_pass_page"] = true;
                                }
                            }
                        }

                    } else {
                        $_SESSION["attempts"]++;
                        $message = "Invalid code. Attempts left: 5 - ".$_SESSION["attempts"];
                    }
                }
            }

            // respond($is_success, $message, ["page_view" => trim($page_view), "success"=>$is_success]);
            return["status"=>$is_success, "message"=>$message, "page_view" => trim($page_view), "success"=>$is_success];
        } else {
            // respond("error", "Invalid request", ["page_view" => trim($page_view), "success"=>false]);
            return["status"=>"error", "message"=>"Invalid request", "page_view" => trim($page_view), "success"=>false];
        }

    } else {
        // http_response_code(403);
        // respond("failed", "Forbidden access!", ["success"=>false]);
        // exit('403 Forbidden');
        return["status"=>"failed", "message"=>"Forbidden access!", "success"=>false];
    }
}

?>