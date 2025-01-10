<?php
session_start();
include 'db.php'; // Pastikan sudah terhubung ke database

// Pastikan hanya admin yang bisa mengakses halaman ini
if (!isset($_SESSION['nik']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Inisialisasi variabel $total_pengaduan
$total_pengaduan = 0;
$pengaduan = [];

// Ambil jumlah pengaduan
try {
    $stmt = $pdo->query("SELECT COUNT(*) as total_pengaduan FROM pengaduan");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($row) {
        $total_pengaduan = $row['total_pengaduan'];
    } else {
        $total_pengaduan = 0; // Jika tidak ada hasil dari query
    }

    // Ambil data pengaduan untuk laporan
    $stmt = $pdo->query("SELECT id, nik, status, proses FROM pengaduan ORDER BY created_at DESC");
    $pengaduan = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    $total_pengaduan = 0; // Set default jika terjadi error
}

// Fungsi untuk menghitung durasi antara dua waktu
function calculateDuration($start, $end) {
    if ($start && $end) {
        $startDate = new DateTime($start);
        $endDate = new DateTime($end);
        $interval = $startDate->diff($endDate);
        return $interval->format('%h jam %i menit %s detik');
    }
    return 'Belum selesai';
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Pengaduan</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <style>
        /* Style dasar */
        body {
            background-color: #f4f4f4;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            height: 100vh; /* Pastikan body memiliki tinggi penuh */
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
            flex: 1; /* Mengambil sisa ruang yang tersedia */
            display: flex;
            flex-direction: column;
        }
        h1 {
            text-align: left; /* Mengubah teks judul menjadi rata kiri */
            margin-bottom: 20px;
        }
        .error {
            color: red;
            text-align: left; /* Mengubah teks error menjadi rata kiri */
            margin-bottom: 20px;
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
    <script>
        window.jsPDF = window.jspdf.jsPDF;

        function printReport() {
            const doc = new jsPDF();
            doc.text("Laporan Pengaduan", 14, 16);
            doc.autoTable({ 
                head: [['ID', 'NIK', 'Status', 'Proses']], 
                body: <?php echo json_encode(array_map(function($item) {
                    return [$item['id'], $item['nik'], $item['status'], $item['proses']];
                }, $pengaduan)); ?> 
            });
            doc.save('laporan_pengaduan.pdf');
        }
    </script>
</head>
<body>
   
    
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
            <h2>Total Pengaduan: <?php echo $total_pengaduan; ?></h2>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>NIK</th>
                            <th>Status</th>
                            <th>Proses</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pengaduan as $item): ?>
                            <tr>
                                <td><?php echo $item['id']; ?></td>
                                <td><?php echo $item['nik']; ?></td>
                                <td><?php echo $item['status']; ?></td>
                                <td><?php echo $item['proses']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="action-container">
                <button class="btn-print" onclick="printReport()">Cetak Laporan</button>
            </div>
        </div>
    </div>
</body>
</html>