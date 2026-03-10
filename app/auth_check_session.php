<?php
/**
 * @author jimBoYz Ni ChOy!!!
 */

require_once __DIR__. '/vendor/autoload.php';
include_once __DIR__.'/../config.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__. '/../');
$dotenv->load();

function obfuscate_email($email) {
    $parts = explode('@', $email);
    $name = $parts[0];
    $domain = $parts[1];

    // Show first letter, replace rest with * (except the last 2 characters if > 3 letters)
    $visible = substr($name, 0, 1);
    // $visible = substr($name, 0, 3); //check if the length of the $name (email name before @) if greater than 3. But I choose 1 for simplicity. Like the above uncommented.
    $masked = str_repeat('*', max(strlen($name) - 1, 0));

    return $visible . $masked . '@' . $domain;
}

function expireTime(): ?string {
    if(isset($_SESSION['formatted-time'])) {
        return $_SESSION['formatted-time'];
    }

    return null;
}

?>