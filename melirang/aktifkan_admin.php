<?php
session_start();

// Sertakan koneksi database
include 'db.php'; // Pastikan untuk mengganti dengan file koneksi database Anda

// Cek apakah form telah disubmit
if (isset($_POST['tambah_admin'])) {
    // Ambil data dari form
    $nama = $_POST['nama'];
    $nik = $_POST['nik'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash password
    $alamat = $_POST['alamat'];
    $jabatan = $_POST['jabatan'];

    // Proses upload file untuk KTP dan foto profil
    $foto_ktp = $_FILES['foto_ktp']['name'];
    $foto_profil = $_FILES['foto_profil']['name'];
    $target_dir = "uploads/";
    move_uploaded_file($_FILES['foto_ktp']['tmp_name'], $target_dir . basename($foto_ktp));
    move_uploaded_file($_FILES['foto_profil']['tmp_name'], $target_dir . basename($foto_profil));

    // Masukkan data ke dalam tabel admin
    $query = "INSERT INTO admin (nama, nik, email, password, alamat, jabatan, foto_ktp, foto_profil, status) 
              VALUES ('$nama', '$nik', '$email', '$password', '$alamat', '$jabatan', '$foto_ktp', '$foto_profil', 'aktif')";

    // Eksekusi query
    if (mysqli_query($koneksi, $query)) {
        $_SESSION['message'] = "Admin berhasil ditambahkan dan diaktifkan.";
        header("Location: admin_dashboard.php"); // Redirect ke dashboard
        exit();
    } else {
        $_SESSION['message'] = "Terjadi kesalahan: " . mysqli_error($koneksi);
    }
}
?>