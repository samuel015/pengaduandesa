<?php 
session_start();
include 'db.php'; 
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Informasi Agenda</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            display: flex;
            width: 100%;
            margin: auto;
            overflow: hidden;
        }
        header {
            background: #35424a;
            color: #ffffff;
            padding: 20px 0;
            text-align: center;
            width: 100%;
        }
        .sidebar {
            width: 20%;
            background: #35424a;
            color: #ffffff;
            padding: 15px;
        }
        .sidebar a {
            color: #ffffff;
            text-decoration: none;
            display: block;
            padding: 10px;
            margin: 5px 0;
            border-radius: 5px;
        }
        .sidebar a:hover {
            background: #444;
        }
        .content {
            width: 80%;
            padding: 20px;
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
    <div class="container">
        <div class="sidebar">
            <a href="admin_dashboard.php">Dashboard</a>
            <a href="konfirmasi_registrasi.php">Konfirmasi Warga</a>
            <a href="konfirmasi_admin.php">Konfirmasi Admin</a>
            <a href="laporan.php">Lihat Pengaduan</a>
            <a href="tambah_informasi.php">Tambah Agenda Desa</a>
            <a href="logout.php">keluar</a>
        </div>
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