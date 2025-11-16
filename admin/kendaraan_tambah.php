<?php
session_start();
include "../config/koneksi.php";
if (!isset($_SESSION['role']) || $_SESSION['role'] != '2') {
  header("Location: ../index.php");
  exit();
}

$success = false;
$error = "";

if (isset($_POST['simpan'])) {
  $jenis = $_POST['jenis'];
  $merk = $_POST['merk'];
  $plat = $_POST['plat_nomor'];
  $status = $_POST['status'];

  $query = "INSERT INTO kendaraan (jenis, merk, plat_nomor, status) VALUES ('$jenis', '$merk', '$plat', '$status')";
  $result = mysqli_query($koneksi, $query);

  if ($result) {
    $success = true;
  } else {
    $error = mysqli_error($koneksi);
  }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <title>Tambah Kendaraan - Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    body {
      background-color: #f8f9fc;
      min-height: 100vh;
    }

    header {
      background: linear-gradient(90deg, #0d6efd, #0dcaf0);
      color: white;
      padding: 15px 0;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
    }

    footer {
      background: linear-gradient(90deg, #0d6efd, #0dcaf0);
      color: #fff;
      padding: 15px;
      text-align: center;
      margin-top: 69px;
    }

    .card {
      border: none;
      border-radius: 15px;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
      background: white;
      transition: 0.3s;
    }

    .card:hover {
      transform: translateY(-5px);
    }

    .btn-custom {
      background: linear-gradient(to right, #198754, #28a745);
      border: none;
      color: white;
      font-weight: 500;
      transition: 0.3s;
    }

    .btn-custom:hover {
      background: linear-gradient(to right, #28a745, #198754);
    }

    .title {
      text-align: center;
      font-weight: bold;
      margin-bottom: 20px;
      color: #333;
    }
  </style>
</head>

<header>
  <div class="container d-flex justify-content-between align-items-center">
    <h4 class="m-0 fw-semibold">🚗 SIPKK Admin</h4>
    <a href="dashboard.php" class="btn btn-light btn-sm">🏠 Dashboard</a>
  </div>
</header>

<body>
  <div class="container d-flex justify-content-center align-items-center mt-5">
    <div class="col-md-6">
      <div class="card p-4">
        <h3 class="title">🚗 Tambah Kendaraan</h3>
        <form method="post">
          <div class="mb-3">
            <label class="form-label">Jenis Kendaraan</label>
            <select name="jenis" class="form-select" required>
              <option value="mobil">Mobil</option>
              <option value="motor_dinas">Motor Dinas</option>
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label">Merk Kendaraan</label>
            <input type="text" name="merk" class="form-control" placeholder="Contoh: Toyota Avanza" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Plat Nomor</label>
            <input type="text" name="plat_nomor" class="form-control" placeholder="Contoh: DA 1234 AB" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Status</label>
            <select name="status" class="form-select" required>
              <option value="1">Sehat</option>
              <option value="4">Rusak</option>
              <option value="5">Perbaikan</option>
            </select>
          </div>

          <div class="d-flex justify-content-between">
            <a href="kendaraan_tampil.php" class="btn btn-secondary">⬅ Kembali</a>
            <button type="submit" name="simpan" class="btn btn-custom">💾 Simpan</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <?php if ($success): ?>
    <script>
      Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: 'Kendaraan berhasil ditambahkan!',
        showConfirmButton: false,
        timer: 2000
      }).then(() => {
        window.location = 'kendaraan_tampil.php';
      });
    </script>
  <?php elseif (!empty($error)): ?>
    <script>
      Swal.fire({
        icon: 'error',
        title: 'Gagal!',
        text: 'Terjadi kesalahan: <?= $error ?>'
      });
    </script>
  <?php endif; ?>
</body>

<!-- FOOTER -->
<footer>
  <small>© <?= date('Y'); ?> Sistem Informasi Peminjaman Kendaraan Kantor | Admin Panel</small>
</footer>

</html>