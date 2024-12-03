<?php
session_start();
include 'db.php';

// Pastikan pengguna telah login
if (!isset($_SESSION['nik'])) {
    header("Location: login.php");
    exit();
}

// Ambil data pengguna dari sesi
$nik = $_SESSION['nik'];

// Ambil nama pengguna dan status konfirmasi dari database berdasarkan NIK
$stmt = $pdo->prepare("SELECT nama, is_confirmed FROM registrasi WHERE nik = :nik");
$stmt->execute(['nik' => $nik]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
$nama = $user ? $user['nama'] : '';
$isConfirmed = $user ? $user['is_confirmed'] : false;

// Pesan untuk pengaduan yang berhasil
$message = "";
if (isset($_GET['message']) && $_GET['message'] === 'success') {
    $message = "Pengaduan berhasil dikirim dan akan segera diproses oleh pihak admin.";
}

// Proses pengaduan
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $isConfirmed) {
    $jenis_aduan = $_POST['jenis_aduan'];
    $isi_pengaduan = $_POST['isi_pengaduan'];
    $status = 'Pending';

    try {
        $stmt = $pdo->prepare("INSERT INTO pengaduan (nik, nama, jenis_aduan, isi_pengaduan, status, created_at) 
                               VALUES (:nik, :nama, :jenis_aduan, :isi_pengaduan, :status, NOW())");
        $stmt->execute([
            'nik' => $nik,
            'nama' => $nama,
            'jenis_aduan' => $jenis_aduan,
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
            padding: 10px 15px;
            border-radius: 5px;
            margin-bottom: 10px;
            transition: background-color 0.3s ease;
        }
        .sidebar a:hover {
            background-color: #34495e;
        }
        .content {
            flex: 1;
            padding: 20px;
        }
        h1 {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 20px;
        }
        form {
            background: #ffffff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin: 0 auto;
        }
        textarea, select, input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 14px;
            margin-bottom: 20px;
            outline: none;
            transition: border-color 0.3s ease;
        }
        textarea:focus, select:focus {
            border-color: #3498db;
        }
        button {
            background-color: #3498db;
            color: white;
            padding:  10px 20px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        button:hover {
            background-color: #2980b9;
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
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>Menu</h2>
        <a href="pengaduan.php">Dashboard</a>
        <a href="pengaduan.php">Kirim Pengaduan</a>
        <a href="logout.php">Logout</a>
    </div>
    <div class="content">
        <h1>Kirim Pengaduan</h1>

        <?php if ($message): ?>
            <div class="message"><?= $message; ?></div>
        <?php endif; ?>

        <?php if (isset($error_message)): ?>
            <div style="color: red;"><?= $error_message; ?></div>
        <?php endif; ?>

        <form action="pengaduan.php" method="POST">
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
            <textarea name="isi_pengaduan" placeholder="Tuliskan pengaduan Anda di sini..." required></textarea>
            <button type="submit" <?= !$isConfirmed ? 'disabled' : ''; ?>>Kirim Pengaduan</button>
        </form>
    </div>
</body>
</html>