<?php
include "../config/koneksi.php";

$id = $_GET['id'] ?? 0;
$data = [];

// Query menyesuaikan struktur tabel di database
$q = mysqli_query($koneksi, "
  SELECT 
    p.id_peminjaman,
    p.tanggal_pinjam,
    p.jam_pinjam,
    p.tanggal_kembali,
    p.jam_kembali,
    p.keperluan,
    k.merk,
    k.plat_nomor,
    u.nama AS nama_peminjam,
    tm.nama_tujuan AS tujuan
  FROM peminjaman p
  JOIN kendaraan k ON p.id_k = k.id_kendaraan
  JOIN users u ON p.id_u_pinjam = u.id_user
  LEFT JOIN tujuan_master tm ON p.id_tujuan = tm.id_tujuan
  WHERE p.id_k = '$id'
  ORDER BY p.id_peminjaman DESC
  LIMIT 5
");

if (!$q) {
  http_response_code(500);
  echo json_encode(["error" => mysqli_error($koneksi)]);
  exit;
}

while ($r = mysqli_fetch_assoc($q)) {
  $data[] = $r;
}

header('Content-Type: application/json');
echo json_encode($data);
?>
