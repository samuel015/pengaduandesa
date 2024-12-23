<?php
session_start();
include 'db.php'; // Sambungkan ke database menggunakan PDO

// Periksa apakah formulir sudah dikirim
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nik = $_POST['nik'];
    $isi_pengaduan = $_POST['isi_pengaduan'];
    $jenis_aduan = $_POST['jenis_aduan']; // Tangkap jenis aduan

    // Inisialisasi variabel untuk evidence
    $evidence = null;

    if (isset($_FILES['evidence']) && $_FILES['evidence']['error'] === UPLOAD_ERR_OK) {
        $evidence = $_FILES['evidence'];

        // Simpan evidence ke direktori
        $target_dir = "C:/xampp/htdocs/melirang/uploads/pengaduan/"; // Ubah direktori target

        // Buat direktori target jika belum ada
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $target_file = $target_dir . basename($evidence["name"]);
        move_uploaded_file($evidence["tmp_name"], $target_file);
    }

    try {
        // Masukkan data ke database
        $stmt = $pdo->prepare("INSERT INTO pengaduan (nik, isi_pengaduan, jenis_aduan, evidence, status, proses) VALUES (:nik, :isi_pengaduan, :jenis_aduan, :evidence, 'Baru', 'Pending')");
        $stmt->execute([
            ':nik' => $nik,
            ':isi_pengaduan' => $isi_pengaduan,
            ':jenis_aduan' => $jenis_aduan,
            ':evidence' => $evidence ? basename($evidence["name"]) : null // Simpan nama file evidence jika ada
        ]);

        // Redirect ke halaman data_pengaduan.php dengan pesan sukses
        header("Location: data_pengaduan.php?message=success");
        exit;
    } catch (PDOException $e) {
        die("Gagal mengirim pengaduan: " . $e->getMessage());
    }
} else {
    header("Location: data_pengaduan.php");
    exit;
}
?>