<?php
include '../../config/koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = $_POST['nama'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    $query = "INSERT INTO users (nama, username, password, role) 
              VALUES ('$nama', '$username', '$password', '$role')";

    if (mysqli_query($koneksi, $query)) {
        echo "<script>
                alert('✅ User berhasil ditambahkan!');
                window.location='../users_tampil.php';
              </script>";
    } else {
        echo "<script>
                alert('❌ Gagal menambahkan user: " . mysqli_error($koneksi) . "');
              </script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Tambah User | SIPKK</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    body {
      background: linear-gradient(180deg, #f8f9fa, #e3f2fd);
      font-family: 'Poppins', sans-serif;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }

    header {
      background: linear-gradient(90deg, #007bff, #0056d6);
      color: #fff;
      padding: 15px 25px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
    }

    header h3 {
      margin: 0;
      font-weight: 600;
      font-size: 1.2rem;
    }

    .btn-logout {
      border: 1px solid #fff;
      color: #fff;
      transition: 0.3s;
      font-size: 0.9rem;
    }

    .btn-logout:hover {
      background: #fff;
      color: #0056d6;
    }

    .content-box {
      background: #fff;
      border-radius: 15px;
      box-shadow: 0 4px 20px rgba(0,0,0,0.1);
      padding: 40px 35px;
      margin: 90px auto 40px auto;
      max-width: 550px;
      width: 90%;
      animation: fadeIn 0.6s ease-in-out;
    }

    h4 {
      color: #003566;
      font-weight: 600;
      margin-bottom: 25px;
      text-align: center;
    }

    input, select {
      border-radius: 8px !important;
      transition: all 0.3s;
    }

    input:focus, select:focus {
      border-color: #007bff;
      box-shadow: 0 0 5px rgba(0,123,255,0.3);
    }

    .btn {
      border-radius: 8px;
      font-weight: 500;
    }

    footer {
      background: linear-gradient(90deg, #007bff, #0056d6);
      color: #fff;
      text-align: center;
      padding: 12px 0;
      font-size: 0.9rem;
      margin-top: auto;
    }

    @keyframes fadeIn {
      from {opacity: 0; transform: translateY(10px);}
      to {opacity: 1; transform: translateY(0);}
    }

    /* 🔹 Responsif */
    @media (max-width: 768px) {
      header {
        flex-direction: column;
        text-align: center;
        gap: 10px;
        padding: 15px;
      }

      .content-box {
        margin: 100px 15px 60px 15px;
        padding: 25px;
      }

      h4 {
        font-size: 1.1rem;
      }

      .btn {
        font-size: 0.9rem;
      }
    }

    @media (max-width: 480px) {
      header h3 {
        font-size: 1rem;
      }

      .content-box {
        padding: 20px;
      }
    }
  </style>
</head>

<body>

  <!-- HEADER -->
  <header>
    <h3>🚀 SIPKK - <span style="color:#ffeb3b;">Tambah User</span></h3>
    <a href="../dashboard.php" class="btn btn-logout btn-sm">🏠 Dashboard</a>
  </header>

  <!-- CONTENT -->
  <div class="content-box">
    <h4>🧑‍💼 Form Tambah User Baru</h4>
    <form method="POST" action="">
      <div class="mb-3">
        <label class="form-label">Nama Lengkap</label>
        <input type="text" name="nama" class="form-control" placeholder="Masukkan nama lengkap..." required>
      </div>
      <div class="mb-3">
        <label class="form-label">Username</label>
        <input type="text" name="username" class="form-control" placeholder="Masukkan username..." required>
      </div>
      <div class="mb-3">
        <label class="form-label">Password</label>
        <input type="password" name="password" class="form-control" placeholder="Masukkan password..." required>
      </div>
      <div class="mb-4">
        <label class="form-label">Role</label>
        <select name="role" class="form-select" required>
          <option value="2">Admin</option>
          <option value="1">Pegawai</option>
          <option value="3">Pimpinan</option>
        </select>
      </div>

      <div class="d-flex justify-content-between flex-wrap gap-2">
        <a href="../users_tampil.php" class="btn btn-secondary flex-fill">⬅️ Kembali</a>
        <button type="submit" class="btn btn-primary flex-fill">💾 Simpan User</button>
      </div>
    </form>
  </div>

  <!-- FOOTER -->
  <footer>
    © <?= date('Y'); ?> SIPKK | Sistem Informasi Pengelolaan Kantor
  </footer>

</body>
</html>
