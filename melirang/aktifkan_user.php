<?php
require_once 'db.php'; // Pastikan koneksi database ($pdo) sudah terdefinisi

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Update status pengguna menjadi 'Aktif'
    $stmt = $pdo->prepare("UPDATE registrasi SET status = 'Aktif' WHERE id = ?");
    $stmt->execute([$id]);

    header("Location: konfirmasi_user.php?message=User berhasil diaktifkan");
    exit();
}
?>
