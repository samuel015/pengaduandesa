<?php
session_start();
include 'db.php'; // Pastikan koneksi database sudah benar

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Mengambil data dari form
    $judul = $_POST['judul'];
    $tanggal = $_POST['tanggal'];
    $waktu = $_POST['waktu'];
    $tempat = $_POST['tempat'];
    $deskripsi = $_POST['deskripsi'];

    // Siapkan statement untuk menghindari SQL Injection
    $stmt = $conn->prepare("INSERT INTO agenda (judul, tanggal, waktu, tempat, deskripsi) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $judul, $tanggal, $waktu, $tempat, $deskripsi);

    // Eksekusi query
    if ($stmt->execute()) {
        // Jika berhasil, redirect ke halaman agenda
        header("Location: agenda.php");
        exit();
    } else {
        // Jika gagal, tampilkan pesan error
        echo "Error: " . $stmt->error;
    }

    // Tutup statement
    $stmt->close();
}

// Tutup koneksi
$conn->close();
?>