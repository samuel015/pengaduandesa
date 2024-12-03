<?php
session_start();
include 'db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = trim($_POST['nama']);
    $nik = trim($_POST['nik']);
    $password = $_POST['password'];
    $email = trim($_POST['email']);
    $role = $_POST['role'];
    $alamat = $_POST['alamat'];
    $jabatan = isset($_POST['jabatan']) ? $_POST['jabatan'] : '';

    // Validasi input
    if (strlen($nik) !== 16) {
        $error = "NIK harus tepat 16 karakter.";
    } elseif (empty($nama)) {
        $error = "Nama tidak boleh kosong.";
    } elseif (strlen($nama) > 100) {
        $error = "Nama terlalu panjang. Maksimal 100 karakter.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Format email tidak valid.";
    }

    // Cek jika email atau NIK sudah terdaftar
    if (empty($error)) {
        $stmt = $pdo->prepare("SELECT * FROM registrasi WHERE nik = :nik OR email = :email");
        $stmt->execute(['nik' => $nik, 'email' => $email]);
        if ($stmt->rowCount() > 0) {
            $error = "NIK atau Email sudah terdaftar.";
        } else {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insert data ke database
            try {
                $stmt = $pdo->prepare("INSERT INTO registrasi (nama, nik, password, role, alamat, email, jabatan) 
                                       VALUES (:nama, :nik, :password, :role, :alamat, :email, :jabatan)");
                $stmt->execute([
                    'nama' => $nama,
                    'nik' => $nik,
                    'password' => $hashed_password,
                    'role' => $role,
                    'alamat' => $alamat,
                    'email' => $email,
                    'jabatan' => $jabatan
                ]);

                // Simpan data ke dalam sesi
                $_SESSION['nik'] = $nik;
                $_SESSION['nama'] = $nama;

                // Redirect ke halaman sesuai dengan role setelah berhasil
                if ($role === 'admin') {
                    header("Location: konfirmasi_admin.php");
                } else {
                    header("Location: konfirmasi_user.php");
                }
                exit();
            } catch (PDOException $e) {
                $error = "Error: " . $e->getMessage();
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
    <title>Form Registrasi</title>
    <style>
        /* Styling CSS untuk halaman */
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
        }
        .container {
            max-width: 400px;
            margin: 50px auto;
            padding: 20px;
            background: #ffffff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            border: 1px solid #ddd;
        }
        .container h2 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }
        label {
            font-weight: bold;
            color: #555;
            display: block;
            margin-bottom: 5px;
        }
        input[type="text"], input[type="password"], input[type="email"], textarea, select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
        }
        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            padding: 12px;
            width: 100%;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        input[type="submit"]: hover {
            background-color: #45a049;
        }
        .error {
            color: red;
            font-size: 14px;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Form Registrasi</h2>
        <?php if ($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="POST" action="">
            <label for="nama">Nama:</label>
            <input type="text" name="nama" id="nama" required>
            <label for="nik">NIK:</label>
            <input type="text" name="nik" id="nik" required>
            <label for="password">Password:</label>
            <input type="password" name="password" id="password" required>
            <label for="email">Email:</label>
            <input type="email" name="email" id="email" required>
            <label for="role">Role:</label>
            <select name="role" id="role" required>
                <option value="user">User </option>
                <option value="admin">Admin</option>
            </select>
            <label for="alamat">Alamat:</label>
            <textarea name="alamat" id="alamat" required></textarea>
            <label for="jabatan">Jabatan (opsional):</label>
            <input type="text" name="jabatan" id="jabatan">
            <input type="submit" value="Daftar">
        </form>
    </div>
</body>
</html>