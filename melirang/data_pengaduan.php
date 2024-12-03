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
    <div class="navbar">
        <a href="index.php">Home</a>
        <a href="data_pengaduan.php">Data Pengaduan</a>
        <a href="agenda.php">agenda desa</a>
    </div>

    <div class="container">
        <!-- Tampilkan tombol Kirim Pengaduan jika pengguna sudah login -->
        <?php if ($is_logged_in): ?>
            <?php if (!$is_confirmed): ?>
                <div class="message">Anda belum di konfirmasi oleh admin. Tombol kirim pengaduan dinonaktifkan.</div>
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
                    <th>Isi Pengaduan</th>
                    <th>Status</th>
                    <th>Proses</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Ambil data pengaduan dari database
                $stmt = $pdo->query("SELECT * FROM pengaduan");
                while ($row = $stmt->fetch()) {
                    echo "<tr>
                            <td>{$row['id']}</td>
                            <td>{$row['nik']}</td>
                            <td>{$row['isi_pengaduan']}</td>
                            <td>{$row['status']}</td>
                            <td>{$row['proses']}</td>
                          </tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
   <!-- Bagian Registrasi dan Login -->
<div class="registration" style="text-align: center;">
    <h3>Jika ingin melakukan pengaduan, bisa melakukan registrasi di bawah:</h3>
    <a href="login.php">Login</a>
    <a href="register.php">Register</a>
</div>
</body>
</html>