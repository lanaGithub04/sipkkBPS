<?php
session_start();
include "../config/koneksi.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $nama = mysqli_real_escape_string($conn, $_POST['nama']);
  $username = mysqli_real_escape_string($conn, $_POST['username']);
  $password = mysqli_real_escape_string($conn, $_POST['password']);
  $konfirmasi = mysqli_real_escape_string($conn, $_POST['konfirmasi']);

  // Cek apakah username sudah ada
  $cek = mysqli_query($conn, "SELECT * FROM users WHERE username='$username'");
  if (mysqli_num_rows($cek) > 0) {
    echo "<script>alert('Username sudah digunakan!');window.location='register.php';</script>";
    exit;
  }

  // Cek konfirmasi password
  if ($password !== $konfirmasi) {
    echo "<script>alert('Konfirmasi password tidak cocok!');window.location='register.php';</script>";
    exit;
  }

  // Hash password
  $hashed = password_hash($password, PASSWORD_DEFAULT);

  // Simpan ke database, role default = 1 (pegawai)
  $query = "INSERT INTO users (nama, username, password, role) VALUES ('$nama', '$username', '$hashed', '1')";
  if (mysqli_query($conn, $query)) {
    echo "<script>alert('Registrasi berhasil! Silakan login.');window.location='../index.php';</script>";
  } else {
    echo "<script>alert('Terjadi kesalahan saat menyimpan data!');</script>";
  }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registrasi Akun | SIPKK</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(135deg, #007bff, #00bcd4);
      min-height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      font-family: 'Poppins', sans-serif;
    }
    .card {
      border: none;
      border-radius: 20px;
      box-shadow: 0 8px 20px rgba(0,0,0,0.2);
    }
    .form-control {
      border-radius: 10px;
    }
    .btn-primary {
      border-radius: 10px;
      background: #007bff;
      border: none;
    }
    .btn-primary:hover {
      background: #0069d9;
    }
    .logo {
      width: 90px;
      margin-bottom: 10px;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-5">
        <div class="card p-4">
          <div class="text-center mb-3">
            <img src="../Logo/logoBPS.png" alt="Logo" class="logo">
            <h4 class="fw-bold text-primary">Registrasi Akun Pegawai SIPKK</h4>
            <p class="text-muted">Silakan buat akun untuk mengakses sistem</p>
          </div>
          <form method="POST" action="register_proses.php">
            <div class="mb-3">
              <label class="form-label">Nama Lengkap</label>
              <input type="text" name="nama" class="form-control" placeholder="Masukkan nama lengkap" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Username</label>
              <input type="text" name="username" class="form-control" placeholder="Masukkan username" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Password</label>
              <input type="password" name="password" class="form-control" placeholder="Masukkan password" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Konfirmasi Password</label>
              <input type="password" name="konfirmasi" class="form-control" placeholder="Ulangi password" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Daftar</button>
          </form>
          <div class="text-center mt-3">
            <p class="text-muted mb-0">Sudah punya akun?</p>
            <a href="../index.php" class="text-primary text-decoration-none fw-semibold">Login di sini</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
