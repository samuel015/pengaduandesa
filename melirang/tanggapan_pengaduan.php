<?php
session_start();
include 'db.php'; // File koneksi database

// Periksa apakah pengguna sudah login sebagai admin
if (!isset($_SESSION['nik']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Ambil data tanggapan
try {
    $stmt = $pdo->query("SELECT * FROM tanggapan");
    $tanggapan = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Kesalahan: " . htmlspecialchars($e->getMessage()));
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tanggapan Aduan - Admin</title>
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
        .tanggapan-aduan {
            margin: 20px auto;
            width: 80%;
            padding: 20px;
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .tanggapan-aduan h3 {
            margin: 0;
            font-size: 24px;
            color: #35424a;
        }
        .tanggapan-aduan table {
            width: 100%;
            border-collapse: collapse;
        }
        .tanggapan-aduan th, .tanggapan-aduan td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        .tanggapan-aduan th {
            background: #35424a;
            color: #ffffff;
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

    <div class="tanggapan-aduan">
        <h3>Daftar Tanggapan Aduan</h3>
        <table>
            <tr>
                <th>No.</th>
                <th>Nama Pelapor</th>
                <th>Jenis Aduan</th>
                <th>Keterangan</th>
                <th>Status</th>
                <th>Tanggapan</th>
                <th>Aksi</th>
            </tr>
            <?php $no = 1; ?>
            <?php foreach ($tanggapan as $row) : ?>
                <tr>
                    <td><?= $no++; ?></td>
                    <td><?= htmlspecialchars($row['nama_pelapor']); ?></td>
                    <td><?= htmlspecialchars($row['jenis_aduan']); ?></td>
                    <td><?= htmlspecialchars($row['keterangan']); ?></td>
                    <td><?= htmlspecialchars($row['status']); ?></td>
                    <td>
                        <textarea readonly><?= htmlspecialchars($row['tanggapan']); ?></textarea>
                    </td>
                    <td>
                        <button onclick="proses(<?= $row['id']; ?>)">Proses</button>
                        <button onclick="tolak(<?= $row['id']; ?>)">Tolak</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <script>
        function proses(id) {
            window.location.href = "proses.php?id=" + id;
        }

        function tolak(id) {
            window.location.href = "reject_user.php?id=" + id;
        }
    </script>
</body>
</html>