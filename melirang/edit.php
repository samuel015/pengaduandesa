<?php
session_start();
if (!isset($_SESSION['nik']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit();
}

include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = $_POST['id'];
    $stmt = $pdo->prepare("SELECT * FROM pengaduan WHERE id = :id AND nik = :nik");
    $stmt->execute(['id' => $id, 'nik' => $_SESSION['nik']]);
    $pengaduan = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$pengaduan) {
        echo "Pengaduan tidak ditemukan.";
        exit();
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['isi_pengaduan'])) {
    $id = $_POST['id'];
    $new_isi_pengaduan = $_POST['isi_pengaduan'];
    $stmt = $pdo->prepare("UPDATE pengaduan SET isi_pengaduan = :isi_pengaduan WHERE id = :id AND nik = :nik");
    $stmt->execute(['isi_pengaduan' => $new_isi_pengaduan, 'id' => $id, 'nik' => $_SESSION['nik']]);
    
    header("Location: pengaduan.php"); // Kembali ke halaman pengaduan setelah edit
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
    <title>Edit Pengaduan</title>
</head>
<body>
    <h1>Edit Pengaduan</h1>
    <form action="edit.php" method="POST">
        <input type="hidden" name="id" value="<?= htmlspecialchars($pengaduan['id']); ?>">
        <textarea name="isi_pengaduan" required><?= htmlspecialchars($pengaduan['isi_pengaduan']); ?></textarea>
        <button type="submit">Update Pengaduan</button>
    </form>
    <a href="pengaduan.php">Kembali</a>
</body>
</html>