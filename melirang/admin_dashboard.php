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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            background-color: #f4f4f4;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
        }
        .sidebar {
            height: 100vh;
            width: 250px;
            background: #35424a;
            padding: 20px;
            position: fixed;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
        }
        .sidebar h3 {
            color: #ffffff;
            margin-top: 0;
            font-size: 1.5rem;
        }
        .sidebar a {
            color: #ffffff;
            text-decoration: none;
            display: flex;
            align-items: center;
            margin: 10px 0;
            padding: 10px;
            border-radius: 4px;
            transition: background 0.3s ease;
        }
        .sidebar a:hover {
            background: #444;
        }
        .sidebar a i {
            margin-right: 10px;
        }
        .content {
            margin-left: 270px; /* Space for sidebar */
            padding: 20px;
        }
        .row {
            display: flex;
            flex-wrap: wrap;
            margin: -10px; /* Adjust for spacing */
        }
        .col {
            flex: 1;
            min-width: 220px; /* Minimum width for responsiveness */
            margin: 10px;
            background: #ffffff;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s, box-shadow 0.3s;
            cursor: pointer;
            position: relative;
        }
        .col:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
        }
        .col h ```php
        .col h3 {
            color: #35424a;
            margin: 0 0 10px;
            font-size: 1.2rem;
        }
        .col p {
            font-size: 2.5rem; /* Ukuran font besar */
            color: #35424a;
            margin: 0;
        }
        .icon {
            position: absolute;
            top: 20px;
            right: 20px;
            font-size: 2rem;
            color: #35424a;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h3>Menu</h3>
        <a href="admin_dashboard.php"><i class="fas fa-tachometer-alt"></i>Dashboard</a>
        <a href="konfirmasi_user.php"><i class="fas fa-user-check"></i>Konfirmasi Warga</a>
        <a href="tambah_admin.php"><i class="fas fa-user-plus"></i>Kelola Admin</a>
        <a href="laporan.php"><i class="fas fa-file-alt"></i>Laporan Pengaduan</a>
        <a href="tambah_informasi.php"><i class="fas fa-calendar-plus"></i>Tambah Agenda Desa</a>
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i>Keluar</a>
    </div>
    <div class="content">
        <div class="row">
            <div class="col" onclick="window.location.href='aduan.php'">
                <h3>Daftar Aduan</h3>
                <p><?php echo $jumlah_aduan; ?></p>
                <i class="fas fa-comments icon"></i>
            </div>
            <div class="col" onclick="window.location.href='tanggapan_pengaduan.php'">
                <h3>Jumlah Tanggapan</h3>
                <p><?php echo $jumlah_tanggapan; ?></p>
                <i class="fas fa-reply icon"></i>
            </div>
            <div class="col" onclick="window.location.href='petugas.php'">
                <h3>Jumlah Petugas</h3>
                <p><?php echo $jumlah_petugas; ?></p>
                <i class="fas fa-user-shield icon"></i>
            </div>
            <div class="col" onclick="window.location.href='pengguna.php'">
                <h3>Jumlah Pengguna</h3>
                <p><?php echo $jumlah_pengguna; ?></p>
                <i class="fas fa-users icon"></i>
            </div>
        </div>
    </div>
</body>
</html>