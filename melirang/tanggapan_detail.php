<?php
session_start();
include 'db.php'; // File koneksi database

// Periksa apakah pengguna sudah login sebagai admin
if (!isset($_SESSION['nik']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Ambil data pengaduan yang dipilih
try {
    $id_pengaduan = $_GET['id'];
    $stmt = $pdo->query("SELECT * FROM pengaduan WHERE id = '$id_pengaduan'");
    $data_pengaduan = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Kesalahan: " . htmlspecialchars($e->getMessage()));
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tanggapan Detail Pengaduan - Admin</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .tanggapan {
            margin: 20px auto;
            width: 80%;
        }
        form {
            margin-top: 20px;
        }
        label {
            display: block;
            margin-bottom: 10px;
        }
        input[type="text"], textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
 border: 1px solid #ccc;
            border-radius: 4px;
        }
        input[type="submit"] {
            background-color: #35424a;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="tanggapan">
        <h2>Tanggapan Detail Pengaduan</h2>
        <form action="proses_tanggapan.php" method="POST">
            <input type="hidden" name="id_pengaduan" value="<?= $data_pengaduan['id']; ?>">
            <label for="tanggapan">Tanggapan:</label>
            <textarea name="tanggapan" rows="5" required></textarea>
            <input type="submit" value="Kirim Tanggapan">
        </form>
        <h3>Detail Pengaduan</h3>
        <p><strong>Jenis Pengaduan:</strong> <?= $data_pengaduan['jenis_pengaduan']; ?></p>
        <p><strong>Isi Pengaduan:</strong> <?= $data_pengaduan['isi_pengaduan']; ?></p>
        <p><strong>Tanggal Pengaduan:</strong> <?= $data_pengaduan['tanggal_pengaduan']; ?></p>
    </div>
</body>
</html>