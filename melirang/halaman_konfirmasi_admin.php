<?php
session_start();
include 'db.php'; // Pastikan file ini berisi koneksi ke database

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $action = $_POST['action'] ?? null;

    if ($id && $action === 'accept') {
        try {
            // Update status pengguna menjadi 'diterima'
            $query = "UPDATE registrasi SET status = 'diterima' WHERE id = :id";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            if ($stmt->execute()) {
                $_SESSION['message'] = "Pengguna berhasil diaktifkan.";
            } else {
                $_SESSION['error'] = "Gagal mengaktifkan pengguna.";
            }
        } catch (PDOException $e) {
            $_SESSION['error'] = "Terjadi kesalahan: " . $e->getMessage();
        }
    } else {
        $_SESSION['error'] = "Data tidak valid.";
    }

    // Redirect kembali ke halaman konfirmasi
    header("Location: konfirmasi_registrasi.php");
    exit();
} else {
    $_SESSION['error'] = "Akses tidak valid.";
    header("Location: konfirmasi_registrasi.php");
    exit();
}
