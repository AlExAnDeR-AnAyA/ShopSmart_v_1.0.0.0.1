<?php
$host = 'localhost';
$dbname = 'shopsmart_v_1.0.0.0.1';
$user = 'root'; // o tu usuario de MySQL
$pass = ''; // o tu contraseña de MySQL

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
}
?>
