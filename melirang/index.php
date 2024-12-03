<?php 
session_start();
include 'db.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
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
            padding: 20px;
        }

        /* Navbar */
        .navbar {
            background: #4CAF50;
            padding: 10px;
            text-align: center;
        }

        .navbar a {
            color: white;
            text-decoration: none;
            padding: 14px 20px;
            display: inline-block;
            transition: background 0.3s ease;
        }

        .navbar a:hover {
            background: #45a049;
        }

        /* Container utama */
        .container {
            max-width: 1200px;
            margin: 20px auto; /* Tambahkan margin untuk pemisahan */
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        /* Slideshow styles */
        .slideshow-container {
            position: relative;
            max-width: 100%;
            margin: auto;
            overflow: hidden;
            border-radius: 8px;
        }

        .slide {
            display: none;
            width: 100%;
        }

        .active {
            display: block;
        }

        /* Profil box */
        .profile-box {
            margin-bottom: 20px;
            padding: 20px;
            background: #f0f8ff;
            border-left: 4px solid #4CAF50;
            border-radius: 4px;
            text-align: center; /* Untuk membuat teks rata tengah */
        }

        .profile-box h2 {
            font-size: 24px;
            margin-bottom: 10px;
            color: #4CAF50;
        }

        .profile-box p {
            font-size: 16px;
            line-height: 1.6;
        }

        /* Gambar di bawah profil */
        .image-container {
            text-align: center;
            margin: 20px 0;
        }

        .image-container img {
            max-width: 80%; /* Ukuran gambar sedang */
            height: auto; /* Menjaga rasio aspek */
            border-radius: 8px; /* Menambahkan sudut melengkung */
        }

        /* Info container */
        .info-container {
            margin-top: 20px;
            text-align: center;
        }

        .nav-links a {
            margin: 0 10px;
            text-decoration: none;
            color: #4CAF50;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <a href="index.php">Home</a>
        <a href="data_pengaduan.php">Data Pengaduan</a>
        <a href="agenda.php">agenda desa</a>
    </div>

    <div class="container" id="home">
        <!-- Slideshow -->
        <div class="slideshow-container">
            <img class="slide active" src="images/image1.jpg" alt="Gambar 1">
            <img class="slide" src="images/image2.jpg" alt="Gambar 2">
            <img class="slide" src="images/image3.jpg" alt="Gambar 3">
        </div>

        <div class="profile-box">
            <h2>Sistem Pengaduan Masyarakat Desa Melirang</h2>
            <p>Desa Melirang terletak di kawasan yang indah, dengan pemandangan alam yang memukau dan masyarakat yang ramah. Desa ini memiliki berbagai program pembangunan untuk meningkatkan kesejahteraan masyarakat. Kami mengajak Anda untuk berpartisipasi dalam pengaduan dan saran demi kemajuan bersama.</p>
        </div>

        <!-- Gambar di bawah sistem pengaduan -->
        <div class="image-container">
            <img src="images/your_image.jpg" alt="Desa Melirang">
        </div>
    </div>
    
    <div class="info-container">
        <p>Jika ingin melakukan pengaduan, bisa melakukan registrasi di bawah:</p>
        <div class="nav-links">
            <a href="login.php">Login</a>
            <a href="register.php">Register</a>
        </div>
    </ ```php
    </div>

    <script>
        let slideIndex = 0;
        const slides = document.querySelectorAll('.slide');

        function showSlides() {
            slides.forEach((slide, index) => {
                slide.style.display = (index === slideIndex) ? 'block' : 'none';
            });
            slideIndex = (slideIndex + 1) % slides.length;
            setTimeout(showSlides, 5000); // Ubah gambar setiap 5 detik
        }

        showSlides(); // Mulai slideshow
    </script>
</body>
</html>