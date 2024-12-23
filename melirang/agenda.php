<?php include 'db.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Agenda Desa Melirang</title>
    <style>
        /* Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Body */
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f9f9f9;
            padding: 20px;
            background-image: url('uploads/desa.png'); /* Gambar desa sebagai latar belakang */
            background-size: cover; /* Agar gambar mengisi seluruh layar */
            background-position: center; /* Menempatkan gambar di tengah */
            height: 100vh;
        }

        /* Navbar */
        .navbar {
            display: flex;
            justify-content: flex-end; /* Navbar berada di sebelah kanan */
            position: absolute;
            top: 20px;
            right: 20px;
            width: auto;
        }

        .navbar a {
            color: white;
            text-decoration: none;
            padding: 14px 20px;
            display: inline-block;
            transition: background 0.3s ease;
        }

        .navbar a:hover {
            background: #45a049;
        }

        /* Container utama */
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background: rgba(255, 255, 255, 0.8); /* Semi transparan putih */
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-top: 100px; /* Memberikan jarak dari navbar */
        }

        /* Agenda box */
        .agenda-box {
            margin-bottom: 20px;
            padding: 15px;
            background: #f0f8ff;
            border-left: 4px solid #4CAF50;
            border-radius: 4px;
        }

        .agenda-box h3 {
            color: #4CAF50;
            margin-bottom: 10px;
        }

        .agenda-box p {
            font-size: 16px;
            line-height: 1.6;
        }

        /* Registrasi dan Login */
        .registration {
            margin-top: 20px;
            padding: 15px;
            background: #e0ffe0;
            border: 1px solid #4CAF50;
            border-radius: 4px;
        }

        .registration h3 {
            margin-bottom: 10px;
            color: #4CAF50;
        }

        .registration a {
            margin-right: 10px;
            color: #4CAF50;
            text-decoration: none;
            font-weight: bold;
        }

        .registration a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <div class="navbar">
        <a href="index.php">Home</a>
            <a href="data_pengaduan.php">Data Pengaduan</a>
            <a href="agenda.php">Agenda Desa</a>
            <a href="login.php">Login</a> <!-- Tautan Login di sebelah kanan navbar -->
    </div>

    <!-- Container untuk agenda -->
    <div class="container" id="agenda">
        <h2>Agenda Desa Melirang</h2>

        <?php
        // Query untuk mengambil data agenda dari database
        $query = "SELECT * FROM agenda ORDER BY tanggal ASC";
        
        try {
            $stmt = $pdo->prepare($query);
            $stmt->execute();

            // Set the resulting array to associative
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            $results = $stmt->fetchAll();

            if ($results) {
                foreach ($results as $row) {
                    echo '<div class="agenda-box">';
                    echo '<h3>' . htmlspecialchars($row['judul']) . '</h3>';
                    echo '<p>Tanggal: ' . htmlspecialchars(date("d F Y", strtotime($row['tanggal']))) . '</p>';
                    echo '<p>Waktu: ' . htmlspecialchars($row['waktu']) . '</p>';
                    echo '<p>Tempat: ' . htmlspecialchars($row['tempat']) . '</p>';
                    echo '<p>Deskripsi: ' . htmlspecialchars($row['deskripsi']) . '</p>';
                    echo '</div>';
                }
            } else {
                echo '<p>Tidak ada agenda yang tersedia.</p>';
            }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        ?>
    </div>

</body>
</html>
