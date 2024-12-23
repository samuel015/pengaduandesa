<?php
session_start();
include 'db.php';

$id = $_GET['id'];

// Ambil email pengguna berdasarkan ID pengaduan
$stmt = $pdo->prepare("SELECT email FROM registrasi WHERE id = (SELECT user_id FROM pengaduan WHERE id = :id)");
$stmt->execute(['id' => $id]);
$user = $stmt->fetch();

if ($user) {
    $email = $user['email'];

    // Update status pengaduan menjadi 'Ditolak'
    $stmt = $pdo->prepare("UPDATE pengaduan SET proses = 'Ditolak' WHERE id = :id");
    $stmt->execute(['id' => $id]);

    // Konfigurasi email
    $to = $email;
    $subject = "Pemberitahuan Penolakan Pengaduan";
    $message = "Pengaduan Anda dengan ID: $id telah ditolak. Silakan hubungi kami untuk informasi lebih lanjut.";
    $headers = "From: noreply@yourdomain.com"; // Ganti dengan alamat email yang valid

    // Konfigurasi SMTP untuk Gmail
    $smtpHost = "smtp.gmail.com";
    $smtpPort = 587;
    $smtpUsername = "your-email@gmail.com"; // Ganti dengan alamat email Gmail Anda
    $smtpPassword = "your-password"; // Ganti dengan password email Gmail Anda

    // Menggunakan fsockopen untuk mengirim email
    $sock = fsockopen($smtpHost, $smtpPort, $errno, $errstr, 30);
    if (!$sock) {
        echo "Gagal menghubungkan ke SMTP: $errstr ($errno)";
        exit;
    }

    // Mengirim perintah HELO
    fwrite($sock, "HELO " . $smtpHost . "\r\n");
    $response = fgets($sock, 1024);
    if (strpos($response, "250") !== 0) {
        echo "Gagal mengirim perintah HELO: $response";
        exit;
    }

    // Mengirim perintah AUTH LOGIN
    fwrite($sock, "AUTH LOGIN\r\n");
    $response = fgets($sock, 1024);
    if (strpos($response, "334") !== 0) {
        echo "Gagal mengirim perintah AUTH LOGIN: $response";
        exit;
    }

    // Mengirim username dan password
    fwrite($sock, base64_encode($smtpUsername) . "\r\n");
    $response = fgets($sock, 1024);
    if (strpos($response, "334") !== 0) {
        echo "Gagal mengirim username: $response";
        exit;
    }
    fwrite($sock, base64_encode($smtpPassword) . "\r\n");
    $response = fgets($sock, 1024);
    if (strpos($response, "235") !== 0) {
        echo "Gagal mengirim password: $response";
        exit;
    }

    // Mengirim perintah MAIL FROM
    fwrite($sock, "MAIL FROM: <" . $smtpUsername . ">\r\n");
    $response = fgets($sock, 1024);
    if (strpos($response, "250") !== 0) {
        echo "Gagal mengirim perintah MAIL FROM: $response";
        exit;
    }

    // Mengirim perintah RCPT TO
    fwrite($sock, "RCPT TO: <" . $to . ">\r TO: <" . $to . ">\r\n");
    $response = fgets($sock, 1024);
    if (strpos($response, "250") !== 0) {
        echo "Gagal mengirim perintah RCPT TO: $response";
        exit;
    }

    // Mengirim perintah DATA
    fwrite($sock, "DATA\r\n");
    $response = fgets($sock, 1024);
    if (strpos($response, "354") !== 0) {
        echo "Gagal mengirim perintah DATA: $response";
        exit;
    }

    // Mengirim email
    fwrite($sock, "Subject: " . $subject . "\r\n");
    fwrite($sock, "From: " . $smtpUsername . "\r\n");
    fwrite($sock, "To: " . $to . "\r\n");
    fwrite($sock, "\r\n");
    fwrite($sock, $message . "\r\n");
    fwrite($sock, ".\r\n");
    $response = fgets($sock, 1024);
    if (strpos($response, "250") !== 0) {
        echo "Gagal mengirim email: $response";
        exit;
    }

    // Mengirim perintah QUIT
    fwrite($sock, "QUIT\r\n");
    fclose($sock);

    echo "Email berhasil dikirim!";
} else {
    // Tidak ditemukan pengguna dengan ID pengaduan
    echo "Tidak ditemukan pengguna dengan ID pengaduan";
}

header("Location: pengaduan.php");
exit();
?>