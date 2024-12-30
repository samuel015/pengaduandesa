<?php
session_start();
include 'db.php'; // Pastikan koneksi database sudah benar

// Cek apakah pengguna yang login adalah admin
if ($_SESSION['role'] !== 'admin') {
    header("Location: konfirmasi_admin.php");
    exit();
}

// Ambil daftar pengguna dengan status 'Pending' dan role 'user' 
$query = "SELECT * FROM registrasi WHERE status = 'Pending' AND role = 'user' ORDER BY created_at DESC";
$result = $pdo->query($query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konfirmasi User</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        header {
            background: #35424a;
            color: white;
            padding: 20px;
            text-align: center;
        }
        .container {
            display: flex;
            margin: auto;
        }
        .sidebar {
            width: 20%;
            background: #35424a;
            padding: 20px;
            height: 100vh;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
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
            background: #666; /* Sedikit lebih terang saat hover */
        }
        .content {
            width: 80%;
            padding: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th {
            background: #35424a;
            color: white;
            padding: 10px;
        }
        td {
            padding: 10px;
        }
        button {
            padding: 8px 12px;
            border: none;
            color: white;
            cursor: pointer;
            border-radius: 4px;
        }
        .btn-accept {
            background: #4CAF50;
        }
        .btn-reject {
            background: #f44336;
        }
    </style>
</head>
<body>
    <header>
        <h1>Halaman Konfirmasi User</h1>
    </header>
    <div class="container">
        <div class="sidebar">
            <h2 style="color: white;">Menu</h2>
            <a href="admin_dashboard.php">Dashboard</a>
            <a href="konfirmasi_user.php">Konfirmasi Warga</a>
            <a href="tambah_admin.php">Tambah admin</a>
            <a href="laporan.php">Lihat Pengaduan</a>
            <a href="tambah_informasi.php">Tambah Agenda Desa</a>
            <a href="logout.php">Keluar</a>
        </div>
        <div class="content">
            <h2>Daftar User Pending Konfirmasi</h2>
            <?php if (isset($_SESSION['message'])): ?>
                <p style="color: green;"><?php echo $_SESSION['message']; unset($_SESSION['message']); ?></p>
            <?php endif; ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama</th>
                        <th>NIK</th>
                        <th>Email</th>
                        <th>Alamat</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->rowCount() > 0): ?>
                        <?php while ($row = $result->fetch(PDO::FETCH_ASSOC)): ?>
                            <tr>
                                <td><?= $row['id']; ?></td>
                                <td><?= htmlspecialchars($row['nama']); ?></td>
                                <td><?= htmlspecialchars($row['nik']); ?></td>
                                <td><?= htmlspecialchars($row['email']); ?></td>
                                <td><?= htmlspecialchars($row['alamat']); ?></td>
                                <td><?= htmlspecialchars($row['status']); ?></td>
                                <td>
                                    <!-- Form untuk Aktifkan -->
                                    <form method="POST" action="aktifkan_user.php" style="display:inline;">
                                        <input type="hidden" name="user_id" value="<?= $row['id']; ?>">
                                        <button type="submit" class="btn-accept">Aktifkan</button>
                                    </form>
                                    <!-- Form untuk Tolak -->
                                    <form method="POST" action="tolak_user.php" style="display:inline;">
                                        <input type="hidden" name="user_id" value="<?= $row['id']; ?>">
                                        <button type="submit" class="btn-reject">Tolak</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" style="text-align:center;">Tidak ada pengguna untuk dikonfirmasi</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
