<?php
session_start();
if (!isset($_SESSION['nik']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit();
}

include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = $_POST['id'];
    $stmt = $pdo->prepare("DELETE FROM pengaduan WHERE id = :id AND nik = :nik");
    $stmt->execute(['id' => $id, 'nik' => $_SESSION['nik']]);
    
    header("Location: pengaduan.php"); // Kembali ke halaman pengaduan setelah hapus
    exit();
} else {
    header("Location: pengaduan.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Hapus Pengaduan</title>
</head>
<body>
    <h1>Hapus Pengaduan</h1>
    <p>Apakah Anda yakin ingin menghapus pengaduan ini?</p>
    <form action="hapus.php" method="POST">
        <input type="hidden" name="id" value="<?= htmlspecialchars($_GET['id']); ?>">
        <button type="submit">Ya, Hapus</button>
    </form>
    <a href="pengaduan.php">Batal</a>
</body>
</html>