<?php
session_start();
include "../config/koneksi.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] != '2') {
  header("Location: ../index.php");
  exit;
}

// ----------------------
// PROSES SETUJU
// ----------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['setuju']) && isset($_POST['id_peminjaman'])) {
  $id_peminjaman = intval($_POST['id_peminjaman']);
  $id_kendaraan = intval($_POST['id_kendaraan']);
  $plat_nomor = trim($_POST['plat_nomor']);

  mysqli_query($koneksi, "UPDATE peminjaman SET status='2', alasan_penolakan=NULL WHERE id_peminjaman='$id_peminjaman'");
  mysqli_query($koneksi, "UPDATE kendaraan SET status='3' WHERE id_kendaraan='$id_kendaraan'");

  $_SESSION['popup'] = [
    "icon" => "success",
    "title" => "Peminjaman Disetujui!",
    "text" => "Peminjaman kendaraan $plat_nomor berhasil disetujui."
  ];
  header("Location: peminjaman_tampil.php");
  exit;
}

// ----------------------
// PROSES TOLAK
// ----------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tolak']) && isset($_POST['id_peminjaman'])) {
  $id_peminjaman = intval($_POST['id_peminjaman']);
  $id_kendaraan = intval($_POST['id_kendaraan']);
  $plat_nomor = trim($_POST['plat_nomor']);
  $alasan = mysqli_real_escape_string($koneksi, trim($_POST['alasan_penolakan']));

  mysqli_query($koneksi, "UPDATE peminjaman SET status='3', alasan_penolakan='$alasan' WHERE id_peminjaman='$id_peminjaman'");
  mysqli_query($koneksi, "UPDATE kendaraan SET status='1' WHERE id_kendaraan='$id_kendaraan'");

  $_SESSION['popup'] = [
    "icon" => "error",
    "title" => "Peminjaman Ditolak!",
    "text" => "Peminjaman kendaraan $plat_nomor telah ditolak."
  ];
  header("Location: peminjaman_tampil.php");
  exit;
}

// --- Proses tombol "Setuju" (kembalikan kendaraan) ---
if (isset($_POST['kembalikan'])) {
  $id_peminjaman = intval($_POST['id_peminjaman']);
  $id_kendaraan  = intval($_POST['id_kendaraan']);

  mysqli_query($koneksi, "UPDATE peminjaman 
                          SET status = '4', dikembalikan = '2'
                          WHERE id_peminjaman = '$id_peminjaman'");
  mysqli_query($koneksi, "UPDATE kendaraan 
                          SET status = '1' 
                          WHERE id_kendaraan = '$id_kendaraan'");

  $_SESSION['popup'] = [
    "icon" => "success",
    "title" => "Kendaraan telah dikembalikan!",
    "text" => "Peminjaman kendaraan $plat_nomor berhasil dikembalikan."
  ];

  header("Location: peminjaman_tampil.php");
  exit;
}
// ----------------------
// FILTER DATA
// ----------------------
$bulan = isset($_GET['bulan']) ? $_GET['bulan'] : date('m');
$tahun = isset($_GET['tahun']) ? $_GET['tahun'] : date('Y');

$query = mysqli_query($koneksi, "
    SELECT 
        p.*, 
        u.nama, 
        k.merk, 
        k.plat_nomor, 
        t.nama_tujuan
    FROM peminjaman p
    JOIN users u ON p.id_u_pinjam = u.id_user
    JOIN kendaraan k ON p.id_k = k.id_kendaraan
    JOIN tujuan_master t ON p.id_tujuan = t.id_tujuan
    WHERE MONTH(p.tanggal_pinjam) = '$bulan' 
      AND YEAR(p.tanggal_pinjam) = '$tahun'
    ORDER BY p.id_peminjaman DESC
");

?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <title>Data Peminjaman Kendaraan</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
  body {
    background-color: #f8fafc;
    font-family: 'Segoe UI', sans-serif;
    margin: 0;
    padding-top: 70px; /* Padding atas untuk menghindari konten tertutup header fixed */
    padding-bottom: 70px; /* Padding bawah untuk menghindari konten tertutup footer fixed */
  }

  header {
    background: linear-gradient(90deg, #0d6efd, #0dcaf0);
    color: white;
    padding: 10px 0;
    position: fixed; /* Membuat header tetap di atas */
    top: 0;
    left: 0;
    right: 0;
    width: 100%;
    z-index: 1000; /* Pastikan header di atas konten lain */
  }

  .navbar-brand {
    font-weight: bold;
  }

  .card {
    border: none;
    border-radius: 16px;
    box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
  }

  .badge-status {
    padding: 6px 10px;
    border-radius: 20px;
    font-weight: 600;
    font-size: 0.85rem;
  }

  .badge-status_dikembalikan {
    display: inline-block;
    text-align: center;
    /* rata tengah teks */
    line-height: 1.2;
    /* biar jarak antar baris tidak terlalu renggang */
    padding: 6px 10px;
    border-radius: 20px;
    font-weight: 600;
    font-size: 0.85rem;
  }

  .status-menunggu {
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
    background-color: #808080;
    color: white;
  }

  footer {
    background: linear-gradient(90deg, #0d6efd, #0dcaf0);
    color: white;
    text-align: center;
    padding: 15px 0;
    font-size: 0.9rem;
    position: fixed; /* Membuat footer tetap di bawah */
    bottom: 0;
    left: 0;
    right: 0;
    width: 100%;
    z-index: 1000; /* Pastikan footer di atas konten lain */
    margin-top: 0; /* Menghapus margin-top yang tidak perlu */
  }

  .table-responsive {
    border-radius: 10px;
    overflow-x: auto;
  }

  table {
    min-width: 900px;
  }

  /* Media queries untuk responsivitas di semua layar elektronik */
  @media (max-width: 768px) {
    body {
      padding-top: 60px; /* Sesuaikan padding untuk header di layar kecil */
      padding-bottom: 60px; /* Sesuaikan padding untuk footer di layar kecil */
    }

    header {
      padding: 8px 0; /* Kurangi padding header di layar kecil */
    }

    footer {
      padding: 10px 0; /* Kurangi padding footer di layar kecil */
      font-size: 0.8rem; /* Kurangi ukuran font di layar kecil */
    }

    .badge-status, .badge-status_dikembalikan {
      font-size: 0.75rem; /* Kurangi ukuran badge di layar kecil */
      padding: 4px 8px;
    }

    table {
      min-width: 100%; /* Buat tabel responsive di layar kecil */
    }
  }

  @media (max-width: 480px) {
    body {
      padding-top: 50px;
      padding-bottom: 50px;
    }

    header {
      padding: 5px 0;
    }

    footer {
      padding: 8px 0;
      font-size: 0.75rem;
    }

    .badge-status, .badge-status_dikembalikan {
      font-size: 0.7rem;
      padding: 3px 6px;
    }
  }

  /* Untuk layar sangat besar, pastikan tetap proporsional */
  @media (min-width: 1200px) {
    header {
      padding: 15px 0; /* Tambah padding di layar besar */
    }

    footer {
      padding: 20px 0;
      font-size: 1rem;
    }
  }
</style>

</head>

<body>
  <header>
    <nav class="navbar navbar-expand-lg navbar-dark shadow-sm">
      <div class="container-fluid px-3">
        <a class="navbar-brand fw-bold" href="dashboard.php">🚘 SIPKK - Admin Peminjaman Tampil</a>
        <div class="collapse navbar-collapse justify-content-end">
          <span class="text-white me-3 fw-semibold d-none d-sm-inline">👋 Halo Admin</span>
          <a href="../admin/dashboard.php" class="btn btn-outline-light btn-sm">Dashboard</a>
        </div>
      </div>
    </nav>
  </header>

  <main class="container my-5">
    <div class="card p-4">
      <h4 class="fw-bold text-primary mb-3 text-center">📑 Data Peminjaman Kendaraan</h4>

      <form method="GET" class="d-flex flex-wrap gap-2 mb-4 justify-content-center">
        <select name="bulan" class="form-select" style="max-width:200px;">
          <?php
          $nama_bulan = [1 => "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
          for ($i = 1; $i <= 12; $i++) {
            $sel = ($i == $bulan) ? "selected" : "";
            echo "<option value='$i' $sel>$nama_bulan[$i]</option>";
          }
          ?>
        </select>
        <select name="tahun" class="form-select" style="max-width:150px;">
          <?php
          $tahun_sekarang = date('Y');
          for ($t = $tahun_sekarang; $t >= 2020; $t--) {
            $sel = ($t == $tahun) ? "selected" : "";
            echo "<option value='$t' $sel>$t</option>";
          }
          ?>
        </select>
        <button class="btn btn-primary">Filter</button>
        <a href="./laporan_pdf.php?bulan=<?= $bulan; ?>&tahun=<?= $tahun; ?>" class="btn btn-success" target="_blank">🖨️ Cetak Laporan</a>
      </form>

      <div class="table-responsive">
        <table class="table table-bordered table-hover text-center align-middle">
          <thead class="table-primary">
            <tr>
              <th>No</th>
              <th>Nama Pegawai</th>
              <th>Kendaraan</th>
              <th>Tgl Pinjam</th>
              <th>Tgl Kembali</th>
              <th>Status</th>
              <th>Status<br>Dikembalikan</th>
              <th>Keperluan</th>
              <th>Tujuan</th> <!-- 🆕 Tambahan -->

              <th>Alasan Penolakan</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $no = 1;
            while ($row = mysqli_fetch_assoc($query)):
              $statusClass = 'status-menunggu';
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

              // --- Status Dikembalikan ---
              $dikembalikanText = 'Belum Dikembalikan';
              if (isset($row['dikembalikan'])) {
                if ($row['dikembalikan'] == '2') {
                  $dikembalikanText = 'Sudah Dikembalikan';
                } elseif ($row['dikembalikan'] == '1') {
                  $dikembalikanText = 'Belum Dikembalikan';
                }
              }
            ?>
              <tr>
                <td><?= $no++; ?></td>
                <td><?= htmlspecialchars($row['nama']); ?></td>
                <td><?= htmlspecialchars($row['merk']) . " (" . $row['plat_nomor'] . ")"; ?></td>
                <td><?= date('d-m-Y', strtotime($row['tanggal_pinjam'])); ?></td>
                <td><?= date('d-m-Y', strtotime($row['tanggal_kembali'])); ?></td>
                <td><span class="badge-status <?= $statusClass; ?>"><?= $statusText; ?></span></td>
                <td style="text-align: center;"><span class="badge-status_dikembalikan <?= $dikembalikanClass; ?>"><?= $dikembalikanText; ?></span></td>
                <td><?= htmlspecialchars($row['keperluan'] ?? '-'); ?></td>
                <td><?= htmlspecialchars($row['nama_tujuan'] ?? '-'); ?></td> <!-- 🆕 Tambahan -->

                <td><?= htmlspecialchars($row['alasan_penolakan'] ?? '-'); ?></td>
                <td>
                  <?php if ($row['status'] == '1'): ?>
                    <!-- Modal Setujui-->
                    <div class="d-flex justify-content-center flex-wrap gap-1">
                      <form method="post">
                        <input type="hidden" name="id_peminjaman" value="<?= $row['id_peminjaman']; ?>">
                        <input type="hidden" name="id_kendaraan" value="<?= $row['id_k']; ?>">
                        <input type="hidden" name="plat_nomor" value="<?= $row['plat_nomor']; ?>">
                        <button type="submit" name="setuju" class="btn btn-success btn-sm">✅ Setujui</button>
                      </form>

                      <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#tolakModal<?= $row['id_peminjaman']; ?>">❌ Tolak</button>
                    </div>

                    <!-- Modal Penolakan -->
                    <div class="modal fade" id="tolakModal<?= $row['id_peminjaman']; ?>" tabindex="-1">
                      <div class="modal-dialog modal-dialog-centered">
                        <form method="post" class="modal-content">
                          <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title">Tolak Peminjaman</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                          </div>
                          <div class="modal-body">
                            <p>Masukkan alasan penolakan untuk kendaraan <b><?= $row['plat_nomor']; ?></b>:</p>
                            <textarea name="alasan_penolakan" class="form-control" rows="3" required></textarea>
                            <input type="hidden" name="id_peminjaman" value="<?= $row['id_peminjaman']; ?>">
                            <input type="hidden" name="id_kendaraan" value="<?= $row['id_k']; ?>">
                          </div>
                          <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" name="tolak" class="btn btn-danger">Kirim Penolakan</button>
                          </div>
                        </form>
                      </div>
                    </div>
                  <?php elseif ($row['dikembalikan'] == '1'): ?>
                    <!-- Modal Kembalikan -->
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
                            <button type="submit" name="kembalikan" class="btn btn-success">Setuju</button>
                          </div>
                        </form>
                      </div>
                    </div>
                  <?php else: ?>
                    <em>-</em>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </div>
  </main>

  <footer>
    © <?= date("Y"); ?> Sistem Informasi Peminjaman Kendaraan Kantor | Admin Panel
  </footer>

  <?php if (isset($_SESSION['popup'])): ?>
    <script>
      Swal.fire({
        icon: '<?= $_SESSION['popup']['icon']; ?>',
        title: '<?= $_SESSION['popup']['title']; ?>',
        text: '<?= $_SESSION['popup']['text']; ?>',
        confirmButtonColor: '#0d6efd'
      });
    </script>
  <?php unset($_SESSION['popup']);
  endif; ?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>