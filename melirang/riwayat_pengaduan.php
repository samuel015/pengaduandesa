<?php
session_start();
include 'db.php'; // File koneksi database

// Pastikan pengguna telah login
if (!isset($_SESSION['nik'])) {
    header("Location: login.php");
    exit();
}

// Ambil riwayat pengaduan dari database
$stmt = $pdo->prepare("SELECT * FROM pengaduan WHERE nik = :nik ORDER BY created_at DESC");
$stmt->execute(['nik' => $_SESSION['nik']]);
$riwayat_pengaduan = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Pengaduan</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            background-color: #f7f9fc;
            color: #333;
            line-height: 1.6;
            display: flex;
        }
        .sidebar {
            width: 250px;
            background-color: #2c3e50;
            color: #ecf0f1;
            height: 100vh;
            padding: 20px;
        }
        .sidebar h2 {
            color: #ecdbff;
            text-align: center;
            margin-bottom: 20px;
        }
        .sidebar a {
            color: #ecf0f1;
            text-decoration: none;
            display: block;
            margin: 10px 0;
            padding: 10px;
            border-radius: 5px;
            transition: background 0.3s;
        }
        .sidebar a:hover {
            background-color: #34495e;
        }
        .content {
            flex: 1;
            padding: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table th, table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        table th {
            background-color: #3498db;
            color: white;
        }
        .message {
            margin: 20px 0;
            color: green;
        }
        .error {
            color: red;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>Menu</h2>
        <a href="pengaduan.php">Kirim Pengaduan</a>
        <a href="riwayat_pengaduan.php">Riwayat Pengaduan</a>
        <a href="logout.php">Logout</a>
    </div>
    <div class="content">
        <h1>Riwayat Pengaduan</h1>
        <?php if (empty($riwayat_pengaduan)): ?>
            <p>Anda belum memiliki pengaduan.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Jenis Aduan</th>
                        <th>Isi Pengaduan</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($riwayat_pengaduan as $pengaduan): ?>
                        <tr>
                            <td><?= htmlspecialchars($pengaduan['created_at']); ?></td>
                            <td><?= htmlspecialchars($pengaduan['jenis_aduan']); ?></td>
                            <td><?= htmlspecialchars($pengaduan['isi_pengaduan']); ?></td>
                            <td><?= htmlspecialchars($pengaduan['status']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>