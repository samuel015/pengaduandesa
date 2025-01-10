<?php
session_start();
include 'db.php'; // File koneksi database

// Pastikan pengguna telah login
if (!isset($_SESSION['nik'])) {
    header("Location: login.php");
    exit();
}

// Ambil data pengguna dari sesi
$nik = $_SESSION['nik'];

// Ambil nama pengguna dari database berdasarkan NIK
$stmt = $pdo->prepare("SELECT nama FROM registrasi WHERE nik = :nik"); // Pastikan tabel yang benar
$stmt->execute(['nik' => $nik]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
$nama = $user ? $user['nama'] : '';

// Proses pengiriman pengaduan
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $jenis_aduan = $_POST['jenis_aduan'];
    $isi_pengaduan = $_POST['isi_pengaduan'];
    $status = 'Pending';
    $evidence = null;

    // Validasi input
    if (empty($nik) || empty($nama) || empty($jenis_aduan) || empty($isi_pengaduan)) {
        $error_message = "Semua field harus diisi.";
    } else {
        // Proses upload file jika ada
        if (isset($_FILES['evidence']) && $_FILES['evidence']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['evidence']['tmp_name'];
            $fileName = $_FILES['evidence']['name'];
            $fileNameCmps = explode(".", $fileName);
            $fileExtension = strtolower(end($fileNameCmps));

            // Validasi dan simpan file
            $allowedfileExtensions = array('jpg', 'gif', 'png', 'pdf', 'doc', 'docx');
            if (in_array($fileExtension, $allowedfileExtensions)) {
                $uploadFileDir = './uploaded_files/';
                if (!is_dir($uploadFileDir)) {
                    mkdir($uploadFileDir, 0755, true); // Buat direktori jika tidak ada
                }
                $dest_path = $uploadFileDir . $fileName;

                if (move_uploaded_file($fileTmpPath, $dest_path)) {
                    $evidence = $dest_path;
                }
            }
        }

        try {
            $stmt = $pdo->prepare("INSERT INTO pengaduan (nik, nama, jenis_aduan, isi_pengaduan, status, evidence, created_at) 
                                   VALUES (:nik, :nama, :jenis_aduan, :isi_pengaduan, :status, :evidence, NOW())");
            $stmt->execute([
                'nik' => $nik,
                'nama' => $nama,
                'jenis_aduan' => $jenis_aduan,
                'isi_pengaduan' => $isi_pengaduan,
                'status' => $status,
                'evidence' => $evidence
            ]);

            header("Location: kirim_pengaduan.php?message=success");
            exit();
        } catch (PDOException $e) {
            $error_message = "Error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kirim Pengaduan</title>
    <style>
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
            display: flex;
        }
        .sidebar {
            width: 250px;
            background-color: #2c3e50;
            color: #ecf0f1;
            height: 100vh;
            padding: 20px;
        }
        .sidebar h2 {
            color: #ecdbff;
            text-align: center;
            margin-bottom: 20px;
        }
        .sidebar a {
            color: #ecf0f1;
            text-decoration: none;
            display: block;
            margin: 10px 0;
            padding: 10px;
            border-radius: 5px;
            transition: background 0.3s;
        }
        .sidebar a:hover {
            background-color: #34495e;
        }
        .content {
            flex: 1;
            padding: 20px;
            display: flex;
            flex-direction: column;
        }
        .form-container {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }
        .form-group {
            margin-bottom: 15px;
            flex: 1;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
        }
        .form-group input, .form-group textarea, .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .form-group button {
            background-color: #3498db;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
        }
        .form-group button:hover {
            background-color: #2980b9;
        }
        .add-button {
            margin-left: 20px;
            background-color: #2ecc71;
        }
        .add-button:hover {
            background-color: #27ae60;
        }
        .message {
            margin: 20px 0;
            color: green;
        }
        .error {
            color: red;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>Menu</h2>
        <a href="kirim_pengaduan.php">Kirim Pengaduan</a>
        <a href="riwayat_pengaduan.php">Riwayat Pengaduan</a>
        <a href="logout.php">Logout</a>
    </div>
    <div class="content">
        <h1>Kirim Pengaduan</h1>
        <div class="form-container">
            <div class="form-group">
                <form action="kirim_pengaduan.php" method="POST" enctype="multipart/form-data">
                    <label for="nik">NIK:</label>
                    <input type="text" name="nik" value="<?= htmlspecialchars($nik); ?>" readonly required>

                    <label for="nama">Nama:</label>
                    <input type="text" name="nama" value="<?= htmlspecialchars($nama); ?>" readonly required>

                    <label for="jenis_aduan">Jenis Aduan:</label>
                    <select name="jenis_aduan" required>
                        <option value="Infrastruktur">Infrastruktur</option>
                        <option value="Kebersihan">Kebersihan</option>
                        <option value="Keamanan">Keamanan</option>
                    </select>

                    <label for="isi_pengaduan">Isi Pengaduan:</label>
                    <textarea name="isi_pengaduan" placeholder="Tuliskan pengaduan Anda di sini..." required></textarea>

                    <label for="evidence">Lampiran (bukti pengaduan):</label>
                    <input type="file" name="evidence" accept=".jpg,.gif,.png,.pdf,.doc,.docx">

                    <div class="form-group">
                        <button type="submit">Kirim Pengaduan</button>
                    </div>
                </form>
            </div>
            <a href="tambah_pengaduan.php">
                <button class="add-button">Tambah Pengaduan</button>
            </a>
        </div>
        <?php if (!empty($message)): ?>
            <div class="message"><?php echo $message; ?></div>
        <?php endif; ?>
        <?php if (!empty($error_message)): ?>
            <div class="error"><?php echo $error_message; ?></div>
        <?php endif; ?>
    </div>
</body>
</html>