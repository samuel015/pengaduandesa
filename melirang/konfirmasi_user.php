<?php
require_once 'db.php'; // Pastikan koneksi database ($pdo) sudah terdefinisi

// Ambil daftar pengguna dengan status 'Menunggu'
$stmt = $pdo->query("SELECT * FROM registrasi WHERE status = 'Menunggu' ORDER BY created_at DESC");
$users = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'];
    $action = $_POST['action']; // 'confirm' atau 'reject'

    if ($action === 'confirm') {
        // Update status menjadi 'Aktif' setelah konfirmasi
        $stmt = $pdo->prepare("UPDATE registrasi SET status = 'Aktif' WHERE id = ?");
        $stmt->execute([$user_id]);
        header("Location: konfirmasi_user.php?message=User berhasil diaktifkan");
        exit();
    } elseif ($action === 'reject') {
        // Hapus pengguna yang ditolak
        $stmt = $pdo->prepare("DELETE FROM registrasi WHERE id = ?");
        $stmt->execute([$user_id]);
        header("Location: konfirmasi_user.php?message=User berhasil dihapus");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konfirmasi User</title>
    <style>
        /* Style dasar */
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
        .btn-print {
            background-color: #35424a;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-bottom: 20px;
        }
        .btn-print:hover {
            background-color: #444;
        }
        .action-container {
            display: flex;
            justify-content: flex-end;
            margin-top: 20px;
        }
    </style>
</head>
<body>

    <header>
        <h1>Konfirmasi User</h1>
    </header>

    <div class="container">
        <div class="sidebar">
            <h2>Menu</h2>        <a href="admin_dashboard.php">Dashboard</a>
            <a href="konfirmasi_user.php">Konfirmasi Warga</a>
            <a href="konfirmasi_admin.php">Konfirmasi Admin</a>
            <a href="laporan.php">Lihat Pengaduan</a>
            <a href="tambah_informasi.php">Tambah Agenda Desa</a>
            <a href="logout.php">keluar</a>
        </div>

        <div class="content">
            <?php if (isset($_GET['message'])): ?>
                <div class="message"><?= htmlspecialchars($_GET['message']) ?></div>
            <?php endif; ?>

            <h2>Daftar User Menunggu Konfirmasi</h2>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Nama</th>
                    <th>NIK</th>
                    <th>Email</th>
                    <th>Alamat</th>
                    <th>Foto KTP</th>
                    <th>Foto Profil</th>
                    <th>Aksi</th>
                </tr>
                <?php foreach ($users as $data): ?>
                    <tr>
                        <td><?= $data['id']; ?></td>
                        <td><?= htmlspecialchars($data['nama']); ?></td>
                        <td><?= htmlspecialchars($data['nik']); ?></td>
                        <td><?= htmlspecialchars($data['email']); ?></td>
                        <td><?= htmlspecialchars($data['alamat']); ?></td>
                        <td>
                            <img src="uploads/ktp/<?= htmlspecialchars($data['foto_ktp']); ?>" 
                                 alt="Foto KTP" style="width:50px;height:50px;">
                        </td>
                        <td>
                            <img src="uploads/profil/<?= htmlspecialchars($data['foto_profil']); ?>" 
                                 alt="Foto Profil" style="width:50px;height:50px;">
                        </td>
                        <td>
                            <form method="POST" action="konfirmasi_user.php">
                                <input type="hidden" name="user_id" value="<?= $data['id']; ?>">
                                <button type="submit" name="action" value="confirm">Aktifkan</button>
                                <button type="submit" name="action" value="reject">Tolak</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>

</body>
</html>
