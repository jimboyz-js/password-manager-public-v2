<?php
include_once dirname(__DIR__).'/config.php';
include 'page-utilities/loading.php';

// Defined from auth.php
if(!isset($_SESSION["forgot_pass_page"])) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/forgot-password.css">
    <link rel="icon" type="images/svg+xml" href="./images/js-software-itsupport1 - favicon0.png">
    <title>Forgot Password</title>
</head>

<body class="bg-dark">
    <div class="container-fluid main-container d-flex justify-content-center align-items-center">
        <div class="form-container d-flex flex-column justify-content-center align-item-center rounded bg-white">
            <div class="container-fluid d-flex justify-content-center mb-3">
                <img src="images/js-software-itsupport1 - favicon0.png" width="50px" alt="jimBoYz's Logo">
            </div>
            <div class="container-fluid d-flex justify-content-center mb-3">
                <h3 class="forgot-desc text-dark">Forgot Password</h3>
            </div>
            <form id="forgotForm" class="w-100" novalidate>
                <div class="forgot-form-body">
                    <div class="mb-3">
                        <label for="username" class="form-label text-dark">Username</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                        <div class="invalid-feedback" id="error-username"></div>
                    </div>
                    <div class="mb-3">
                        <label for="new-password" class="form-label text-dark">New Password</label>
                        <input type="password" class="form-control" id="new-password" name="new-password" required
                            autocomplete="off">
                        <div class="invalid-feedback text-dark" id="error-new-password"></div>
                    </div>
                    <div class="mb-3">
                        <label for="confirm-password" class="form-label text-dark">Confirm Password</label>
                        <input type="password" class="form-control" id="confirm-password" name="confirm-password"
                            autocomplete="off" required>
                        <div class="invalid-feedback" id="error-confirm-password"></div>
                    </div>
                </div>

                <div class="d-flex justify-content-center align-items-center">
                    <button type="submit" class="btn btn-primary w-100" name="forgot" value="1">Change</button>
                </div>
            </form>
            <h3 class="sign-up d-flex justify-content-center align-item-center mt-2 text-dark">Don't have an account?
                <span id="create_account" class="user-auth">Sign up</span></h3>
        </div>

        <?php graduallyProgressBar();?>

    </div>
    <div class="banner">
        <img src="images/js-software-itsupport1-final-02.png" width="210px" alt="jimBoYz Logo">
    </div>

    <script type="module" src="./script/forgot.js"></script>
    <script type="module" src="./script/user-auth.js?v=1.0&mode=sign_up"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <script src="./script/disable-dev-tools.js"></script>
</body>

</html>