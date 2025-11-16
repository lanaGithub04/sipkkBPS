<?php
session_start();
include("../config/koneksi.php");
if (!isset($_SESSION['role']) || $_SESSION['role'] != '2') {
  header("Location: ../index.php");
  exit();
}

// Ambil data kendaraan berdasarkan ID
if (isset($_GET['id'])) {
  $id = $_GET['id'];
  $result = mysqli_query($koneksi, "SELECT * FROM kendaraan WHERE id_kendaraan='$id'");
  $data = mysqli_fetch_assoc($result);

  if (!$data) {
    echo "<script>alert('Data tidak ditemukan');window.location='kendaraan_tampil.php';</script>";
    exit();
  }
} else {
  echo "<script>alert('ID tidak valid');window.location='kendaraan_tampil.php';</script>";
  exit();
}

// Proses update
if (isset($_POST['update'])) {
  $jenis = $_POST['jenis'];
  $merk = $_POST['merk'];
  $plat = $_POST['plat_nomor'];
  $status = $_POST['status'];

  $query = "UPDATE kendaraan SET 
              jenis='$jenis', 
              merk='$merk', 
              plat_nomor='$plat', 
              status='$status' 
              WHERE id_kendaraan='$id'";
  $result = mysqli_query($koneksi, $query);

  if ($result) {
    echo "<script>alert('✅ Data berhasil diperbarui');window.location='kendaraan_tampil.php';</script>";
  } else {
    echo "<script>alert('❌ Gagal memperbarui data: " . mysqli_error($koneksi) . "');</script>";
  }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <title>Edit Kendaraan | SIPKK</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    body {
      background: linear-gradient(180deg, #f8f9fa, #e3f2fd);
      font-family: 'Poppins', sans-serif;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }

    /* Header */
    header {
      background: linear-gradient(90deg, #007bff, #0056d6);
      color: #fff;
      padding: 15px 25px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    header h3 {
      margin: 0;
      font-weight: 600;
      font-size: 1.2rem;
    }

    .btn-logout {
      border: 1px solid #fff;
      color: #fff;
      transition: 0.3s;
      font-size: 0.9rem;
    }

    .btn-logout:hover {
      background: #fff;
      color: #0056d6;
    }

    /* Card Form */
    .card {
      border: none;
      border-radius: 15px;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
      background: #fff;
      padding: 35px 40px;
      max-width: 600px;
      width: 90%;
      margin: 90px auto 40px auto;
      animation: fadeIn 0.6s ease-in-out;
    }

    h3 {
      text-align: center;
      font-weight: 600;
      color: #003566;
      margin-bottom: 25px;
    }

    input,
    select {
      border-radius: 8px !important;
      transition: all 0.3s ease;
    }

    input:focus,
    select:focus {
      border-color: #007bff;
      box-shadow: 0 0 5px rgba(0, 123, 255, 0.3);
    }

    .btn {
      border-radius: 8px;
      font-weight: 500;
      transition: all 0.3s ease;
    }

    .btn-primary {
      background-color: #007bff;
      border: none;
    }

    .btn-primary:hover {
      background-color: #0056d6;
      transform: scale(1.05);
    }

    .btn-secondary {
      background-color: #6c757d;
      border: none;
    }

    .btn-secondary:hover {
      background-color: #5a6268;
      transform: scale(1.05);
    }

    footer {
      background: linear-gradient(90deg, #007bff, #0056d6);
      color: #fff;
      text-align: center;
      padding: 12px 0;
      font-size: 0.9rem;
      margin-top: auto;
    }

    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: translateY(10px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    /* Responsif */
    @media (max-width: 768px) {
      header {
        flex-direction: column;
        gap: 10px;
        text-align: center;
      }

      .card {
        margin: 100px 15px 60px 15px;
        padding: 25px;
      }

      h3 {
        font-size: 1.1rem;
      }

      .btn {
        font-size: 0.9rem;
      }
    }

    @media (max-width: 480px) {
      header h3 {
        font-size: 1rem;
      }

      .card {
        padding: 20px;
      }
    }
  </style>
</head>

<body>

  <!-- HEADER -->
  <header>
    <h3>🚗 SIPKK - <span style="color:#ffeb3b;">Edit Kendaraan</span></h3>
    <a href="dashboard.php" class="btn btn-logout btn-sm">🏠 Dashboard</a>
  </header>

  <!-- CONTENT -->
  <div class="card">
    <h3>✏️ Edit Data Kendaraan</h3>
    <form method="post">
      <div class="mb-3">
        <label class="form-label">Jenis Kendaraan</label>
        <select name="jenis" class="form-select" required>
          <option value="mobil" <?= ($data['jenis'] == 'mobil') ? 'selected' : ''; ?>>Mobil</option>
          <option value="motor_dinas" <?= ($data['jenis'] == 'motor_dinas') ? 'selected' : ''; ?>>Motor Dinas</option>
        </select>
      </div>

      <div class="mb-3">
        <label class="form-label">Merk Kendaraan</label>
        <input type="text" name="merk" class="form-control" value="<?= $data['merk']; ?>" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Plat Nomor</label>
        <input type="text" name="plat_nomor" class="form-control" value="<?= $data['plat_nomor']; ?>" required>
      </div>

      <div class="mb-4">
        <label class="form-label">Status</label>
        <select name="status" class="form-select" required>
          <option value="sehat" <?= ($data['status'] == 'sehat') ? 'selected' : ''; ?>>Sehat</option>
          <option value="perbaikan" <?= ($data['status'] == 'perbaikan') ? 'selected' : ''; ?>>Perbaikan</option>
          <option value="rusak" <?= ($data['status'] == 'rusak') ? 'selected' : ''; ?>>Rusak</option>
        </select>
      </div>

      <div class="d-flex justify-content-between flex-wrap gap-2">
        <a href="kendaraan_tampil.php" class="btn btn-secondary flex-fill">⬅️ Kembali</a>
        <button type="submit" name="update" class="btn btn-primary flex-fill">💾 Simpan Perubahan</button>
      </div>
    </form>
  </div>

  <!-- FOOTER -->
  <footer>
    © <?= date('Y'); ?> SIPKK | Sistem Informasi Peminjaman Kendaraan Kantor
  </footer>

</body>
</html>
