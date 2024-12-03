<?php
session_start();
include 'db.php'; // Pastikan file ini terkoneksi dengan benar
$error = '';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nik = trim($_POST['nik']); // Ganti username dengan nik
    $password = $_POST['password'];

    if (empty($nik) || empty($password)) {
        $error = "NIK dan password harus diisi.";
    } else {
        try {
            // Periksa pengguna berdasarkan NIK
            $stmt = $pdo->prepare("SELECT * FROM registrasi WHERE nik = :nik");
            $stmt->execute(['nik' => $nik]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                // Set session
                $_SESSION['nik'] = $user['nik'];
                $_SESSION['role'] = $user['role'];

                // Redirect sesuai role
                if ($user['role'] === 'admin') {
                    header("Location: admin_dashboard.php");
                    exit();
                } elseif ($user['role'] === 'user') {
                    header("Location: pengaduan.php"); // Ubah menjadi halaman dashboard untuk user
                    exit();
                }
            } else {
                $error = "NIK atau password salah.";
            }
        } catch (PDOException $e) {
            // Log the error message for debugging
            error_log("Database error: " . $e->getMessage());
            $error = "Terjadi kesalahan sistem. Silakan coba lagi.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 400px;
            margin: 100px auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            color: #333;
        }
        label {
            display: block;
            margin: 10px 0 5px;
        }
        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            transition: border-color 0.3s;
        }
        input[type="text"]:focus,
        input[type="password"]:focus {
            border-color: #007BFF;
            outline: none;
        }
        input[type="submit"] {
            width: 100%;
            padding: 10px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        input[type="submit"]:hover {
            background-color: #0056b3;
        }
        .error {
            color: red;
            text-align: center;
            margin-bottom: 20px;
        }
        .register-link {
            text-align: center;
        }
        .register-link a {
            color: #007BFF;
            text-decoration: none;
        }
        .register-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Login Pengguna</h2>
        <form action="login.php" method="post">
            <label for="nik">NIK:</label>
            <input type="text" name="nik" required>
            <label for="password">Password:</label>
            <input type="password" name="password" required>
            <input type="submit" value="Login">
        </form>
        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <div class="register-link">
            Belum terdaftar? <a href="register.php">Klik di sini untuk registrasi</a>
        </div>
    </div>
</body>
</html>