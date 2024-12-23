<?php
session_start();
include 'db.php'; // Pastikan koneksi database sudah benar

// Periksa apakah pengguna sudah login sebagai admin
if (!isset($_SESSION['nik']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Ambil semua data pengguna dari database
try {
    $query = "SELECT * FROM registrasi WHERE role = 'user' ORDER BY created_at DESC"; // Mengambil data pengguna
    $stmt = $pdo->query($query);
    $pengguna = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Kesalahan: " . htmlspecialchars($e->getMessage()));
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Pengguna - Admin</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            display: flex; /* Menggunakan flexbox untuk layout */
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
        .content {
            padding: 20px;
            width: 80%; /* Lebar konten utama */
        }
        .data-header {
            background: #35424a;
            color: #ffffff;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #35424a;
            color: #fff;
        }
        .img-thumbnail {
            max-width: 100px;
            max-height: 100px;
        }
        .action-btn {
            padding: 5px 10px;
            margin-right: 5px;
            cursor: pointer;
        }
        .edit-btn { background-color: #4CAF50; color: white; }
        .copy-btn { background-color: #008CBA; color: white; }
        .delete-btn { background-color: #f44336; color: white; }
    </style>
</head>
<body>
    <div class="container">
        <div class="sidebar">
            <h3>Menu</h3>
            <a href="admin_dashboard.php">Dashboard</a>
            <a href="konfirmasi_user.php">Konfirmasi Warga</a>
            <a href="halaman_konfirmasi_admin.php">Kelola Admin</a>
            <a href="laporan.php">Lihat Pengaduan</a>
            <a href="tambah_informasi.php">Tambah Agenda Desa</a>
            <a href="daftar_petugas.php">Daftar Petugas</a>
            <a href="logout.php">Keluar</a>
        </div>
        <div class="content">
            <div class="data-header">
                <h2>Data Pengguna</h2>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama</th>
                        <th>NIK</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Alamat</th>
                        <th>Jabatan</th>
                        <th>Foto KTP</th>
                        <th>Foto</th>
                        <th>Status</th>
                       
                        
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pengguna as $data) : ?>
                        <tr>
                            <td><?php echo htmlspecialchars($data['id']); ?></td>
                            <td><?php echo htmlspecialchars($data['nama']); ?></td>
                            <td><?php echo htmlspecialchars($data['nik']); ?></td>
                            <td><?php echo htmlspecialchars($data['email']); ?></td>
                            <td><?php echo htmlspecialchars($data['role']); ?></td>
                            <td><?php echo htmlspecialchars($data['alamat']); ?></td>
                            <td><?php echo htmlspecialchars($data['jabatan']); ?></td>
                            <td><img src="<?php echo htmlspecialchars($data['foto_ktp']); ?>" class="img-thumbnail" alt="Foto KTP"></td>
                            <td><img src="<?php echo htmlspecialchars($data['foto']); ?>" class="img-thumbnail" alt="Foto"></td>
                            <td><?php echo htmlspecialchars($data['status']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>