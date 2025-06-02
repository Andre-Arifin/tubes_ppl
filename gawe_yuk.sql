-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 02, 2025 at 03:08 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `gawe_yuk`
--

-- --------------------------------------------------------

--
-- Table structure for table `lamaran`
--

CREATE TABLE `lamaran` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `lowongan_id` int(11) NOT NULL,
  `status` enum('pending','diterima','ditolak') NOT NULL DEFAULT 'pending',
  `cv` varchar(255) NOT NULL,
  `surat_lamaran` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lamaran`
--

INSERT INTO `lamaran` (`id`, `user_id`, `lowongan_id`, `status`, `cv`, `surat_lamaran`, `created_at`) VALUES
(2, 18, 4, 'diterima', '18_1746363963_Modul 2_praktikum DWBI_speedcargo.pdf', 'saya ingin berkerja disini', '2025-05-04 13:06:03'),
(3, 22, 4, 'pending', '22_1748795551_SPD Q1 2025 (1).pdf', 'saya ingin kerja', '2025-06-01 16:32:31');

-- --------------------------------------------------------

--
-- Table structure for table `lowongan`
--

CREATE TABLE `lowongan` (
  `id` int(11) NOT NULL,
  `nama_perusahaan` varchar(255) NOT NULL,
  `alamat` text NOT NULL,
  `gaji` varchar(100) NOT NULL,
  `jenis_pekerjaan` varchar(100) NOT NULL,
  `tunjangan` text NOT NULL,
  `umur` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `whatsapp` varchar(20) NOT NULL,
  `instagram` varchar(100) DEFAULT NULL,
  `facebook` varchar(100) DEFAULT NULL,
  `website` varchar(100) DEFAULT NULL,
  `deskripsi` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lowongan`
--

INSERT INTO `lowongan` (`id`, `nama_perusahaan`, `alamat`, `gaji`, `jenis_pekerjaan`, `tunjangan`, `umur`, `email`, `whatsapp`, `instagram`, `facebook`, `website`, `deskripsi`, `created_at`) VALUES
(3, 'PT.Semangat', 'jl semangat', '3500000 - 4000000', 'Full-time', 'tidak ada', '20-30', 'semangat@gmail.com', 'semangat', 'semangat_86', 'semangat_86', 'semangat.com', 'terster projek', '2025-05-04 10:37:45'),
(4, 'PT.Maju', 'jl maju', '3500000 - 4000000', 'Part-time', 'tidak ada', '20-30', 'maju@gmail.com', 'maju', 'maju', 'maju', 'maju', 'Memasak', '2025-05-04 13:05:13');

-- --------------------------------------------------------

--
-- Table structure for table `masukan`
--

CREATE TABLE `masukan` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `nomor_hp` varchar(15) NOT NULL,
  `email` varchar(100) NOT NULL,
  `jenis_kelamin` varchar(10) NOT NULL,
  `masukan` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `masukan`
--

INSERT INTO `masukan` (`id`, `nama`, `nomor_hp`, `email`, `jenis_kelamin`, `masukan`, `created_at`) VALUES
(6, 'Andre', '978234862', 'contoh@gmail.com', 'Laki-laki', 'kurang lengkap fiturnya', '2025-01-07 06:43:48'),
(7, 'egg', '1234', 'fickiantoegar@gmail.com', 'Laki-laki', 'sayaaaaaaaaaaaaa', '2025-04-27 12:25:50');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `sent_at` datetime DEFAULT current_timestamp(),
  `is_read` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `sender_id`, `receiver_id`, `message`, `sent_at`, `is_read`) VALUES
(1, 18, 22, 'tolong saya', '2025-06-01 23:57:38', 0),
(2, 22, 18, 'ada apa mas', '2025-06-02 00:01:49', 0),
(3, 21, 22, 'halo', '2025-06-02 00:07:13', 0),
(4, 22, 18, 'ada yang bisa saya bantu', '2025-06-02 00:10:05', 0),
(5, 18, 22, 'saya ingin menghubungi cs', '2025-06-02 00:11:12', 0);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `gender` varchar(50) DEFAULT NULL,
  `birthdate` date DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `skills` text DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `education` varchar(100) DEFAULT NULL,
  `major` varchar(100) DEFAULT NULL,
  `work_experience` text DEFAULT NULL,
  `certifications` text DEFAULT NULL,
  `linkedin` varchar(255) DEFAULT NULL,
  `github` varchar(255) DEFAULT NULL,
  `portfolio` varchar(255) DEFAULT NULL,
  `profile_photo` varchar(255) DEFAULT NULL,
  `role` enum('user','admin') NOT NULL DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `full_name`, `email`, `password`, `created_at`, `gender`, `birthdate`, `status`, `skills`, `phone`, `address`, `education`, `major`, `work_experience`, `certifications`, `linkedin`, `github`, `portfolio`, `profile_photo`, `role`) VALUES
(18, 'andre', 'andre@gmail.com', '123456', '2025-01-07 02:44:44', 'Laki-laki', '2025-01-01', 'Pencari Kerja', 'memasak', '', '', '', '', '', '', '', '', '', 'uploads/profile_photos/18_1746363729.jpg', 'user'),
(20, 'Andre Arifin', 'contoh@gmail.com', '123456', '2025-01-07 06:40:28', 'Laki-laki', '2004-01-16', 'Belum Menikah', 'programing', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'user'),
(21, 'eggar', 'egg@bla.com', '123', '2025-04-27 10:50:15', 'Laki-laki', '2025-04-27', 'Pencari Kerja', 'mancing', '', '', '', '', '', '', '', '', '', 'uploads/profile_photos/21_1745755942.jpeg', 'user'),
(22, 'Admin', 'admin@gaweyuk.com', 'admin123', '2025-06-01 16:25:22', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'admin');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `lamaran`
--
ALTER TABLE `lamaran`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `lowongan_id` (`lowongan_id`);

--
-- Indexes for table `lowongan`
--
ALTER TABLE `lowongan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `masukan`
--
ALTER TABLE `masukan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `lamaran`
--
ALTER TABLE `lamaran`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `lowongan`
--
ALTER TABLE `lowongan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `masukan`
--
ALTER TABLE `masukan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `lamaran`
--
ALTER TABLE `lamaran`
  ADD CONSTRAINT `lamaran_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `lamaran_ibfk_2` FOREIGN KEY (`lowongan_id`) REFERENCES `lowongan` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
