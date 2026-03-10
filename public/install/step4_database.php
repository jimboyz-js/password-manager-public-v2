<?php
if(session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Protect against skipping:
// Must have completed previous steps. To reach step 3, session must be at least 3.
$allowedStep = 3;
if (!isset($_SESSION['install_step']) || $_SESSION['install_step'] < $allowedStep) {
    $target = $_SESSION['install_step'] ?? 1;
    header("Location: index.php?step=" . (int)$target);
    exit;
}

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['test_db'])) {
  $db_host = trim($_POST['db_host'] ?? '');
  $db_name = trim($_POST['db_name'] ?? '');
  $db_user = trim($_POST['db_user'] ?? '');
  $db_pass = trim($_POST['db_pass'] ?? '');
  $db_port = trim($_POST['db_port'] ?? '');

  try {
    // Basic validation
    if ($db_host === '' || $db_name === '' || $db_user === '') {
      $errors[] = "Please fill DB host, name and user.";
    } else {
      // Try mysqli connection (do not create .env yet)
      if($db_port) {
        $mysqli = @new mysqli($db_host, $db_user, $db_pass, $db_name, $db_port);
      } else {
        $mysqli = @new mysqli($db_host, $db_user, $db_pass, $db_name);
      }

      if ($mysqli->connect_errno) {
        $errors[] = "DB connection failed: " . htmlspecialchars($mysqli->connect_error);
      } else {
        // connection ok
        $success = true;

        // Save DB credentials to session (store only necessary values)
        $_SESSION['installer_db'] = [
          'DB_HOST' => $db_host,
          'DB_PORT' => $db_port,
          'DB_NAME' => $db_name,
          'DB_USER' => $db_user,
          'DB_PASS' => $db_pass
        ];

        // Unlock next step (4)
        $_SESSION['install_step'] = 5;

        // Close connection
        $mysqli->close();
      }
    }
  } catch(Exception $e) {
    $errors[] = "DB connection failed: " . htmlspecialchars($e->getMessage());
  }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Installer — Database</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="icon" type="images/svg+xml" href="/images/js-software-itsupport1 - favicon0.png">
  <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
  <div class="col-md-7 offset-md-2 install-card">

    <h4>Step 4 — Database Settings</h4>

    <?php if ($errors): ?>
      <div class="alert alert-danger">
        <?php foreach ($errors as $e) echo "<div>" . htmlspecialchars($e) . "</div>"; ?>
      </div>
    <?php endif; ?>

    <?php if ($success): ?>
      <div class="alert alert-success">
        Database connection successful. Click Continue to proceed.
      </div>
    <?php endif; ?>

    <form method="post" novalidate>
      <div class="mb-3">
        <label class="form-label">DB Host</label>
        <input class="form-control" name="db_host" required
               value="<?= htmlspecialchars($_SESSION['installer_db']['DB_HOST'] ?? 'localhost') ?>">
      </div>

      <div class="mb-3">
        <label class="form-label">DB Port</label>
        <input class="form-control" name="db_port" required
               value="<?= htmlspecialchars($_SESSION['installer_db']['DB_PORT'] ?? '') ?>"> <!--remove default port: 3306-->
      </div>

      <div class="mb-3">
        <label class="form-label">DB Name</label>
        <input class="form-control" name="db_name" required
               value="<?= htmlspecialchars($_SESSION['installer_db']['DB_NAME'] ?? '') ?>">
      </div>

      <div class="mb-3">
        <label class="form-label">DB Username</label>
        <input class="form-control" name="db_user" required
               value="<?= htmlspecialchars($_SESSION['installer_db']['DB_USER'] ?? '') ?>">
      </div>

      <div class="mb-3">
        <label class="form-label">DB Password</label>
        <input class="form-control" name="db_pass" type="password"
               value="<?= htmlspecialchars($_SESSION['installer_db']['DB_PASS'] ?? '') ?>">
      </div>

      <div class="d-flex justify-content-between">
        <a class="btn btn-secondary" href="index.php?step=3">← Back</a>

        <div>
          <button type="submit" name="test_db" class="btn btn-outline-primary">Test Connection</button>

          <?php if ($success): ?>
            <a class="btn btn-primary" href="index.php?step=5">Continue →</a>
          <?php else: ?>
            <button type="button" class="btn btn-primary" disabled>Continue →</button>
          <?php endif; ?>
        </div>
      </div>
    </form>

  </div>
</div>
<div class="banner">
  <img src="/images/js-software-itsupport1-final-02.png" width="210px" alt="jimBoYz Logo">
</div>
</body>
</html>
