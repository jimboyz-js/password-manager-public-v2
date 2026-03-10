<?php
// Step 1: Welcome

if(isset($_POST['start_check'])) {
  step2();
}

function step2() {

  // $ROOT = realpath(__DIR__ . '/../') ?: __DIR__ . '/../';
  $ROOT = realpath(__DIR__ . '/../../') ?: __DIR__ . '/../../';
  
  // installer lock check
  if (!file_exists($ROOT . '/.env')) {
    $_SESSION['install_step'] = 2;
    header("Location: index.php?step=2");
  } else {
    // Already installed / locked
    header('Content-Type: text/html; charset=utf-8');
    echo "<!doctype html><html lang='en'><head><meta charset='utf-8'><link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css' rel='stylesheet'>";
    echo "<link rel='icon' type='images/svg+xml' href='/images/js-software-itsupport1 - favicon0.png'><link rel='stylesheet' href='style.css'><title>Installer Locked</title></head><body style='font-family:Arial,Helvetica,sans-serif;padding:30px;'>";
    echo "<div class='col-md-7 offset-md-2 install-card'>";
    echo "<h2>Installer locked</h2>";
    echo "<p>An <code>.env</code> file already exists. To reinstall remove the <code>.env</code> file manually.</p>";
    echo '<div class="d-flex justify-content-end"><button type="button" id="goto-login" onclick="window.location.href=`/`" class="btn btn-primary">Go to App</button></div>';
    echo "</div>";
    echo '<div class="banner"><img src="/images/js-software-itsupport1-final-02.png" width="210px" alt="jimBoYz Logo"></div>';
    echo "</body></html>";
    exit;
  }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Installer — Welcome</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="icon" type="images/svg+xml" href="/images/js-software-itsupport1 - favicon0.png">
  <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
  <div class="col-md-7 offset-md-2 install-card">
    <h3>Welcome to JS Password Manager Installer</h3>
    <p>This installer will guide you through setting up the application. You will be asked for database credentials, email address for SMTP configuration, and to create the initial admin account.</p>
    <ul>
      <li>Estimated time: <strong>2–5 minutes</strong></li>
      <li>Please have your MySQL credentials ready</li>
      <li>The installer will create a <code>.env</code> file and the <code>users</code> table</li>
    </ul>
    <!-- <a class="btn btn-primary" href="index.php?step=2">Start Requirements Check →</a> -->
     <form method="POST">
      <button name="start_check" class="btn btn-primary" type="submit">Start Requirements Check →</button>
     </form>
  </div>
</div>
<div class="banner">
  <img src="/images/js-software-itsupport1-final-02.png" width="210px" alt="jimBoYz Logo">
</div>
</body>
</html>
