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

// Tolak tanggapan
try {
    $stmt = $pdo->prepare("UPDATE tanggapan SET status = 'Ditolak' WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: tanggapan.php?success=tolak");
    exit();
} catch (PDOException $e) {
    die("Kesalahan: " . htmlspecialchars($e->getMessage()));
}
?>

<!-- Tambahkan kode HTML untuk menampilkan halaman penolakan tanggapan -->
<h1>Tanggapan Ditolak</h1>
<p>Tanggapan dengan id <?= $id ?> telah ditolak.</p>

<!-- Tambahkan kode untuk menampilkan pesan sukses -->
<?php if (isset($_GET['success']) && $_GET['success'] == 'tolak') : ?>
    <p>Tanggapan berhasil ditolak!</p>
    
<?php endif; ?>