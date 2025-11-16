<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != '1') {
  header("Location: ../index.php");
  exit;
}
include "../config/koneksi.php";

$id_user = $_SESSION['id_user'];

$total  = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as jml FROM peminjaman WHERE id_u_pinjam='$id_user'"))['jml'];
$proses = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as jml FROM peminjaman WHERE id_u_pinjam='$id_user' AND status='1'"))['jml'];
$setuju = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as jml FROM peminjaman WHERE id_u_pinjam='$id_user' AND status='2'"))['jml'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Dashboard Pegawai - SIPKK</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
  <style>
body {
  background: linear-gradient(135deg, #f0f4ff, #e8f1ff);
  font-family: 'Poppins', sans-serif;
  min-height: 100vh;
  display: flex;
  flex-direction: column;
}

/* Navbar */
.navbar {
  background: linear-gradient(90deg, #0d6efd, #0dcaf0);
}
.navbar-brand {
  font-weight: 600;
  letter-spacing: 0.5px;
  display: flex;
  align-items: center;
  gap: 8px;
}

/* Statistik */
.stat-card {
  border-radius: 18px;
  border: none;
  padding: 25px;
  box-shadow: 0 4px 15px rgba(0,0,0,0.05);
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.stat-card:hover {
  transform: translateY(-6px);
  box-shadow: 0 10px 25px rgba(0,0,0,0.1);
}
.stat-icon {
  font-size: 45px;
  margin-bottom: 10px;
}
.bg-total {
  background: linear-gradient(135deg, #0d6efd, #47a3ff);
  color: white;
}
.bg-proses {
  background: linear-gradient(135deg, #ffc107, #ffd75e);
  color: #000;
}
.bg-setuju {
  background: linear-gradient(135deg, #198754, #33c685);
  color: white;
}

/* Menu Card - PERBAIKAN UTAMA */
.menu-card {
  border-radius: 18px;
  border: none;
  padding: 30px 20px;
  transition: all 0.3s ease;
  box-shadow: 0 5px 15px rgba(0,0,0,0.05);
  background: white;
  /* Tambahan untuk ukuran seragam */
  min-height: 200px; /* Tinggi minimum sama untuk semua card, sesuaikan jika perlu */
  display: flex;
  flex-direction: column;
  justify-content: space-between; /* Konten di atas, link di bawah */
  align-items: center; /* Center ikon dan teks */
  text-align: center; /* Teks center */
}
.menu-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 10px 25px rgba(0,0,0,0.1);
}
.menu-card i {
  font-size: 55px;
  transition: transform 0.3s ease;
  margin-bottom: 15px; /* Jarak ikon ke teks */
}
.menu-card:hover i {
  transform: scale(1.2);
}
.menu-card h5, .menu-card p { /* Asumsi ada heading dan paragraf di card */
  margin: 0;
  flex-grow: 1; /* Biar teks mengisi ruang */
}

/* Grid untuk Menu Card - Pastikan konsisten */
.menu-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); /* Kolom otomatis, lebar min 250px, sama untuk semua */
  gap: 20px; /* Jarak antar card */
  margin-bottom: 30px;
}

/* Responsif Grid */
@media (max-width: 768px) {
  .navbar-brand {
    font-size: 16px;
  }
  .menu-card {
    padding: 25px 10px;
    min-height: 180px; /* Tinggi lebih kecil di mobile */
  }
  .stat-icon {
    font-size: 35px;
  }
  .menu-grid {
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); /* Lebih kecil di mobile */
  }
}

footer {
  margin-top: auto;
  background: linear-gradient(90deg, #0d6efd, #0dcaf0);
  border-top: 1px solid #dee2e6;
  padding: 15px 0;
  color: #ffffffff;
  font-size: 14px;
}
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
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                    <span class="text-white me-3">Halo, <b><?= $_SESSION['username']; ?></b> 👋</span>
                    <a href="../logout.php" class="btn btn-outline-light btn-sm">Logout</a>
                </div>
            </div>
        </nav>
    </header>

  <!-- Statistik -->
  <div class="container mt-5">
    <div class="row g-4 text-center">
      <div class="col-md-4 col-sm-6">
        <div class="stat-card bg-total">
          <i class="bi bi-list-check stat-icon"></i>
          <h6>Total Peminjaman</h6>
          <h2 class="fw-bold"><?= $total; ?></h2>
        </div>
      </div>
      <div class="col-md-4 col-sm-6">
        <div class="stat-card bg-proses">
          <i class="bi bi-hourglass-split stat-icon"></i>
          <h6>Sedang Diproses</h6>
          <h2 class="fw-bold"><?= $proses; ?></h2>
        </div>
      </div>
      <div class="col-md-4 col-sm-6">
        <div class="stat-card bg-setuju">
          <i class="bi bi-check-circle stat-icon"></i>
          <h6>Disetujui</h6>
          <h2 class="fw-bold"><?= $setuju; ?></h2>
        </div>
      </div>
    </div>
  </div>

  <!-- Menu Utama -->
  <div class="container mt-5 mb-5">
    <div class="text-center mb-4">
      <h3 class="fw-bold text-primary">Dashboard Pegawai</h3>
      <p class="text-muted">Kelola peminjaman kendaraan dengan mudah di bawah ini</p>
    </div>

    <div class="row justify-content-center g-4">
      <div class="col-lg-4 col-md-6 col-sm-12">
        <a href="peminjaman_tampil.php" class="text-decoration-none text-dark">
          <div class="card menu-card text-center">
            <div class="card-body">
              <i class="bi bi-journal-plus text-primary"></i>
              <h5 class="fw-bold mt-3">Peminjaman</h5>
              <p class="text-muted small">Ajukan dan pantau status peminjaman kendaraan Anda</p>
            </div>
          </div>
        </a>
      </div>

      <div class="col-lg-4 col-md-6 col-sm-12">
        <a href="riwayat_peminjaman.php" class="text-decoration-none text-dark">
          <div class="card menu-card text-center">
            <div class="card-body">
              <i class="bi bi-clock-history text-success"></i>
              <h5 class="fw-bold mt-3">Riwayat Peminjaman</h5>
              <p class="text-muted small">Lihat riwayat lengkap peminjaman Anda</p>
            </div>
          </div>
        </a>
      </div>

    <!-- Tambahkan card menu baru untuk Ubah Password -->
    <div class="col-lg-3 col-md-6 col-sm-12">  <!-- Menggunakan col-lg-3 untuk 4 card di satu baris di desktop -->
      <a href="./mengubah_password.php" class="text-decoration-none text-dark">
        <div class="card menu-card text-center">
          <div class="card-body">
            <i class="bi bi-shield-lock text-danger"></i>  <!-- Ikon kunci dengan warna merah untuk keamanan -->
            <h5 class="fw-bold mt-3">Ubah Password</h5>
            <p class="text-muted small">Perbarui password akun Anda untuk keamanan maksimal</p>
          </div>
        </div>
      </a>
    </div>
  </div>
</div>

  <!-- Footer -->
  <footer class="text-center">
    <div class="container">
      &copy; <?= date("Y"); ?> SIPKK | Sistem Informasi Peminjaman Kendaraan Kantor
    </div>
  </footer>

</body>
</html>