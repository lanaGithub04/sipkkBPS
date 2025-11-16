<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != '2') {
    header("Location: ../index.php");
    exit();
}

include '../config/koneksi.php';

// Ambil 5 peminjaman terakhir
$query = "
    SELECT 
        p.id_peminjaman, 
        u.nama AS nama_user,
        CONCAT(k.merk, ' (', k.plat_nomor, ')') AS nama_kendaraan, 
        p.tanggal_pinjam, 
        p.jam_pinjam,
        p.status 
    FROM peminjaman p
    JOIN users u ON p.id_u_pinjam = u.id_user
    JOIN kendaraan k ON p.id_k = k.id_kendaraan
    ORDER BY CONCAT(p.tanggal_pinjam, ' ', p.jam_pinjam) DESC
    LIMIT 5
";
$result = mysqli_query($koneksi, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Responsif -->
    <title>Dashboard Admin - SIPKK</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
    * {
        box-sizing: border-box;
    }

    html, body {
        height: 100%;
        margin: 0;
        font-family: 'Segoe UI', sans-serif;
        background-color: #f8fafc;
    }

    main {
        flex: 1;
        padding-top: 90px;
    }

    header {
        background: linear-gradient(90deg, #0d6efd, #0dcaf0);
        color: white;
        padding: 12px 0;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
    }

    .navbar-brand span {
        color: #ffdd57;
    }

    .dashboard-header {
        text-align: center;
        margin-bottom: 40px;
    }

    .dashboard-header h3 {
        font-weight: 700;
        color: #0d6efd;
    }

    .card {
        transition: all 0.3s ease;
        border-radius: 16px;
        border: none;
        height: 100%;
    }

    .card:hover {
        transform: translateY(-6px);
        box-shadow: 0 8px 24px rgba(13, 110, 253, 0.15);
    }

    /* ====== TABLE STYLE ====== */
    /* Garis di semua sisi tabel */
    .table, 
    .table th, 
    .table td {
        border: 1px solid #dee2e6;
    }
    
    .recent-table {
        margin-top: 60px;
    }

    .table-modern {
        border-radius: 16px;
        overflow: auto; /* biar bisa geser kiri-kanan di hp */
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
        background: white;
        width: 100%;
    }

    .table {
        min-width: 800px; /* jaga tampilan tetap rapi di layar kecil */
    }

    .table thead {
        background: linear-gradient(90deg, #0d6efd, #0dcaf0);
        color: white;
    }

    .table tbody tr:hover {
        background-color: #f1f5ff;
        transition: 0.2s;
    }

    /* ====== STATUS BADGE ====== */
    .status-badge {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
    }

    .status-diproses {
        background-color: #3498db;
        color: white;
    }

    .status-disetujui {
        background-color: #2ecc71;
        color: white;
    }

    .status-ditolak {
        background-color: #e74c3c;
        color: white;
    }

    .status-dikembalikan {
        background-color: #95a5a6;
        color: white;
    }

    footer {
        background: linear-gradient(90deg, #0d6efd, #0dcaf0);
        color: white;
        text-align: center;
        padding: 16px 0;
        font-size: 0.9rem;
        margin-top: 243px;
    }

    /* ====== RESPONSIVE DESIGN ====== */
    @media (max-width: 1200px) {
        .card p {
            font-size: 0.95rem;
        }
    }

    @media (max-width: 992px) {
        .dashboard-header h3 {
            font-size: 1.5rem;
        }

        .card h1 {
            font-size: 2rem;
        }

        .table th, .table td {
            font-size: 0.85rem;
            border: 1px solid #000000ff;
        }
    }

    @media (max-width: 768px) {
        main {
            padding-top: 70px;
        }

        .dashboard-header {
            margin-bottom: 25px;
        }

        .card {
            padding: 1rem;
            margin-bottom: 15px;
        }

        .table {
            font-size: 0.8rem;
        }

        .status-badge {
            font-size: 0.75rem;
            padding: 5px 10px;
        }

        footer {
            font-size: 0.8rem;
        }
    }

    @media (max-width: 576px) {
        .navbar-brand {
            font-size: 1rem;
        }

        .dashboard-header h3 {
            font-size: 1.2rem;
        }

        .table {
            min-width: 700px;
        }

        .table th, .table td {
            font-size: 0.75rem;
            padding: 0.5rem;
            border: 1px solid #000000ff;
        }

        .card h1 {
            font-size: 1.6rem;
        }

        .card h5 {
            font-size: 1rem;
        }

        .card p {
            font-size: 0.85rem;
        }

        .status-badge {
            font-size: 0.7rem;
            padding: 3px 8px;
        }
    }
</style>

</head>
<body>

    <!-- Navbar -->
    <header>
        <nav class="navbar navbar-expand-lg navbar-dark shadow-sm">
            <div class="container-fluid px-3">
                <a class="navbar-brand fw-bold" href="dashboard.php">
                    🚘 <span>SIPKK</span> - Admin
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                    <span class="text-white me-3 fw-semibold d-none d-sm-inline">👋 Halo Admin</span>
                    <a href="../logout.php" class="btn btn-outline-light btn-sm">Logout</a>
                </div>
            </div>
        </nav>
    </header>

    <!-- Konten Utama -->
    <main class="container-fluid px-4">
        <div class="dashboard-header">
            <h3>Selamat Datang di Dashboard Admin</h3>
            <p class="text-muted">Kelola data sistem dengan mudah, cepat, dan efisien</p>
        </div>

        <!-- Menu Cards -->
        <div class="row g-4 justify-content-center">
            <div class="col-lg-3 col-md-4 col-sm-6 col-10">
                <a href="kendaraan_tampil.php" class="text-decoration-none text-dark">
                    <div class="card shadow-sm text-center p-4">
                        <h1>🚗</h1>
                        <h5 class="card-title">Data Kendaraan</h5>
                        <p class="text-muted">Kelola data kendaraan dinas kantor.</p>
                    </div>
                </a>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6 col-10">
                <a href="peminjaman_tampil.php" class="text-decoration-none text-dark">
                    <div class="card shadow-sm text-center p-4">
                        <h1>📑</h1>
                        <h5 class="card-title">Data Peminjaman</h5>
                        <p class="text-muted">Atur pengajuan dan status peminjaman kendaraan.</p>
                    </div>
                </a>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6 col-10">
                <a href="users_tampil.php" class="text-decoration-none text-dark">
                    <div class="card shadow-sm text-center p-4">
                        <h1>👤</h1>
                        <h5 class="card-title">Data User</h5>
                        <p class="text-muted">Kelola akun admin, pegawai, dan pimpinan.</p>
                    </div>
                </a>
            </div>
        </div>

        <!-- 5 Peminjaman Terakhir -->
        <div class="recent-table mt-5">
            <h4 class="fw-bold text-primary mb-3">📋 5 Peminjaman Terbaru</h4>
            <div class="table-responsive table-modern">
                <table class="table table-hover mb-0 align-middle">
                    <thead>
                        <tr>
                            <th style="text-align: center;">No</th>
                            <th>Nama Peminjam</th>
                            <th>Kendaraan</th>
                            <th>Tanggal Pinjam</th>
                            <th>Jam Pinjam</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        if (mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                switch ($row['status']) {
                                    case '1': $status_text = 'Diproses'; $status_class = 'status-diproses'; break;
                                    case '2': $status_text = 'Disetujui'; $status_class = 'status-disetujui'; break;
                                    case '3': $status_text = 'Ditolak'; $status_class = 'status-ditolak'; break;
                                    case '4': $status_text = 'Dikembalikan'; $status_class = 'status-dikembalikan'; break;
                                    default: $status_text = 'Tidak Diketahui'; $status_class = 'status-diproses'; break;
                                }

                                echo "<tr>
                                    <td style='text-align:center;'>{$no}</td>
                                    <td>{$row['nama_user']}</td>
                                    <td>{$row['nama_kendaraan']}</td>
                                    <td>" . date('d-m-Y', strtotime($row['tanggal_pinjam'])) . "</td>
                                    <td>{$row['jam_pinjam']}</td>
                                    <td><span class='status-badge {$status_class}'>{$status_text}</span></td>
                                </tr>";
                                $no++;
                            }
                        } else {
                            echo "<tr><td colspan='6' class='text-center text-muted py-3'>Belum ada data peminjaman</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            <div class="text-end mt-3">
                <a href="peminjaman_tampil.php" class="btn btn-outline-primary btn-sm">Lihat Semua &raquo;</a>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer>
        &copy; <?= date("Y"); ?> SIPKK | Sistem Informasi Peminjaman Kendaraan Kantor
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
