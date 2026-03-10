<?php
/**
 * @author jimBoYz Ni ChOy!!!
 * Finalize: 12-18-2025-4-57-pm-thu (all script)
 */
header('Content-Type: application/json');

function respond($status, $message, $extra = []) {
    echo json_encode(array_merge([
        "status" => $status,
        "message" => $message
    ], $extra));
    exit; // ensures nothing else prints
}
?>