<?php
session_start();
include 'db.php'; // File koneksi database

// Periksa apakah pengguna sudah login sebagai admin
if (!isset($_SESSION['nik']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Pastikan ada parameter ID
if (!isset($_GET['id'])) {
    die("Kesalahan: ID tidak ditemukan.");
}

$id_pengaduan = intval($_GET['id']);

try {
    // Ambil email dan status pengaduan berdasarkan ID pengaduan
    $stmt = $pdo->prepare("SELECT r.email, p.proses FROM registrasi r JOIN pengaduan p ON r.nik = p.nik WHERE p.id = ?");
    $stmt->execute([$id_pengaduan]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    // Periksa apakah data ditemukan
    if (!$result) {
        die("Kesalahan: Data pengaduan tidak ditemukan.");
    }

    // Periksa proses pengaduan
    if ($result['proses'] === 'Diterima') { // Jika status adalah 'Diterima'
        // Update proses pengaduan menjadi "Proses"
        $updateStmt = $pdo->prepare("UPDATE pengaduan SET proses = ? WHERE id = ?");
        $updateStmt->execute(['Proses', $id_pengaduan]);

        if ($updateStmt->rowCount() === 0) {
            die("Kesalahan: Proses pengaduan tidak dapat diperbarui.");
        }

        // Redirect ke halaman tanggapan_pengaduan.php dengan pesan sukses
        $_SESSION['message'] = "Pengaduan berhasil diproses.";
        header("Location: tanggapan_pengaduan.php"); // Ganti dengan nama file yang sesuai
        exit();
    } elseif ($result['proses'] === 'Proses') {
        die("Kesalahan: Pengaduan sudah dalam proses.");
    } else {
        die("Kesalahan: Pengaduan tidak dapat diproses (status saat ini: " . htmlspecialchars($result['proses']) . ").");
    }
} catch (PDOException $e) {
    die("Kesalahan: " . htmlspecialchars($