<?php
session_start();
include 'db.php'; // File koneksi database

// Periksa apakah pengguna sudah login sebagai admin
if (!isset($_SESSION['nik']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Ambil data aduan dari database
try {
    $stmt = $pdo->query("SELECT p.id, p.jenis_aduan, r.nama, r.nik, p.isi_pengaduan, p.status, p.evidence 
                         FROM pengaduan p
                         LEFT JOIN registrasi r ON p.nik = r.nik");
    $data_pengaduan = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Kesalahan: " . htmlspecialchars($e->getMessage()));
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Aduan - Pengaduan</title>
    <style>
        /* CSS styles */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
        }
        .sidebar {
            width: 250px;
            background-color: #35424a;
            color: white;
            padding: 20px;
            height: 100vh;
            position: fixed;
        }
        .sidebar h2 {
            color: #ffffff;
        }
        .sidebar a {
            color: #ffffff;
            text-decoration: none;
            display: block;
            padding: 10px 0;
        }
        .sidebar a:hover {
            background-color: #4a4a4a;
        }
        .container {
            margin-left: 270px; /* Space for sidebar */
            width: calc(100% - 270px);
            padding: 20px;
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        h2 {
            color: #35424a;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #35424a;
            color: white;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
    </style>
</head>
<body>
    <div class="sidebar">
       <h3>Menu</h3>
        <a href="admin_dashboard.php">Dashboard</a>
        <a href="konfirmasi_user.php">Konfirmasi Warga</a>
        <a href="halaman_konfirmasi_admin.php">kelola Admin</a>
        <a href="laporan.php">Lihat Pengaduan</a>
        <a href="tambah_informasi.php">Tambah Agenda Desa</a>
        <a href="logout.php">Keluar</a>
    </div>
    <div class="container">
        <h2>Daftar Aduan</h2>
        <table border="1" cellpadding="5" cellspacing="0">
            <tr>
                <th>No.</th>
                <th>NIK</th>
                <th>Nama</th>
                <th>Jenis Aduan</th>
                <th>Isi Pengaduan</th>
                <th>Lampiran</th>
                <th>Status</th>
            </tr>
            <?php
            $stmt = $pdo->prepare("SELECT * FROM pengaduan");
            $stmt->execute();
            $no = 1;
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                ?>
                <tr>
                    <td><?= $no++; ?></td>
                    <td><?= $row['nik']; ?></td>
                    <td><?= $row['nama']; ?></td>
                    <td><?= $row['jenis_aduan']; ?></td>
                    <td><?= $row['isi_pengaduan']; ?></td>
                    <td><?= $row['evidence']; ?></td>
                    <td><?= $row['proses']; ?></td>
                </tr>
                <?php
            }
            ?>
        </table>
    </div>
</body>
</html>
