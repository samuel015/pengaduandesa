<?php
session_start();
include 'db.php'; // Pastikan koneksi database ($pdo) sudah terdefinisi

if (isset($_POST['user_id'])) {
    $user_id = $_POST['user_id'];

    // Update status pengguna menjadi 'aktif'
    $query = "UPDATE registrasi SET status = 'aktif' WHERE id = :user_id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();

    // Redirect setelah berhasil
    $_SESSION['message'] = "User berhasil diaktifkan.";
    header("Location: konfirmasi_user.php");
    exit();
}
?>
