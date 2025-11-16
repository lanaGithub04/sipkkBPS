<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != '1') {
  header("Location: ../index.php");
  exit;
}

include "../config/koneksi.php";

$today = date('Y-m-d');
$success = false;

if (isset($_POST['submit'])) {
  $id_user      = $_SESSION['id_user'];
  $selected_id  = $_POST['id_kendaraan'];
  $id_tujuan    = $_POST['id_tujuan'];
  $tgl_pinjam   = $_POST['tanggal_pinjam'];
  $jam_pinjam   = $_POST['jam_pinjam'];
  $tgl_kembali  = $_POST['tanggal_kembali'];
  $jam_kembali  = $_POST['jam_kembali'];
  $keperluan    = mysqli_real_escape_string($koneksi, $_POST['keperluan']);

  // validasi tanggal
  if (strtotime($tgl_kembali) < strtotime($tgl_pinjam)) {
    echo "<script>alert('Tanggal kembali tidak boleh sebelum tanggal pinjam!');</script>";
  } elseif (strtotime($tgl_pinjam) < strtotime($today)) {
    echo "<script>alert('Tanggal pinjam tidak boleh sebelum hari ini!');</script>";
  } else {

// Pastikan koneksi aktif
include "../config/koneksi.php"; // ubah path sesuai posisi file kamu

    // 🔍 CEK BENTROK WAKTU PEMINJAMAN
    $query_bentrok = "
      SELECT jam_pinjam, jam_kembali 
      FROM peminjaman
      WHERE id_k = '$selected_id'
        AND tanggal_pinjam = '$tgl_pinjam'
        AND (
          ('$jam_pinjam' BETWEEN jam_pinjam AND jam_kembali)
          OR ('$jam_kembali' BETWEEN jam_pinjam AND jam_kembali)
          OR (jam_pinjam BETWEEN '$jam_pinjam' AND '$jam_kembali')
        )
        AND status IN ('1','2','disetujui','dipinjam')
    ";

    $cek_bentrok = mysqli_query($koneksi, $query_bentrok);

    // ✅ Tambah pengecekan error SQL
    if (!$cek_bentrok) {
      die("Query gagal: " . mysqli_error($koneksi) . "<br>SQL: " . $query_bentrok);
    }

    // 🔥 Jika bentrok
    if (mysqli_num_rows($cek_bentrok) > 0) {
      $row_bentrok = mysqli_fetch_assoc($cek_bentrok);
      $jam_mulai_bentrok = date('H:i', strtotime($row_bentrok['jam_pinjam']));
      $jam_selesai_bentrok = date('H:i', strtotime($row_bentrok['jam_kembali']));

      echo "
      <!DOCTYPE html>
      <html lang='id'>
      <head>
        <meta charset='UTF-8'>
        <title>Jadwal Bentrok</title>
        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css'/>
      </head>
      <body>
        <script>
          document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
              icon: 'error',
              title: 'Jadwal Bentrok!',
              html: `Mobil sudah dipinjam dari <b>$jam_mulai_bentrok</b> s.d. <b>$jam_selesai_bentrok</b>.<br>Silakan pilih waktu setelah <b>$jam_selesai_bentrok</b>.`,
              confirmButtonText: 'OK',
              confirmButtonColor: '#d33',
              showClass: { popup: 'animate__animated animate__shakeX' },
              hideClass: { popup: 'animate__animated animate__fadeOut' }
            }).then(() => {
              window.history.back();
            });
          });
        </script>
      </body>
      </html>
      ";
      exit;
    }else {

      // cek status kendaraan
      $cek_sql = "SELECT * FROM kendaraan WHERE id_kendaraan = '$selected_id' LIMIT 1";
      $cek = mysqli_query($koneksi, $cek_sql);
      if (!$cek) {
        die('Query cek kendaraan gagal: ' . mysqli_error($koneksi));
      }

      $data = mysqli_fetch_assoc($cek);
      $veh_status = isset($data['status']) ? strtolower(trim($data['status'])) : '';
      $blocked_numeric = in_array($veh_status, ['2', '3', '4', '5']);

      if ($veh_status === 'sedang dalam peminjaman' || $veh_status === 'dipinjam' || $blocked_numeric) {
        echo "<script>alert('Mobil sedang tidak tersedia (sudah dipinjam atau dalam proses).');</script>";
      } elseif ($veh_status === 'rusak') {
        echo "<script>alert('Mobil rusak dan tidak bisa dipinjam.');</script>";
      } else {

        // Simpan data peminjaman
        $insert_sql = "
          INSERT INTO peminjaman 
            (id_u_pinjam, id_k, id_tujuan, tanggal_pinjam, jam_pinjam, tanggal_kembali, jam_kembali, keperluan, status, dikembalikan)
          VALUES
            ('$id_user', '$selected_id', '$id_tujuan', '$tgl_pinjam', '$jam_pinjam', '$tgl_kembali', '$jam_kembali', '$keperluan', '1', '1')
        ";
        
        $query = mysqli_query($koneksi, $insert_sql);

        // Ambil nama tujuan
        $q_tujuan = mysqli_query($koneksi, "SELECT nama_tujuan FROM tujuan_master WHERE id_tujuan = '$id_tujuan'");
        $data_tujuan = mysqli_fetch_assoc($q_tujuan);
        $nama_tujuan = $data_tujuan['nama_tujuan'] ?? '-';

        // Ambil data kendaraan
        $q_kendaraan = mysqli_query($koneksi, "SELECT merk, plat_nomor FROM kendaraan WHERE id_kendaraan = '$selected_id'");
        $data_kendaraan = mysqli_fetch_assoc($q_kendaraan);
        $nama_kendaraan = $data_kendaraan['merk'] ?? '-';
        $nopol = $data_kendaraan['plat_nomor'] ?? '-';

        // Ambil nama pegawai
        $q_user = mysqli_query($koneksi, "SELECT nama FROM users WHERE id_user = '$id_user'");
        $data_user = mysqli_fetch_assoc($q_user);
        $nama_pegawai = $data_user['nama'] ?? '-';

        // 🔔 Kirim Notifikasi WA
        if ($query) {
          include_once "../config/whatsapp_send.php";
          $nomor_admin = "6281549112543";
          $pesan = "📢 *Peminjaman Baru Diajukan*\n\n"
                . "👤 Pegawai: *$nama_pegawai*\n"
                . "🚗 Kendaraan: *$nama_kendaraan* ($nopol)\n"
                . "📍 Tujuan: *$nama_tujuan*\n"
                . "📅 Tgl Pinjam: *$tgl_pinjam* ($jam_pinjam)*\n"
                . "📅 Tgl Kembali: *$tgl_kembali* ($jam_kembali)*\n"
                . "📝 Keperluan: $keperluan\n\n"
                . "Silakan cek detail di *Sistem SIPKK*.";
          $hasil_kirim = kirim_wa($nomor_admin, $pesan);

          file_put_contents(__DIR__ . '/../config/wa_log.txt',
            date('Y-m-d H:i:s') . " | WA ke: $nomor_admin | " . $hasil_kirim . PHP_EOL,
            FILE_APPEND
          );
        }
      }
    }
  }
}

$query = "
  SELECT 
    k.*,
    CASE 
      WHEN EXISTS (
        SELECT 1 FROM peminjaman p
        WHERE p.id_k = k.id_kendaraan
          AND (p.dikembalikan = '0' OR p.dikembalikan IS NULL)
          AND (p.status IN ('2','disetujui','dipinjam','3'))
      ) THEN 'Dipinjam'
      ELSE k.status
    END AS status_kendaraan
  FROM kendaraan k
  ORDER BY k.merk ASC
";
$kendaraan = mysqli_query($koneksi, $query);
if (!$kendaraan) {
  die("Query kendaraan gagal: " . mysqli_error($koneksi));
}
?>


<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Ajukan Peminjaman - SIPKK</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
body {
  background: #f8fafc;
  font-family: 'Segoe UI', sans-serif;
  margin: 0;
  padding: 0;
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  line-height: 1.6;
  color: #333;
}

.navbar {
  background: linear-gradient(90deg, #0d6efd, #0dcaf0);
  padding: 10px 15px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}
.navbar-brand {
  font-size: 18px;
  font-weight: 600;
  color: white !important;
  display: flex;
  align-items: center;
  gap: 8px;
}
.navbar-nav .nav-link {
  color: white !important;
  font-size: 16px;
  padding: 8px 12px;
}
.navbar-toggler {
  border: none;
  background: rgba(255,255,255,0.2);
}

.card {
  border: none;
  border-radius: 12px;
  box-shadow: 0 3px 12px rgba(0,0,0,0.08);
  background: white;
  margin-bottom: 20px;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  overflow: hidden; 
}
.card:hover {
  transform: translateY(-5px);
  box-shadow: 0 6px 20px rgba(0,0,0,0.15);
}
.card-body {
  padding: 20px;
}
.card-title {
  font-size: 18px;
  font-weight: 600;
  margin-bottom: 10px;
}
.card-text {
  font-size: 14px;
  color: #666;
}

.card-grid {
  display: grid;
  gap: 20px;
  margin: 20px 0;
  padding: 0 15px;
}

@media (min-width: 1200px) {
  .card-grid {
    grid-template-columns: repeat(4, 1fr);
  }
}

@media (min-width: 992px) and (max-width: 1199px) {
  .card-grid {
    grid-template-columns: repeat(3, 1fr);
  }
}
/* Tablet: 2 kolom */
@media (min-width: 768px) and (max-width: 991px) {
  .card-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}
/* Mobile: 1 kolom */
@media (max-width: 767px) {
  .card-grid {
    grid-template-columns: 1fr;
    padding: 0 10px;
  }
}


.stat-card {
  text-align: center;
  padding: 25px;
  min-height: 150px; 
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
}
.stat-icon {
  font-size: 40px;
  margin-bottom: 10px;
}
.stat-card h3 {
  font-size: 24px;
  font-weight: bold;
  margin: 0;
}
.stat-card p {
  font-size: 14px;
  margin: 5px 0 0;
}

.menu-card {
  text-align: center;
  padding: 30px 20px;
  min-height: 200px; /* Tinggi seragam */
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  align-items: center;
  cursor: pointer;
}
.menu-card i {
  font-size: 50px;
  margin-bottom: 15px;
  transition: transform 0.3s ease;
}
.menu-card:hover i {
  transform: scale(1.1);
}
.menu-card h5 {
  font-size: 16px;
  font-weight: 600;
  margin: 0 0 10px;
}
.menu-card p {
  font-size: 13px;
  color: #666;
  margin: 0;
  flex-grow: 1;
}
.menu-card a {
  margin-top: 15px;
  padding: 8px 16px;
  background: #0d6efd;
  color: white;
  text-decoration: none;
  border-radius: 8px;
  font-size: 14px;
  transition: background 0.3s ease;
}
.menu-card a:hover {
  background: #0b5ed7;
}


footer {
  background: linear-gradient(90deg, #0d6efd, #0dcaf0);
  color: #fff;
  padding: 12px 0;
  text-align: center;
  margin-top: auto; /* Push footer ke bawah */
  font-size: 14px;
  clear: both;
}

@media (max-width: 767px) {
  body {
    font-size: 14px;
  }
  .navbar-brand {
    font-size: 16px;
  }
  .navbar-nav .nav-link {
    font-size: 14px;
    padding: 6px 10px;
  }
  .card-body {
    padding: 15px;
  }
  .card-title {
    font-size: 16px;
  }
  .card-text {
    font-size: 13px;
  }
  .stat-card {
    padding: 20px;
    min-height: 120px;
  }
  .stat-icon {
    font-size: 35px;
  }
  .stat-card h3 {
    font-size: 20px;
  }
  .menu-card {
    padding: 20px 15px;
    min-height: 160px;
  }
  .menu-card i {
    font-size: 40px;
  }
  .menu-card h5 {
    font-size: 14px;
  }
  .menu-card p {
    font-size: 12px;
  }
  footer {
    font-size: 12px;
    padding: 10px 0;
  }
}

@media (min-width: 768px) and (max-width: 991px) {
  .card-title {
    font-size: 17px;
  }
  .stat-card h3 {
    font-size: 22px;
  }
  .menu-card h5 {
    font-size: 15px;
  }
}

/* Utility - Pastikan tidak ada overflow */
* {
  box-sizing: border-box;
}
img {
  max-width: 100%;
  height: auto;
}
</style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark" style="background: linear-gradient(90deg,#0d6efd,#0dcaf0);">
  <div class="container">
    <a class="navbar-brand fw-bold" href="dashboard.php"><i class="bi bi-car-front"></i> SIPKK - Pegawai</a>
    <div class="d-flex align-items-center">
      <span class="text-white me-3">👋 Halo, <?= htmlspecialchars($_SESSION['username']); ?></span>
      <a href="../pegawai/dashboard.php" class="btn btn-outline-light btn-sm"><i class="bi bi-box-arrow-right"></i> Dashboard</a>
    </div>
  </div>
</nav>

<main class="container my-5">
  <div class="text-center mb-4">
    <h3 class="fw-bold text-primary">🚗 Ajukan Peminjaman Kendaraan</h3>
    <p class="text-muted">Isi form di bawah untuk mengajukan peminjaman kendaraan</p>
  </div>

  <div class="row g-4">
<!-- Formulir Pengajuan -->
<div class="col-lg-6">
  <div class="card h-100">
    <div class="card-header bg-primary text-white">
      <i class="bi bi-pencil-square me-2"></i> Formulir Pengajuan
    </div>
    <div class="card-body p-4">
      <form method="POST">
        <div class="mb-3">
          <label class="form-label">Pilih Kendaraan</label>
          <select id="id_kendaraan" name="id_kendaraan" class="form-select" required>
            <option value="">-- Pilih Kendaraan --</option>
            <?php
            mysqli_data_seek($kendaraan, 0);
            while ($r = mysqli_fetch_assoc($kendaraan)) {
              $val_id = isset($r['id_kendaraan']) ? $r['id_kendaraan'] : (isset($r['id_k']) ? $r['id_k'] : '');
              $statusVal = strtolower(trim($r['status_kendaraan'] ?? $r['status'] ?? ''));
              if ($statusVal === '1' || $statusVal === 'tersedia' || $statusVal === 'sehat') {
                $statusText = '🟢 Tersedia'; $disabled = '';
              } elseif (in_array($statusVal, ['2','diproses','sedang dalam pengajuan'])) {
                $statusText = '🟡 Diproses'; $disabled = 'disabled';
              } elseif (in_array($statusVal, ['3','dipinjam','sedang dalam peminjaman','disetujui'])) {
                $statusText = '🔵 Dipinjam'; $disabled = 'disabled';
              } elseif (in_array($statusVal, ['4','rusak'])) {
                $statusText = '🔴 Rusak'; $disabled = 'disabled';
              } elseif (in_array($statusVal, ['5','perbaikan'])) {
                $statusText = '🟠 Perbaikan'; $disabled = 'disabled';
              } else {
                $statusText = '⚪ Tidak Diketahui'; $disabled = 'disabled';
              }

              echo '<option value="' . htmlspecialchars($val_id) . '" ' . $disabled . '>';
              echo htmlspecialchars($r['merk'] ?? '') . ' (' . htmlspecialchars($r['plat_nomor'] ?? '') . ') - ' . $statusText;
              echo '</option>';
            }
            ?>
          </select>
          <small class="text-muted d-block mt-2">
            Hanya kendaraan <span class="fw-semibold text-success">Tersedia</span> yang dapat diajukan.
          </small>
        </div>

        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Tanggal Pinjam</label>
            <input type="date" name="tanggal_pinjam" class="form-control" min="<?= $minDate ?>" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">Jam Pinjam</label>
            <input type="time" name="jam_pinjam" class="form-control" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">Tanggal Kembali</label>
            <input type="date" name="tanggal_kembali" class="form-control" min="<?= $minDate ?>" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">Jam Kembali</label>
            <input type="time" name="jam_kembali" class="form-control" required>
          </div>
        </div>

        <div class="mt-3">
          <label class="form-label">Kabupaten Kota Tujuan <label>
          <select name="id_tujuan" class="form-select" required>
            <option value="">-- Pilih Tujuan --</option>
            <?php
              $sql = mysqli_query($koneksi, "SELECT * FROM tujuan_master ORDER BY nama_tujuan ASC");
              while ($data = mysqli_fetch_array($sql)) {
                echo "<option value='$data[id_tujuan]'>$data[nama_tujuan]</option>";
              }
            ?>
          </select>
        </div>

        <div class="mt-3">
          <label class="form-label">Keperluan</label>
          <textarea name="keperluan" class="form-control" rows="3" placeholder="Tuliskan Keperluan, Nama Pengemudi, dan Penumpang" required></textarea>
        </div>

        <div class="mt-4 text-end">
          <button type="submit" name="submit" class="btn btn-success px-4">
            <i class="bi bi-send-check"></i> Ajukan
          </button>
          <a href="dashboard.php" class="btn btn-secondary px-4">Batal</a>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Detail Kendaraan -->
<div class="col-lg-6">
  <div class="card h-100">
    <div class="card-header bg-dark text-white">
      <i class="bi bi-car-front me-2"></i> Detail & Riwayat Kendaraan
    </div>
    <div class="card-body p-3">
      <div class="table-responsive">
        <table id="tabelDetail" class="table table-bordered align-middle">
          <thead class="table-light">
            <tr class="text-center">
              <th>No</th>
              <th>Kendaraan</th>
              <th>Peminjam</th>
              <th>Tgl & Jam Pinjam</th>
              <th>Tgl & Jam Kembali</th>
              <th>Keperluan</th>
              <th>Tujuan</th>
            </tr>
          </thead>
          <tbody>
            <tr><td colspan="6" class="text-center text-muted">Pilih kendaraan untuk melihat detail</td></tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- JS Fetch -->
<script>
document.getElementById('id_kendaraan').addEventListener('change', function() {
  const id = this.value;
  const tbody = document.querySelector('#tabelDetail tbody');
  if (!id) {
    tbody.innerHTML = `<tr><td colspan="6" class="text-center text-muted">Pilih kendaraan untuk melihat detail</td></tr>`;
    return;
  }

  fetch(`get_detail_kendaraan.php?id=${id}`)
    .then(res => res.json())
    .then(data => {
      tbody.innerHTML = '';
      if (data.length > 0) {
        data.forEach((d, i) => {
          tbody.innerHTML += `
            <tr>
              <td class="text-center">${i+1}</td>
              <td>${d.merk} (${d.plat_nomor})</td>
              <td>${d.nama_peminjam}</td>
              <td>${d.tanggal_pinjam} ${d.jam_pinjam}</td>
              <td>${d.tanggal_kembali} ${d.jam_kembali}</td>
              <td>${d.keperluan}</td>
              <td>${d.tujuan}</td>
            </tr>`;
        });
      } else {
        tbody.innerHTML = `<tr><td colspan="6" class="text-center text-muted">Belum ada riwayat peminjaman kendaraan ini</td></tr>`;
      }
    })
    .catch(err => {
      tbody.innerHTML = `<tr><td colspan="6" class="text-center text-danger">Terjadi kesalahan saat mengambil data.</td></tr>`;
      console.error(err);
    });
});
</script>

  </div>
</main>

<footer>© <?= date("Y"); ?> SIPKK - Sistem Informasi Peminjaman Kendaraan Kantor</footer>

<?php if ($success): ?>
<script>
Swal.fire({
  title: "Berhasil!",
  text: "Peminjaman kendaraan berhasil diajukan 🎉",
  icon: "success",
  showConfirmButton: false,
  timer: 1800
}).then(() => { window.location = "peminjaman_tampil.php"; });
</script>
<?php endif; ?>

</body>
</html>
