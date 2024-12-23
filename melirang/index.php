<?php 
session_start();
include 'db.php'; 
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Desa Melirang</title>
    <style>
        /* Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Body */
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
            height: 100vh; /* Pastikan tinggi halaman sesuai tinggi layar */
        }

        /* Gambar Full Screen */
        .image-container {
            width: 100%;
            height: 100vh; /* Membuat gambar mengambil seluruh layar */
            display: flex;
            justify-content: center; /* Membuat gambar terpusat secara horizontal */
            align-items: center; /* Membuat gambar terpusat secara vertikal */
            overflow: hidden; /* Menghindari scroll saat gambar lebih besar dari layar */
            position: relative; /* Untuk posisi navbar di dalam gambar */
        }

        .image-container img {
            width: 100%;
            height: 100%; /* Membuat gambar mengisi seluruh layar */
            object-fit: cover; /* Menjaga gambar tetap terpotong dan sesuai proporsi */
        }

        /* Navbar di dalam gambar (di sebelah kanan) */
        .navbar {
            position: absolute;
            top: 20px;
            right: 20px; /* Navbar berada di kanan */
            padding: 0; /* Menghapus padding */
            z-index: 10;
            display: flex; /* Membuat navbar menjadi flex container */
        }

        .navbar a {
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            display: inline-block;
            margin: 0 10px;
            transition: background 0.3s ease;
        }

        .navbar a:hover {
            background: rgba(0, 0, 0, 0.8);
        }

        /* Info Container */
        .info-container {
            position: absolute;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            text-align: center;
            color: white;
            z-index: 10; /* Agar berada di atas gambar */
        }

        .info-container p {
            font-size: 18px;
            margin-bottom: 10px;
        }

        .info-container a {
            color: #ffffff;
            text-decoration: none;
            padding: 10px 20px;
            background-color: rgba(0, 0, 0, 0.6); /* Background semi-transparan */
            border-radius: 5px;
            font-size: 18px;
            transition: background-color 0.3s ease;
        }

        .info-container a:hover {
            background-color: rgba(0, 0, 0, 0.8);
        }
    </style>
</head>
<body>

    <!-- Gambar Full Screen -->
    <div class="image-container">
        <img src="uploads/desa.png" alt="Desa Melirang">
        
        <!-- Navbar di dalam gambar (di sebelah kanan) -->
        <div class="navbar">
            <a href="index.php">Home</a>
            <a href="data_pengaduan.php">Data Pengaduan</a>
            <a href="agenda.php">Agenda Desa</a>
            <a href="login.php">Login</a> <!-- Tautan Login di sebelah kanan navbar -->
        </div>
    </div>

</body>
</html>
