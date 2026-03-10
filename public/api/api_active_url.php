
<?php
/**
 * @author jimBoYz Ni ChOy!!!
 * Store the URL Param into the session
 * @Date updated: 12-02-2025 TUE. 3:49 PM
 */
if(session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once 'helpers.php';

// $status = $_POST["status"] ?? null;
// In this version (2.2.1) instead removing this status I set to "active" maybe for future use. A lot of configuration if I remove this line.
// This work in the version 2.2.unfinish
$status = "active"; // Nov. 21, 2025 FRI.
$m_id = $_POST["m_id"] ?? null;
$id = $_POST["id"] ?? null;
$k = $_POST["k"] ?? null;
$page = $_POST["page"] ?? null;

if($status) {
    $_SESSION["status"] = $status;
}

if($id) {
    $_SESSION["id"] = $id;
}

if($k) {
    $_SESSION["k"] = $k;
}

if($page) {
    $_SESSION["page"] = $page;
}

if($m_id) {
    $_SESSION["m_id"] = $m_id;
}

if($id || $k || $page || $m_id) {
    respond("success", "Active url stored to session.", ["session_id"=>session_id()]);
} else {
    respond("failed", "Something went wrong.", []);
}