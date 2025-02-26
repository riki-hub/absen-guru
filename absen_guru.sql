-- phpMyAdmin SQL Dump
-- version 4.9.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 11 Agu 2024 pada 15.14
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
  `jam0` varchar(300) NOT NULL,
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
  `foto_absen` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
(14, 'rpl', 'rpl', 'rpl', '66b8b5242d76e.png');

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
  `status` varchar(1234) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `izin`
--

INSERT INTO `izin` (`id`, `tanggal`, `nama`, `jenis`, `keterangan`, `status`) VALUES
(4, '2024-08-07', 'Bayu', 'izin', 'Pergi', 'Di izinkan');

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
(1, 'XI RPl 1'),
(2, 'XI RPL 2');

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
  MODIFY `id` int(230) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT untuk tabel `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT untuk tabel `akun_guru`
--
ALTER TABLE `akun_guru`
  MODIFY `id` int(34) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- AUTO_INCREMENT untuk tabel `izin`
--
ALTER TABLE `izin`
  MODIFY `id` int(122) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `kelas`
--
ALTER TABLE `kelas`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
