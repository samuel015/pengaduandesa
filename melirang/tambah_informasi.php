<?php 
session_start();
include 'db.php'; 
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Informasi Agenda</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            background-color: #f4f4f4;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            height: 100vh; /* Pastikan body memiliki tinggi penuh */
        }
        .sidebar {
            height: 100vh;
            width: 250px;
            background: #35424a;
            padding: 20px;
            position: fixed;
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
            margin-left: 270px; /* Space for sidebar */
            padding: 20px;
            flex: 1; /* Mengambil sisa ruang yang tersedia */
        }
        .form-container {
            margin-left: 20px; /* Memberi jarak dari sidebar */
        }
        .form-container h2 {
            margin-bottom: 20px;
        }
        input[type="text"], input[type="date"], textarea {
            width: 100%; /* Memastikan semua input memiliki lebar penuh */
            padding: 10px;
            margin-bottom: 15px; /* Memberi jarak antara input */
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button.submit-btn {
            padding: 10px;
            background: #35424a;
            color: #ffffff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 100%; /* Tombol juga lebar penuh */
        }
        button.submit-btn:hover {
            background: #444;
        }
        .error {
            color: red;
        }
        .success {
            color: green;
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
        <div class="form-container">
            <h2>Tambah Informasi Agenda</h2>
            <form action="proses_tambah_agenda.php" method="POST">
                <input type="text" name="judul" placeholder="Judul Agenda" required>
                <input type="date" name="tanggal" required>
                <input type="text" name="waktu" placeholder="Waktu (contoh: 10:00 - 12:00 WIB)" required>
                <input type="text" name="tempat" placeholder="Tempat" required>
                <textarea name="deskripsi" placeholder="Deskripsi" rows="4" required></textarea>
                <button type="submit" class="submit-btn">Tambah Agenda</button>
            </form>
        </div>
    </div>
</body>
</html>