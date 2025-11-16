<?php
session_start();
include "../config/koneksi.php";

$username = $_POST['username'];
$password = $_POST['password'];

// Hash password agar cocok dengan yang di database
$hashed_password = md5($password);

$query = mysqli_query($koneksi, "SELECT * FROM users WHERE username='$username' AND password='$hashed_password'");
$data = mysqli_fetch_assoc($query);

if ($data) {
    // Simpan data ke session
    $_SESSION['id_user'] = $data['id_user'];
    $_SESSION['nama'] = $data['nama'];
    $_SESSION['role'] = $data['role'];

    // Arahkan ke dashboard sesuai role
    if ($data['role'] == 'admin') {
        header("Location: ../admin/dashboard.php");
    } elseif ($data['role'] == 'pegawai') {
        header("Location: ../pegawai/dashboard.php");
    } elseif ($data['role'] == 'pimpinan') {
        header("Location: ../pimpinan/dashboard.php");
    }
    exit();
} else {
    echo "<script>
        alert('Username atau Password salah!');
        window.location.href = 'login.php';
    </script>";
}
?>


<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login SIPKK</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            height: 100vh;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            background: linear-gradient(135deg, #007BFF, #00C6FF);
            font-family: 'Poppins', sans-serif;
            overflow: hidden;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.18);
            border-radius: 20px;
            backdrop-filter: blur(12px);
            box-shadow: 0 10px 35px rgba(0, 0, 0, 0.25);
            padding: 40px;
            width: 360px;
            color: #f7f9fc;
            animation: fadeInUp 0.8s ease-out;
        }

        .login-card h3 {
            font-weight: 700;
            text-align: center;
            margin-bottom: 30px;
            color: #ffffff;
            text-shadow: 0 0 5px rgba(0, 0, 0, 0.3);
        }

        label {
            font-size: 14px;
            font-weight: 500;
            color: #eef2f7;
        }

        .form-control {
            background-color: rgba(255, 255, 255, 0.75);
            border: none;
            color: #333;
            border-radius: 10px;
            transition: all 0.3s;
            font-weight: 500;
        }

        .form-control::placeholder {
            color: #6c757d;
        }

        .form-control:focus {
            background-color: #fff;
            box-shadow: 0 0 12px rgba(0, 174, 255, 0.7);
        }

        .btn-login {
            background: linear-gradient(135deg, #1d8cf8, #0066ff);
            border: none;
            border-radius: 10px;
            color: white;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(0, 102, 255, 0.3);
        }

        .btn-login:hover {
            background: linear-gradient(135deg, #0066ff, #00c3ff);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 102, 255, 0.6);
        }

        .login-card p {
            text-align: center;
            margin-top: 20px;
            font-size: 13px;
            color: #e0e0e0;
        }

        @keyframes fadeInUp {
            0% {
                opacity: 0;
                transform: translateY(40px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Efek partikel halus di background */
        .bubbles {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0; left: 0;
            overflow: hidden;
            z-index: -1;
        }

        .bubbles span {
            position: absolute;
            bottom: -50px;
            background: rgba(255, 255, 255, 0.18);
            border-radius: 50%;
            animation: rise 10s infinite ease-in;
        }

        @keyframes rise {
            0% { transform: translateY(0) scale(1); opacity: 1; }
            100% { transform: translateY(-1000px) scale(1.5); opacity: 0; }
        }

        .bubbles span:nth-child(1) { left: 10%; width: 40px; height: 40px; animation-delay: 0s; }
        .bubbles span:nth-child(2) { left: 25%; width: 60px; height: 60px; animation-delay: 2s; }
        .bubbles span:nth-child(3) { left: 40%; width: 30px; height: 30px; animation-delay: 4s; }
        .bubbles span:nth-child(4) { left: 55%; width: 50px; height: 50px; animation-delay: 1s; }
        .bubbles span:nth-child(5) { left: 70%; width: 35px; height: 35px; animation-delay: 3s; }
        .bubbles span:nth-child(6) { left: 85%; width: 45px; height: 45px; animation-delay: 5s; }

    </style>
</head>
<body>

<div class="bubbles">
    <span></span><span></span><span></span>
    <span></span><span></span><span></span>
</div>

<div class="login-card">
    <h3>🚗 Login SIPKK</h3>
<form method="POST" action="proses_login.php">
    <div class="mb-3">
        <label for="username">Username</label>
        <input id="username" type="text" name="username" class="form-control" required placeholder="Masukkan username">
    </div>
    <button class="btn btn-login w-100 mt-3" type="submit">Masuk</button>
</form>

    <p>© <?= date('Y'); ?> Sistem Informasi Peminjaman Kendaraan Kantor</p>
</div>

</body>
</html>
