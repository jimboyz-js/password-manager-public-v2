<?php
/**
 * @author jimBoYz Ni ChOy!!!
 * @Date: Dec. 12, 2025 FRI. 4:00 PM
 * @version (app)2.2.2
 * This script is to get version from mysqli_client that converted into numeric version.
 */
function get_mysqli_client_version() {
    $clientVersion = mysqli_get_client_version(); // e.g. 80036
    $versionString = sprintf("%d.%d.%d",
        $clientVersion / 10000,
        ($clientVersion % 10000) / 100,
        $clientVersion % 100
    );
    // echo mysqli_get_client_info(); // mysqlnd 8.2.1

    return $versionString; // 8.0.36
}