<?php
session_start();
include '../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil inputan dari form
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Validasi awal
    if (empty($username) || empty($password)) {
        echo "<script>alert('Username dan password harus diisi!');window.location='../index.php';</script>";
        exit;
    }

    // Gunakan prepared statement agar aman dari SQL Injection
    $stmt = mysqli_prepare($koneksi, "SELECT * FROM users WHERE username = ? LIMIT 1");
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    // Cek apakah user ditemukan
    if ($result && mysqli_num_rows($result) === 1) {
        $data = mysqli_fetch_assoc($result);

        // Cek password (MD5 untuk sekarang)
        if ($data['password'] === md5($password)) {
            // Simpan session login
            $_SESSION['id_user'] = $data['id_user'];
            $_SESSION['username'] = $data['username'];
            $_SESSION['role'] = $data['role'];

            // Arahkan sesuai role
            switch ($data['role']) {
                case '1':
                    header("Location: ../pegawai/dashboard.php");
                    break;
                case '2':
                    header("Location: ../admin/dashboard.php");
                    break;
                case '3':
                    header("Location: ../pimpinan/dashboard.php");
                    break;
                default:
                    echo "<script>alert('Role tidak dikenal!');window.location='../index.php';</script>";
            }
            exit;
        } else {
            echo "<script>alert('Password salah!');window.location='../index.php';</script>";
            exit;
        }
    } else {
        echo "<script>alert('Username tidak ditemukan!');window.location='../index.php';</script>";
        exit;
    }
} else {
    header("Location: ../index.php");
    exit;
}
?>
