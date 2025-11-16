<?php
include "../config/koneksi.php";

$bulan = $_GET['bulan'];
$tahun = $_GET['tahun'];

// Set header untuk export Excel
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=laporan_peminjaman_$bulan-$tahun.xls");

// Query data peminjaman + kendaraan + user
$query = mysqli_query($koneksi, "
    SELECT 
        p.*, 
        u.nama, 
        k.merk, 
        k.plat_nomor, 
        k.status AS status_kendaraan
    FROM peminjaman p
    JOIN users u ON p.id_pengguna = u.id_user
    JOIN kendaraan k ON p.id_kendaraan = k.id_kendaraan
    WHERE MONTH(p.tanggal_pinjam) = '$bulan' 
      AND YEAR(p.tanggal_pinjam) = '$tahun'
    ORDER BY p.tanggal_pinjam DESC
");

?>

<h3 style="text-align:center;">Laporan Peminjaman Kendaraan<br>
Bulan <?= $bulan ?> Tahun <?= $tahun ?></h3>

<table border="1" cellpadding="5" cellspacing="0">
  <tr style="background-color:#e0e0e0; font-weight:bold;">
    <th>No</th>
    <th>Nama Peminjam</th>
    <th>Kendaraan</th>
    <th>Tanggal Pinjam</th>
    <th>Tanggal Kembali</th>
    <th>Status Peminjaman</th>
    <th>Status Kendaraan</th>
    <th>Keperluan</th>
    <th>Dikembalikan</th>
  </tr>

  <?php 
  $no = 1; 
  while($row = mysqli_fetch_assoc($query)) { 
      // Ubah format status agar huruf pertama kapital
      $status_peminjaman = ucfirst($row['status']);
      $status_kendaraan = ucfirst($row['status_kendaraan']);
  ?>
  <tr>
    <td><?= $no++; ?></td>
    <td><?= htmlspecialchars($row['nama']); ?></td>
    <td><?= htmlspecialchars($row['merk'])." (".htmlspecialchars($row['plat_nomor']).")"; ?></td>
    <td><?= $row['tanggal_pinjam']." ".$row['jam_pinjam']; ?></td>
    <td><?= $row['tanggal_kembali']." ".$row['jam_kembali']; ?></td>
    <td><?= $status_peminjaman; ?></td>
    <td><?= $status_kendaraan; ?></td>
    <td><?= htmlspecialchars($row['keperluan']); ?></td>
    <td><?= htmlspecialchars($row['dikembalikan']); ?></td>
  </tr>
  <?php } ?>
</table>
