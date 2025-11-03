<?php
$host = 'localhost';
$db = '';
$user = '';  
$pass = '';      

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // For view-based app, output a simple HTML error instead of JSON
    echo '<!DOCTYPE html><html><body><h1>Database Error</h1><p>Connection failed: ' . htmlspecialchars($e->getMessage()) . '</p></body></html>';
    exit;
}
