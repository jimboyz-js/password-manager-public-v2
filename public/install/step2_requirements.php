<?php
if(session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once 'get_mysqli_client_info.php';

// Prevent step skipping
if (!isset($_SESSION['install_step']) || $_SESSION['install_step'] < 2) {
    header("Location: index.php?step=1");
    exit;
}

// ---------------------------
// Requirement checks
// ---------------------------

// Required PHP version
// $requiredPhpVersion = "8.0.0";

$requiredPhpVersion = "7.4.0";
$phpOk = version_compare(PHP_VERSION, $requiredPhpVersion, ">=");
$requiredDBVersion = "5.7";
$versionDBString = get_mysqli_client_version();
$cleanVersion = preg_replace('/[^0-9.]/', '', $versionDBString);
$dbOk = version_compare($cleanVersion, $requiredDBVersion, '>='); // PHP Client Only

// Required PHP extensions
$reqExtensions = ['mysqli', 'mbstring', 'openssl', 'json'];
$missingExtensions = [];

foreach ($reqExtensions as $ext) {
    if (!extension_loaded($ext)) {
        $missingExtensions[] = $ext;
    }
}
$extensionsOk = empty($missingExtensions);

// Folder permissions
$rootWritable = is_writable(__DIR__ . "/../../");
$envWritable  = is_writable(__DIR__ . "/../../.env.config");

// start
// Step 2: Requirements check
$checks = [];

$checks[] = [
    'name' => 'PHP Version >= '.$requiredPhpVersion,
    'ok' => version_compare(PHP_VERSION, $requiredPhpVersion, '>=')
];
$checks[] = [
    'name' => 'DB Version >= '.$requiredDBVersion,
    'ok' => $dbOk
];
$reqExtensions = ['mysqli', 'mbstring', 'openssl', 'json'];
foreach ($reqExtensions as $ext) {
    $checks[] = [
        'name' => "PHP extension: {$ext}",
        'ok' => extension_loaded($ext)
    ];
}

// check writable for project root .env
$root = realpath(__DIR__ . '/../../') ?: __DIR__ . '/../../';
// $root = realpath(__DIR__ . '/../../../') ?: __DIR__ . '/../../../';
$envWritable = is_writable($root) || (!file_exists($root . '/.env') && is_writable($root));
$permissionsOk = $rootWritable && $envWritable;

// Final status
$allGood = $phpOk && $extensionsOk && $permissionsOk && $dbOk;

// If user clicks NEXT and requirements are met
if (isset($_POST['continue']) && $allGood) {
    $_SESSION['install_step'] = 3;
    header("Location: index.php?step=3");
    exit;
}

$checks[] = [
    'name' => "Project root writable (to create .env)",
    'ok' => $envWritable
];

$checks[] = [
    // 'name' => "File .env.config exists",
    'name' => "File .env.config readable",
    'ok' => file_exists($root . '/.env.config')
];

error_log("ROOT: ".$root);

$all_ok = true;
foreach ($checks as $c) { if (!$c['ok']) $all_ok = false; }
?>

<!-- end -->

<!DOCTYPE html>
<html>
<head>
    <title>Installer – Requirements Check</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css">
    <link rel="icon" type="images/svg+xml" href="/images/js-software-itsupport1 - favicon0.png">
    <link rel="stylesheet" href="style.css">
</head>

<body class="bg-light">

<div class="container py-4">

    <div class="col-md-8 offset-md-2 bg-white p-4 rounded shadow-sm">

        <h3>Step 2: Server Requirements</h3>
        <hr>

        <h5>PHP Version</h5>
        <?php if ($phpOk): ?>
            <div class="alert alert-success">
                PHP <?= PHP_VERSION ?> ✔ (OK)
            </div>
        <?php else: ?>
            <div class="alert alert-danger">
                Installed PHP <?= PHP_VERSION ?> — requires <?= $requiredPhpVersion ?> or higher ❌
            </div>
        <?php endif; ?>


        <!-- MySQL or MariaDB -->
         <h5>MySQL/MariaDB Version</h5>
        <?php if ($dbOk): ?>
            <div class="alert alert-success">
                DB <?= $versionDBString ?> ✔ (OK)
            </div>
        <?php else: ?>
            <div class="alert alert-danger">
                Installed MySQL/MariaDB <?= $versionDBString ?> — requires <?= $requiredDBVersion ?> or higher ❌
            </div>
        <?php endif; ?>


        <h5>Required Extensions</h5>

        <?php if ($extensionsOk): ?>
            <div class="alert alert-success">
                All required extensions are enabled ✔
            </div>
        <?php else: ?>
            <div class="alert alert-danger">
                Missing extensions:<br>
                <b><?= implode(", ", $missingExtensions) ?></b>
            </div>
        <?php endif; ?>

        <!-- start -->
         <!-- System Requirements -->
        <div class="req-container">
        <table class="table">
      <tbody>
      <?php foreach ($checks as $c): ?>
        <tr>
          <td><?=htmlspecialchars($c['name'])?></td>
          <td style="width:140px">
            <?php if ($c['ok']): ?>
              <span class="badge bg-success">OK</span>
            <?php else: ?>
              <span class="badge bg-danger">Missing</span>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
    </div>

    <?php if ($all_ok): ?>
      <!-- <a class="btn btn-secondary" href="index.php?step=1">← Back</a>
      <a class="btn btn-primary" href="index.php?step=3">Continue →</a> -->
      
    <?php else: ?>
      <div class="alert alert-warning">
        One or more checks failed. Please fix the issues and reload this page.
      </div>
      <!-- <a class="btn btn-secondary" href="index.php?step=1">← Back</a> -->
      <a class="btn btn-outline-primary" href="index.php?step=2">Re-run checks</a>
    <?php endif; ?>

    <div class="cont d-flex justify-content-end">
        <!-- <div class="btn btn-primary" id="show">&lt;&lt; Show</div> -->
        <div class="btn btn-primary" id="show">&lt; Show</div>
    </div>
        <!-- end -->

        <h5>Folder Permissions</h5>
        <ul class="list-group mb-3">
            <li class="list-group-item">
                Root folder writable:
                <?= $rootWritable ? "<span class='text-success'>✔</span>" :
                                    "<span class='text-danger'>❌</span>" ?>
            </li>
            <li class="list-group-item">
                <!-- .env.example writable: -->
                 .env.config writable:
                <?= $envWritable ? "<span class='text-success'>✔</span>" :
                                   "<span class='text-danger'>❌</span>" ?>
            </li>
        </ul>


        <form method="POST">
            <div class="d-flex justify-content-between">

                <a href="index.php?step=1" class="btn btn-secondary">
                    ◀ Back
                </a>

                <?php if ($allGood): ?>
                    <button type="submit" name="continue" class="btn btn-primary">
                        Continue ▶
                    </button>
                <?php else: ?>
                    <button type="button" class="btn btn-secondary" disabled>
                        Fix Issues to Continue
                    </button>
                <?php endif; ?>

            </div>
        </form>

    </div>

</div>

<div class="banner">
  <img src="/images/js-software-itsupport1-final-02.png" width="210px" alt="jimBoYz Logo">
</div>

<script>
    let isShowing = false;

    const tableCon = document.querySelector('.req-container');
    const btnShow = document.getElementById("show");

    tableCon.style.display = "none";

    document.getElementById("show").addEventListener("click", ()=> {
        if(isShowing) {
            tableCon.style.display = "none";
            // btnShow.textContent = "<< Show"
            btnShow.textContent = "< Show"
            isShowing = false;
        } else {
            tableCon.style.display = "block";
            // btnShow.textContent = ">> Hide";
            btnShow.textContent = "> Hide";
            isShowing = true;
        }
    })
</script>
</body>
</html>
