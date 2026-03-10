<?php
if(session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Protect against skipping:
// To reach step 4, session must be at least 4.
// $allowedStep = 4;
// To reach step 5, session must be at least 5.
$allowedStep = 5;
if (!isset($_SESSION['install_step']) || $_SESSION['install_step'] < $allowedStep) {
    $target = $_SESSION['install_step'] ?? 1;
    header("Location: index.php?step=" . (int)$target);
    exit;
}

$errors = [];

// if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_admin']))
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  // HASH
  $admin_user = trim($_POST['admin_user'] ?? '');
  $admin_pass = trim($_POST['admin_pass'] ?? '');
  $admin_pass_confirm = trim($_POST['admin_pass_confirm'] ?? '');
  $admin_firstname = trim($_POST['admin_firstname'] ?? '');
  $admin_lastname = trim($_POST['admin_lastname'] ?? '');
  $admin_master_key = trim($_POST['admin_master_key'] ?? '');

  // ENC
  $admin_user_enc = trim($_POST['admin_username_enc'] ?? '');
  $admin_firstname_enc = trim($_POST['admin_firstname_enc'] ?? '');
  $admin_lastname_enc = trim($_POST['admin_lastname_enc'] ?? '');

  // PLAIN
  $admin_user_plain = trim($_POST['admin_username_plain'] ?? '');
  $admin_firstname_plain = trim($_POST['admin_firstname_plain'] ?? '');
  $admin_lastname_plain = trim($_POST['admin_lastname_plain'] ?? '');

  if ($admin_user === '' || $admin_pass === '') {
    $errors[] = "Admin username & password are required.";
  } elseif ($admin_pass !== $admin_pass_confirm) {
    $errors[] = "Password confirmation does not match.";
  } else {
    // Save admin temporarily in session (do not insert to DB yet)
    $_SESSION['installer_admin'] = [
      'ADMIN_USER' => $admin_user,
      'ADMIN_PASS' => $admin_pass,
      'ADMIN_FIRSTNAME' => $admin_firstname,
      'ADMIN_LASTNAME' => $admin_lastname,
      'ADMIN_MASTER_KEY' => $admin_master_key,
      'ADMIN_USER_ENC' => $admin_user_enc,
      'ADMIN_FIRSTNAME_ENC' => $admin_firstname_enc,
      'ADMIN_LASTNAME_ENC' => $admin_lastname_enc,
      'ADMIN_USER_PLAIN' => $admin_user_plain,
      'ADMIN_FIRSTNAME_PLAIN' => $admin_firstname_plain,
      'ADMIN_LASTNAME_PLAIN' => $admin_lastname_plain,
    ];

    // Unlock next step (6)
    $_SESSION['install_step'] = 6;

    // Redirect to install step
    header('Location: index.php?step=6');
    exit;
  }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Installer — Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="icon" type="images/svg+xml" href="/images/js-software-itsupport1 - favicon0.png">
  <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
  <div class="col-md-7 offset-md-2 install-card">

    <h4>Step 5 — Create Admin Account</h4>

    <?php if ($errors): ?>
      <div class="alert alert-danger"><?php foreach ($errors as $e) echo "<div>" . htmlspecialchars($e) . "</div>"; ?></div>
    <?php endif; ?>

    <form method="post" id="admin_form" novalidate>
      <div class="mb-3">
        <label class="form-label">Firstname</label>
        <input class="form-control" name="admin_firstname" id="admin_firstname" required
               value="<?= htmlspecialchars($_SESSION['installer_admin']['ADMIN_FIRSTNAME_PLAIN'] ?? '') ?>">
        <input class="form-control" type="hidden" name="admin_firstname_enc" id="admin_firstname_enc" > <!-- Hide element for encrypted firstname value the same as lastname and username -->
        <input class="form-control" type="hidden" name="admin_firstname_plain" id="admin_firstname_plain" > <!-- Hide element for plain text firstname value the same as lastname and username to store to session in the back-end to display in the input field as plain text not encrypted or hash-->
      </div>
      <div class="mb-3">
        <label class="form-label">Lastname</label>
        <input class="form-control" name="admin_lastname" id="admin_lastname" required
               value="<?= htmlspecialchars($_SESSION['installer_admin']['ADMIN_LASTNAME_PLAIN'] ?? '') ?>">
        <input class="form-control" type="hidden" name="admin_lastname_enc" id="admin_lastname_enc" >
        <input class="form-control" type="hidden" name="admin_lastname_plain" id="admin_lastname_plain" >
      </div>
      <div class="mb-3">
        <label class="form-label">Admin Username</label>
        <input class="form-control" name="admin_user" id="admin_user" required
               value="<?= htmlspecialchars($_SESSION['installer_admin']['ADMIN_USER_PLAIN'] ?? 'admin') ?>">
        <input class="form-control" type="hidden" name="admin_username_enc" id="admin_username_enc" >
        <input class="form-control" type="hidden" name="admin_username_plain" id="admin_username_plain" >
      </div>

      <div class="mb-3">
        <label class="form-label">Master Key <span><img src="/../images/tips.png" width="10px" class="icon ic_bc_docs" alt="Images light bulb" id="icon" title="Don't forget your master key. It acts like a password and cannot be changed. Its purpose is to encrypt and decrypt your data." style="cursor: pointer;"></span></label>
        <input class="form-control" name="admin_master_key" id="admin_master_key" type="password" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Admin Password</label>
        <input class="form-control" name="admin_pass" id="admin_pass" type="password" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Confirm Password</label>
        <input class="form-control" name="admin_pass_confirm" id="admin_pass_confirm" type="password" required>
      </div>

      <div class="d-flex justify-content-between">
        <a class="btn btn-secondary" href="index.php?step=4">← Back</a>
        <button class="btn btn-primary" name="save_admin" type="submit">Save & Continue</button>
      </div>
    </form>

  </div>
</div>

<div class="banner">
  <img src="/images/js-software-itsupport1-final-02.png" width="210px" alt="jimBoYz Logo">
</div>

<script type="module">

  import { hashWithDefaultKey, encrypt } from "/script/web-crypto.js";

  document.getElementById("admin_form").addEventListener("submit", async (e)=> {
    e.preventDefault();
  
    let form = e.target;

    if(!form.checkValidity()) {
      e.preventDefault();
      form.reportValidity();
      return;
    }

    const firstname = document.getElementById("admin_firstname");
    const lastname = document.getElementById("admin_lastname");
    const username = document.getElementById("admin_user");
    const master_key = document.getElementById("admin_master_key");
    const password = document.getElementById("admin_pass");
    const confirm = document.getElementById("admin_pass_confirm");

    const firstnameEl_enc = document.getElementById("admin_firstname_enc");
    const lastnameEl_enc = document.getElementById("admin_lastname_enc");
    const usernameEl_enc = document.getElementById("admin_username_enc");

    // Plain text
    document.getElementById("admin_firstname_plain").value = firstname.value;
    document.getElementById("admin_lastname_plain").value = lastname.value;
    document.getElementById("admin_username_plain").value = username.value;

    const firstname_hash = await hashWithDefaultKey(firstname.value.trim().toLowerCase()); // Use for searching
    const lastname_hash = await hashWithDefaultKey(lastname.value.trim().toLowerCase());
    const firstname_enc = await encrypt(firstname.value, master_key.value);
    const lastname_enc = await encrypt(lastname.value, master_key.value);
    const username_enc = await encrypt(username.value, master_key.value);
    const username_hash = await hashWithDefaultKey(username.value.trim().toLowerCase());
    const master_key_hash = await hashWithDefaultKey(master_key.value);
    const password_hash = await hashWithDefaultKey(password.value);
    const password_confirm_hash = await hashWithDefaultKey(confirm.value);

    // Hash and encrypt
    firstname.value = firstname_hash;
    firstnameEl_enc.value = firstname_enc;
    lastname.value = lastname_hash;
    lastnameEl_enc.value = lastname_enc;
    username.value = username_hash;
    usernameEl_enc.value = username_enc;
    master_key.value = master_key_hash;
    password.value = password_hash;
    confirm.value = password_confirm_hash;

    // this.submit();
    form.submit();

  })
</script>
</body>
</html>
