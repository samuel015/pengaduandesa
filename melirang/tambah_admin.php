<?php
session_start();
include 'db.php'; // Pastikan koneksi database sudah benar

// Cek apakah pengguna yang login adalah admin
if (!isset($_SESSION['nik']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$error_message = ''; // Inisialisasi variabel untuk pesan error

if (isset($_POST['submit'])) {
    $nik = $_POST['nik'];
    $nama = $_POST['nama'];
    $password = $_POST['password'];
    $jabatan = $_POST['jabatan'];
    $role = 'petugas'; // Set role menjadi 'petugas'
    $alamat = $_POST['alamat']; // Ambil alamat dari input
    $email = $_POST['email']; // Ambil email dari input

    // Tangkap file gambar
    $profilePic = $_FILES['profile_pic'];
    $ktpPic = $_FILES['foto_ktp']; // Ambil file foto KTP
    $uploadDir = 'uploads/';
    
    // Generate unique file names
    $uniqueProfilePicName = $uploadDir . time() . '_' . basename($profilePic['name']);
    $uniqueKtpPicName = $uploadDir . time() . '_' . basename($ktpPic['name']);
    
    $imageFileTypeProfile = strtolower(pathinfo($uniqueProfilePicName, PATHINFO_EXTENSION));
    $imageFileTypeKTP = strtolower(pathinfo($uniqueKtpPicName, PATHINFO_EXTENSION));

    $validExtensions = ['jpg', 'jpeg', 'png', 'gif'];

    // Validasi input
    if (!preg_match('/^[0-9]{16}$/', $nik)) {
        $error_message = "NIK harus berupa 16 digit angka.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Format email tidak valid.";
    } elseif (!in_array($imageFileTypeProfile, $validExtensions) || !in_array($imageFileTypeKTP, $validExtensions)) {
        $error_message = "Format gambar tidak valid. Harus JPG, JPEG, PNG, atau GIF.";
    } elseif ($profilePic['size'] > 5000000 || $ktpPic['size'] > 5000000) {
        $error_message = "Ukuran gambar terlalu besar. Maksimal 5MB.";
    } else {
        // Cek apakah NIK sudah ada
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM registrasi WHERE nik = :nik");
        $stmt->execute([':nik' => $nik]);
        if ($stmt->fetchColumn() > 0) {
            $error_message = "NIK sudah terdaftar.";
        } else {
            // Pindahkan file dan tambahkan ke database
            if (move_uploaded_file($profilePic['tmp_name'], $uniqueProfilePicName) && move_uploaded_file($ktpPic['tmp_name'], $uniqueKtpPicName)) {
                try {
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                    // Simpan data ke tabel registrasi
                    $stmt = $pdo->prepare("
                        INSERT INTO registrasi (nik, nama, password, jabatan, role, foto, foto_ktp, alamat, email, status) 
                        VALUES (:nik, :nama, :password, :jabatan, :role, :foto, :foto_ktp, :alamat, :email, 'Pending')
                    ");
                    $stmt->execute([
                        ':nik' => $nik, 
                        ':nama' => $nama, 
                        ':password' => $hashed_password, 
                        ':jabatan' => $jabatan, 
                        ':role' => $role, // Menyimpan role sebagai petugas
                        ':foto' => $uniqueProfilePicName,
                        ':foto_ktp' => $uniqueKtpPicName, // Menyimpan foto KTP
                        ':alamat' => $alamat, // Menyimpan alamat
                        ':email' => $email // Menyimpan email
                    ]);

                    // Redirect ke halaman aktifkan_admin.php
                    header("Location: aktifkan_admin.php?message=Petugas berhasil ditambahkan, status Pending.");
                    exit();
                } catch (PDOException $e) {
                    $error_message = "Error: " . $e->getMessage();
                }
            } else {
                $error_message = "Gagal mengunggah gambar.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name ="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Petugas</title>
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            background-color: #f4f4f4;
            font-family: Arial, sans-serif;
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
            justify-content: center; /* Memusatkan konten secara horizontal */
            align-items: center; /* Memusatkan konten secara vertikal */
        }
        .container {
            background: #ffffff;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            padding: 20px;
            width: 100%; /* Memastikan kontainer mengambil lebar penuh */
            max-width: 400px; /* Membatasi lebar maksimum kontainer */
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
        form {
            display: flex;
            flex-direction: column;
            align-items: flex-start; /* Mengatur agar form tidak terpusat */
            width: 100%; /* Memastikan form mengambil lebar penuh */
        }
        label {
            margin: 10px 0 5px;
        }
        input, select {
            padding: 10px;
            width: 100%; /* Memastikan input dan select mengambil lebar penuh */
            max-width: 400px; /* Membatasi lebar maksimum */
            margin-bottom: 15px;
        }
        button {
            padding: 10px 20px;
            background-color: #35424a;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #444;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="sidebar">
        <h3>Menu</h3>
        <a href="admin_dashboard.php"><i class="fas fa-tachometer-alt"></i>Dashboard</a>
        <a href="konfirmasi_user.php"><i class="fas fa-user-check"></i>Konfirmasi Warga</a>
        <a href="tambah_admin.php"><i class="fas fa-user-plus"></i>Kelola Admin</a>
        <a href="laporan.php"><i class="fas fa-file-alt"></i>Laporan Pengaduan</a>
        <a href="tambah_informasi.php"><i class="fas fa-calendar-plus"></i>Tambah Agenda Desa</a>
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i>Keluar</a>
    </div>
    </div>
    <div class="content">
        <div class="container">
            <h1>Tambah Petugas</h1>

            <?php if (isset($error_message)): ?>
                <div class="error"><?= $error_message; ?></div>
            <?php endif; ?>

            <form action="tambah_admin.php" method="POST" enctype="multipart/form-data">
                <label for="nik">NIK:</label>
                <input type="text" name="nik" placeholder="Masukkan NIK (16 digit)" required>

                <label for="nama">Nama:</label> 
                <input type="text" name="nama" placeholder="Masukkan Nama Lengkap" required>

                <label for="password">Password:</label>
                <input type="password" name="password" placeholder="Masukkan Password" required>

                <label for="jabatan">Jabatan:</label>
                <input type="text" name="jabatan" placeholder="Masukkan Jabatan" required>

                <label for=" role">Role:</label>
                <select name="role" required>
                    <option value="petugas" selected>Petugas</option>
                </select>

                <label for="alamat">Alamat:</label>
                <input type="text" name="alamat" placeholder="Masukkan Alamat" required>

                <label for="email">Email:</label>
                <input type="email" name="email" placeholder="Masukkan Email" required>

                <label for="profile_pic">Foto Profil:</label>
                <input type="file" name="profile_pic" accept="image/*" required>

                <label for="foto_ktp">Foto KTP:</label>
                <input type="file" name="foto_ktp" accept="image/*" required>

                <button type="submit" name="submit">Tambah Petugas</button>
            </form>
        </div>
    </div>
</body>
</html>