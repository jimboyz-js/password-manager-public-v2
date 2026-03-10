<?php
/**
 * @author jimBoYz Ni ChOy!!!
 * PHP 11-25-2025 5:11PM
 * send-email.php handles SMTP to send verification code for 2FA
 */

// Import PHPMailer classes into the global namespace
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Load Composer's autoloader

require_once __DIR__.'/../../app/vendor/autoload.php';
include_once __DIR__.'/../../app/get-url.php';

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
$isSendingMsg = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['smtp'])) {
  $smtp_username = trim($_POST['smtp_username'] ?? '');
  $smtp_email = trim($_POST['smtp_email'] ?? '');
  $smtp_host = trim($_POST['smtp_host'] ?? 'smtp.gmail.com');
  $smtp_pass = trim($_POST['smtp_pass'] ?? '');

  $isSendingMsg = true;

  try {
    // Basic validation
    if ($smtp_username === '' || $smtp_email === '' || $smtp_pass === '') {
      $errors[] = "Please fill SMTP username, email and password.";
      $isSendingMsg = false;
    } else {

      if(sendCode($smtp_host, $smtp_username, $smtp_email, $smtp_pass)) {
        // SMTP Connection ok
        $success = true;

        // Save SMTP credentials to session (store only necessary values)
        $_SESSION['installer_smtp'] = [
          'SMTP_USERNAME' => $smtp_username,
          'SMTP_EMAIL' => $smtp_email,
          'SMTP_HOST' => $smtp_host,
          'SMTP_PASS' => $smtp_pass,
        ];

      } else {
        $isSendingMsg = false;
        $errors[] = "SMTP validation fails. The authentication code send failed!";
        $success = false;
      }

      if ($success) {
        // Redirect to remove POST (PRG pattern)
        // header("Location: index.php?step=3&success=1");
        header("Location: auth_validation.php?success=1");
        
        exit;
      }
      
    }
  } catch(Exception $e) {
    $errors[] = "SMTP configuration failed: " . htmlspecialchars($e->getMessage());
  }

}

function sendCode($smtp_host, $smtp_username, $smtp_email, $smtp_pass) {
  try {
    // $code = str_pad(random_int(0, 9999999), 7, '0', STR_PAD_LEFT); // e.g., "0777333"
    $code = str_pad(random_int(0, 99999), 5, '0', STR_PAD_LEFT); // e.g., "07773"
    $html_body = file_get_contents(__DIR__."/../../app/email-template/smtp-setup-email-template.html");
    
    $link = getDomainURL() ?? "https://jspasswordmanager.com";
    $url = getFullURL() ?? "https://jspasswordmanager.com";
    
    $html_body = str_replace(
        ["{{USERNAME}}", "{{CODE}}", "{{LINK}}", "{{URL}}"],
        [$smtp_email, $code, $link, $url],
        $html_body
    );

    $_SESSION["smtp_auth_code"] = $code;

    // PHPMailer Configuration
    $mail = new PHPMailer(true); // remove args if you do not want try/catch
    $mail->SMTPDebug = 0;        // (default 0) force no debug output put 1 for debug

    // (Optional) Prevent short script timeout — adjust if needed
    // Default is 30
    set_time_limit(50);

    // Server settings
    $mail->isSMTP();                                            // Use SMTP
    $mail->Host       = $smtp_host;                             // Set the SMTP server
    $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
    $mail->Username   = $smtp_username;                         // SMTP username
    $mail->Password   = $smtp_pass;                             // SMTP password (use app password if 2FA is on)
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // Enable TLS encryption
    $mail->Port       = 587;                                    // TCP port to connect to

    // Recipients
    $mail->setFrom($smtp_username, 'Password Manager V2.2.2');
    $mail->addAddress($smtp_email, $smtp_username);

    // Content
    $mail->isHTML(true);                                       // Set email format to HTML
    $mail->Subject = "SMTP auth validation for an app JS Password Manager v2.2.2 2025";// App Name and Version
    $mail->Body    = $html_body;
    $mail->AltBody = "<p>Your authentication code is <strong>{$code}</strong>.</p>";

    $mail->send();
    return true;
  } catch (Exception $e) {
    $errors[] = "SMTP configuration failed: " . htmlspecialchars($e->getMessage());
    return false;
  }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Installer — SMTP Settings</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="icon" type="images/svg+xml" href="/images/js-software-itsupport1 - favicon0.png">
  <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
  <div class="col-md-7 offset-md-2 install-card">

    <h4>Step 3 — SMTP Configuration<span><a href="./smtp-guide.html" target="_blank"><img src="/images/tips.png" width="10px" class="icon ic_bc_docs" alt="Images light bulb" id="icon" title="Click here for more information about SMTP Configuration." style="cursor: pointer;"></a></span></h4>

    <?php if ($errors): ?>
      <div class="alert alert-danger">
        <?php foreach ($errors as $e) echo "<div>" . htmlspecialchars($e) . "</div>"; ?>
      </div>
    <?php endif; ?>

    <!--?php if ($success): ?>
      <div class="alert alert-success">
        SMTP configuration successful. Click Continue to proceed.
      </div>
    <php endif; ?-->

    <form method="post">
      <div class="mb-3">
        <label class="form-label">SMTP Username</label>
        <input class="form-control" name="smtp_username" type="email" required
                placeholder="e.g. example@mail.com (email sender)">
      </div>

      <div class="mb-3">
        <label class="form-label">SMTP Email Receiver</label>
        <input class="form-control" name="smtp_email" type="email" required
               placeholder="e.g. recipients@mail.com">
      </div>

      <div class="mb-3">
        <label class="form-label">SMTP Host</label>
        <input class="form-control" name="smtp_host" value="<?= htmlspecialchars('smtp.gmail.com') ?>" placeholder="default host (smtp.gmail.com)" >
      </div>
      
      <div class="mb-3">
        <label class="form-label">SMTP Password</label>
        <input class="form-control" name="smtp_pass" type="password">
      </div>
      
      <div class="d-flex justify-content-between">
        <a class="btn btn-secondary" href="index.php?step=2">← Back</a>
        <div>
          <button type="submit" name="smtp" class="btn btn-outline-primary" >Send Code</button>
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
