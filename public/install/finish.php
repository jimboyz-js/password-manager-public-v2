<?php

if(session_start() === PHP_SESSION_NONE) {
  session_start();
}

// Only allow if installer finished (step 999)
if (!isset($_SESSION['install_step']) || $_SESSION['install_step'] < 999) {
    $target = $_SESSION['install_step'] ?? 1;
    header("Location: index.php?step=" . (int)$target);
    exit;
}

// Optionally, destroy session data for security
session_destroy();

?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Installer — Complete</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="icon" type="images/svg+xml" href="/images/js-software-itsupport1 - favicon0.png">
  <link rel="stylesheet" href="style.css">
</head>
<body class="bg-light">
<div class="container py-4">
  <div class="col-md-8 offset-md-2 bg-white p-4 rounded shadow-sm text-center">
    <h3>Installation Complete</h3>
    <p class="mt-3">Congratulations! Your application has been successfully installed.</p>
    <p><strong>Security tip:</strong> Delete or rename the <code>install/</code> folder from your server.</p>
    <a href="../" class="btn btn-success mt-3">Go to Application →</a>
  </div>
</div>
<div class="banner">
  <img src="/images/js-software-itsupport1-final-02.png" width="210px" alt="jimBoYz Logo">
</div>
</body>
</html>