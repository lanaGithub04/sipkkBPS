<?php
session_start();
include "../config/koneksi.php";

// Cek role admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != '2') {
    header("Location: ../index.php");
    exit();
}

// Cek apakah ada ID dikirim
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Jalankan query hapus
    $query = "DELETE FROM kendaraan WHERE id_kendaraan='$id'";
    $result = mysqli_query($koneksi, $query);

    if ($result) {
        echo "<script>alert('Data berhasil dihapus');window.location='kendaraan_tampil.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus data: " . mysqli_error($koneksi) . "');window.location='kendaraan_tampil.php';</script>";
    }
} else {
    echo "<script>alert('ID tidak ditemukan');window.location='kendaraan_tampil.php';</script>";
}
