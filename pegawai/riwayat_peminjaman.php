<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != '1') {
  header("Location: ../index.php");
  exit;
}
include "../config/koneksi.php";

$id_user = $_SESSION['id_user'];

// --- Proses tombol "Setuju" (kembalikan kendaraan) ---
if (isset($_POST['setuju'])) {
  $id_peminjaman = intval($_POST['id_peminjaman']);
  $id_kendaraan  = intval($_POST['id_kendaraan']);

  mysqli_query($koneksi, "UPDATE peminjaman 
                          SET status = '4', dikembalikan = '2'
                          WHERE id_peminjaman = '$id_peminjaman'");
  mysqli_query($koneksi, "UPDATE kendaraan 
                          SET status = '1' 
                          WHERE id_kendaraan = '$id_kendaraan'");

  header("Location: " . $_SERVER['PHP_SELF']);
  exit;
}
// --- Query daftar peminjaman milik user + ambil nama tujuan ---
$sql = "
  SELECT 
    p.*, 
    u.username, 
    k.merk, 
    k.plat_nomor, 
    t.nama_tujuan
  FROM peminjaman AS p
  LEFT JOIN users AS u ON p.id_u_pinjam = u.id_user
  LEFT JOIN kendaraan AS k ON p.id_k = k.id_kendaraan
  LEFT JOIN tujuan_master AS t ON p.id_tujuan = t.id_tujuan
  WHERE p.id_u_pinjam = '$id_user'
  ORDER BY p.tanggal_pinjam DESC
";
$kendaraan = mysqli_query($koneksi, $sql);

?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <title>Riwayat Peminjaman - SIPKK</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body {
      background-color: #f8fafc;
      font-family: 'Segoe UI', sans-serif;
      display: flex;
      flex-direction: column;
      min-height: 100vh;
    }

    main {
      flex: 1;
    }

    .card {
      border: none;
      border-radius: 15px;
      box-shadow: 0 3px 12px rgba(0, 0, 0, 0.08);
      overflow: hidden;
    }

    table {
      border-collapse: collapse;
      width: 100%;
      margin: auto;
      table-layout: fixed;
    }

    .table th {
      background-color: #fff;
      text-align: center;
      white-space: nowrap;
      vertical-align: middle;
    }

    .table td {
      border: 1px solid #dee2e6;
      padding: 10px 14px;
      vertical-align: middle;
      word-wrap: break-word;
      word-break: break-word;
    }

    .table td.text-center {
      text-align: center;
    }

    .table tbody tr:hover {
      background-color: #f1f6ff;
      transition: 0.3s;
    }

    .badge {
      padding: 6px 10px;
      border-radius: 8px;
    }

    .table td.long-text {
      text-align: left;
      white-space: normal;
    }

    .table-responsive {
      border-radius: 12px;
      overflow-x: auto;
    }

    .btn-success {
      background: linear-gradient(90deg, #198754, #20c997);
      border: none;
    }

    .btn-success:hover {
      background: linear-gradient(90deg, #157347, #198754);
    }

    footer {
      background: linear-gradient(90deg, #0d6efd, #0dcaf0);
      color: white;
      text-align: center;
      padding: 12px 0;
      margin-top: auto;
    }

    .navbar {
      background: linear-gradient(90deg, #0d6efd, #0dcaf0);
    }

    .navbar-brand {
      font-weight: bold;
    }
  </style>
</head>

<body>
  <!-- Header -->
  <nav class="navbar navbar-expand-lg navbar-dark shadow-sm">
    <div class="container">
      <a class="navbar-brand d-flex align-items-center gap-2" href="dashboard.php">
        <i class="bi bi-clock-history"></i> Riwayat Peminjaman
      </a>
      <div class="d-flex align-items-center">
        <span class="text-white me-3">👋 Halo, <?= $_SESSION['username']; ?></span>
        <a href="../pegawai/dashboard.php" class="btn btn-outline-light btn-sm">
          <i class="bi bi-box-arrow-right"></i> Dashboard
        </a>
      </div>
    </div>
  </nav>

  <!-- Main Content -->
  <main class="container my-5">
    <div class="text-center mb-4">
      <h3 class="fw-bold text-primary">📜 Riwayat Peminjaman Kendaraan</h3>
      <p class="text-muted">Lihat seluruh data peminjaman yang pernah Anda lakukan</p>
    </div>

    <div class="card">
      <div class="card-header bg-dark text-white d-flex align-items-center">
        <i class="bi bi-list-check me-2"></i> Daftar Riwayat Peminjaman
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-bordered mb-0 align-middle text-center">
            <thead>
              <tr>
                <th>No</th>
                <th>Merk</th>
                <th>Plat Nomor</th>
                <th>Tgl & Jam Pinjam</th>
                <th>Tgl & Jam Kembali</th>
                <th>Keperluan</th>
                <th>Tujuan</th> <!-- 🆕 Tambahan -->
                <th>Alasan Penolakan</th>
                <th>Dikembalikan</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $no = 1;
              while ($row = mysqli_fetch_assoc($kendaraan)) { ?>
                <tr>
                  <td><?= $no++; ?></td>
                  <td><?= htmlspecialchars($row['merk']); ?></td>
                  <td><?= htmlspecialchars($row['plat_nomor']); ?></td>
                  <td><?= date('d-m-Y', strtotime($row['tanggal_pinjam'])); ?> <br><span class="text-muted"><?= $row['jam_pinjam']; ?></span></td>
                  <td><?= date('d-m-Y', strtotime($row['tanggal_kembali'])); ?> <br><span class="text-muted"><?= $row['jam_kembali']; ?></span></td>
                  <td><?= htmlspecialchars($row['keperluan']); ?></td>
                  <td><?= htmlspecialchars($row['nama_tujuan'] ?? '-'); ?></td> <!-- 🆕 Tambahan -->
                  <td><?= !empty($row['alasan_penolakan']) ? htmlspecialchars($row['alasan_penolakan']) : '-'; ?></td>
                  <td>
                    <?php if ($row['dikembalikan'] == '1') { ?>
                      <span class="badge bg-danger">Belum <br> Dikembalikan</span>
                    <?php } elseif ($row['dikembalikan'] == '2') { ?>
                      <span class="badge bg-success">Dikembalikan</span>
                    <?php } else { ?>
                      <span class="badge bg-secondary">-</span>
                    <?php } ?>
                  </td>
                  <td>
                    <?php if ($row['dikembalikan'] == '1') { ?>
                      <button class="btn btn-sm btn-success"
                        data-bs-toggle="modal"
                        data-bs-target="#kembaliModal<?= $row['id_peminjaman']; ?>">
                        <i class="bi bi-check2-circle"></i> Kembalikan
                      </button>

                      <!-- Modal Konfirmasi -->
                      <div class="modal fade" id="kembaliModal<?= $row['id_peminjaman']; ?>" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog">
                          <form method="post" class="modal-content">
                            <div class="modal-header">
                              <h5 class="modal-title">Konfirmasi Pengembalian</h5>
                              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                              <p>Apakah Anda yakin ingin mengembalikan kendaraan 
                                <b><?= htmlspecialchars($row['merk']); ?></b> 
                                (<?= htmlspecialchars($row['plat_nomor']); ?>)?
                              </p>
                              <input type="hidden" name="id_peminjaman" value="<?= $row['id_peminjaman']; ?>">
                              <input type="hidden" name="id_kendaraan" value="<?= $row['id_k']; ?>">
                            </div>
                            <div class="modal-footer">
                              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                              <button type="submit" name="setuju" class="btn btn-success">Setuju</button>
                            </div>
                          </form>
                        </div>
                      </div>
                    <?php } else {
                      echo '<span class="text-muted">-</span>';
                    } ?>
                  </td>
                </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </main>

  <footer>
    © <?= date("Y"); ?> SIPKK - Sistem Informasi Peminjaman Kendaraan Kantor
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
