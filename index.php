<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Ambil data dari form
  $username = $_POST['username'] ?? '';
  $password = $_POST['password'] ?? '';

  // Validasi sederhana: username dan password harus sama
  if ($username === $password) {

    // Tentukan role berdasarkan username
    if ($username === 'admin') {
      $_SESSION['role'] = 'admin';
      header("Location: admin/dashboard.php");
      exit();
    } elseif ($username === 'pegawai') {
      $_SESSION['role'] = 'pegawai';
      header("Location: pegawai/dashboard.php");
      exit();
    } elseif ($username === 'pimpinan') {
      $_SESSION['role'] = 'pimpinan';
      header("Location: pimpinan/dashboard.php");
      exit();
    } else {
      // Default role jika username tidak dikenal
      $_SESSION['role'] = 'pegawai';
      header("Location: pegawai/dashboard.php");
      exit();
    }
  } else {
    echo "<script>alert('Username dan Password tidak cocok!');</script>";
  }
}
?>


<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>SIPKK - Sistem Informasi Peminjaman Kendaraan Kantor</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

  <style>
    * {
      box-sizing: border-box;
    }

    body {
      margin: 0;
      padding: 0;
      height: 100vh;
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(135deg, #007bff, #00c6ff);
      overflow: hidden;
      display: flex;
      justify-content: center;
      align-items: center;
      color: #fff;
    }

    /* Animasi gelembung */
    .bubbles {
      position: absolute;
      width: 100%;
      height: 100%;
      top: 0;
      left: 0;
      overflow: hidden;
      z-index: 0;
    }

    .bubbles span {
      position: absolute;
      bottom: -50px;
      background: rgba(255, 255, 255, 0.15);
      border-radius: 50%;
      animation: rise 12s infinite ease-in;
    }

    @keyframes rise {
      0% {
        transform: translateY(0) scale(1);
        opacity: 1;
      }

      100% {
        transform: translateY(-1000px) scale(1.5);
        opacity: 0;
      }
    }

    .bubbles span:nth-child(1) {
      left: 5%;
      width: 40px;
      height: 40px;
      animation-delay: 0s;
    }

    .bubbles span:nth-child(2) {
      left: 20%;
      width: 60px;
      height: 60px;
      animation-delay: 2s;
    }

    .bubbles span:nth-child(3) {
      left: 40%;
      width: 30px;
      height: 30px;
      animation-delay: 4s;
    }

    .bubbles span:nth-child(4) {
      left: 60%;
      width: 50px;
      height: 50px;
      animation-delay: 1s;
    }

    .bubbles span:nth-child(5) {
      left: 80%;
      width: 35px;
      height: 35px;
      animation-delay: 3s;
    }

    .bubbles span:nth-child(6) {
      left: 90%;
      width: 45px;
      height: 45px;
      animation-delay: 5s;
    }

    /* Card login */
    .content {
      position: relative;
      z-index: 2;
      background: rgba(255, 255, 255, 0.12);
      backdrop-filter: blur(12px);
      border: 1px solid rgba(255, 255, 255, 0.2);
      border-radius: 20px;
      padding: 40px 35px;
      width: 90%;
      max-width: 420px;
      box-shadow: 0 10px 35px rgba(0, 0, 0, 0.25);
      animation: fadeIn 1s ease-in-out;
    }

    .logo {
      width: 100px;
      height: 100px;
      object-fit: contain;
      margin-bottom: 15px;
      animation: fadeInDown 1s ease-in-out;
    }

    h1 {
      font-weight: 700;
      font-size: 1.9rem;
      letter-spacing: 0.5px;
    }

    h4 {
      font-weight: 400;
      color: #eaf4ff;
      margin-bottom: 30px;
      font-size: 1rem;
    }

    .form-control {
      border-radius: 12px;
      border: none;
      padding: 13px;
      font-size: 0.95rem;
      box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.05);
    }

    .form-control:focus {
      outline: none;
      box-shadow: 0 0 6px rgba(0, 123, 255, 0.6);
    }

    .btn-login {
      border: none;
      font-weight: 600;
      border-radius: 12px;
      padding: 12px;
      background: linear-gradient(135deg, #00a8ff, #0059ff);
      color: #fff;
      transition: all 0.3s ease;
      font-size: 1rem;
      box-shadow: 0 4px 15px rgba(0, 91, 255, 0.4);
    }

    .btn-login:hover {
      transform: translateY(-3px);
      background: linear-gradient(135deg, #007bff, #00c6ff);
      box-shadow: 0 6px 20px rgba(0, 91, 255, 0.5);
    }

    footer {
      margin-top: 40px;
      color: #e8e8e8;
      font-size: 13px;
    }

    /* Animasi masuk */
    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: translateY(30px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    @keyframes fadeInDown {
      from {
        opacity: 0;
        transform: translateY(-30px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    /* Responsif */
    @media (max-width: 768px) {
      .content {
        padding: 30px 25px;
      }

      h1 {
        font-size: 1.6rem;
      }

      h4 {
        font-size: 0.95rem;
      }

      .btn-login {
        font-size: 0.9rem;
      }
    }

    @media (max-width: 480px) {
      .logo {
        width: 80px;
        height: 80px;
      }

      .content {
        border-radius: 16px;
      }

      h1 {
        font-size: 1.4rem;
      }

      footer {
        font-size: 12px;
      }
    }
  </style>
</head>

<body>

  <div class="bubbles">
    <span></span><span></span><span></span>
    <span></span><span></span><span></span>
  </div>

  <div class="content text-center">
    <img src="logo/bpskalsel.png" alt="Logo Instansi" class="logo">
    <h1>🚗 SIPKK</h1>
    <h4>Sistem Informasi Peminjaman Kendaraan Kantor</h4>

    <form action="auth/proses_login.php" method="POST">
      <div class="mb-3">
        <input type="text" name="username" class="form-control" placeholder="Masukkan Username" required>
      </div>
      <div class="mb-3">
        <input type="password" name="password" class="form-control" placeholder="Masukkan Password" required>
      </div>
      <button type="submit" class="btn btn-login w-100">Masuk</button>
          <p class="text-center mt-3 text-white">Pian belum punya akun?
          <a href="./auth/register.php" class="text-warning fw-bold">Daftar di sini</a>
        </p>
    </form>

    <footer>
      <p>© <?= date('Y'); ?> SIPKK | Sistem Informasi Peminjaman Kendaraan Kantor</p>
    </footer>
  </div>

</body>

</html>