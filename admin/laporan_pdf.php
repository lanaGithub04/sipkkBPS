<?php
require __DIR__ . '/../vendor/autoload.php';
use Dompdf\Dompdf;

include __DIR__ . "/../config/koneksi.php";

// Ambil bulan & tahun dari query string (GET)
$bulan = isset($_GET['bulan']) ? ltrim($_GET['bulan'], '0') : date('n');
$tahun  = isset($_GET['tahun']) ? $_GET['tahun'] : date('Y');

// Nama bulan Indonesia
$bulanIndo = [
  1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
  5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
  9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
];
$namaBulan = isset($bulanIndo[(int)$bulan]) ? $bulanIndo[(int)$bulan] : $bulan;

// Ambil data peminjaman lengkap
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
    ORDER BY p.id_peminjaman ASC
");

// Tanggal cetak
$tanggalCetak = date("d") . " " . $bulanIndo[date("n")] . " " . date("Y");

// Bangun HTML
$html = "
<!doctype html>
<html lang='id'>
<head>
<meta charset='utf-8'>
<title>Laporan Peminjaman Kendaraan</title>
<style>
  body { font-family: Arial, sans-serif; font-size: 12px; margin: 18px; }
  .judul { text-align: center; font-size: 16px; font-weight: bold; margin-bottom: 4px; text-transform: uppercase; }
  .subjudul { text-align: center; font-size: 13px; margin-bottom: 12px; }
  table { width: 100%; border-collapse: collapse; }
  th, td { border: 1px solid #000; padding: 6px; text-align: center; vertical-align: middle; }
  th { background: #f2f2f2; }
  .ttd { width: 280px; float: right; text-align: center; margin-top: 30px; }
</style>
</head>
<body>

<div class='judul'>LAPORAN PEMINJAMAN KENDARAAN</div>
<div class='subjudul'>Bulan {$namaBulan} Tahun {$tahun}</div>

<table>
  <thead>
    <tr>
      <th>No</th>
      <th>Nama Pegawai</th>
      <th>Kendaraan</th>
      <th>Tgl Pinjam</th>
      <th>Tgl Kembali</th>
      <th>Status</th>
      <th>Status Dikembalikan</th>
      <th>Keperluan</th>
      <th>Tujuan</th>
      <th>Alasan Penolakan</th>
    </tr>
  </thead>
  <tbody>
";

// Isi tabel
$no = 1;
if ($query && mysqli_num_rows($query) > 0) {
  while ($row = mysqli_fetch_assoc($query)) {

    // Format tanggal dan jam
    $tglPinjam = !empty($row['tanggal_pinjam']) ? date('d-m-Y', strtotime($row['tanggal_pinjam'])) : '-';
    if (!empty($row['jam_pinjam'])) $tglPinjam .= ' ' . htmlspecialchars($row['jam_pinjam']);

    $tglKembali = !empty($row['tanggal_kembali']) ? date('d-m-Y', strtotime($row['tanggal_kembali'])) : '-';
    if (!empty($row['jam_kembali'])) $tglKembali .= ' ' . htmlspecialchars($row['jam_kembali']);

    // Status pinjam (tampil “-” saja seperti contoh)
    $status = '-';

    // Status dikembalikan
    $dikembalikanText = 'Belum Dikembalikan';
    if (isset($row['dikembalikan']) && ($row['dikembalikan'] == '2' || $row['dikembalikan'] == 2)) {
      $dikembalikanText = 'Sudah Dikembalikan';
    }

    // Kolom lain
    $namaPegawai = htmlspecialchars($row['nama'] ?? '-');
    $kendaraan   = htmlspecialchars($row['merk'] ?? '-') . " (" . htmlspecialchars($row['plat_nomor'] ?? '-') . ")";
    $keperluan   = htmlspecialchars($row['keperluan'] ?? '-');
    $tujuan      = htmlspecialchars($row['nama_tujuan'] ?? '-');
    $alasan      = htmlspecialchars($row['alasan_penolakan'] ?? '-');

    $html .= "
      <tr>
        <td>{$no}</td>
        <td>{$namaPegawai}</td>
        <td>{$kendaraan}</td>
        <td>{$tglPinjam}</td>
        <td>{$tglKembali}</td>
        <td>{$status}</td>
        <td>{$dikembalikanText}</td>
        <td>{$keperluan}</td>
        <td>{$tujuan}</td>
        <td>{$alasan}</td>
      </tr>
    ";
    $no++;
  }
} else {
  $html .= "<tr><td colspan='10'>Tidak ada data peminjaman pada bulan ini.</td></tr>";
}

$html .= "
  </tbody>
</table>

<div class='ttd'>
  Banjarmasin, {$tanggalCetak}<br>
  Pimpinan Instansi<br><br><br><br>
  <b>Nama Pimpinan</b><br>
  NIP. 1234567890
</div>

</body>
</html>
";

// Render PDF
$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'landscape');
$dompdf->render();

header("Content-Type: application/pdf");
header("Content-Disposition: inline; filename=\"laporan_peminjaman_{$bulan}_{$tahun}.pdf\"");
echo $dompdf->output();
exit;
?>
