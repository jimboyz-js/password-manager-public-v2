<?php
if(session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Initialize installer step if not set
if (!isset($_SESSION['install_step'])) {
    $_SESSION['install_step'] = 1;
}

// Determine requested step
$step = isset($_GET['step']) ? (int)$_GET['step'] : 1;

// Prevent skipping steps
if ($step > $_SESSION['install_step']) {
    $step = $_SESSION['install_step'];
}

// Map step numbers to PHP files
$stepsMap = [
    1 => 'step1_welcome.php',      // Welcome / License
    2 => 'step2_requirements.php', // Requirements check
    3 => 'step3_smtp.php',         // SMTP Configuration
    4 => 'step4_database.php',     // DB connection
    5 => 'step5_admin.php',        // Admin creation
    6 => 'step6_install.php',      // Install / DB import / .env
    'finish' => 'finish.php',      // Installation complete
];

// Determine which file to include
if ($step === 'finish' || (isset($_GET['step']) && $_GET['step'] === 'finish')) {
    $file = $stepsMap['finish'];
} elseif (isset($stepsMap[$step])) {
    $file = $stepsMap[$step];
} else {
    // fallback to step 1
    $file = $stepsMap[1];
}

// Include the step file
include $file;
