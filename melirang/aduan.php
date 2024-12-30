<?php
session_start();
include 'db.php'; // File koneksi database

// Periksa apakah pengguna sudah login sebagai admin
if (!isset($_SESSION['nik']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Ambil data tanggapan berdasarkan pengaduan
try {
    $stmtTanggapan = $pdo->query("SELECT t.*, p.jenis_aduan, r.nama AS nama_pelapor FROM tanggapan t LEFT JOIN pengaduan p ON t.id = p.id LEFT JOIN registrasi r ON p.nik = r.nik");
    $tanggapan = $stmtTanggapan->fetchAll(PDO::FETCH_ASSOC);

    $stmtPengaduan = $pdo->query("SELECT p.*, r.nama AS nama_pelapor FROM pengaduan p LEFT JOIN registrasi r ON p.nik = r.nik");
    $pengaduan = $stmtPengaduan->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Kesalahan: " . htmlspecialchars($e->getMessage()));
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaduan dan Tanggapan</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
        }
        .sidebar {
            width: 20%;
            background: #35424a;
            color: #ffffff;
            padding: 20px;
            height: 100vh;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
        }
        .sidebar h3 {
            color: #ffffff;
            margin-top: 0;
        }
        .sidebar a {
            color: #ffffff;
            text-decoration: none;
            display: block;
            margin: 10px 0;
            padding: 10px;
            border-radius: 4px;
            transition: background 0.3s ease;
        }
        .sidebar a:hover {
            background: #444;
        }
        .content {
            width: 80%;
            padding: 20px;
        }
        .content h3 {
            font-size: 24px;
            color: #35424a;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        th {
            background: #35424a;
            color: #ffffff;
        }
        textarea {
            width: 100%;
            height: 50px;
            resize: none;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h3>Menu</h3>
        <a href="admin_dashboard.php">Dashboard</a>
        <a href="konfirmasi_user.php">Konfirmasi Warga</a>
        <a href="halaman_konfirmasi_admin.php">Kelola Admin</a>
        <a href="laporan.php">Lihat Pengaduan</a>
        <a href="tambah_informasi.php">Tambah Agenda Desa</a>
        <a href="logout.php">Keluar</a>
    </div>

    <div class="content">
        <h3>Daftar Pengaduan</h3>
        <table>
            <tr>
                <th>No.</th>
                <th>Nama Pelapor</th>
                <th>Jenis Aduan</th>
                <th>Keterangan</th>
                <th>Status</th>
            </tr>
            <?php $no = 1; ?>
            <?php foreach ($pengaduan as $row) : ?>
                <tr>
                    <td><?= $no++; ?></td>
                    <td><?= htmlspecialchars($row['nama_pelapor']); ?></td>
                    <td><?= htmlspecialchars($row['jenis_aduan']); ?></td>
                    <td><?= htmlspecialchars($row['isi_pengaduan']); ?></td>
                    <td><?= htmlspecialchars($row['status']); ?></td>
                    
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>

</body>
</html>
