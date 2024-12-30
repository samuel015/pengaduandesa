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
    die("Kesalahan: ID tidak ditemukan. Parameter GET: " . htmlspecialchars(print_r($_GET, true)));
}

$id = intval($_GET['id']); // Mengubah nama variabel menjadi $id

try {
    // Ambil email dan status pengaduan berdasarkan ID pengaduan
    $stmt = $pdo->prepare("SELECT r.email, p.proses FROM registrasi r JOIN pengaduan p ON r.nik = p.nik WHERE p.id = ?");
    $stmt->execute([$id]); // Menggunakan $id yang baru
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    // Periksa apakah data ditemukan
    if (!$result) {
        die("Kesalahan: Data pengaduan tidak ditemukan.");
    }

    $email = $result['email'];
    if (!$email) {
        die("Kesalahan: Email tidak ditemukan.");
    }

    // Periksa proses pengaduan
    if ($result['proses'] === 'Pending') {
        // Update proses pengaduan menjadi "Selesai"
        $updateStmt = $pdo->prepare("UPDATE pengaduan SET proses = ? WHERE id = ?");
        $updateStmt->execute(['Selesai', $id]); // Menggunakan $id yang baru

        if ($updateStmt->rowCount() === 0) {
            die("Kesalahan: Proses pengaduan tidak dapat diperbarui.");
        }

        // Alamat email desa
        $email_desa = "desa.melirang@gmail.com"; // Ganti dengan email desa yang sesuai
        $gmail_url = "https://mail.google.com/mail/?view=cm&fs=1" . 
                     "&to=" . urlencode($email) . 
                     "&from=" . urlencode($email_desa) . 
                     "&su=" . urlencode("Penolakan Pengaduan") . 
                     "&body=" . urlencode("Pengaduan Anda telah ditolak oleh pihak desa. Silakan hubungi pihak desa untuk informasi lebih lanjut.");
        
        // Redirect ke URL Gmail
        header("Location: " . $gmail_url);
        exit();
    } else {
        // Jika pengaduan sudah diproses, arahkan ke Gmail tanpa mengupdate status
        $email_desa = "desa.melirang@gmail.com"; // Ganti dengan email desa yang sesuai
        $gmail_url = "https://mail.google.com/mail/?view=cm&fs=1" . 
                     "&to=" . urlencode($email) . 
                     "&from=" . urlencode($email_desa) . 
                     "&su=" . urlencode("Penolakan Pengaduan") . 
                     "&body=" . urlencode("Pengaduan Anda telah ditolak oleh pihak desa. Silakan hubungi pihak desa untuk informasi lebih lanjut.");
        
        // Redirect ke URL Gmail
        header("Location: " . $gmail_url);
        exit();
    }

} catch (PDOException $e) {
    die("Kesalahan: " . htmlspecialchars($e->getMessage()));
}
?>