<?php
require_once 'db.php'; // Pastikan koneksi database ($pdo) sudah terdefinisi

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Hapus pengguna dari database
    $stmt = $pdo->prepare("DELETE FROM registrasi WHERE id = ?");
    $stmt->execute([$id]);

    header("Location: konfirmasi_user.php?message=User berhasil dihapus");
    exit();
}
?>
