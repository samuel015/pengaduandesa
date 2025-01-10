<?php
session_start();
include 'db.php'; // Pastikan koneksi database sudah benar

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = trim($_POST['nama']);
    $nik = trim($_POST['nik']);
    $password = $_POST['password'];
    $email = trim($_POST['email']);
    $role = 'user'; // Set role secara langsung sebagai 'user'
    $alamat = $_POST['alamat'];
    $jabatan = isset($_POST['jabatan']) ? trim($_POST['jabatan']) : '';
    $foto_ktp = '';
    $foto_profil = '';
    $foto_kk = '';

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
            // Validasi dan proses upload foto KTP
            if (isset($_FILES['foto_ktp']) && $_FILES['foto_ktp']['error'] == 0) {
                $allowed_ext = ['jpg', 'jpeg', 'png'];
                $file_ext = pathinfo($_FILES['foto_ktp']['name'], PATHINFO_EXTENSION);
                $file_size = $_FILES['foto_ktp']['size'];

                if (!in_array(strtolower($file_ext), $allowed_ext)) {
                    $error = "Format foto KTP tidak valid. Harap unggah file dengan ekstensi .jpg, .jpeg, atau .png.";
                } elseif ($file_size > 2000000) {
                    $error = "Ukuran foto KTP terlalu besar. Maksimal 2MB.";
                } else {
                    $foto_ktp = 'uploads/ktp/' . uniqid() . '.' . $file_ext;
                    move_uploaded_file($_FILES['foto_ktp']['tmp_name'], $foto_ktp);
                }
            } else {
                $error = "Foto KTP harus diunggah.";
            }

            // Validasi dan proses upload foto profil
            if (empty($error) && isset($_FILES['foto_profil']) && $_FILES['foto_profil']['error'] == 0) {
                $allowed_ext = ['jpg', 'jpeg', 'png'];
                $file_ext = pathinfo($_FILES['foto_profil']['name'], PATHINFO_EXTENSION);
                $file_size = $_FILES['foto_profil']['size'];

                if (!in_array(strtolower($file_ext), $allowed_ext)) {
                    $error = "Format foto profil tidak valid. Harap unggah file dengan ekstensi .jpg, .jpeg, atau .png.";
                } elseif ($file_size > 2000000) {
                    $error = "Ukuran foto profil terlalu besar. Maksimal 2MB.";
                } else {
                    $foto_profil = 'uploads/profil/' . uniqid() . '.' . $file_ext;
                    move_uploaded_file($_FILES['foto_profil']['tmp_name'], $foto_profil);
                }
            } else {
                $error = "Foto profil harus diunggah.";
            }

            // Validasi dan proses upload foto KK
            if (empty($error) && isset($_FILES['foto_kk']) && $_FILES['foto_kk']['error'] == 0) {
                $allowed_ext = ['jpg', 'jpeg', 'png'];
                $file_ext = pathinfo($_FILES['foto_kk']['name'], PATHINFO_EXTENSION);
                $file_size = $_FILES['foto_kk']['size'];

                if (!in_array(strtolower($file_ext), $allowed_ext)) {
                    $error = "Format foto KK tidak valid. Harap unggah file dengan ekstensi .jpg, .jpeg, atau .png.";
                } elseif ($file_size > 2000000) {
                    $error = "Ukuran foto KK terlalu besar. Maksimal 2MB.";
                } else {
                    $foto_kk = 'uploads/kk/' . uniqid() . '.' . $file_ext;
                    move_uploaded_file($_FILES['foto_kk']['tmp_name'], $foto_kk);
                }
            } else {
                $error = "Foto KK harus diunggah.";
            }

            if (empty($error)) {
                // Hash password
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                // Insert data ke database dengan status 'Pending'
                try {
                    $stmt = $pdo->prepare("INSERT INTO registrasi (nama, nik, password, role, alamat, email, jabatan, foto_ktp, foto, status, foto_kk) 
                                           VALUES (:nama, :nik, :password, :role, :alamat, :email, :jabatan, :foto_ktp, :foto, 'Pending', :foto_kk)");
                    $stmt->execute([
                        'nama' => $nama,
                        'nik' => $nik,
                        'password' => $hashed_password,
                        'role' => $role,
                        'alamat' => $alamat,
                        'email' => $email,
                        'jabatan' => $jabatan,
                        'foto_ktp' => $foto_ktp,
                        'foto' => $foto_profil,
                        'foto_kk' => $foto_kk
                    ]);

                    $_SESSION['nik'] = $nik;
                    $_SESSION['nama'] = $nama;

                    // Redirect ke halaman login
                    header("Location: login.php");
                    exit();
                } catch (PDOException $e) {
                    $error = "Error: " . $e->getMessage();
                }
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
        body { margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f4f4f9; }
        .container { max-width: 400px; margin: 50px auto; padding: 20px; background: #ffffff; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); border-radius: 8px; border: 1px solid #ddd; }
        .container h2 { text-align: center; color: #333; margin-bottom: 20px; }
        label { font-weight: bold; color: #555; display: block; margin-bottom: 5px; }
        input[type="text"], input[type="password"], input[type="email"], textarea { width: 100%; padding: 10px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 4px; font-size: 14px; }
        input[type="file"] { margin-bottom: 15px; }
        input[type="submit"] { background-color: #4CAF50; color: white; padding: 12px; width: 100%; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; }
        input[type="submit"]:hover { background-color: #45a049; }
        .error { color: red; font-size: 14px; margin-bottom: 15px; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Form Registrasi</h2>
        <?php if ($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="POST" action="" enctype="multipart/form-data">
            <label for="nama">Nama:</label>
            <input type="text" name="nama" id="nama" required>
            
            <label for="nik">NIK:</label>
            <input type="text" name="nik" id="nik" required>

            <label for="password">Password:</label>
            <input type="password" name="password" id ="password" required>

            <label for="email">Email:</label>
            <input type="email" name="email" id="email" required>

            <label for="alamat">Alamat:</label>
            <textarea name="alamat" id="alamat" required></textarea>

            <label for="foto_ktp">Foto KTP:</label>
            <input type="file" name="foto_ktp" id="foto_ktp" accept="image/*" required>

            <label for="foto_profil">Foto Profil:</label>
            <input type="file" name="foto_profil" id="foto_profil" accept="image/*" required>

            <label for="foto_kk">Foto KK:</label>
            <input type="file" name="foto_kk" id="foto_kk" accept="image/*" required>

            <input type="submit" value="Daftar">
        </form>
    </div>
</body>
</html>