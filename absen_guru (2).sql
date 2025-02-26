-- phpMyAdmin SQL Dump
-- version 4.9.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 06 Okt 2024 pada 09.41
-- Versi server: 10.4.8-MariaDB
-- Versi PHP: 7.3.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `absen_guru`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `absen_msk`
--

CREATE TABLE `absen_msk` (
  `id` int(230) NOT NULL,
  `nama` varchar(230) NOT NULL,
  `tanggal` date NOT NULL,
  `jam1` varchar(100) NOT NULL,
  `jam2` varchar(100) NOT NULL,
  `jam3` varchar(100) NOT NULL,
  `jam4` varchar(100) NOT NULL,
  `jam5` varchar(100) NOT NULL,
  `jam6` varchar(100) NOT NULL,
  `jam7` varchar(100) NOT NULL,
  `jam8` varchar(100) NOT NULL,
  `jam9` varchar(100) NOT NULL,
  `jam10` varchar(100) NOT NULL,
  `kordinat` varchar(1234) NOT NULL,
  `activity` varchar(800) NOT NULL,
  `foto_absen` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `absen_msk`
--

INSERT INTO `absen_msk` (`id`, `nama`, `tanggal`, `jam1`, `jam2`, `jam3`, `jam4`, `jam5`, `jam6`, `jam7`, `jam8`, `jam9`, `jam10`, `kordinat`, `activity`, `foto_absen`) VALUES
(62, 'Uwoh Pramijaya, S.Pd.I., M.M.', '2024-08-14', '07:54:51', '', '', '', '', '', '', '', '13:27:00', '', '-6.434088857142857,106.92476885714285', '', '66bc4e45ecfcf.png'),
(64, 'Ari Januar Pratama, S.Kom.', '2024-08-20', '07.31.08', '08.07.32', '09.07.06', '09.17.10', '10.28.08', '11.06.07', '11.36.10', '12.51.14', '13.24.08', '14.10.02', '-6.4293758,106.9218303', '', '66c4256ac28ce.png'),
(65, 'Ari Januar Pratama, S.Kom.', '2024-08-23', '08.16.38', '08.26.02', '09.22.52', '09.53.07', '10.29.07', '11.15.08', '', '', '', '', '-6.4293616,106.9217386', '', '66c822f856545.png'),
(66, 'Ari Januar Pratama, S.Kom.', '2024-08-19', '08.32.51', '', '09.21.52', '', '', '', '', '', '', '', '-6.4342962,106.9245715', '', '66c2ac6810601.png'),
(67, 'Ari Januar Pratama, S.Kom.', '2024-08-21', '3', '1', '', '', '', '', '', '', '', '14.01.05', '-6.4097995,106.9054268', 'Terakhir absen di kelas ,X PPLG 1', '66c590bee8103.png'),
(68, 's', '2024-08-16', '', '', '', '', '', '', '', 'e', '', '', '', '', ''),
(69, 'Uwoh Pramijaya, S.Pd.I., M.M.', '2024-10-03', '', 'X PM', '', '', '', '', 'X DKV 1', '', '', '', '', 'Terakhir absen di kelas ,X DKV 1', '66fe1ffd93ee8.png'),
(73, 'Uwoh Pramijaya, S.Pd.I., M.M.', '2024-10-04', 'X DKV 1', '', '', '', '', '', '', '', '', '', '', 'Terakhir absen di kelas ,X DKV 1', '66ff415527f4f.png');

-- --------------------------------------------------------

--
-- Struktur dari tabel `admin`
--

CREATE TABLE `admin` (
  `id` int(10) NOT NULL,
  `username` varchar(1234) NOT NULL,
  `password` varchar(1234) NOT NULL,
  `nama` varchar(1234) NOT NULL,
  `gambar` varchar(1234) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `admin`
--

INSERT INTO `admin` (`id`, `username`, `password`, `nama`, `gambar`) VALUES
(7, 'bayu', '$2y$10$ko6fNByKFKlfYbX6Jd.E/.O9/kwoDZ7HNwEu/CDvHLVb793vnsHhS', 'bayu', '66adb31920783.png'),
(14, 'rpl', 'rpl', 'rpl', '66b8b5242d76e.png'),
(15, '123', '123', '123', '66b9575edab82.png');

-- --------------------------------------------------------

--
-- Struktur dari tabel `akun_guru`
--

CREATE TABLE `akun_guru` (
  `id` int(34) NOT NULL,
  `username` varchar(232) NOT NULL,
  `password` varchar(342) NOT NULL,
  `nama` varchar(1234) NOT NULL,
  `gender` varchar(70) NOT NULL,
  `jabatan` varchar(40) NOT NULL,
  `gambar` varchar(1234) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `akun_guru`
--

INSERT INTO `akun_guru` (`id`, `username`, `password`, `nama`, `gender`, `jabatan`, `gambar`) VALUES
(1, 'Uwoh', '$2y$10$ntCIXQ1O7PWn59reb2QC2emnuo7P9sqw.OTM/oFJ3CXscRRqd31su', 'Uwoh Pramijaya, S.Pd.I., M.M.', 'male', 'Kepala Sekolah', 'avatar1.jpg'),
(2, 'Umar', '123', 'Umar Ashari, S.Pd.', 'male', 'Guru Bahasa Inggris', 'avatar1.jpg'),
(3, 'Hendra', '123', 'Hendra, S.Pd.I.', '', '', ''),
(4, 'Evrina', '123', 'Evrina, S.E', 'female', 'TU', 'avatar2.jpg'),
(5, 'Sutrisno', '123', 'Sutrisno, S.Pd.I', '', '', ''),
(6, 'Jaelani', '123', 'Jaelani, S.H.I.', '', '', ''),
(7, 'Erna', '123', 'Erna Muhajaroh, S.Pd.', '', '', ''),
(8, 'Bayu', '123', 'Bayu Sampana, S.Pd.I.', '', '', ''),
(9, 'Dwi', '123', 'Dwi Lestari, S.Kom.', '', '', ''),
(10, 'Resha', '123', 'Resha Desma Yulia, S.E.', '', '', ''),
(11, 'Nyai', '123', 'Nyai, S.E.', '', '', ''),
(12, 'Muhamad', '123', 'Muhamad Amin, S.Pd.I.', '', '', ''),
(13, 'Siti', '123', 'Siti Juriah, S.E.', '', '', ''),
(14, 'Kemal', '123', 'M. Kemal Idris, S.Pd.', '', '', ''),
(15, 'Sumarni', '123', 'Sumarni, S.Pd.', '', '', ''),
(16, 'Eka', '123', 'Eka Is Yuliani, S.Pd.', '', '', ''),
(17, 'Rahmat', '123', 'Rahmat Apriyanto, M.Pd.', '', '', ''),
(18, 'Supriyono', '123', 'Supriyono, S.T.', '', '', ''),
(19, 'Solehudin', '123', 'H. Solehuddin, M.Ag.', '', '', ''),
(20, 'Ari', '123', 'Ari Januar Pratama, S.Kom.', 'male', 'Kaprog RPL', 'avatar1.jpg'),
(21, 'Haryanto', '123', 'Haryanto, S.Pd., M.M.', '', '', ''),
(22, 'Yasin', '123', 'M. Yasin, S.Fil.I.', '', '', ''),
(23, 'Kurnia', '123', 'Kurnia Puspita, S.Pd', '', '', ''),
(24, 'Indra', '123', 'Indra Sari, S.E', '', '', ''),
(25, 'Rizki', '123', 'Rizki Fauzi, SE', '', '', ''),
(26, 'Robi', '123', 'Robi Suryadinata. S.Sos', '', '', ''),
(27, 'Oktofarita', '123', 'Oktofarita Rani Hidayati', '', '', ''),
(28, 'Iqbal', '123', 'M. lqbal, Amd', '', '', ''),
(29, 'Abdul', '123', 'Abdul Rohman, S.Pdl.', '', '', ''),
(30, 'Wiar', '123', 'Wiar Winengsih S.T', '', '', ''),
(31, 'Tria', '123', 'Tria Martagina', '', '', ''),
(32, 'Sahrudin', '123', 'SAHRUDIN, S.Pdl', '', '', '');

-- --------------------------------------------------------

--
-- Struktur dari tabel `izin`
--

CREATE TABLE `izin` (
  `id` int(122) NOT NULL,
  `tanggal` date NOT NULL,
  `nama` varchar(1234) NOT NULL,
  `jenis` varchar(1234) NOT NULL,
  `keterangan` varchar(1234) NOT NULL,
  `gambar` varchar(250) NOT NULL,
  `status` varchar(1234) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `izin`
--

INSERT INTO `izin` (`id`, `tanggal`, `nama`, `jenis`, `keterangan`, `gambar`, `status`) VALUES
(4, '2024-08-07', 'Bayu', 'izin', 'Pergi', '', 'Tidak di izinkan'),
(5, '2024-09-09', 'Uwoh Pramijaya, S.Pd.I., M.M.', 'izin', 'pulang', '66de9749c1360.png', 'Di izinkan'),
(6, '2024-09-09', 'Uwoh Pramijaya, S.Pd.I., M.M.', 'izin', 'pulang', '66de975701108.png', 'Tidak di izinkan'),
(7, '2024-10-05', 'Uwoh Pramijaya, S.Pd.I., M.M.', 'izin', 'sakit', '6700a8a988873.png', 'Di izinkan'),
(8, '2024-10-05', 'Uwoh Pramijaya, S.Pd.I., M.M.', 'izin', 'sakit', '6700a8d017323.png', 'belum di acc'),
(9, '2024-10-05', 'Uwoh Pramijaya, S.Pd.I., M.M.', 'izin', 'sakit', '6700a8e950f2c.png', 'belum di acc'),
(10, '2024-10-04', 'Uwoh Pramijaya, S.Pd.I., M.M.', 'izin', 'sakit', '66ff416bad66b.png', 'Tidak di izinkan'),
(11, '2024-10-06', 'Uwoh Pramijaya, S.Pd.I., M.M.', 'izin', 'sakit', '67023e75b125f.png', 'belum di acc');

-- --------------------------------------------------------

--
-- Struktur dari tabel `kelas`
--

CREATE TABLE `kelas` (
  `id` int(10) NOT NULL,
  `nama_kelas` varchar(1234) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `kelas`
--

INSERT INTO `kelas` (`id`, `nama_kelas`) VALUES
(4, 'X PPLG 1'),
(5, 'X PPLG 2'),
(6, 'X DKV 1'),
(7, 'X AKL'),
(8, 'X PM');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `absen_msk`
--
ALTER TABLE `absen_msk`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `akun_guru`
--
ALTER TABLE `akun_guru`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `izin`
--
ALTER TABLE `izin`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `kelas`
--
ALTER TABLE `kelas`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `absen_msk`
--
ALTER TABLE `absen_msk`
  MODIFY `id` int(230) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=74;

--
-- AUTO_INCREMENT untuk tabel `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT untuk tabel `akun_guru`
--
ALTER TABLE `akun_guru`
  MODIFY `id` int(34) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- AUTO_INCREMENT untuk tabel `izin`
--
ALTER TABLE `izin`
  MODIFY `id` int(122) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT untuk tabel `kelas`
--
ALTER TABLE `kelas`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
