<?php
include '../../config/koneksi.php';
session_start();

// Pastikan hanya admin yang bisa akses
if (!isset($_SESSION['role'])) {
    header("Location: ../users_tampil.php");
    exit();
}

// Pastikan parameter ID dikirim
if (!isset($_GET['id'])) {
    header("Location: ../users_tampil.php");
    exit();
}

$id = $_GET['id'];

// Ambil data user berdasarkan ID
$query = mysqli_query($koneksi, "SELECT * FROM users WHERE id_user='$id'");
$data = mysqli_fetch_assoc($query);

// Jika data tidak ditemukan
if (!$data) {
    header("Location: ../users_tampil.php?pesan=notfound");
    exit();
}

// Flag untuk menandai update sukses
$update_sukses = false;

// Jika tombol update ditekan
if (isset($_POST['update'])) {
    $nama = $_POST['nama'];
    $username = $_POST['username'];
    $role = $_POST['role'];

    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $update = mysqli_query($koneksi, "UPDATE users SET nama='$nama', username='$username', password='$password', role='$role' WHERE id_user='$id'");
    } else {
        $update = mysqli_query($koneksi, "UPDATE users SET nama='$nama', username='$username', role='$role' WHERE id_user='$id'");
    }

    if ($update) {
        $update_sukses = true; // tandai berhasil
    } else {
        echo "<script>alert('Gagal update data!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Edit User - SIPKK</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    body {
      background-color: #f8f9fc;
      font-family: 'Poppins', sans-serif;
      min-height: 100vh;
    }

    header {
      background: linear-gradient(90deg, #0d6efd, #0dcaf0);
      color: white;
      padding: 15px 0;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
    }

    .navbar-brand {
      font-weight: 600;
      color: white !important;
      font-size: 1.2rem;
    }

    .card {
      border: none;
      border-radius: 16px;
      box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
      background: white;
      animation: fadeInUp 0.6s ease;
    }

    @keyframes fadeInUp {
      from {
        opacity: 0;
        transform: translateY(30px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .card h3 {
      font-weight: 600;
      color: #333;
    }

    .btn-primary {
      background-color: #4e73df;
      border: none;
      transition: all 0.3s ease;
    }

    .btn-primary:hover {
      background-color: #375ac3;
      transform: scale(1.05);
    }

    .btn-secondary {
      background-color: #858796;
      border: none;
      transition: all 0.3s ease;
    }

    .btn-secondary:hover {
      background-color: #6c757d;
      transform: scale(1.05);
    }

    footer {
      background: linear-gradient(90deg, #0d6efd, #0dcaf0);
      color: #fff;
      padding: 15px;
      text-align: center;
      margin-top: 210px;
    }

    label {
      font-weight: 500;
      color: #444;
    }

    input, select {
      border-radius: 10px !important;
    }
  </style>
</head>

<body>

  <!-- HEADER -->
  <header>
    <div class="container">
      <a class="navbar-brand" href="../dashboard.php">👤 SIPKK - Edit Data User</a>
    </div>
  </header>

  <!-- FORM -->
  <div class="container py-5">
    <div class="card mx-auto p-4" style="max-width: 650px;">
      <h3 class="text-center mb-4">✏️ Edit Data User</h3>
      <form method="POST">
        <div class="mb-3">
          <label class="form-label">Nama Lengkap</label>
          <input type="text" name="nama" class="form-control" 
                 value="<?= $data['nama'] ?>" required>
        </div>

        <div class="mb-3">
          <label class="form-label">Username</label>
          <input type="text" name="username" class="form-control" 
                 value="<?= $data['username'] ?>" required>
        </div>

        <div class="mb-3">
          <label class="form-label">Password</label>
          <input type="password" name="password" class="form-control" 
                 placeholder="Kosongkan jika tidak ingin diubah">
        </div>

        <div class="mb-4">
          <label class="form-label">Role</label>
          <select name="role" class="form-select" required>
            <option value="2" <?= $data['role'] == '2' ? 'selected' : '' ?>>Admin</option>
            <option value="1" <?= $data['role'] == '1' ? 'selected' : '' ?>>Pegawai</option>
            <option value="3" <?= $data['role'] == '3' ? 'selected' : '' ?>>Pimpinan</option>
          </select>
        </div>

        <div class="d-flex justify-content-between">
          <a href="../users_tampil.php" class="btn btn-secondary">⬅ Kembali</a>
          <button type="submit" name="update" class="btn btn-primary">💾 Update</button>
        </div>
      </form>
    </div>
  </div>

  <!-- FOOTER -->
  <footer>
    <small>© <?= date('Y'); ?> SIPKK | Sistem Informasi Pengelolaan Kantor</small>
  </footer>

  <!-- SweetAlert untuk animasi sukses -->
  <?php if ($update_sukses): ?>
  <script>
    Swal.fire({
      title: 'Berhasil!',
      text: 'Data user berhasil diperbarui 🎉',
      icon: 'success',
      showConfirmButton: false,
      timer: 1800,
      timerProgressBar: true,
      position: 'center',
      backdrop: `
        rgba(23, 23, 43, 0.59)
        url("https://cdn.dribbble.com/users/1186261/screenshots/3718681/party_popper.gif")
        center top
        no-repeat
      `
    }).then(() => {
      window.location.href = "../users_tampil.php";
    });
  </script>
  <?php endif; ?>

</body>
</html>
