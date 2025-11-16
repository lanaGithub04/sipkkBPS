<?php
session_start();
include "../config/koneksi.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] != '2') {
  header("Location: ../index.php");
  exit;
}

// Ambil parameter pencarian & filter
$search = isset($_GET['search']) ? $_GET['search'] : '';
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';

// Query dengan filter
$sql = "SELECT p.*, u.nama, k.jenis, k.merk, k.plat_nomor 
        FROM peminjaman p
        JOIN users u ON p.id_user=u.id_user
        JOIN kendaraan k ON p.id_kendaraan=k.id_kendaraan
        WHERE 1=1";

if ($search != '') {
  $sql .= " AND u.nama LIKE '%$search%'";
}
if ($status_filter != '') {
  $sql .= " AND p.status = '$status_filter'";
}

$sql .= " ORDER BY p.id_peminjaman DESC";
$result = mysqli_query($koneksi, $sql);
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <title>Kelola Peminjaman</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .badge-status {
      font-size: 13px;
      padding: 6px 12px;
      border-radius: 20px;
    }

    .badge-pending {
      background-color: #fff3cd;
      color: #856404;
    }

    .badge-disetujui {
      background-color: #d4edda;
      color: #155724;
    }

    .badge-ditolak {
      background-color: #f8d7da;
      color: #721c24;
    }
  </style>
</head>

<body class="container mt-5">
  <h2 class="mb-4">📋 Data Peminjaman</h2>
  <a href="dashboard.php" class="btn btn-secondary mb-3">⬅ Kembali</a>

  <!-- 🔎 Form Pencarian & Filter -->
  <form class="row g-2 mb-4" method="GET" action="">
    <div class="col-md-4">
      <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" class="form-control" placeholder="Cari nama pegawai...">
    </div>
    <div class="col-md-3">
      <select name="status" class="form-select">
        <option value="">-- Semua Status --</option>
        <option value="pending" <?= ($status_filter == 'pending' ? 'selected' : '') ?>>Pending</option>
        <option value="disetujui" <?= ($status_filter == 'disetujui' ? 'selected' : '') ?>>Disetujui</option>
        <option value="ditolak" <?= ($status_filter == 'ditolak' ? 'selected' : '') ?>>Ditolak</option>
      </select>
    </div>
    <div class="col-md-2">
      <button type="submit" class="btn btn-primary w-100">🔎 Cari</button>
    </div>
    <div class="col-md-2">
      <a href="kelola_peminjaman.php" class="btn btn-outline-secondary w-100">Reset</a>
    </div>
  </form>

  <!-- 📊 Tabel -->
  <table class="table table-striped table-bordered align-middle shadow-sm">
    <thead class="table-dark text-center">
      <tr>
        <th>No</th>
        <th>Nama Pegawai</th>
        <th>Kendaraan</th>
        <th>Tanggal Pinjam</th>
        <th>Tanggal Kembali</th>
        <th>Keperluan</th>
        <th>Status</th>
        <th>Aksi</th>
      </tr>
    </thead>
    <tbody>
      <?php
      if (mysqli_num_rows($result) > 0) {
        $no = 1;
        while ($row = mysqli_fetch_assoc($result)) { ?>
          <tr>
            <td class="text-center"><?= $no++; ?></td>
            <td><?= htmlspecialchars($row['nama']); ?></td>
            <td><?= $row['jenis'] . " - " . $row['merk'] . " (" . $row['plat_nomor'] . ")"; ?></td>
            <td class="text-center"><?= $row['tanggal_pinjam']; ?></td>
            <td class="text-center"><?= $row['tanggal_kembali']; ?></td>
            <td><?= htmlspecialchars($row['keperluan']); ?></td>
            <td class="text-center">
              <?php if ($row['status'] == 'pending') { ?>
                <span class="badge badge-status badge-pending">⏳ Pending</span>
              <?php } elseif ($row['status'] == 'disetujui') { ?>
                <span class="badge badge-status badge-disetujui">✅ Disetujui</span>
              <?php } else { ?>
                <span class="badge badge-status badge-ditolak">❌ Ditolak</span>
              <?php } ?>
            </td>
            <td class="text-center">
              <?php if ($row['status'] == 'pending') { ?>
                <a href="peminjaman_proses.php?id=<?= $row['id_peminjaman']; ?>&aksi=disetujui"
                  class="btn btn-sm btn-success">✔ Setujui</a>
                <a href="peminjaman_proses.php?id=<?= $row['id_peminjaman']; ?>&aksi=ditolak"
                  class="btn btn-sm btn-danger">✖ Tolak</a>
              <?php } else {
                echo "-";
              } ?>
            </td>
          </tr>
      <?php }
      } else {
        echo "<tr><td colspan='8' class='text-center text-muted'>Tidak ada data ditemukan</td></tr>";
      } ?>
    </tbody>
  </table>
</body>

</html>