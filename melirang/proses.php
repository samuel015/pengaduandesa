<?php
session_start();
include 'db.php'; // Pastikan Anda memiliki file koneksi database di sini

// Cek apakah pengguna sudah login sebagai admin
if (!isset($_SESSION['nik']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Cek apakah ID aduan ada di URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Update status aduan menjadi 'Proses'
    $stmt = $pdo->prepare("UPDATE tanggapan SET status = 'Proses' WHERE id = :id");
    $stmt->execute([':id' => $id]);

    // Redirect kembali ke halaman tanggapan dengan pesan sukses
    $_SESSION['message'] = "Tanggapan berhasil diproses.";
    header("Location: tanggapan_aduan.php"); // Ganti dengan nama file yang sesuai
    exit();
} else {
    $_SESSION['message'] = "ID tidak ditemukan.";
    header("Location: tanggapan_aduan.php"); // Ganti dengan nama file yang sesuai
    exit();
}
?>