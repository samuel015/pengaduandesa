<?php
session_start();
include 'db.php'; // Pastikan Anda sudah mengatur koneksi database di db.php

// Ambil data registrasi yang belum dikonfirmasi
$query = "SELECT * FROM registrasi WHERE status = 'pending'"; // Misalkan ada kolom status di tabel registrasi
$result = $pdo->query($query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konfirmasi Registrasi Pengguna</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        header {
            background: #35424a;
            color: #ffffff;
            padding: 20px 0;
            text-align: center;
            width: 100%;
        }
        .container {
            display: flex;
            width: 100%;
            margin: auto;
            overflow: hidden;
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
            display: flex;
            flex-direction: column;
        }
        .table-container {
            flex: 1;
            overflow-x: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        table, th, td {
            border: 1px solid #dddddd;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background: #35424a;
            color: #ffffff;
        }
        button {
            padding: 8px 12px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }
        button:hover {
            background-color: #45a049;
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
    <header>
        <h1>Halaman Konfirmasi Admin</h1>
    </header>
    <div class="container">
        <div class="sidebar">
            <h2>Menu</h2>
            <a href="admin_dashboard.php">Dashboard</a>
            <a href="konfirmasi_user.php">Konfirmasi Warga</a>
            <a href="konfirmasi_admin.php">Konfirmasi Admin</a>
            <a href="laporan.php">Lihat Pengaduan</a>
            <a href="tambah_informasi.php">Tambah Agenda Desa</a>
            <a href="logout.php">keluar</a>
        </div>

        <div class="content">
            <h2>Konfirmasi Registrasi Pengguna</h2>

            <?php if (isset($_SESSION['error'])): ?>
                <p class="error"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></p>
            <?php endif; ?>
            <?php if (isset($_SESSION['message'])): ?>
                <p class="success"><?php echo $_SESSION['message']; unset($_SESSION['message']); ?></p>
            <?php endif; ?>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nama</th>
                            <th>NIK</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Jabatan</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->rowCount() > 0): ?>
                            <?php while ($row = $result->fetch(PDO::FETCH_ASSOC)): ?>
                                <tr>
                                    <td><?php echo $row['id']; ?></td>
                                    <td><?php echo htmlspecialchars($row['nama']); ?></td>
                                    <td><?php echo htmlspecialchars($row['nik']); ?></td>
                                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                                    <td><?php echo htmlspecialchars($row['role']); ?></td>
                                    <td><?php echo htmlspecialchars($row['jabatan']); ?></td>
                                    <td><?php echo htmlspecialchars($row['status']); ?></td>
                                   <td>
    <form action="halaman_konfirmasi_admin.php" method="POST">
        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
        <button type="submit" name="action" value="accept">Aktifkan</button>
        <form action="konfirmasi_reject.php" method="POST">
    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
    <button type="submit" name="action" value="reject">Tolak</button>
</form>
    </form>
</td>

                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8">Tidak ada pengguna yang perlu dikonfirmasi.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
