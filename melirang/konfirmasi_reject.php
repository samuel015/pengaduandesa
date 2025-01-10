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

    $email = $result['email'];
    if (!$email) {
        die("Kesalahan: Email tidak ditemukan.");
    }

    // Periksa proses pengaduan
    if ($result['proses'] === 'Pending') {
        // Update proses pengaduan menjadi "Tolak"
        $updateStmt = $pdo->prepare("UPDATE pengaduan SET proses = ? WHERE id = ?");
        $updateStmt->execute(['Tolak', $id_pengaduan]);

        if ($updateStmt->rowCount() === 0) {
            die("Kesalahan: Proses pengaduan tidak dapat diperbarui.");
        }

        // Kirim notifikasi penolakan (misalnya, simpan ke log atau kirim email)
        // Anda bisa menambahkan logika di sini untuk mengirim email atau menyimpan ke database
        echo "Pengaduan Anda telah ditolak. Silakan hubungi pihak desa untuk informasi lebih lanjut.";
        
    } else {
        // Jika pengaduan sudah diproses
        echo "Pengaduan Anda sudah diproses sebelumnya.";
    }

} catch (PDOException $e) {
    die("Kesalahan: " . htmlspecialchars($e->getMessage()));
}
?>