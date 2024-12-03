<?php
session_start();
if (!isset($_SESSION['nik']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

include 'db.php';

// Ambil pengaduan yang tidak berstatus ditolak
$stmt = $pdo->prepare("SELECT * FROM pengaduan WHERE status != 'ditolak' ORDER BY created_at DESC");
$stmt->execute();
$pengaduans = $stmt->fetchAll();

// Proses perubahan status pengaduan
if (isset($_GET['id']) && isset($_GET['status'])) {
    $id = $_GET['id'];
    $status = $_GET['status'];

    // Update status pengaduan
    $stmt = $pdo->prepare("UPDATE pengaduan SET status = :status WHERE id = :id");
    $stmt->execute(['status' => $status, 'id' => $id]);

    // Jika status ditolak, kirim email pemberitahuan ke pengguna
    if ($status === 'ditolak') {
        $stmt = $pdo->prepare("SELECT p.nik, u.email FROM pengaduan p JOIN users u ON p.nik = u.nik WHERE p.id = :id");
        $stmt->execute(['id' => $id]);
        $pengaduan = $stmt->fetch();

        // Kirim email pemberitahuan
        $to = $pengaduan['email'];
        $subject = "Pengaduan Anda Ditolak";
        $message = "Pengaduan Anda dengan NIK {$pengaduan['nik']} telah ditolak oleh admin.";
        $headers = "From: no-reply@domain.com";

        mail($to, $subject, $message, $headers);
    }

    // Redirect setelah update
    header("Location: pengaduan.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Admin - Pengaduan</title>
    <style>
        /* CSS styling untuk halaman */
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }

        h1 {
            color: #4CAF50;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 8px;
            text-align: left;
        }

        a {
            text-decoration: none;
            color: #4CAF50;
        }

        .actions a {
            margin-right: 10px;
        }
    </style>
</head>
<body>
    <h1>Halaman Pengaduan</h1>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Isi Pengaduan</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($pengaduans as $index => $pengaduan): ?>
                <tr>
                    <td><?= $index + 1 ?></td>
                    <td><?= htmlspecialchars($pengaduan['isi_pengaduan']) ?></td>
                    <td><?= ucfirst($pengaduan['status']) ?></td>
                    <td class="actions">
                        <a href="?id=<?= $pengaduan['id'] ?>&status=ditolak">Tolak</a>
                        <a href="?id=<?= $pengaduan['id'] ?>&status=diterima">Terima</a>
                        <a href="?id=<?= $pengaduan['id'] ?>&status=diproses">Proses</a>
                        <a href="?id=<?= $pengaduan['id'] ?>&status=diselesaikan">Selesaikan</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
