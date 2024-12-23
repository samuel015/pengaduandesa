<?php
session_start();
include 'db.php'; // Pastikan koneksi database ($pdo) sudah terdefinisi

// Cek apakah pengguna yang login adalah admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: konfirmasi_admin.php");
    exit();
}

// Proses penolakan admin
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'])) {
    $user_id = $_POST['user_id'];

    try {
        // Update status pengguna menjadi 'ditolak'
        $stmt = $pdo->prepare("UPDATE registrasi SET status = 'ditolak' WHERE id = :id AND status = 'pending'");
        $stmt->execute(['id' => $user_id]);

        if ($stmt->rowCount() > 0) {
            $_SESSION['message'] = "Admin berhasil ditolak.";
        } else {
            $_SESSION['error'] = "Gagal menolak admin. Pastikan statusnya adalah 'pending'.";
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Terjadi kesalahan: " . $e->getMessage();
    }

    // Redirect kembali ke halaman konfirmasi admin
    header("Location: halaman_konfirmasi_admin.php");
    exit();
}
?>