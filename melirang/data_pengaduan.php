<?php
session_start(); // Memulai sesi
include 'db.php'; 

// Pastikan Anda memiliki variabel sesi untuk mengecek apakah pengguna telah login
$is_logged_in = isset($_SESSION['user_id']); // Misalnya, 'user_id' disimpan dalam sesi saat login

// Ambil status konfirmasi pengguna dari database
$user_id = $_SESSION['user_id'] ?? null; // Ambil user_id dari sesi
$is_confirmed = false; // Default belum terkonfirmasi

if ($user_id) {
    $stmt = $pdo->prepare("SELECT is_confirmed FROM users WHERE id = :id");
    $stmt->execute(['id' => $user_id]);
    $user = $stmt->fetch();
    if ($user) {
        $is_confirmed = $user['is_confirmed'] == 1; // Cek status konfirmasi
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Pengaduan Masyarakat Desa Melirang</title>
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
            margin: 0;
            padding: 0;
            height: 100vh; /* Pastikan tinggi halaman sesuai tinggi layar */
        }

        /* Gambar Full Screen */
        .image-container {
            width: 100%;
            height: 100vh; /* Membuat gambar mengambil seluruh layar */
            display: flex;
            justify-content: center; /* Memastikan gambar terpusat secara horizontal */
            align-items: center; /* Memastikan gambar terpusat secara vertikal */
            overflow: hidden; /* Menghindari scroll saat gambar lebih besar dari layar */
            position: relative; /* Untuk posisi navbar di dalam gambar */
        }

        .image-container img {
            width: 100%;
            height: 100%; /* Membuat gambar mengisi seluruh layar */
            object-fit: cover; /* Menjaga gambar tetap terpotong dan sesuai proporsi */
        }

        /* Navbar */
        .navbar {
            display: flex;
            justify-content: flex-end; /* Navbar berada di sebelah kanan */
            padding: 10px;
            position: absolute;
            top: 20px;
            width: 100%;
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
            position: absolute;
            top: 120px; /* Memberikan jarak dari navbar */
            width: 80%; /* Menyesuaikan lebar konten */
            max-width: 1200px;
            background: rgba(255, 255, 255, 0.8); /* Semi transparan putih */
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #4CAF50;
            color: white;
        }

        /* Tombol Kirim Pengaduan */
        .kirim-button {
            margin: 20px 0;
            padding: 10px 15px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
        }

        .kirim-button:disabled {
            background-color: #ccc;
            cursor: not-allowed;
        }

        /* Pesan */
        .message {
            color: red;
            margin: 10px 0;
        }

    </style>
</head>
<body>
    <!-- Gambar Full Screen -->
    <div class="image-container">
        <img src="uploads/desa.png" alt="Desa Melirang"> <!-- Gambar desa yang diambil -->
    </div>

    <!-- Navbar tanpa background, dipindahkan ke kanan -->
    <div class="navbar">
        <a href="index.php">Home</a>
        <a href="data_pengaduan.php">Data Pengaduan</a>
        <a href="agenda.php">Agenda Desa</a>
        <a href="login.php">Login</a> <!-- Tautan Login di sebelah kanan navbar -->
    </div>

    <!-- Container untuk menampilkan data pengaduan -->
    <div class="container">
        <!-- Tampilkan tombol Kirim Pengaduan jika pengguna sudah login -->
        <?php if ($is_logged_in): ?>
            <?php if (!$is_confirmed): ?>
                <div class="message">Anda belum dikonfirmasi oleh admin. Tombol kirim pengaduan dinonaktifkan.</div>
                <button class="kirim-button" disabled>Kirim Pengaduan</button>
            <?php else: ?>
                <a href="kirim_pengaduan.php" class="kirim-button">Kirim Pengaduan</a>
            <?php endif; ?>
        <?php endif; ?>

        <!-- Tampilkan daftar pengaduan -->
        <h3>Daftar Pengaduan</h3>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>NIK</th>
                    <th>Nama</th>
                    <th>jenis pengaduan</th>
                    <th>Isi Pengaduan</th>
                    <th>Status</th>
                    <th>Proses</th>
                    <th>Bukti</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $stmt = $pdo->query("SELECT * FROM pengaduan");
            while ($row = $stmt->fetch()) {
                echo "<tr>
                        <td>{$row['id']}</td>
                        <td>{$row['nik']}</td>
                        <td>{$row['nama']}</td>
                        <td>{$row['jenis_aduan']}</td>
                        <td>{$row['isi_pengaduan']}</td>
                        <td>{$row['status']}</td>
                        <td>{$row['proses']}</td>
                        <td>";
                if ($row['evidence']) {
                    echo "<a href='uploads/{$row['evidence']}'>Download</a>";
                } else {
                    echo "Tidak ada";
                }
                echo "</td></tr>";
            }
            ?>
        </tbody>
        </table>
    </div>
</body>
</html>
