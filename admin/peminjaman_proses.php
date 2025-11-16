<?php
session_start();
include "../config/koneksi.php";

$id = $_GET['id'];
mysqli_query($koneksi, "UPDATE peminjaman SET status='diproses' WHERE id_peminjaman='$id'");
header("Location: peminjaman_tampil.php");

if (!isset($_SESSION['role']) || $_SESSION['role'] != '2') {
    header("Location: ../index.php");
    exit;
}

// Approve via GET: ?id=...&aksi=acc
if (isset($_GET['aksi']) && $_GET['aksi'] == 'acc' && isset($_GET['id'])) {
    $id = mysqli_real_escape_string($koneksi, $_GET['id']);

    // ambil id_kendaraan dulu
    $q = mysqli_query($koneksi, "SELECT id_kendaraan FROM peminjaman WHERE id_peminjaman='$id'");
    if ($q && mysqli_num_rows($q) > 0) {
        $r = mysqli_fetch_assoc($q);
        $id_kend = $r['id_kendaraan'];

        // update peminjaman jadi disetujui dan tandai kendaraan dipinjam
        mysqli_query($koneksi, "UPDATE peminjaman SET status='disetujui', dikembalikan='belum', alasan_penolakan=NULL WHERE id_peminjaman='$id'");
        mysqli_query($koneksi, "UPDATE kendaraan SET status='dipinjam' WHERE id_kendaraan='$id_kend'");
    }

    header("Location: peminjaman_tampil.php");
    exit;
}

// Tolak via POST (dari modal)
if (isset($_POST['tolak']) && isset($_POST['id_peminjaman'])) {
    $id = mysqli_real_escape_string($koneksi, $_POST['id_peminjaman']);
    $alasan = mysqli_real_escape_string($koneksi, $_POST['alasan_penolakan']);

    mysqli_query($koneksi, "UPDATE peminjaman SET status='ditolak', alasan_penolakan='$alasan' WHERE id_peminjaman='$id'");

    // kendaraan tetap tidak berubah (tetap tersedia jika memang sebelumnya tersedia)
    header("Location: peminjaman_tampil.php");
    exit;
}

// fallback
header("Location: peminjaman_tampil.php");
exit;
