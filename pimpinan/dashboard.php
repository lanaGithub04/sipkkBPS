<?php
session_start();
include "../config/koneksi.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] != '3') {
  header("Location: ../index.php");
  exit();
}

$totalPeminjaman = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM peminjaman"))['total'];
$totalDisetujui = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM peminjaman WHERE status='disetujui'"))['total'];
$totalPending = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM peminjaman WHERE status='pending'"))['total'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Dashboard Pimpinan - SIPKK</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body {
      background: #f4f7fb;
      font-family: 'Poppins', sans-serif;
      display: flex;
      flex-direction: column;
      min-height: 100vh;
    }

    /* Navbar */
    nav.navbar {
      background: linear-gradient(90deg, #007bff, #00bcd4);
      box-shadow: 0 3px 10px rgba(0,0,0,0.15);
    }

    .navbar-brand {
      font-weight: 700;
      letter-spacing: 0.5px;
    }

    /* Header */
    h3 {
      font-weight: 700;
      color: #0d6efd;
      text-shadow: 1px 1px 3px rgba(0,0,0,0.1);
    }

    /* Card */
    .card {
      border: none;
      border-radius: 14px;
      transition: all 0.3s ease;
    }

    .card-hover:hover {
      transform: translateY(-6px);
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }

    .stat-card {
      color: white;
      padding: 25px;
      text-align: center;
    }

    .stat-card h5 {
      font-weight: 600;
      margin-bottom: 10px;
    }

    .stat-value {
      font-size: 2.8rem;
      font-weight: bold;
      margin: 0;
    }

    .bg-gradient-primary {
      background: linear-gradient(135deg, #0d6efd, #00bcd4);
    }

    .bg-gradient-success {
      background: linear-gradient(135deg, #28a745, #5be584);
    }

    .bg-gradient-warning {
      background: linear-gradient(135deg, #ffc107, #ff9800);
    }

    /* Menu Card */
    .menu-card {
      background: #fff;
      border-radius: 15px;
      box-shadow: 0 4px 18px rgba(0, 0, 0, 0.1);
      transition: all 0.3s ease;
      cursor: pointer;
    }

    .menu-card:hover {
      transform: scale(1.03);
      box-shadow: 0 6px 25px rgba(0, 0, 0, 0.15);
    }

    .menu-card h5 {
      color: #007bff;
      font-weight: 600;
    }

    /* Footer */
    footer {
      background: linear-gradient(90deg, #007bff, #00bcd4);
      color: white;
      text-align: center;
      padding: 15px 0;
      margin-top: auto;
      font-size: 0.9rem;
    }

    /* Responsif */
    @media (max-width: 768px) {
      .stat-value { font-size: 2rem; }
      .menu-card { padding: 20px; }
    }
  </style>
</head>

<body>

  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container">
      <a class="navbar-brand" href="dashboard.php"><i class="bi bi-speedometer2"></i> SIPKK - Pimpinan</a>
      <div class="d-flex align-items-center">
        <span class="text-white me-3">👋 Halo, <b><?= htmlspecialchars($_SESSION['username']); ?></b></span>
        <a href="../logout.php" class="btn btn-outline-light btn-sm fw-semibold"><i class="bi bi-box-arrow-right"></i> Logout</a>
      </div>
    </div>
  </nav>

  <!-- Konten -->
  <main class="container my-5">
    <div class="text-center mb-5">
      <h3>📊 Dashboard Pimpinan</h3>
      <p class="text-muted">Pantau ringkasan kegiatan peminjaman kendaraan di kantor</p>
    </div>

    <!-- Statistik -->
    <div class="row g-4 mb-5">
      <div class="col-md-4">
        <div class="stat-card bg-gradient-primary card-hover">
          <h5><i class="bi bi-list-check"></i> Total Peminjaman</h5>
          <p class="stat-value"><?= $totalPeminjaman; ?></p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="stat-card bg-gradient-success card-hover">
          <h5><i class="bi bi-check2-circle"></i> Disetujui</h5>
          <p class="stat-value"><?= $totalDisetujui; ?></p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="stat-card bg-gradient-warning card-hover">
          <h5><i class="bi bi-hourglass-split"></i> Pending</h5>
          <p class="stat-value"><?= $totalPending; ?></p>
        </div>
      </div>
    </div>

    <!-- Menu Cepat -->
    <div class="row justify-content-center">
      <div class="col-md-6 col-lg-5">
        <a href="laporan_peminjaman.php" class="text-decoration-none">
          <div class="menu-card p-5 text-center">
            <i class="bi bi-file-earmark-bar-graph fs-1 text-primary mb-3"></i>
            <h5>Laporan Peminjaman</h5>
            <p class="text-muted mb-0">Lihat seluruh data peminjaman kendaraan kantor dengan detail lengkap.</p>
          </div>
        </a>
      </div>
    </div>
  </main>

  <!-- Footer -->
  <footer>
    © <?= date('Y'); ?> SIPKK | Sistem Informasi Peminjaman Kendaraan Kantor
  </footer>

</body>
</html>
