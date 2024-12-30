<?php
session_start();
include 'db.php'; // Koneksi database

if (!isset($_SESSION['nik']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    try {
        $stmt = $pdo->prepare("SELECT proses FROM pengaduan WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $pengaduan = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($pengaduan) {
            if ($pengaduan['proses'] === 'Diterima') {
                $updateStmt = $pdo->prepare("UPDATE pengaduan SET proses = 'Selesai' WHERE id = :id");
                $updateStmt->bindParam(':id', $id, PDO::PARAM_INT);
                $updateStmt->execute();
                
                header("Location: tanggapan_pengaduan.php?message=Pengaduan berhasil diperbarui menjadi 'Selesai'");
                exit();
            } else {
                header("Location: tanggapan_pengaduan.php?error=Pengaduan tidak dapat diproses karena statusnya bukan 'Diterima'");
                exit();
            }
        } else {
            header("Location: tanggapan_pengaduan.php?error=ID pengaduan tidak valid");
            exit();
        }
    } catch (PDOException $e) {
        die("Kesalahan: " . htmlspecialchars($e->getMessage()));
    }
} else {
    header("Location: tanggapan_pengaduan.php?error=ID pengaduan tidak valid");
    exit();
}
?>