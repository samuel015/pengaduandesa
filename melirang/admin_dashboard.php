<?php
session_start();
include 'db.php'; // File koneksi database

// Periksa apakah pengguna sudah login sebagai admin
if (!isset($_SESSION['nik']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Ambil jumlah data untuk ditampilkan pada kartu dashboard
try {
    // Query untuk menghitung jumlah pengaduan
    $jumlah_aduan = $pdo->query("SELECT COUNT(*) FROM pengaduan")->fetchColumn();
    
    // Query untuk menghitung jumlah pengaduan yang memiliki tanggapan (isi_pengaduan)
    $jumlah_tanggapan = $pdo->query("
        SELECT COUNT(*) 
        FROM pengaduan
        WHERE isi_pengaduan IS NOT NULL AND isi_pengaduan != ''
    ")->fetchColumn();
    
    // Query untuk menghitung jumlah petugas yang aktif (role = 'admin' dan status = 'aktif')
    $jumlah_petugas = $pdo->query("SELECT COUNT(*) FROM registrasi WHERE role = 'admin' AND status = 'aktif'")->fetchColumn();
    
    // Query untuk menghitung jumlah pengguna yang aktif (role = 'user' dan status = 'aktif')
    $jumlah_pengguna = $pdo->query("SELECT COUNT(*) FROM registrasi WHERE role = 'user' AND status = 'aktif'")->fetchColumn();
} catch (PDOException $e) {
    die("Kesalahan: " . htmlspecialchars($e->getMessage()));
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Pengaduan</title>
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
        .dashboard {
            width: 80%;
            padding: 20px;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
        }
        .card {
            background: #ffffff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            width: 23%; /* Adjust to fit four cards in a row */
            margin-bottom: 20px;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        .card h3 {
            margin: 0 0 10px;
            color: #35424a;
        }
        .card p {
            margin: 0;
            font-size: 24px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h3>Menu</h3>
        <a href="admin_dashboard.php">Dashboard</a>
        <a href="konfirmasi_user.php">Konfirmasi Warga</a>
        <a href="tambah_admin.php">Kelola Admin</a>
        <a href="laporan.php">Lihat Pengaduan</a>
        <a href="tambah_informasi.php">Tambah Agenda Desa</a>
        <a href="logout.php">Keluar</a>
    </div>
    <div class="dashboard">
        <div class="card" onclick="window.location.href='laporan.php'">
            <h3>Daftar Aduan</h3>
            <p><?php echo $jumlah_aduan; ?></p>
        </div>
        <div class="card" onclick="window.location.href='tanggapan_pengaduan.php'">
            <h3>Tanggapan Pengaduan</h3>
            <p><?php echo $jumlah_tanggapan; ?></p>
        </div>
        <div class="card" onclick="window.location.href='daftar_petugas.php'">
            <h3>Petugas</h3>
            <p><?php echo $jumlah_petugas; ?></p>
        </div>
        <div class="card" onclick="window.location.href='daftar_pengguna.php'">
            <h3>Pengguna</h3>
            <p><?php echo $jumlah_pengguna; ?></p>
        </div>
    </div>
</body>
</html>