<?php
session_start();
include "../config/koneksi.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] != '2') {
  header("Location: ../index.php");
  exit;
}

// === BAGIAN CRUD ===
if (isset($_POST['action'])) {
  $action = $_POST['action'];

  // TAMBAH DATA
  if ($action == 'add') {
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = mysqli_real_escape_string($koneksi, $_POST['password']);
    $role = mysqli_real_escape_string($koneksi, $_POST['role']);
    
    // DIUBAH: Hash password pake MD5 sebelum simpan
    $hashed_password = md5($password);
    
    mysqli_query($koneksi, "INSERT INTO users (nama, username, password, role) VALUES ('$nama', '$username', '$hashed_password', '$role')");
    exit('success');
  }

  // EDIT DATA
  if ($action == 'edit') {
    $id = intval($_POST['id_user']);
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = mysqli_real_escape_string($koneksi, $_POST['password']);
    $role = mysqli_real_escape_string($koneksi, $_POST['role']);
    
    // DIUBAH: Hash password pake MD5 sebelum update, tapi cuma kalau password diisi
    $update_password = '';
    if (!empty($password)) {
      $hashed_password = md5($password);
      $update_password = ", password='$hashed_password'";
    }
    
    mysqli_query($koneksi, "UPDATE users SET nama='$nama', username='$username'$update_password, role='$role' WHERE id_user=$id");
    exit('success');
  }

  // HAPUS DATA
  if ($action == 'delete') {
    $id = intval($_POST['id_user']);
    mysqli_query($koneksi, "DELETE FROM users WHERE id_user=$id");
    exit('success');
  }
}

// === BAGIAN LOAD DATA UNTUK AJAX ===
if (isset($_GET['ajax'])) {
  $keyword = mysqli_real_escape_string($koneksi, $_GET['keyword'] ?? '');
  $role = mysqli_real_escape_string($koneksi, $_GET['role'] ?? '');

  $query = "SELECT * FROM users WHERE 1=1";
  if ($keyword != '') {
    $query .= " AND (nama LIKE '%$keyword%' OR username LIKE '%$keyword%')";
  }
  if ($role != '') {
    $query .= " AND role = '$role'";
  }
  $query .= " ORDER BY id_user DESC";
  $result = mysqli_query($koneksi, $query);

  $no = 1;
  if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
      echo "<tr>
        <td>{$no}</td>
        <td>" . htmlspecialchars($row['nama']) . "</td>
        <td>" . htmlspecialchars($row['username']) . "</td>
        <td>";
      if ($row['role'] == '1') echo '<span class=\"badge bg-info\">Pegawai</span>';
      elseif ($row['role'] == '2') echo '<span class=\"badge bg-success\">Admin</span>';
      else echo '<span class=\"badge bg-warning text-dark\">Pimpinan</span>';
      echo "</td>
        <td>
          <button class='btn btn-sm btn-outline-primary' onclick='openEditModal(" . json_encode($row) . ")'>✏️ Edit</button>
          <button class='btn btn-sm btn-danger' onclick='openDeleteModal(" . $row['id_user'] . ")'>🗑️ Hapus</button>
        </td>
      </tr>";
      $no++;
    }
  } else {
    echo "<tr><td colspan='5' class='text-muted'>Tidak ada data ditemukan.</td></tr>";
  }
  exit;
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Data User | SIPKK</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    body {
      background-color: #f4f6fb;
      font-family: 'Poppins', sans-serif;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }

    header {
      background: linear-gradient(90deg, #0d6efd, #0dcaf0);
      color: white;
      padding: 10px 0;
    }

    .navbar {
      background: linear-gradient(90deg, #007bff, #0056d6);
      color: white;
      padding: 1rem 20px;
    }

    .navbar-brand {
      font-weight: 600;
      color: #fff !important;
    }

    .navbar span {
      color: #ffeb3b;
    }

    .btn-logout {
      border: 1px solid #fff;
      color: #fff;
      transition: 0.3s;
    }

    .btn-logout:hover {
      background-color: #fff;
      color: #0056d6;
    }

    .content-box {
      background-color: #fff;
      border-radius: 15px;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
      padding: 30px;
      margin-top: 40px;
      animation: fadeIn 0.5s ease-in-out;
    }

    h2 {
      color: #003566;
      font-weight: 600;
    }

    .table thead {
      background-color: #0056d6;
      color: #fff;
    }

    .table tbody tr:hover {
      background-color: #f2f7ff;
      transition: 0.3s;
    }

    footer {
      background: linear-gradient(90deg, #007bff, #0056d6);
      color: #fff;
      text-align: center;
      padding: 20px 0;
      margin-top: auto;
      font-size: 0.9rem;
    }

    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: translateY(-10px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
  </style>
</head>

<body>

  <header>
    <nav class="navbar navbar-expand-lg navbar-dark shadow-sm">
      <div class="container-fluid px-3">
        <a class="navbar-brand fw-bold" href="dashboard.php">🚘 <span>SIPKK</span> - Admin</a>
        <div class="collapse navbar-collapse justify-content-end">
          <span class="text-white me-3 fw-semibold">👋 Halo Admin</span>
          <a href="../admin/dashboard.php" class="btn btn-outline-light btn-sm">Dashboard</a>
        </div>
      </div>
    </nav>
  </header>

  <div class="container my-4 flex-grow-1">
    <div class="content-box">
      <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <h2>👥 Data User</h2>
        <div class="text-center text-lg-end">
          <button class="btn btn-primary btn-sm" onclick="openAddModal()">+ Tambah User</button>
        </div>
      </div>

      <!-- Filter -->
      <div class="row g-2 mb-4">
        <div class="col-md-4 col-12">
          <input type="text" id="keyword" class="form-control" placeholder="Cari nama atau username...">
        </div>
        <div class="col-md-3 col-12">
          <select id="role" class="form-select">
            <option value="">Semua Role</option>
            <option value="1">Pegawai</option>
            <option value="2">Admin</option>
            <option value="3">Pimpinan</option>
          </select>
        </div>
      </div>

      <!-- Tabel -->
      <div class="table-responsive">
        <table class="table table-bordered align-middle text-center">
          <thead>
            <tr>
              <th>No</th>
              <th>Nama</th>
              <th>Username</th>
              <th>Role</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody id="userTableBody">
            <!-- data loaded by ajax -->
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Modal Form -->
  <div class="modal fade" id="userModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <form id="userForm" class="modal-content">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title">Tambah User</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="id_user" id="id_user">
          <input type="hidden" name="action" id="action">
          <div class="mb-3">
            <label class="form-label">Nama</label>
            <input type="text" name="nama" id="nama" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Username</label>
            <input type="text" name="username" id="username" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="text" name="password" id="password" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Role</label>
            <select name="role" id="role_select" class="form-select" required>
              <option value="1">Pegawai</option>
              <option value="2">Admin</option>
              <option value="3">Pimpinan</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">💾 Simpan</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        </div>
      </form>
    </div>
  </div>

  <footer>
    © <?= date('Y'); ?> SIPKK | Sistem Informasi Pengelolaan Kantor
  </footer>

  <!-- 1) Pastikan SweetAlert2 dimuat -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <!-- 2) Bootstrap bundle (tetap) -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <script>
  document.addEventListener('DOMContentLoaded', () => {
    // ambil elemen (aman karena sudah DOMContentLoaded)
    const tableBody = document.getElementById('userTableBody');
    const keywordInput = document.getElementById('keyword');
    const roleSelect = document.getElementById('role');
    const userModalEl = document.getElementById('userModal');
    const userModal = new bootstrap.Modal(userModalEl);

    // fetch users (AJAX)
    async function fetchUsers() {
      try {
        const keyword = keywordInput.value.trim();
        const role = roleSelect.value;
        const response = await fetch(`users_tampil.php?ajax=1&keyword=${encodeURIComponent(keyword)}&role=${encodeURIComponent(role)}`);
        const data = await response.text();
        tableBody.innerHTML = data;
      } catch (err) {
        console.error('fetchUsers error:', err);
        // opsi: tampilkan toast kecil
      }
    }

    // buka modal tambah
    window.openAddModal = function() {
      document.getElementById('userForm').reset();
      document.getElementById('action').value = 'add';
      document.querySelector('#userModal .modal-title').innerText = 'Tambah User';
      userModal.show();
    };

    // buka modal edit (dipanggil dari HTML via openEditModal(json))
    window.openEditModal = function(data) {
      document.getElementById('id_user').value = data.id_user;
      document.getElementById('nama').value = data.nama;
      document.getElementById('username').value = data.username;
      document.getElementById('password').value = data.password;
      document.getElementById('role_select').value = data.role;
      document.getElementById('action').value = 'edit';
      document.querySelector('#userModal .modal-title').innerText = 'Edit User';
      userModal.show();
    };

    // fungsi hapus dengan Swal konfirmasi
    window.openDeleteModal = function(id) {
      Swal.fire({
        title: "Yakin ingin menghapus?",
        text: "Data ini akan dihapus permanen!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Ya, Hapus!",
        cancelButtonText: "Batal"
      }).then(async (result) => {
        if (result.isConfirmed) {
          try {
            const formData = new FormData();
            formData.append('action', 'delete');
            formData.append('id_user', id);
            const response = await fetch('users_tampil.php', {
              method: 'POST',
              body: formData
            });
            const hasil = await response.text();
            if (hasil.trim() === 'success') {
              Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: 'User telah dihapus.',
                showConfirmButton: false,
                timer: 1500
              });
              fetchUsers();
            } else {
              Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: 'Terjadi masalah saat menghapus.'
              });
              console.log('delete result:', hasil);
            }
          } catch (err) {
            console.error('delete error:', err);
            Swal.fire({
              icon: 'error',
              title: 'Error',
              text: 'Terjadi kesalahan jaringan.'
            });
          }
        }
      });
    };

    // submit form (tambah / edit) dengan konfirmasi
    const userForm = document.getElementById('userForm');
    userForm.addEventListener('submit', async function(e) {
      e.preventDefault();
      // PERBAIKAN: Gunakan this.elements['action'].value untuk ambil nilai input hidden 'action'
      const action = this.elements['action'].value;
      const formData = new FormData(this);

      Swal.fire({
        title: action === 'add' ? "Tambah User?" : "Simpan Perubahan?",  // PERBAIKAN: Logika benar
        icon: "question",
        showCancelButton: true,
        confirmButtonText: "Ya",
        cancelButtonText: "Batal"
      }).then(async (result) => {
        if (result.isConfirmed) {
          try {
            const response = await fetch('users_tampil.php', {
              method: 'POST',
              body: formData
            });
            const hasil = await response.text();
            if (hasil.trim() === 'success') {
              userModal.hide();
              Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: action === 'add' ? 'User berhasil ditambahkan!' : 'Perubahan berhasil disimpan!',  // PERBAIKAN: Logika benar
                showConfirmButton: false,
                timer: 1500
              });
              fetchUsers();
            } else {
              // jika backend mengembalikan pesan error, tampilkan
              Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: 'Terjadi masalah saat menyimpan data.'
              });
              console.log('submit result:', hasil);
            }
          } catch (err) {
            console.error('submit error:', err);
            Swal.fire({
              icon: 'error',
              title: 'Error',
              text: 'Terjadi kesalahan jaringan.'
            });
          }
        }
      });
    });

    // event listeners filter
    keywordInput.addEventListener('keyup', () => {
      // debounce kecil
      clearTimeout(window._userFetchTimer);
      window._userFetchTimer = setTimeout(fetchUsers, 300);
    });
    roleSelect.addEventListener('change', fetchUsers);

    // initial load
    fetchUsers();
  });
</script>

</body>

</html>
