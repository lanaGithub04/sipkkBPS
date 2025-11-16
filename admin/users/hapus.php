<?php
include '../../config/koneksi.php';

$id = $_GET['id'] ?? null;

if ($id) {
  mysqli_query($koneksi, "DELETE FROM users WHERE id_user='$id'");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Hapus Data</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    * {
      box-sizing: border-box;
      font-family: 'Poppins', sans-serif;
    }

    body {
      margin: 0;
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      background: radial-gradient(circle at top left, #0d6efd, #0dcaf0);
      overflow: hidden;
    }

    /* Overlay blur */
    .overlay {
      position: fixed;
      top: 0; left: 0;
      width: 100%; height: 100%;
      background: rgba(0,0,0,0.35);
      backdrop-filter: blur(6px);
      display: flex;
      justify-content: center;
      align-items: center;
      z-index: 999;
    }

    .popup {
      background: white;
      border-radius: 20px;
      width: 90%;
      max-width: 420px;
      text-align: center;
      padding: 30px 25px;
      box-shadow: 0 10px 35px rgba(0,0,0,0.2);
      animation: slideUp 0.6s ease forwards;
    }

    @keyframes slideUp {
      from { transform: translateY(40px); opacity: 0; }
      to { transform: translateY(0); opacity: 1; }
    }

    .icon-box {
      width: 90px;
      height: 90px;
      border-radius: 50%;
      background: #fff3cd;
      display: flex;
      justify-content: center;
      align-items: center;
      margin: 0 auto 15px;
      border: 3px solid #ffc107;
      animation: pulse 2s infinite;
    }

    .icon-box svg {
      width: 50px;
      height: 50px;
      fill: #ffc107;
    }

    @keyframes pulse {
      0%,100% { box-shadow: 0 0 0 0 rgba(255,193,7,0.5); }
      50% { box-shadow: 0 0 0 15px rgba(255,193,7,0); }
    }

    .popup h2 {
      font-size: 1.3rem;
      margin-bottom: 10px;
      color: #333;
    }

    .popup p {
      font-size: 0.95rem;
      color: #666;
      margin-bottom: 20px;
    }

    .popup button {
      border: none;
      outline: none;
      padding: 10px 22px;
      margin: 0 10px;
      border-radius: 8px;
      font-weight: 500;
      cursor: pointer;
      transition: 0.3s ease;
    }

    .btn-cancel {
      background: #dee2e6;
      color: #333;
    }

    .btn-cancel:hover {
      background: #ced4da;
    }

    .btn-delete {
      background: #dc3545;
      color: white;
    }

    .btn-delete:hover {
      background: #c82333;
    }

    /* Notif success */
    .success-box {
      background: white;
      border-radius: 20px;
      text-align: center;
      padding: 40px 30px;
      box-shadow: 0 10px 35px rgba(0,0,0,0.25);
      animation: fadeIn 0.6s ease forwards;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: scale(0.8); }
      to { opacity: 1; transform: scale(1); }
    }

    .success-check {
      width: 100px;
      height: 100px;
      margin: 0 auto 15px;
      border-radius: 50%;
      background: #d1f7e3;
      border: 3px solid #20c997;
      display: flex;
      justify-content: center;
      align-items: center;
      animation: pulseGreen 2s infinite;
    }

    @keyframes pulseGreen {
      0%,100% { box-shadow: 0 0 0 0 rgba(32,201,151,0.5); }
      50% { box-shadow: 0 0 0 12px rgba(32,201,151,0); }
    }

    .success-check svg {
      width: 60px;
      height: 60px;
      stroke: #20c997;
      stroke-width: 5;
      fill: none;
      stroke-dasharray: 70;
      stroke-dashoffset: 70;
      animation: drawCheck 1s ease forwards 0.4s;
    }

    @keyframes drawCheck {
      to { stroke-dashoffset: 0; }
    }

    .success-box h2 {
      color: #20c997;
      font-size: 1.4rem;
      margin-top: 10px;
    }

    .success-box p {
      color: #555;
      font-size: 0.95rem;
      margin-top: 5px;
    }

    @media (max-width: 480px) {
      .popup, .success-box {
        padding: 25px 20px;
      }

      .popup h2, .success-box h2 {
        font-size: 1.2rem;
      }
    }
  </style>
</head>
<body>

  <!-- Konfirmasi Hapus -->
  <div class="overlay" id="confirmBox">
    <div class="popup">
      <div class="icon-box">
        <svg viewBox="0 0 24 24">
          <path d="M3 6h18M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2m2 0v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6h14z"/>
        </svg>
      </div>
      <h2>Konfirmasi Penghapusan</h2>
      <p>Apakah Anda yakin ingin menghapus user ini?</p>
      <div>
        <button class="btn-cancel" onclick="cancelDelete()">Batal</button>
        <button class="btn-delete" onclick="confirmDelete()">Hapus</button>
      </div>
    </div>
  </div>

  <!-- Notifikasi Sukses -->
  <div class="overlay" id="successBox" style="display:none;">
    <div class="success-box">
      <div class="success-check">
        <svg viewBox="0 0 50 50">
          <path d="M15 27 L22 34 L37 18"/>
        </svg>
      </div>
      <h2>Data Berhasil Dihapus!</h2>
      <p>Mengalihkan ke halaman utama...</p>
    </div>
  </div>

  <script>
    function confirmDelete() {
      document.getElementById('confirmBox').style.display = 'none';
      document.getElementById('successBox').style.display = 'flex';
      setTimeout(() => {
        window.location.href = '../users_tampil.php';
      }, 2200);
    }

    function cancelDelete() {
      window.location.href = '../users_tampil.php';
    }
  </script>

</body>
</html>
