<?php
session_start();
include 'db.php'; // Koneksi ke database

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id'])) {
    $id = $_POST['id'];
    $status = $_POST['status'];

    try {
        $stmt = $pdo->prepare("UPDATE registrasi SET status = :status WHERE id = :id");
        $stmt->execute(['status' => $status, 'id' => $id]);
        header("Location: konfirmasi_registrasi.php"); // Redirect ke halaman konfirmasi setelah update
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    echo "Data tidak valid.";
}
?>
