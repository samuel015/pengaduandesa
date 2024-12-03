<?php
session_start();
include 'db.php'; // Pastikan Anda sudah mengatur koneksi database

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']);

    try {
        // Update status pengguna menjadi "rejected"
        $query = "UPDATE registrasi SET status = 'rejected' WHERE id = :id";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        if ($stmt->execute()) {
            $_SESSION['message'] = "Pengguna berhasil ditolak.";
        } else {
            $_SESSION['error'] = "Terjadi kesalahan saat menolak pengguna.";
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Kesalahan: " . $e->getMessage();
    }

    // Redirect kembali ke halaman sebelumnya
    header('Location: konfirmasi_registrasi.php');
    exit;
} else {
    $_SESSION['error'] = "Akses tidak valid.";
    header('Location: konfirmasi_registrasi.php');
    exit;
}
