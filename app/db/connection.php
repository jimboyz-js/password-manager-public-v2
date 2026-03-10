<?php
/**
 * @author jimBoYz Ni ChOy!!!
 * Dec. 01, 2025 Tue. 4:37 PM
 * JS Password Manager v2.2.2
 */

require_once dirname(__DIR__ ) . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__) . '../../');
$dotenv->load();

function database_connection() {
    try {
    
        $host = isset($_ENV['app.serverhost']) ? $_ENV['app.serverhost'] : 'localhost';
        $username = $_ENV['app.username'];
        $password = $_ENV['app.password'];
        $database_name = $_ENV['app.name'];
        $envPort = $_ENV['app.port'] ?? null;

        $port = (!empty($envPort) && is_numeric($envPort)) ? (int) $envPort : null;

        $connection = new mysqli($host, $username, $password, $database_name, $port);

        if($connection->connect_error) {
            return null;
        }

        return $connection;

    } catch(Exception $e) {
        return null;
    }
}