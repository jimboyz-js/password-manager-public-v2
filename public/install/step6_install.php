<?php
if(session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once __DIR__.'/../../app/get-url.php';

// Protect against skipping:
// To reach step 6, session must be at least 6.
$allowedStep = 6;
if (!isset($_SESSION['install_step']) || $_SESSION['install_step'] < $allowedStep) {
    $target = $_SESSION['install_step'] ?? 1;
    header("Location: index.php?step=" . (int)$target);
    exit;
}

// $root = realpath(__DIR__ . '/../') ?: __DIR__ . '/../';
$root = realpath(__DIR__ . '/../../') ?: __DIR__ . '/../../';
$errors = [];
$messages = [];

if (!isset($_SESSION['installer_db'], $_SESSION['installer_admin'])) {
    $errors[] = "Database or admin info missing. Complete previous steps.";
} else {
    $db = $_SESSION['installer_db'];
    $admin = $_SESSION['installer_admin'];
    $smtp = $_SESSION["installer_smtp"];

    // If user confirmed install (button pressed), perform actions
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['run_install'])) {

        // 1) Create .env from .env.config (we will write it first)
        $examplePath = $root . '/.env.config'; //config path
        if (!file_exists($examplePath)) {
            $errors[] = ".env.config not found at project root.";
        } else {

            $envTemplate = file_get_contents($examplePath);

            // Ensure APP_KEY exists
            // $replacements = array_merge($db, [
            //     // 'APP_KEY' => bin2hex(random_bytes(16))
            //     'app.secret.key' => bin2hex(random_bytes(16))
            // ]);

            $config = [
                "app.serverhost" => '"'.$db["DB_HOST"].'"',
                "app.username" => '"'.$db["DB_USER"].'"',
                "app.password" => '"'.$db["DB_PASS"].'"',
                "app.name" => '"'.$db["DB_NAME"].'"',
                "app.port" => $db["DB_PORT"],
                "app.smtp.username" => '"'.$smtp["SMTP_USERNAME"].'"',
                "app.smtp.password" => '"'.$smtp["SMTP_PASS"].'"',
                "app.secret.key" => '"'.bin2hex(random_bytes(16)).'"', //'"'."jimBoYz Ni ChOy!!!".'"',
                "app.smtp.email" => '"'.$smtp["SMTP_EMAIL"].'"',
                "app.smtp.host" => '"'.$smtp["SMTP_HOST"].'"',
                "app.site.link" => '"'.getDomainURL().'"', //app.link
                "app.firstname" => '"'.$admin["ADMIN_FIRSTNAME_PLAIN"].'"',
                // "app.lastname" => '"'.$admin["ADMIN_LASTNAME_PLAIN"].'"' //Disable this part until further update version
            ];

            // Step 1: Read default from .env.config
            // Add this lines of code which handles app.limit.attempt added.
            $defaults = [];
            $lines = file('.env.config', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

            foreach ($lines as $line) {
                if (str_starts_with(trim($line), '#')) continue;
                [$key, $value] = array_pad(explode('=', $line, 2), 2, '');
                $defaults[$key] = $value;
            }

            // Step 2: Merge user config with default
            // User config ($config) overrides the default
            $config = array_replace($defaults, $config);

            $replacements = array_merge($config);

            // Replace or append keys
            foreach ($replacements as $k => $v) {
                $pattern = "/^" . preg_quote($k, '/') . "=.*$/m";
                if (preg_match($pattern, $envTemplate)) {
                    $envTemplate = preg_replace($pattern, "{$k}={$v}", $envTemplate);
                } else {
                    $envTemplate .= PHP_EOL . "{$k}={$v}";
                }
            }

            $envPath = $root . '/.env';
            try {
                file_put_contents($envPath, $envTemplate, LOCK_EX);
                $messages[] = ".env file created.";
            } catch (Exception $e) {
                $errors[] = "Unable to write .env file: " . $e->getMessage();
            }
        }

        // 2) Connect to DB using mysqli
        if (empty($errors)) {
            $mysqli = null;
            $user_id = null;
            $stmt = null;
            $stmt2 = null;
            try {
                $mysqli = @new mysqli($db['DB_HOST'], $db['DB_USER'], $db['DB_PASS'], $db['DB_NAME']);

                if ($mysqli->connect_errno) {
                    $errors[] = "DB connection failed: " . htmlspecialchars($mysqli->connect_error);
                } else {
                    // 3) Run SQL file (schema.sql)
                    // $sqlFile = __DIR__ . '/schema.sql';
                    $sqlFile = $root. '/schema.sql';
                    if (!file_exists($sqlFile)) {
                        $errors[] = "schema.sql is missing.";
                    } else {
                        $sql = file_get_contents($sqlFile);

                        // Use multi_query to execute multiple statements.
                        if ($mysqli->multi_query($sql)) {
                            do {
                                // store_result / free if exists
                                if ($res = $mysqli->store_result()) {
                                    $res->free();
                                }
                            } while ($mysqli->more_results() && $mysqli->next_result());
                            $messages[] = "Database tables created.";
                        } else {
                            $errors[] = "SQL import error: " . htmlspecialchars($mysqli->error);
                        }
                    }

                    // 4) Create admin account
                    if (empty($errors)) {
                        $adminUser = $admin['ADMIN_USER'];
                        $adminPassHash = password_hash($admin['ADMIN_PASS'], PASSWORD_DEFAULT);
                        $firstname_hash = $admin["ADMIN_FIRSTNAME"];
                        $lastname_hash = $admin["ADMIN_LASTNAME"];
                        $key_hash = $admin["ADMIN_MASTER_KEY"];

                        $firstname_enc = $admin["ADMIN_FIRSTNAME_ENC"];
                        $lastname_enc = $admin["ADMIN_LASTNAME_ENC"];
                        $username_enc = $admin["ADMIN_USER_ENC"];

                        $ip = $_SERVER["REMOTE_ADDR"] ?? "UNKNOWN";
                        $hostname_by_addr = gethostbyaddr($ip);
                        $hostname = gethostname();
                        $deviceInfo = $hostname." | ".$hostname_by_addr;

                        $dt = new DateTime('now', new DateTimeZone('Asia/Manila'));
                        $dateRegistered = $dt->format('Y-m-d H:i:s T');

                        $mysqli->begin_transaction();
                        $stmt = $mysqli->prepare("INSERT INTO users (firstname_hash, lastname_hash, username_hash, password_hash, device_terminal, ip, dateRegistered, firstname, lastname, username) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                        if ($stmt === false) {
                            $errors[] = "Prepare statement failed: " . htmlspecialchars($mysqli->error);
                        } else {
                            $stmt->bind_param('ssssssssss', $firstname_hash, $lastname_hash, $adminUser, $adminPassHash, $deviceInfo, $ip, $dateRegistered, $firstname_enc, $lastname_enc, $username_enc);
                            if ($stmt->execute()) {
                                $user_id = $mysqli->insert_id;

                                $stmt2 = $mysqli->prepare("INSERT INTO `master_key`(`user_id`, `key_hash`) VALUES(?, ?)");
                                $stmt2->bind_param("is", $user_id, $key_hash);
                                if($stmt2->execute()) {
                                    $messages[] = "Admin account created (username: " . htmlspecialchars($adminUser) . ").";
                                } else {
                                    $errors[] = "Failed to create admin: " . htmlspecialchars($stmt->error);
                                }

                            } else {
                                $errors[] = "Failed to create admin: " . htmlspecialchars($stmt->error);
                            }
                            $mysqli->commit();
                        }
                    }
                }

            } catch (Exception $e) {
                $mysqli->rollback();
                $errors[] = "Exception Error: " . htmlspecialchars($e);
                // delete_env_file_if_exception_occurs($root . '/.env');
            }

            finally {
                if($stmt) {
                    $stmt->close();
                }

                if($stmt2) {
                    $stmt2->close();
                }

                if($mysqli) {
                    $mysqli->close();
                }
            }
        }

        // 5) Finalize
        if (empty($errors)) {
            // Clear installer session data
            unset($_SESSION['installer_db'], $_SESSION['installer_admin'], $_SESSION["installer_smtp"]);
            // Lock installer
            $_SESSION['install_step'] = 999;
            // Redirect to finish page
            header("Location: index.php?step=finish");
            exit;
        }

        // Recommended place to delete .env not in the catch block.
        // Delete this file if there is an exception or failed to setup the installer.
        $envPath = $root . '/.env';
        delete_env_file_if_exception_occurs($envPath);
    }
}

function delete_env_file_if_exception_occurs($file) {

    if (file_exists($file)) {
        if (unlink($file)) {
            // echo "File deleted successfully.";
        }
    }

}

?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Installer — Installing</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="icon" type="images/svg+xml" href="/images/js-software-itsupport1 - favicon0.png">
  <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
  <div class="col-md-8 offset-md-2 install-card">

    <h4>Step 6 — Install</h4>

    <?php if ($errors): ?>
      <div class="alert alert-danger">
        <?php foreach ($errors as $e) echo "<div>" . htmlspecialchars($e) . "</div>"; ?>
      </div>
      <div class="mb-3">
        <a class="btn btn-secondary" href="index.php?step=5">← Back</a>
      </div>
    <?php else: ?>
      <?php if (!empty($messages)): ?>
        <div class="alert alert-success">
          <?php foreach ($messages as $m) echo "<div>" . htmlspecialchars($m) . "</div>"; ?>
        </div>
      <?php endif; ?>

      <p>The installer will:</p>
      <ul>
        <li>Create <code>.env</code> from <code>.env.config</code></li>
        <li>Import database schema (tables)</li>
        <li>Create the admin account you provided</li>
      </ul>

      <form method="post">
        <div class="d-flex justify-content-between">
          <a class="btn btn-secondary" href="index.php?step=5">← Back</a>
          <button class="btn btn-danger" type="submit" name="run_install" onclick="return confirm('Run installation now? This will write files and create database tables.')">
            Run Installation Now
          </button>
        </div>
      </form>
    <?php endif; ?>

  </div>
</div>
<div class="banner">
  <img src="/images/js-software-itsupport1-final-02.png" width="210px" alt="jimBoYz Logo">
</div>
</body>
</html>
