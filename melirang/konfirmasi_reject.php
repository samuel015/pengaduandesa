<?php
session_start();
include 'db.php'; // Pastikan koneksi database sudah benar

// Cek apakah pengguna yang login adalah admin
if ($_SESSION['role'] !== 'admin') {
    header("Location: konfirmasi_admin.php");
    exit();
}

if (isset($_POST['user_id'])) {
    $user_id = $_POST['user_id'];

    // Ambil email pengguna berdasarkan ID
    $stmt = $pdo->prepare("SELECT email, nama FROM registrasi WHERE id = :id");
    $stmt->execute([':id' => $user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Kirim email penolakan
        $to = $user['email'];
        $subject = "Pengaduan Anda Ditolak";
        $message = "Halo " . htmlspecialchars($user['nama']) . ",\n\n"
                 . "Kami mohon maaf, pengaduan Anda telah ditolak. "
                 . "Silakan hubungi kami jika Anda memiliki pertanyaan lebih lanjut.\n\n"
                 . "Terima kasih,\n"
                 . "Tim Desa Melirang";
        $headers = "From: desa.melirang@gmail.com\r\n";

        // Kirim email
        if (mail($to, $subject, $message, $headers)) {
            // Update status pengguna menjadi ditolak
            $stmt = $pdo->prepare("UPDATE registrasi SET status = 'Rejected' WHERE id = :id");
            $stmt->execute([':id' => $user_id]);

            $_SESSION['message'] = "Pengaduan berhasil ditolak dan email telah dikirim.";
        } else {
            $_SESSION['message'] = "Pengaduan berhasil ditolak, tetapi gagal mengirim email.";
        }
    } else {
        $_SESSION['message'] = "Pengguna tidak ditemukan.";
    }
} else {
    $_SESSION['message'] = "ID pengguna tidak valid.";
}

// Redirect kembali ke halaman konfirmasi user
header("Location: konfirmasi_user.php");
exit();
?>