<?php
/**
 * @author jimBoYz Ni ChOy!!!
 */
include_once '../config.php';
include_once 'page-utilities/popup-modal.php';
include 'page-utilities/loading.php';

if(!isset($_SESSION["page_view"])) {
    header("Location: login.php");
    exit;
}

if($_SESSION["page_view"] !== "create_account") {
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
    <link rel="stylesheet" href="css/sign-up.css?v=1.0">
    <link rel="icon" type="images/svg+xml" href="./images/js-software-itsupport1 - favicon0.png">
    <title>Sign-up to Password Manager v2.2.2</title>
</head>

<body class="bg-dark">
    <div class="container-fluid main-container d-flex justify-content-center align-items-center">
        <div class="form-container d-flex flex-column justify-content-center align-item-center rounded bg-white">
            <div class="container-fluid d-flex justify-content-center mb-3">
                <img src="images/js-software-itsupport1 - favicon0.png" width="50px" alt="jimBoYz's Logo">
            </div>
            <div class="container-fluid d-flex justify-content-center mb-2">
                <h3 class="sign-up-desc text-dark">Sign-up to password manager</h3>
            </div>
            <form id="signupForm" class="w-100" novalidate data-mode="add">
                
                <div class="mb-2">
                    <label for="firstname" class="form-label text-dark">Firstname</label>
                    <input type="text" class="form-control" id="firstname" name="firstname" required>
                </div>
                <div class="mb-2">
                    <label for="lastname" class="form-label text-dark">Lastname</label>
                    <input type="text" class="form-control" id="lastname" name="lastname" required>
                </div>
                <div class="mb-2">
                    <label for="username" class="form-label text-dark">Username</label>
                    <input type="text" class="form-control" id="username" name="username" required>
                    <div class="invalid-feedback" id="error-username"></div>
                </div>
                <div class="mb-2">
                    <label for="master-key" class="form-label text-dark">Key</label>
                    <input type="text" class="form-control" id="master-key" name="master-key" required>
                    <div class="invalid-feedback" id="error-master-key"></div>
                </div>
                <div class="mb-2">
                    <label for="password" class="form-label text-dark">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required
                        autocomplete="off">
                    <div class="invalid-feedback" id="error-password"></div>
                </div>
                <div class="mb-3">
                    <label for="confirm-password" class="form-label text-dark">Confirm Password</label>
                    <input type="password" class="form-control" id="confirm-password" name="confirm-password" required
                        autocomplete="off">
                    <div class="invalid-feedback" id="error-password"></div>
                </div>

                <div class="d-flex justify-content-center align-items-center">
                    <button type="submit" class="btn btn-primary w-100" value="1">Create</button>
                </div>
            </form>
            <h3 class="account-exists d-flex justify-content-center align-item-center mt-2 text-dark">Already have an
                account?
            <span><a href="./login.php">Sign in</a></span></h3>
        </div>

        <?php customModal(); ?>
        <?php graduallyProgressBar(); ?>

    </div>
    <div class="banner">
        <img src="images/js-software-itsupport1-final-02.png" width="210px" alt="jimBoYz Logo">
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <script type="module" src="script/sign_up.js?v=1.0"></script>
    <script src="./script/disable-dev-tools.js"></script>
</body>

</html>