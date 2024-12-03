<?php
$host = 'localhost';
$db = 'desa';
$user = 'root'; // Username MySQL
$pass = ''; // Password MySQL

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Could not connect to the database $db :" . $e->getMessage());
}
?>
