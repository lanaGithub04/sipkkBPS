<?php
$host = "localhost";
$user = "root";     // default XAMPP
$pass = "";         // default XAMPP kosong
$db   = "sipkk";    // sesuai nama database

$koneksi = mysqli_connect("localhost", "root", "", "sipkk");

if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}
?>