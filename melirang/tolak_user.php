<?php
session_start();
include 'db.php'; // Pastikan koneksi database ($pdo) sudah terdefinisi

// Cek apakah pengguna yang login adalah user
if ($_SESSION['role'] !== 'user') {
    header("Location: konfirmasi_admin.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_POST['id'];

    // Hapus pengguna yang ditolak
    $stmt = $pdo->prepare("DELETE FROM registrasi WHERE id = ?");
    $stmt->execute([$user_id]);

    $_SESSION['message'] = "Pengguna berhasil dihapus.";
    header("Location: konfirmasi_user.php");
    exit();
}
?>
