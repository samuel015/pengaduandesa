<?php
session_start();
include 'db.php';

// Periksa apakah pengguna sudah login sebagai admin
if (!isset($_SESSION['nik']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Ambil id tanggapan
$id = $_GET['id'];

// Ambil email pengguna berdasarkan id tanggapan
try {
    $stmt_email = $pdo->prepare("SELECT email FROM registrasi WHERE nik = (SELECT nik FROM tanggapan WHERE id = ?)");
    $stmt_email->execute([$id]);
    $user = $stmt_email->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $email_user = $user['email'];

        // Tolak tanggapan
        $stmt = $pdo->prepare("UPDATE tanggapan SET status = 'Ditolak' WHERE id = ?");
        $stmt->execute([$id]);

        // Kirim email pemberitahuan
        $to = $email_user;
        $subject = "Tanggapan Anda Ditolak";
        $message = "Tanggapan dengan ID $id telah ditolak.";
        $headers = "From: admin@example.com\r\n" . // Ganti dengan alamat email pengirim
                   "Reply-To: admin@example.com\r\n" . // Ganti dengan alamat email pengirim
                   "X-Mailer: PHP/" . phpversion();

        if (mail($to, $subject, $message, $headers)) {
            // Redirect ke halaman tanggapan dengan pesan sukses
            header("Location: tanggapan.php?success=tolak");
            exit();
        } else {
            // Jika email gagal dikirim
            die("Email gagal dikirim.");
        }
    } else {
        // Jika tidak ada email ditemukan
        header("Location: tanggapan.php?error=email_not_found");
        exit();
    }
} catch (PDOException $e) {
    die("Kesalahan: " . htmlspecialchars($e->getMessage()));
}
?>