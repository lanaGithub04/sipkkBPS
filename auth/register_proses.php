<?php
session_start();
include "../config/koneksi.php";

// Pastikan data dikirim via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
  $username = mysqli_real_escape_string($koneksi, $_POST['username']);
  $password = mysqli_real_escape_string($koneksi, $_POST['password']);
  $konfirmasi = mysqli_real_escape_string($koneksi, $_POST['konfirmasi']);

  // Cek apakah username sudah ada
  $cek = mysqli_query($koneksi, "SELECT * FROM users WHERE username='$username'");
  if (mysqli_num_rows($cek) > 0) {
    $pesan = "username_ada";
  }
  // Cek konfirmasi password
  elseif ($password !== $konfirmasi) {
    $pesan = "password_salah";
  } 
  else {
    // Hash password pakai MD5 (biar sama dengan di database kamu)
    $hashed = md5($password);

    // Simpan ke database (role = 1 untuk Pegawai)
    $query = "INSERT INTO users (nama, username, password, role) VALUES ('$nama', '$username', '$hashed', '1')";
    if (mysqli_query($koneksi, $query)) {
      $pesan = "sukses";
    } else {
      $pesan = "gagal";
    }
  }
} else {
  header("Location: ./register.php");
  exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Proses Registrasi</title>
  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<script>
<?php
if (isset($pesan)) {
  switch ($pesan) {
    case "username_ada":
      echo "
        Swal.fire({
          icon: 'warning',
          title: 'Username sudah digunakan!',
          text: 'Silakan gunakan username lain.',
          showConfirmButton: false,
          timer: 2000,
          timerProgressBar: true
        }).then(() => {
          window.location = './register.php';
        });
      ";
      break;

    case "password_salah":
      echo "
        Swal.fire({
          icon: 'error',
          title: 'Konfirmasi Password Salah!',
          text: 'Pastikan kedua password sama ya.',
          showConfirmButton: false,
          timer: 2000,
          timerProgressBar: true
        }).then(() => {
          window.location = './register.php';
        });
      ";
      break;

    case "sukses":
      echo "
        Swal.fire({
          icon: 'success',
          title: 'Registrasi Berhasil!',
          text: 'Silakan login untuk melanjutkan.',
          showConfirmButton: false,
          timer: 2000,
          timerProgressBar: true
        }).then(() => {
          window.location = '../index.php';
        });
      ";
      break;

    case "gagal":
      echo "
        Swal.fire({
          icon: 'error',
          title: 'Terjadi Kesalahan!',
          text: 'Gagal menyimpan data ke database.',
          showConfirmButton: false,
          timer: 2000,
          timerProgressBar: true
        }).then(() => {
          window.location = './register.php';
        });
      ";
      break;
  }
}
?>
</script>
</body>
</html>
