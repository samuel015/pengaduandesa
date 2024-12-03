<?php
session_start();
include 'db.php'; // Pastikan sudah terhubung ke database

if (!isset($_SESSION['nik']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];

    try {
        $stmt = $pdo->prepare("DELETE FROM registrasi WHERE id = :id");
        $stmt->execute([':id' => $id]);
        
        header("Location: admin_dashboard.php?message=user_deleted");
        exit();
    } catch (PDOException $e) {
        $error_message = "Error: " . $e->getMessage();
    }
}
?>