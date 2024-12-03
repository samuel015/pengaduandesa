<?php
session_start();
include 'db.php'; // Pastikan file ini menghubungkan ke database

if (!isset($_SESSION['nik'])) {
    header("Location: login.php");
    exit();
}

// Ambil NIK dari session
$nik = $_SESSION['nik'];

// Periksa apakah NIK valid di tabel registrasi
$stmt = $pdo->prepare("SELECT COUNT(*) FROM registrasi WHERE nik = ?");
$stmt->execute([$nik]);
if ($stmt->fetchColumn() == 0) {
    die("Error: NIK tidak valid. Silakan hubungi admin.");
}

// Ambil status pengguna dan foto dari tabel registrasi
$stmt = $pdo->prepare("SELECT status, foto FROM registrasi WHERE nik = ?");
$stmt->execute([$nik]);
$user = $stmt->fetch();

// Ambil notifikasi terbaru
$stmt = $pdo->prepare("SELECT message FROM notifikasi ORDER BY created_at DESC LIMIT 5");
$stmt->execute();
$notifikasi = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Ambil statistik pengaduan
$stmt = $pdo->prepare("SELECT COUNT(*) as total FROM pengaduan");
$stmt->execute();
$total_pengaduan = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) as pending FROM pengaduan WHERE status = 'Pending'");
$stmt->execute();
$jumlah_pending = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) as selesai FROM pengaduan WHERE status = 'Selesai'");
$stmt->execute();
$jumlah_selesai = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) as ditolak FROM pengaduan WHERE status = 'Ditolak'");
$stmt->execute();
$jumlah_ditolak = $stmt->fetchColumn();

$message = "";
$error_message = "";

if (isset($_GET['message']) && $_GET['message'] === 'success') {
    $message = "Pengaduan berhasil dikirim dan akan segera diproses oleh pihak admin.";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $isi_pengaduan = $_POST['isi_pengaduan'];
    $status = 'Pending';

    try {
        // Simpan data pengaduan ke dalam tabel
        $stmt = $pdo->prepare("INSERT INTO pengaduan (nik, isi_pengaduan, status, created_at) 
                               VALUES (:nik, :isi_pengaduan, :status, NOW())");
        $stmt->execute([
            'nik' => $nik,
            'isi_pengaduan' => $isi_pengaduan,
            'status' => $status
        ]);

        header("Location: pengaduan.php?message=success");
        exit();
    } catch (PDOException $e) {
        $error_message = "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* CSS yang sudah ada */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f7f9fc;
            color: #333;
            line-height: 1.6;
            display: flex; /* Menggunakan flexbox untuk layout */
        }

        .sidebar {
            width: 250px; /* Lebar sidebar */
            background-color: #2c3e50;
            color: white;
            padding: 20px;
            height: 100vh; /* Tinggi sidebar */
        }

        .sidebar h2 {
            font-size: 20px;
            margin-bottom: 20px;
        }

        .sidebar a {
            color: white;
            text-decoration: none;
            display: block;
            padding: 10px;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .sidebar a:hover {
            background-color: #3498db; /* Warna saat hover */
        }

        .content {
            flex: 1; /* Mengisi sisa ruang */
            padding: 20px;
        }

        h1 {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 20 px;
        }

        .message {
            padding: 15px; 
            margin: 20px auto; 
            color: green; 
            background-color: #eaf7e4; 
            border: 1px solid #d4edda; 
            border-radius: 5px; 
            max-width: 600px; 
            text-align: center; 
            font-size: 16px; 
        }

        div[style="color: red;"] {
            text-align: center;
            color: red;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .profile {
            margin-left: 20px; /* Jarak dari judul */
        }

        .profile img {
            width: 100px; /* Lebar gambar */
            height: 100px; /* Tinggi gambar */
            border-radius: 50%; /* Membuat gambar menjadi lingkaran */
            border: 2px solid #3498db; /* Menambahkan border */
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>Menu</h2>
        <a href="pengaduan.php?message=success">Dashboard</a>
        <a href="halaman_pengaduan.php">Kirim Pengaduan</a>
        <a href="logout.php">Logout</a>
    </div>

    <div class="content">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <h1>Dashboard</h1>
            <div class="profile">
                <?php if ($user && $user['foto']): ?>
                    <img src="uploads/<?= htmlspecialchars($user['foto']); ?>" alt="Foto Profil">
                <?php else: ?>
                    <p>Tidak ada foto profil.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Menampilkan pesan sukses jika ada -->
        <?php if ($message): ?>
            <div class="message"><?= htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <!-- Menampilkan pesan error jika ada -->
        <?php if ($error_message): ?>
            <div style="color: red;"><?= htmlspecialchars($error_message); ?></div>
        <?php endif; ?>

        <!-- Cek status pengguna -->
        <?php if ($user && $user['status'] === 'Aktif'): ?>
            <!-- Bagian ini dihapus sesuai permintaan -->
        <?php else: ?>
            <div style="color: red; text-align: center;">
                Anda belum dikonfirmasi oleh admin. Silakan tunggu konfirmasi untuk mengirim pengaduan.
            </div>
        <?php endif; ?>

        <div class="summary">
            <canvas id="pengaduanChart"></canvas>
        </div>
        <h2>Notifikasi Terbaru</h2>
        <ul>
            <?php if (!empty($notifikasi)): ?>
                <?php foreach ($notifikasi as $notif): ?>
                    <li><?= htmlspecialchars($notif['message']); ?></li>
                <?php endforeach; ?>
            <?php else: ?>
                <li>Tidak ada notifikasi terbaru.</li>
            <?php endif; ?>
        </ul>
    </div>

    <script>
        const ctx = document.getElementById('pengaduanChart').getContext('2d');
        const pengaduanChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Pending', 'Selesai', 'Ditolak'],
                datasets: [{
                    label: 'Jumlah Pengaduan',
                    data: [<?= $jumlah_pending; ?>, <?= $jumlah_selesai; ?>, <?= $jumlah_ditolak; ?>],
                    backgroundColor: ['#3498db', '#2ecc71', '#e74c3c'],
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>
</html>