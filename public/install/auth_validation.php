<?php
if(session_status() === PHP_SESSION_NONE) {
    session_start();
}

if(isset($_SESSION['install_step'])) {
    $target = $_SESSION['install_step'];
}

$errors = [];

if($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["verify_code"])) {
    $auth_code = trim($_POST["auth_code"] ?? "");

    if($auth_code === $_SESSION["smtp_auth_code"]) {
        // Unlock next step (4)
        $_SESSION['install_step'] = 4;
        // unset this session after use
        unset($_SESSION["smtp_auth_code"]);
        // redirect to step 4
        header("Location: index.php?step=4");
        exit;
    } else {
        $errors["invalid_code"] = "Invalid authentication code.";
    }
}
?>


<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Auth Validation</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="icon" type="images/svg+xml" href="/images/js-software-itsupport1 - favicon0.png">
  <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
  <div class="col-md-7 offset-md-2 install-card">
    <?php if (!isset($_SESSION["smtp_auth_code"])): ?>
        <!-- <h4>Uncomplete steps!</h4> -->
         <h4>Uncomplete Setup!</h4>
        <div class="alert alert-danger">
            Complete the previous steps first.
        </div>
        <div class="d-flex justify-content-between">
        <a class="btn btn-secondary" href="index.php?step=<?=$target ?? 1?>">← Back</a>
      </div>
    <?php else:?>
    <h4>SMTP Code</h4>
    <p>We just sent your authentication code via email to <strong><?= $_SESSION["installer_smtp"]["SMTP_EMAIL"]?></strong>.</p>
    <?php if ($errors): ?>
      <div class="alert alert-danger">
        <?php foreach ($errors as $e) echo "<div>" . htmlspecialchars($e) . "</div>"; ?>
      </div>
    <?php endif; ?>
    <form method="post" novalidate>
      <div class="mb-3">
        <label class="form-label">Auth Code</label>
        <input class="form-control" name="auth_code" required>
      </div>

      <div class="d-flex justify-content-between">
        <a class="btn btn-secondary" href="index.php?step=3">← Back</a>
        <div>
          <button type="submit" name="verify_code" class="btn btn-outline-primary">Submit</button>
        </div>
      </div>
    </form>

  </div>
  <?php endif;?>
</div>
<div class="banner">
  <img src="/images/js-software-itsupport1-final-02.png" width="210px" alt="jimBoYz Logo">
</div>
</body>
</html>