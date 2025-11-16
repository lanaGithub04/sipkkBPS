<?php
session_start();
include "../config/koneksi.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] != '3') {
  header("Location: ../index.php");
  exit;
}

$sql = "SELECT p.*, u.nama, k.jenis, k.merk, k.plat_nomor 
        FROM peminjaman p
        JOIN users u ON p.id_u_pinjam = u.id_user
        JOIN kendaraan k ON p.id_k = k.id_kendaraan
        ORDER BY p.id_peminjaman DESC";
$result = mysqli_query($koneksi, $sql);
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <title>Laporan Peminjaman - Pimpinan</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: #f0f4f8;
      font-family: 'Segoe UI', sans-serif;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }

    /* Navbar */
    header {
      background: linear-gradient(90deg, #0d6efd, #0dcaf0);
      color: white;
      padding: 12px 0;
      box-shadow: 0 3px 10px rgba(0, 0, 0, 0.15);
    }

    .navbar-brand {
      font-weight: 700;
      letter-spacing: 0.5px;
    }

    .container-custom {
      background: white;
      border-radius: 16px;
      box-shadow: 0 5px 25px rgba(0, 0, 0, 0.08);
      padding: 30px;
      margin: 50px auto;
      width: 95%;
      max-width: 1200px;
      animation: fadeIn 0.6s ease;
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

    h2 {
      font-weight: 700;
      color: #0d6efd;
      text-align: center;
      margin-bottom: 35px;
    }

    .btn-modern {
      border-radius: 10px;
      font-weight: 600;
      transition: all 0.3s ease;
    }

    .btn-modern:hover {
      transform: translateY(-3px);
      box-shadow: 0 6px 15px rgba(13, 110, 253, 0.3);
    }

    /* Table style */
    .table-responsive {
      border-radius: 14px;
      overflow-x: auto;
    }

    table {
      border-radius: 10px;
      overflow: hidden;
      animation: fadeInTable 0.7s ease;
    }

    @keyframes fadeInTable {
      from {
        opacity: 0;
        transform: translateY(15px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    thead {
      background: linear-gradient(90deg, #0d6efd, #0dcaf0);
      color: white;
    }

    tbody tr:hover {
      background-color: #f1f9ff;
      transform: scale(1.01);
      transition: 0.2s;
    }

    .badge-status {
      padding: 8px 12px;
      border-radius: 8px;
      font-weight: 600;
      font-size: 0.85rem;
    }

    .status-proses {
      background-color: #ffc107;
      color: #212529;
    }

    .status-disetujui {
      background-color: #198754;
      color: white;
    }

    .status-ditolak {
      background-color: #dc3545;
      color: white;
    }

    .status-selesai {
      background-color: #00c851;
      color: white;
    }

    footer {
      background: linear-gradient(90deg, #0d6efd, #0dcaf0);
      color: white;
      text-align: center;
      padding: 15px 0;
      font-size: 0.9rem;
      margin-top: auto;
    }

    /* Responsiveness */
    @media (max-width: 768px) {
      h2 {
        font-size: 1.4rem;
      }

      .container-custom {
        padding: 20px;
      }

      table th,
      table td {
        font-size: 0.85rem;
      }

      .btn-modern {
        font-size: 0.85rem;
      }
    }

    @media (max-width: 576px) {
      .navbar-brand {
        font-size: 1rem;
      }

      .table-responsive {
        border: none;
      }
    }
  </style>
</head>

<body>
<header>
  <nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container">
      <a class="navbar-brand" href="#">🚗 SIPKK Pimpinan</a>
      <a href="dashboard.php" class="btn btn-light btn-sm">🏠 Dashboard</a>
    </div>
  </nav>
</header>

  <div class="container container-custom">
    <h2>📑 Laporan Peminjaman Kendaraan</h2>

    <div class="d-flex justify-content-between flex-wrap gap-2 mb-3">
      <a href="dashboard.php" class="btn btn-outline-primary btn-modern">← Kembali ke Dashboard</a>
    </div>

    <div class="table-responsive mt-4">
      <table class="table table-hover align-middle text-center shadow-sm">
        <thead>
          <tr>
            <th>No</th>
            <th>Nama Pegawai</th>
            <th>Kendaraan</th>
            <th>Tgl Pinjam</th>
            <th>Tgl Kembali</th>
            <th>Jam Pinjam</th>
            <th>Jam Kembali</th>
            <th>Keperluan</th>
            <th>Status</th>
            <th>Alasan Penolakan</th>
          </tr>
        </thead>
        <tbody>
          <?php $no = 1;
          while ($row = mysqli_fetch_assoc($result)):
            $statusClass = 'status-proses';
            $statusText = 'Menunggu';
            if ($row['status'] == '2') {
              $statusClass = 'status-disetujui';
              $statusText = 'Disetujui';
            } elseif ($row['status'] == '3') {
              $statusClass = 'status-ditolak';
              $statusText = 'Ditolak';
            } elseif ($row['status'] == '4') {
              $statusClass = 'status-selesai';
              $statusText = 'Selesai';
            }
          ?>
            <tr>
              <td><?= $no++; ?></td>
              <td><?= htmlspecialchars($row['nama']); ?></td>
              <td><?= htmlspecialchars($row['jenis'] . ' ' . $row['merk'] . ' (' . $row['plat_nomor'] . ')'); ?></td>
              <td><?= date('d-m-Y', strtotime($row['tanggal_pinjam'])); ?></td>
              <td><?= date('d-m-Y', strtotime($row['tanggal_kembali'])); ?></td>
              <td><?= htmlspecialchars($row['jam_pinjam']); ?></td>
              <td><?= htmlspecialchars($row['jam_kembali']); ?></td>
              <td><?= htmlspecialchars($row['keperluan']); ?></td>
              <td><span class="badge-status <?= $statusClass; ?>"><?= $statusText; ?></span></td>
              <td><?= htmlspecialchars($row['alasan_penolakan'] ?? '-'); ?></td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>

  <footer>
    © <?= date('Y'); ?> SIPKK | Sistem Informasi Peminjaman Kendaraan Kantor
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
