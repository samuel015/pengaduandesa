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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            background-color: #f4f4f4;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
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
        }
        .col:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
        }
        .col h3 {
            color: #35424a;
            margin: 0 0 10px;
        }
        .col p {
            font-size: 2.5rem; /* Ukuran font besar */
            color: #35424a;
            margin: 0;
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
    </header>
    <div class="container">
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
            <h2>Daftar User</h2>
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
