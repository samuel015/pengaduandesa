<?php
session_start();
include 'db.php'; // Pastikan sudah terhubung ke database

// Pastikan hanya admin yang dapat mengakses halaman ini
if (!isset($_SESSION['nik']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Menampilkan pesan sukses jika admin baru berhasil ditambahkan
$message = '';
$error_message = '';
if (isset($_GET['message'])) {
    if ($_GET['message'] == 'user_deleted') {
        $message = 'Pengguna telah berhasil dihapus.';
    } elseif ($_GET['message'] == 'admin_added') {
        $message = 'Admin baru telah berhasil ditambahkan.';
    }
}

// Mengambil data admin yang terdaftar
$stmt = $pdo->prepare("SELECT * FROM users WHERE role = 'admin'");
$stmt->execute();
$admins = $stmt->fetchAll();

// Ambil data pengguna untuk sidebar
try {
    $stmt = $pdo->prepare("
        SELECT r.nama, 
               COALESCE(r.foto, 'default.jpg') AS foto
        FROM registrasi r
        WHERE r.nik = :nik
    ");
    $stmt->execute([':nik' => $_SESSION['nik']]);
    $user_data = $stmt->fetch(PDO::FETCH_ASSOC);

    // Ambil data pengaduan dari database
    $nik_filter = isset($_POST['nik']) ? trim($_POST['nik']) : '';
    if ($nik_filter) {
        $stmt = $pdo->prepare("SELECT * FROM pengaduan WHERE nik = :nik ORDER BY created_at DESC");
        $stmt->execute([':nik' => $nik_filter]);
    } else {
        $stmt = $pdo->query("SELECT * FROM pengaduan ORDER BY created_at DESC");
    }
    $pengaduan = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error_message = "Error: " . htmlspecialchars($e->getMessage());
}

// Cek jika status diubah
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['status'])) {
    $id = $_POST['id'];
    $status = $_POST['status'];
    $reason = isset($_POST['reason']) ? trim($_POST['reason']) : null;

    // Validasi status (hanya 'Proses', 'Tolak', atau 'Selesai' yang diizinkan)
    $valid_status = ['Proses', 'Tolak', 'Selesai'];
    if (!in_array($status, $valid_status)) {
        $error_message = "Status yang dipilih tidak valid.";
    } elseif ($status === 'Tolak' && empty($reason)) {
        $error_message = "Alasan penolakan harus diisi.";
    } else {
        try {
            // Update status pengaduan
            $stmt = $pdo->prepare("UPDATE pengaduan SET status = :status, reason = :reason, proses_at = :proses_at WHERE id = :id");
            $stmt->execute([ 
                ':status' => $status,
                ':reason' => $status === 'Tolak' ? $reason : null,
                ':proses_at' => $status === 'Proses' ? date('Y-m-d H:i:s') : null,
                ':id' => $id
            ]);

            // Kirim email jika status adalah 'Tolak'
            if ($status === 'Tolak') {
                // Ambil email pengguna berdasarkan NIK
                $stmt = $pdo->prepare("
                    SELECT u.email 
                    FROM users u 
                    INNER JOIN pengaduan p ON u.nik = p.nik 
                    WHERE p.id = :id
                ");
                $stmt->execute([':id' => $id]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($user && isset($user['email'])) {
                    $to = $user['email'];
                    $subject = "Pengaduan Anda Ditolak";
                    $message = "Pengaduan Anda dengan ID #$id telah ditolak.\n\nAlasan: $reason";
                    $headers = "From: admin@melirang.com";

                    // Kirim email
                    if (!mail($to, $subject, $message, $headers)) {
                        $error_message = "Gagal mengirim email.";
                    }
                }
            }

            header("Location: admin_dashboard.php?message=success");
            exit();
        } catch (PDOException $e) {
            $error_message = "Error: " . htmlspecialchars($e->getMessage ());
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Pengaduan</title>
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
        .profile {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }
        .profile img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            margin-right: 10px;
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
        .error {
            color: red;
        }
        .success {
            color: green;
        }
        .search-container {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 20px;
        }
        .search-container input[type="text"] {
            padding: 10px;
            margin-right: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .search-container button {
            padding: 10px;
            background: #35424a;
            color: #ffffff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .search-container button:hover {
            background: #444;
        }
    </style>
</head>
<body>
    <header>
        <h1>Dashboard Admin</h1>
    </header>
    <div class="container">
        <div class="sidebar">
            <h2>Menu</h2>
            <a href="admin_dashboard.php">Dashboard</a>
            <a href="konfirmasi_user.php">Konfirmasi Warga</a>
            <a href="konfirmasi_admin.php">Konfirmasi Admin</a>
            <a href="laporan.php">Lihat Pengaduan</a>
            <a href="tambah_informasi.php">Tambah Agenda Desa</a>
            <a href="logout.php">Keluar</a>
        </div>
        <div class="content">
            <div class="profile">
                <img src="uploads/<?php echo htmlspecialchars($user_data['foto']); ?>" alt="Foto Profil">
                <h2><?php echo htmlspecialchars($user_data['nama']); ?></h2>
            </div>
            <?php if (!empty($error_message)): ?>
                <div class="error"><?php echo $error_message; ?></div>
            <?php endif; ?>
            <?php if (!empty($message)): ?>
                <div class="success"><?php echo $message; ?></div>
            <?php endif; ?>
            <h2>Data Pengaduan</h2>
            <div class="search-container">
                <form action="admin_dashboard.php" method="post">
                    <input type="text" name="nik" placeholder="Masukkan NIK" value="<?php echo isset($nik_filter) ? $nik_filter : ''; ?>">
                    <button type="submit">Cari</button>
                </form>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>NIK</th>
                        <th>Jenis Aduan</th>
                        <th>Status</th>
                        <th>Foto</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pengaduan as $p): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($p['id']); ?></td>
                            <td><?php echo htmlspecialchars($p['nik']); ?></td>
                            <td><?php echo htmlspecialchars($p['jenis_aduan']); ?></td>
                            <td><?php echo htmlspecialchars($p['status']); ?></td>
                            <td><img src="uploads/<?php echo htmlspecialchars($p['foto']); ?>" width="100"></td>
                            <td>
                                <form action="admin_dashboard.php" method="post">
                                    <select name="status" required>
                                        <option value="Proses" <?php echo $p['status'] === 'Proses' ? 'selected' : ''; ?>>Proses</option>
                                        <option value="Selesai" <?php echo $p['status'] === 'Selesai' ? 'selected' : ''; ?>>Selesai</option>
                                        <option value="Tolak" <?php echo $p['status'] === 'Tolak' ? 'selected' : ''; ?>>Tolak</option>
                                    </select>
                                    <input type="hidden" name="id" value="<?php echo $p['id']; ?>">
                                    <input type="text" name="reason" placeholder="Alasan (jika ditolak)">
                                    <button type="submit">Ubah Status</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>