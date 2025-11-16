-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 16 Nov 2025 pada 06.39
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sipkk`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `data_tujuan`
--

CREATE TABLE `data_tujuan` (
  `id_tujuan` int(11) NOT NULL,
  `id_pengajuan` int(11) DEFAULT NULL,
  `nama_pegawai` varchar(100) DEFAULT NULL,
  `tujuan` text DEFAULT NULL,
  `jarak_km` float DEFAULT NULL,
  `konsumsi_bbm_liter` float DEFAULT NULL,
  `tanggal_pengajuan` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `kendaraan`
--

CREATE TABLE `kendaraan` (
  `id_kendaraan` int(11) NOT NULL,
  `jenis` int(1) NOT NULL,
  `merk` varchar(100) NOT NULL,
  `plat_nomor` varchar(20) NOT NULL,
  `status` int(1) DEFAULT NULL,
  `konsumsi_bbm` float DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `kendaraan`
--

INSERT INTO `kendaraan` (`id_kendaraan`, `jenis`, `merk`, `plat_nomor`, `status`, `konsumsi_bbm`) VALUES
(47, 1, 'Hilux', 'DA 8182 PT', 1, NULL),
(48, 1, 'Terios', 'DA 1615 JT', 1, NULL),
(49, 1, 'Terios', 'DA 1039 PG', 3, NULL),
(50, 2, 'Honda Supra', 'DA 2034 NZ', 1, NULL),
(51, 2, 'Honda Supra', 'DA 2169 RB', 1, NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `peminjaman`
--

CREATE TABLE `peminjaman` (
  `id_peminjaman` int(11) NOT NULL,
  `id_u_pinjam` int(11) NOT NULL,
  `id_k` int(11) NOT NULL,
  `id_tujuan` int(11) DEFAULT NULL,
  `tanggal_pinjam` date NOT NULL,
  `jam_pinjam` time DEFAULT NULL,
  `tanggal_kembali` date DEFAULT NULL,
  `jam_kembali` time DEFAULT NULL,
  `keperluan` text DEFAULT NULL,
  `status` int(1) DEFAULT NULL,
  `alasan_penolakan` text DEFAULT NULL,
  `dikembalikan` int(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `tujuan_master`
--

CREATE TABLE `tujuan_master` (
  `id_tujuan` int(11) NOT NULL,
  `nama_tujuan` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tujuan_master`
--

INSERT INTO `tujuan_master` (`id_tujuan`, `nama_tujuan`) VALUES
(1, 'Kabupaten Balangan'),
(2, 'Kabupaten Banjar'),
(3, 'Kabupaten Barito Kuala'),
(4, 'Kabupaten Hulu Sungai Selatan'),
(5, 'Kabupaten Hulu Sungai Tengah'),
(6, 'Kabupaten Hulu Sungai Utara'),
(7, 'Kabupaten Kotabaru'),
(8, 'Kabupaten Tabalong'),
(9, 'Kabupaten Tanah Bumbu'),
(10, 'Kabupaten Tanah Laut'),
(11, 'Kabupaten Tapin'),
(13, 'Kota Banjarmasin'),
(14, 'Kota Banjarbaru');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id_user` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` int(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id_user`, `nama`, `username`, `password`, `role`) VALUES
(1, 'Admin', 'admin', '0192023a7bbd73250516f069df18b500', 2),
(3, 'Pimpinan', 'pimpinan', '7d3207a13dc221ac13c2f3dac3011f50', 3),
(22, 'Pegawai', 'pegawai', 'b69706c80477d3d04ecc1d8f62cdb35a', 1),
(32, 'Akhmad Maulana', 'lana', '37acca285a7c9a51e33e9e0f6cedde90', 1),
(47, 'yendi', 'yendi', 'f1ceff0f3cc50d71530d05f8fa85cf7e', 1),
(53, 'Rofiq', 'Rofiq', '7b791f5362125321a32604b204f70381', 1),
(54, 'nazia', 'nazia', '73f2822c5b9088c0f02dc07483f3bfd6', 1),
(55, 'Bapak Yusuf', 'Yusuf', 'a3bd98f1e515f35e96d1ee5d2a4cf5d6', 3),
(56, 'adul', 'adul', '38fe82638eb5feb6912842db67457800', 1);

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `data_tujuan`
--
ALTER TABLE `data_tujuan`
  ADD PRIMARY KEY (`id_tujuan`),
  ADD KEY `fk_data_tujuan_peminjaman` (`id_pengajuan`);

--
-- Indeks untuk tabel `kendaraan`
--
ALTER TABLE `kendaraan`
  ADD PRIMARY KEY (`id_kendaraan`);

--
-- Indeks untuk tabel `peminjaman`
--
ALTER TABLE `peminjaman`
  ADD PRIMARY KEY (`id_peminjaman`),
  ADD KEY `id_user` (`id_u_pinjam`),
  ADD KEY `id_kendaraan` (`id_k`);

--
-- Indeks untuk tabel `tujuan_master`
--
ALTER TABLE `tujuan_master`
  ADD PRIMARY KEY (`id_tujuan`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `data_tujuan`
--
ALTER TABLE `data_tujuan`
  MODIFY `id_tujuan` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `kendaraan`
--
ALTER TABLE `kendaraan`
  MODIFY `id_kendaraan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT untuk tabel `peminjaman`
--
ALTER TABLE `peminjaman`
  MODIFY `id_peminjaman` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=102;

--
-- AUTO_INCREMENT untuk tabel `tujuan_master`
--
ALTER TABLE `tujuan_master`
  MODIFY `id_tujuan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `data_tujuan`
--
ALTER TABLE `data_tujuan`
  ADD CONSTRAINT `fk_data_tujuan_peminjaman` FOREIGN KEY (`id_pengajuan`) REFERENCES `peminjaman` (`id_peminjaman`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `peminjaman`
--
ALTER TABLE `peminjaman`
  ADD CONSTRAINT `peminjaman_ibfk_1` FOREIGN KEY (`id_u_pinjam`) REFERENCES `users` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `peminjaman_ibfk_2` FOREIGN KEY (`id_k`) REFERENCES `kendaraan` (`id_kendaraan`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
