<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != '1') {
  header("Location: ../index.php");
  exit;
}
include "../config/koneksi.php";

if (!$koneksi || !is_object($koneksi) || get_class($koneksi) !== 'mysqli') {
  die("Error: Koneksi database tidak valid. Periksa config/koneksi.php");
}

$id_user = $_SESSION['id_user'];
$message = "";
$alertType = ""; // success | error | warning

$table_check = $koneksi->query("SHOW TABLES LIKE 'users'");
if ($table_check->num_rows == 0) {
  $message = "Error: Tabel 'users' tidak ditemukan di database.";
  $alertType = "error";
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $table_check->num_rows > 0) {
  $old_password = trim($_POST['old_password']);
  $new_password = trim($_POST['new_password']);
  $confirm_password = trim($_POST['confirm_password']);

  if (empty($old_password) || empty($new_password) || empty($confirm_password)) {
    $message = "Semua field harus diisi!";
    $alertType = "error";
  } elseif (strlen($new_password) < 8) {
    $message = "Password baru minimal 8 karakter!";
    $alertType = "error";
  } elseif (!preg_match('/[A-Z]/', $new_password) || !preg_match('/[a-z]/', $new_password) || !preg_match('/[0-9]/', $new_password)) {
    $message = "Password baru harus mengandung huruf besar, kecil, dan angka!";
    $alertType = "error";
  } elseif ($new_password !== $confirm_password) {
    $message = "Konfirmasi password tidak cocok!";
    $alertType = "error";
  } else {
    $stmt = $koneksi->prepare("SELECT password FROM users WHERE id_user = ?");
    $stmt->bind_param("i", $id_user);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
      $user = $result->fetch_assoc();
      if (md5($old_password) === $user['password']) {
        if (md5($new_password) === $user['password']) {
          $message = "Password baru tidak boleh sama dengan password lama!";
          $alertType = "warning";
        } else {
          $hashed_new_password = md5($new_password);
          $update = $koneksi->prepare("UPDATE users SET password = ? WHERE id_user = ?");
          $update->bind_param("si", $hashed_new_password, $id_user);
          if ($update->execute()) {
            $message = "Password berhasil diubah! Anda akan dialihkan ke halaman login...";
            $alertType = "success";
            session_destroy();
          } else {
            $message = "Gagal update password!";
            $alertType = "error";
          }
          $update->close();
        }
      } else {
        $message = "Password lama salah!";
        $alertType = "error";
      }
    }
    $stmt->close();
  }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Ubah Password - SIPKK Pegawai</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    body {
      background: linear-gradient(135deg, #f0f4ff, #e8f1ff);
      font-family: 'Poppins', sans-serif;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      opacity: 1;
      transition: opacity 0.8s ease;
    }
    body.fade-out {
      opacity: 0;
    }
    .navbar { background: linear-gradient(90deg, #0d6efd, #0dcaf0); }
    .form-container { max-width: 500px; margin: 50px auto; padding: 30px; background: white; border-radius: 18px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
    footer { margin-top: auto; background: linear-gradient(90deg, #0d6efd, #0dcaf0); padding: 15px 0; color: white; font-size: 14px; }
    .password-container { position: relative; }
    .password-toggle { position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #6c757d; }
  </style>
</head>
<body>
  <!-- Navbar -->
  <header>
    <nav class="navbar navbar-expand-lg navbar-dark shadow-sm">
      <div class="container-fluid px-3">
        <a class="navbar-brand fw-bold" href="dashboard.php">
          🚘 <span>SIPKK</span> - Pegawai
        </a>
        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
          <span class="text-white me-3">Halo, <b><?= $_SESSION['username']; ?></b> 👋</span>
          <a href="../logout.php" class="btn btn-outline-light btn-sm">Logout</a>
        </div>
      </div>
    </nav>
  </header>

  <!-- Form -->
  <div class="container form-container">
    <h3 class="text-center fw-bold text-primary mb-4">Ubah Password</h3>
    <form method="POST">
      <div class="mb-3">
        <label for="old_password" class="form-label">Password Lama</label>
        <div class="password-container">
          <input type="password" class="form-control" id="old_password" name="old_password" required>
          <i class="bi bi-eye password-toggle" onclick="togglePassword('old_password')"></i>
        </div>
      </div>
      <div class="mb-3">
        <label for="new_password" class="form-label">Password Baru</label>
        <div class="password-container">
          <input type="password" class="form-control" id="new_password" name="new_password" required>
          <i class="bi bi-eye password-toggle" onclick="togglePassword('new_password')"></i>
        </div>
        <small class="text-muted">Minimal 8 karakter, kombinasi huruf besar, kecil, dan angka.</small>
      </div>
      <div class="mb-3">
        <label for="confirm_password" class="form-label">Konfirmasi Password Baru</label>
        <div class="password-container">
          <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
          <i class="bi bi-eye password-toggle" onclick="togglePassword('confirm_password')"></i>
        </div>
      </div>
      <button type="submit" class="btn btn-primary w-100">Ubah Password</button>
      <a href="dashboard.php" class="btn btn-secondary w-100 mt-2">Kembali ke Dashboard</a>
    </form>
  </div>

  <footer class="text-center">
    <div class="container">
      &copy; <?= date("Y"); ?> SIPKK | Sistem Informasi Peminjaman Kendaraan Kantor
    </div>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    function togglePassword(fieldId) {
      const field = document.getElementById(fieldId);
      const icon = field.nextElementSibling;
      if (field.type === 'password') {
        field.type = 'text';
        icon.classList.remove('bi-eye');
        icon.classList.add('bi-eye-slash');
      } else {
        field.type = 'password';
        icon.classList.remove('bi-eye-slash');
        icon.classList.add('bi-eye');
      }
    }

    <?php if (!empty($message)) : ?>
      Swal.fire({
        icon: '<?= $alertType; ?>',
        title: '<?= ucfirst($alertType); ?>',
        text: '<?= $message; ?>',
        showConfirmButton: <?= ($alertType == 'success') ? 'false' : 'true'; ?>,
        timer: <?= ($alertType == 'success') ? 3000 : 0; ?>,
        timerProgressBar: <?= ($alertType == 'success') ? 'true' : 'false'; ?>,
        didClose: () => {
          <?php if ($alertType == 'success') : ?>
            document.body.classList.add('fade-out');
            setTimeout(() => {
              window.location.href = '../index.php';
            }, 800);
          <?php endif; ?>
        }
      });
    <?php endif; ?>
  </script>
</body>
</html>
