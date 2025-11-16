

<?php
ob_start();
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != '2') {
  header("Location: ../index.php");
  exit;
}
include "../config/koneksi.php";



/* --------------------------
   Proses Tambah / Edit / Hapus
   -------------------------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  // Tambah
  if (isset($_POST['tambah'])) {
    $jenis  = mysqli_real_escape_string($koneksi, $_POST['jenis']);
    $merk   = mysqli_real_escape_string($koneksi, $_POST['merk']);
    $plat   = mysqli_real_escape_string($koneksi, $_POST['plat_nomor'] ?? ''); 
    $status = mysqli_real_escape_string($koneksi, $_POST['status']);

    // Cek apakah plat nomor sudah ada
    $cek_plat = mysqli_query($koneksi, "SELECT * FROM kendaraan WHERE plat_nomor = '$plat'");
    if (mysqli_num_rows($cek_plat) > 0) {
      header("Location: kendaraan_tampil.php?aksi=plat_sama");
      exit;
    }

    // Jika tidak ada duplikat, lanjutkan tambah
    $q = "INSERT INTO kendaraan (jenis, merk, plat_nomor, status) VALUES ('$jenis', '$merk', '$plat', '$status')";
    $exec = mysqli_query($koneksi, $q);
    if ($exec) header("Location: kendaraan_tampil.php?aksi=tambah_sukses");
    else header("Location: kendaraan_tampil.php?aksi=gagal");
    exit;
  }

  // Edit
  if (isset($_POST['edit'])) {
    $id = $_POST['id_kendaraan'];
    $merk = mysqli_real_escape_string($koneksi, $_POST['merk']);
    $jenis = mysqli_real_escape_string($koneksi, $_POST['jenis']);
    $plat = strtoupper(trim(mysqli_real_escape_string($koneksi, $_POST['plat_nomor'])));
    $status = $_POST['status'];

    // Cek apakah plat nomor sudah digunakan kendaraan lain
    $cek_plat = mysqli_query($koneksi, "
      SELECT * FROM kendaraan 
      WHERE plat_nomor = '$plat' 
      AND id_kendaraan != '$id'
    ");
    if (mysqli_num_rows($cek_plat) > 0) {
      header("Location: kendaraan_tampil.php?aksi=plat_sama_edit");
      exit;
    }

    // Jika aman, update data
    mysqli_query($koneksi, "
      UPDATE kendaraan SET 
        merk = '$merk',
        jenis = '$jenis',
        plat_nomor = '$plat',
        status = '$status'
      WHERE id_kendaraan = '$id'
    ");

    header("Location: kendaraan_tampil.php?aksi=edit_sukses");
    exit;
  }


  // Hapus
  if (isset($_POST['hapus'])) {
    $id = intval($_POST['id_kendaraan']);
    $q = "DELETE FROM kendaraan WHERE id_kendaraan=$id";
    $exec = mysqli_query($koneksi, $q);
    if ($exec) header("Location: kendaraan_tampil.php?aksi=hapus_sukses");
    else header("Location: kendaraan_tampil.php?aksi=gagal");
    exit;
  }
}

/* --------------------------
   Filter (opsional)
   -------------------------- */
$filter_status = isset($_GET['status']) ? mysqli_real_escape_string($koneksi, $_GET['status']) : '';
$filter_plat   = isset($_GET['plat']) ? mysqli_real_escape_string($koneksi, trim($_GET['plat'])) : '';

$query = "SELECT * FROM kendaraan WHERE 1=1";
if ($filter_status !== '') $query .= " AND status = '".$filter_status."'";
if ($filter_plat !== '') $query .= " AND plat_nomor LIKE '%".$filter_plat."%'";
$query .= " ORDER BY id_kendaraan DESC";
$result = mysqli_query($koneksi, $query);
?>


<?php
if (isset($_GET['aksi']) && $_GET['aksi'] == 'plat_sama') {
  echo "
    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
          icon: 'error',
          title: 'Duplikat Data!',
          text: 'No plat kendaraan tidak boleh sama',
          confirmButtonColor: '#d33',
          confirmButtonText: 'OK'
        });
      });
    </script>
  ";
}
?>

<?php
// --- Notifikasi plat nomor sama (saat edit) ---
if (isset($_GET['aksi']) && $_GET['aksi'] == 'plat_sama_edit') {
  echo "
    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
          icon: 'error',
          title: 'Duplikat Plat Nomor!',
          text: 'No plat kendaraan tidak boleh sama dengan kendaraan lain',
          confirmButtonColor: '#d33',
          confirmButtonText: 'OK'
        });
      });
    </script>
  ";
}
?>


<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Admin - Data Kendaraan</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
  <style>
    body {
      background-color: #f8fafc;
      font-family: 'Poppins', sans-serif;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }
    header { background: linear-gradient(90deg,#007bff,#0dcaf0); }
    .card { border-radius: 12px; box-shadow: 0 6px 16px rgba(0,0,0,0.06); }
    .table th { background:#0d6efd; color:#fff; }
    .action-buttons {
      display: flex;
      justify-content: center;
      gap: 8px;
    }
    .action-buttons .btn { min-width: 70px; }
    footer {
      background: linear-gradient(90deg,#007bff,#0dcaf0);
      color: #fff;
      text-align: center;
      padding: 10px 0;
      margin-top: auto;
      font-size: 14px;
    }
    .modal.fade .modal-dialog {
      display: flex;
      align-items: center;
      min-height: calc(100vh - 1rem);
    }
    .modal-dialog {
      max-width: 560px;
      width: 92%;
      margin: auto;
    }
    .modal-content {
      border-radius: 12px;
      box-shadow: 0 6px 20px rgba(0,0,0,0.12);
      border: none;
    }
    @media (max-width:576px){
      .modal-dialog { width:95%; }
      .action-buttons { flex-direction: column; gap: 6px; }
    }
  </style>
</head>
<body>

<header class="py-2 mb-4 shadow-sm">
  <div class="container d-flex justify-content-between align-items-center">
    <a class="navbar-brand text-white fw-bold" href="dashboard.php">🚘 SIPKK - Admin Kendaraan</a>
    <div>
      <span class="text-white me-3">👋 <?= htmlspecialchars($_SESSION['username'] ?? 'Admin'); ?></span>
      <a href="dashboard.php" class="btn btn-outline-light btn-sm">Dashboard</a>
    </div>
  </div>
</header>

<main class="container mb-5">
  <div class="card p-4">
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
      <h4 class="text-primary fw-bold mb-2">📋 Data Kendaraan</h4>
      <div class="d-flex gap-2">
        <form id="filterForm" method="get" class="d-flex gap-2 align-items-center">
          <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
            <option value="">-- Semua Status --</option>
            <option value="1" <?= $filter_status==='1'?'selected':''; ?>>Tersedia</option>
            <option value="2" <?= $filter_status==='2'?'selected':''; ?>>Diproses</option>
            <option value="3" <?= $filter_status==='3'?'selected':''; ?>>Dipinjam</option>
            <option value="4" <?= $filter_status==='4'?'selected':''; ?>>Rusak</option>
            <option value="5" <?= $filter_status==='5'?'selected':''; ?>>Perbaikan</option>
          </select>
          <input type="text" name="plat" id="platInput" class="form-control form-control-sm" placeholder="Cari plat..." value="<?= htmlspecialchars($filter_plat); ?>">
        </form>
        <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambahKendaraan"><i class="bi bi-plus-circle"></i> Tambah Kendaraan</button>
      </div>
    </div>

    <div class="table-responsive">
      <table class="table table-bordered align-middle text-center mb-0">
        <thead>
          <tr>
            <th>No</th>
            <th>Nama</th>
            <th>Jenis</th>
            <th>Plat Nomor</th>
            <th>Status</th>
            <th style="width:160px">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php if (mysqli_num_rows($result) > 0): $no=1;
          while($row=mysqli_fetch_assoc($result)): ?>
          <tr>
            <td><?= $no++; ?></td>
            <td class="text-start"><?= htmlspecialchars($row['merk']); ?></td>
            <td><?= $row['jenis']=='1'?'Mobil':'Motor Dinas'; ?></td>
            <td><?= htmlspecialchars($row['plat_nomor']); ?></td>
            <td>
              <?php
                $status_map = [
                  '1'=>['Tersedia','success'],
                  '2'=>['Diproses','info'],
                  '3'=>['Dipinjam','warning text-dark'],
                  '4'=>['Rusak','danger'],
                  '5'=>['Perbaikan','primary']
                ];
                $slabel=$status_map[$row['status']][0];
                $sclass=$status_map[$row['status']][1];
              ?>
              <span class="badge bg-<?= $sclass ?>"><?= $slabel ?></span>
            </td>
            <td>
              <div class="action-buttons">
                <button class="btn btn-primary btn-sm"
                        data-bs-toggle="modal" data-bs-target="#modalEditKendaraan"
                        data-id="<?= $row['id_kendaraan'] ?>"
                        data-merk="<?= htmlspecialchars($row['merk'], ENT_QUOTES) ?>"
                        data-jenis="<?= $row['jenis'] ?>"
                        data-plat="<?= htmlspecialchars($row['plat_nomor'], ENT_QUOTES) ?>"
                        data-status="<?= $row['status'] ?>"><i class="bi bi-pencil-square"></i> Edit</button>
                <button class="btn btn-danger btn-sm"
                        data-bs-toggle="modal" data-bs-target="#modalHapus"
                        data-id="<?= $row['id_kendaraan'] ?>"><i class="bi bi-trash"></i> Hapus</button>
              </div>
            </td>
          </tr>
          <?php endwhile; else: ?>
          <tr><td colspan="6" class="text-muted py-3">🚘 Tidak ada data kendaraan.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</main>

<footer>
  <p class="mb-0">© <?= date('Y'); ?> SIPKK | Sistem Informasi Peminjaman Kendaraan Kantor</p>
</footer>

<!-- MODAL TAMBAH -->
<div class="modal fade" id="modalTambahKendaraan" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form method="post">
        <div class="modal-header bg-success text-white">
          <h5 class="modal-title">Tambah Kendaraan</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Nama Kendaraan</label>
            <input type="text" name="merk" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Jenis Kendaraan</label>
            <select name="jenis" class="form-select" required>
              <option value="">-- Pilih Jenis --</option>
              <option value="1">Mobil</option>
              <option value="2">Motor Dinas</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Plat Nomor</label>
            <input type="text" name="plat_nomor" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Status</label>
            <select name="status" class="form-select" required>
              <option value="1">Tersedia</option>
              <option value="2">Diproses</option>
              <option value="3">Dipinjam</option>
              <option value="4">Rusak</option>
              <option value="5">Perbaikan</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" name="tambah" class="btn btn-success">Simpan</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        </div>
      </form>
    </div>
  </div>
</div>


<!-- MODAL EDIT -->
<div class="modal fade" id="modalEditKendaraan" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form method="post">
        <input type="hidden" name="id_kendaraan" id="edit_id_kendaraan">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title">Edit Kendaraan</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Nama Kendaraan</label>
            <input type="text" name="merk" id="edit_merk" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Jenis Kendaraan</label>
            <select name="jenis" id="edit_jenis" class="form-select" required>
              <option value="1">Mobil</option>
              <option value="2">Motor Dinas</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Plat Nomor</label>
            <input type="text" name="plat_nomor" id="edit_plat" class="form-control">
          </div>
          <div class="mb-3">
            <label class="form-label">Status</label>
            <select name="status" id="edit_status" class="form-select" required>
              <option value="1">Tersedia</option>
              <option value="2">Diproses</option>
              <option value="3">Dipinjam</option>
              <option value="4">Rusak</option>
              <option value="5">Perbaikan</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" name="edit" class="btn btn-primary">Update</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- MODAL HAPUS -->
<div class="modal fade" id="modalHapus" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form method="post">
        <input type="hidden" name="id_kendaraan" id="hapus_id">
        <div class="modal-header bg-danger text-white">
          <h5 class="modal-title">Hapus Kendaraan</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body text-center">
          <p>Apakah Anda yakin ingin menghapus kendaraan ini?</p>
        </div>
        <div class="modal-footer justify-content-center">
          <button type="submit" name="hapus" class="btn btn-danger">Ya, Hapus</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
  const modalEdit = document.getElementById('modalEditKendaraan');
  modalEdit.addEventListener('show.bs.modal', function (event) {
    const btn = event.relatedTarget;
    document.getElementById('edit_id_kendaraan').value = btn.getAttribute('data-id');
    document.getElementById('edit_merk').value = btn.getAttribute('data-merk');
    document.getElementById('edit_jenis').value = btn.getAttribute('data-jenis');
    document.getElementById('edit_plat').value = btn.getAttribute('data-plat');
    document.getElementById('edit_status').value = btn.getAttribute('data-status');
  });

  const modalHapus = document.getElementById('modalHapus');
  modalHapus.addEventListener('show.bs.modal', function (event) {
    const btn = event.relatedTarget;
    document.getElementById('hapus_id').value = btn.getAttribute('data-id');
  });
</script>

</body>
</html>
<?php ob_end_flush(); ?>
