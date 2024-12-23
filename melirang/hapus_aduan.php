<?php
session_start();
include 'db.php'; // File koneksi database

// Periksa apakah pengguna sudah login sebagai admin
if (!isset($_SESSION['nik']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Hapus data pengaduan dari database
try {
    $stmt = $pdo->query("TRUNCATE TABLE pengaduan");
    header("Location: daftar_aduan.php?hapus=success");
    exit();
} catch (PDOException $e) {
    die("Kesalahan: " . htmlspecialchars($e->getMessage()));
}
?>