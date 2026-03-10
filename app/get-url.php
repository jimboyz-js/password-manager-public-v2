<?php
function getFullURL() {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
    $currentUrl = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

    return $currentUrl;
}

function getDomainURL() {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        ? "https://" 
        : "http://";

    $host = $_SERVER['HTTP_HOST'];

    // Remove port if exists (e.g. example.com:8000 → example.com)
    $host = preg_replace('/:\d+$/', '', $host);

    return $protocol . $host;
}