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
    $role = $_POST['role'];
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
                        ':role' => $role,
                        ':foto' => $uniqueProfilePicName,
                        ':foto_ktp' => $uniqueKtpPicName, // Menyimpan foto KTP
                        ':alamat' => $alamat, // Menyimpan alamat
                        ':email' => $email // Meny impan email
                    ]);

                    // Redirect ke halaman aktifkan_admin.php
                    header("Location: aktifkan_admin.php?message=Admin berhasil ditambahkan, status Pending.");
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Admin</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            height: 100vh; /* Pastikan body memiliki tinggi penuh */
        }
        .sidebar {
            width: 20%;
            background: #35424a;
            color: #ffffff;
            padding: 20px;
            height: 100%; /* Pastikan sidebar memiliki tinggi penuh */
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
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
        .container {
            width: 80%;
            padding: 20px;
            display: flex;
            flex-direction: column; /* Mengatur agar konten ditampilkan secara vertikal */
            align-items: flex-start; /* Mengatur agar form tidak terpusat */
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
        <h3>Menu</h3>
        <a href="admin_dashboard.php">Dashboard</a>
        <a href="konfirmasi_user.php">Konfirmasi Warga</a>
        <a href="tambah_admin.php">Kelola Admin</a>
        <a href="laporan.php">Lihat Pengaduan</a>
        <a href="tambah_informasi.php">Tambah Agenda Desa</a>
        <a href="daftar_petugas.php">Daftar Petugas</a> <!-- Tautan baru untuk daftar petugas -->
        <a href="logout.php">Keluar</a>
    </div>
    <div class="container">
        <h1>Tambah Admin</h1>

        <?php if (isset($error_message)): ?>
            <div class="error"><?= $error_message; ?></div>
        <?php endif; ?>

        <form action="tambah_admin.php" method="POST" enctype="multipart/form-data">
            <label for="nik">NIK:</label>
            <input type="text" name="nik" placeholder="Masukkan NIK (16 digit)" required>

            <label for="nama">Nama:</label> 
            <input type="text" name="nama" placeholder="Masukkan Nama Lengkap" required>

            <label for="password">Password:</label>
            <input type="password" name="password" placeholder="Masukkan Password" required >

            <label for="jabatan">Jabatan:</label>
            <input type="text" name="jabatan" placeholder="Masukkan Jabatan" required>

            <label for="role">Role:</label>
            <select name="role" required>
                <option value="admin">Admin</option>
            </select>

            <label for="alamat">Alamat:</label>
            <input type="text" name="alamat" placeholder="Masukkan Alamat" required>

            <label for="email">Email:</label>
            <input type="email" name="email" placeholder="Masukkan Email" required>

            <label for="profile_pic">Foto Profil:</label>
            <input type="file" name="profile_pic" accept="image/*" required>

            <label for="foto_ktp">Foto KTP:</label>
            <input type="file" name="foto_ktp" accept="image/*" required>

            <button type="submit" name="submit">Tambah Admin</button>
        </form>
    </div>
</body>
</html>