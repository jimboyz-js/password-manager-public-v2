<?php
// Incase the file.zip uploaded without unzipper use this one then delete.
// $zipFile = 'project.zip';      // 🔁 change to your zip filename
$zipFile = 'password-manager-v2.2.2.zip';
$extractTo = __DIR__;          // extract to current directory

$zip = new ZipArchive;

//==========//
// Extract to a Specific Folder (Optional)
// e.g., password-manager/
/*
$extractTo = __DIR__ . '/password-manager';

if (!is_dir($extractTo)) {
    mkdir($extractTo, 0755, true);
}
*/

if ($zip->open($zipFile) === TRUE) {
    $zip->extractTo($extractTo);
    $zip->close();
    echo "✅ ZIP file extracted successfully.";
} else {
    echo "❌ Failed to open the ZIP file.";
}
