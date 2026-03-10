<?php

/**
 * @author jimBoYz Ni ChOy!!!
 * Dec. 01, 2025 Mon. 4:27 PM
 */
session_start();

include "page-utilities/loading.php";

$ROOT = realpath(__DIR__ . '/../') ?: __DIR__ . '/../';

// installer lock check
if (!file_exists($ROOT . '/.env')) {
    header("Location: ../install/index.php?step=1");
    exit;
}

if (isset($_SESSION["is_login_success"])) {

    $params = [];

    if(isset($_SESSION["status"])) $params['status'] = $_SESSION["status"];
    if(isset($_SESSION["id"]))     $params['id']     = $_SESSION["id"];
    if(isset($_SESSION["k"]))      $params['k']      = $_SESSION["k"];
    if(isset($_SESSION["page"]))   $params['page']   = $_SESSION["page"];
    if(isset($_SESSION["m_id"]))   $params['m_id']   = $_SESSION["m_id"];

    $param = http_build_query($params);
    
    header("Location: dashboard.php?$param");
    exit;
}

// Reset
session_unset();
session_destroy();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/login.css">
    <link rel="icon" type="images/svg+xml" href="./images/js-software-itsupport1 - favicon0.png">
    <title>Login to Password Manager v2.2.2</title>
</head>

<body class="bg-dark">

    <div class="container-fluid main-container d-flex justify-content-center flex-column align-items-center">

        <div class="error-box" id="errorMsg">
            <span id="feedback"></span>
            <button id="js-777" class="close-btn" type="button" onclick="document.getElementById('errorMsg').style.display='none'" >&times;</button>
        </div>

        <div class="form-container d-flex flex-column justify-content-center align-item-center rounded bg-white mx-5">
            <div class="container-fluid d-flex justify-content-center mb-3">
                <img class="js-logo" src="images/js-software-itsupport1 - favicon0.png" width="50px" alt="jimBoYz's Logo">
            </div>
            <div class="container-fluid d-flex justify-content-center mb-3">
                <h3 class="sign-in-desc text-dark">Sign in to password manager</h3>
            </div>
            
            <form id="loginForm" class="w-100" novalidate>
                <div class="login-form-body">
                    <div class="mb-3">
                        <label for="username" class="form-label text-dark">Username</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                        <div class="invalid-feedback" id="error-username"></div>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label text-dark">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required
                            autocomplete="off">
                        <div class="invalid-feedback" id="error-password"></div>
                    </div>
                    <div class="mb-3 key-container" style="display:none;">
                        <label for="master-key" class="form-label text-dark">Master Key</label>
                        <input type="password" class="form-control" id="master-key" name="master-key"
                            autocomplete="off">
                        <div class="invalid-feedback" id="error-master-key"></div>
                    </div>
                </div>

                <div class="d-flex justify-content-center align-items-center">
                    <button type="submit" name="login" class="btn btn-primary w-100" value="1">Login</button>
                </div>
            </form>
            <h3 class="forgot d-flex justify-content-center align-item-center mt-2 text-dark">Forgot password? <span
                    id="change_password" class="user-auth"> Forgot</span></h3>
        </div>

        <?php infiniteLoading()?>
        <?php graduallyProgressBar()?>
    
    </div>

    <div class="banner">
        <img src="./images/js-software-itsupport1-final-02.png" width="210px" alt="jimBoYz Logo">
    </div>
    <script>
        let isBlock = false;
        document.addEventListener("keydown", (e)=>{
            
            if(e.ctrlKey && e.key.toLowerCase() === "k") {
                e.preventDefault();
                const container = document.querySelector(".key-container");
                isBlock = !isBlock;
                container.style.display = isBlock ? "block" : "none";
            }
        })
    </script>
    <script type="module" src="./script/login.js"></script>
    <script type="module" id="authScript" src="./script/user-auth.js?v=1.0&mode=reset"></script>
    <script src="./script/disable-dev-tools.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>