<?php
/**
 * @author jimBoYz Ni ChOy!!!
 * Date: Dec. 01, 2025 MON. 7:39 PM
 */

if(session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once 'page-utilities/loading.php';

// Defined from auth.php
if(isset($_SESSION["is_login_success"])) {
    header("Location: dashboard.php");
    exit;
}

// Defined from authentication_code.php
if(!isset($_SESSION["auth_code"])) {

    // Ensure that for login page specific session dashboard_login
    // The login page is not using the same session to open this page (auth-page.php).
    // "dashboard_login" defined from login_controller.php
    if (isset($_SESSION["dashboard_login"])) {
        return;
    } else {
        header("Location: login.php");
        exit;
    }
} 

// If user tries to change it manually
if(isset($_SESSION["status"])) {
    if (isset($_GET['status']) && $_GET['status'] !== $_SESSION['status']) {
        // Redirect back to the correct value
        header("Location: ?status=" . urlencode($_SESSION['status']) . "&id=".urlencode($_SESSION["id"])."&k=".urlencode($_SESSION["k"]));
        exit;
    }
}

if(isset($_SESSION["id"])) {
    if (isset($_GET['id']) && $_GET['id'] !== $_SESSION['id']) {
        header("Location: ?status=" . urlencode($_SESSION['status']) . "&id=".urlencode($_SESSION["id"])."&k=".urlencode($_SESSION["k"]));
        exit;
    }
}

if(isset($_SESSION["k"])) {
    if (isset($_GET['k']) && $_GET['k'] !== $_SESSION['k']) {
        header("Location: ?status=" . urlencode($_SESSION['status']) . "&id=".urlencode($_SESSION["id"])."&k=".urlencode($_SESSION["k"]));
        exit;
    }
}

if(isset($_SESSION["m_id"])) {
    if (isset($_GET['m_id']) && $_GET['m_id'] !== $_SESSION['m_id']) {
        header("Location: ?status=" . urlencode($_SESSION['status']) . "&id=".urlencode($_SESSION["id"])."&k=".urlencode($_SESSION["k"])."&m_id=".urlencode($_SESSION["m_id"]));
        exit;
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/auth.css?v=1.0">
    <link rel="icon" type="images/svg+xml" href="./images/js-software-itsupport1 - favicon0.png">
    <title>Login to Password Manager v2.2.2</title>
</head>

<body class="bg-dark">
    <div class="container-fluid main-container d-flex justify-content-center align-items-center">
        <div class="form-container d-flex flex-column justify-content-center align-item-center rounded bg-white">
            <div class="container-fluid d-flex flex-column justify-content-center align-items-center mb-2">
                <i class="fa-regular fa-envelope fa-3x"></i>
                <h3 class="text-desc">Email</h3>
            </div>
            <p class="text-dark">We just sent your authentication code via email to <span id="email"></span>. The code
                will expire at <span id="expire_time"></span>.</p>
            <form id="authForm" class="w-100" novalidate>
                <div class="verification-form-body">
                    <div class="mb-3">
                        <label for="code" class="form-label text-dark">Device Verification Code</label>
                        <input type="text" class="form-control" id="code" name="verification-code" autocomplete="off"
                            required>
                        <div class="invalid-feedback" id="error-code"></div>
                    </div>
                </div>

                <div class="d-flex justify-content-center align-items-center">
                    <button type="submit" class="btn btn-primary w-100" name="submit-code" value="1">Verify</button>
                </div>
            </form>
            <h3 class="resend-timer d-flex justify-content-center align-item-center mt-2 text-dark">You can resend the
                code in: <span id='timer'>-:-</span></h3>
        </div>

        <?php spinnerLoading()?>

    </div>
    <div class="banner">
        <img src="images/js-software-itsupport1-final-02.png" width="210px" alt="jimBoYz Logo">
    </div>
    <script type="module" src="./script/auth.js?v=1.0"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <script src="./script/disable-dev-tools.js"></script>
</body>

</html>