-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 15 Apr 2025 pada 15.31
-- Versi server: 10.4.24-MariaDB
-- Versi PHP: 8.1.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `spk_bansos_ahp`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `analisa`
--

CREATE TABLE `analisa` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`data`)),
  `tipe` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `criteria_id` int(11) DEFAULT NULL,
  `subcriteria_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `analisa`
--

INSERT INTO `analisa` (`id`, `data`, `tipe`, `criteria_id`, `subcriteria_id`, `created_at`, `updated_at`) VALUES
(1, '[\"1\",\"5\",\"7\"]', 'Sub Kriteria 1', NULL, 1, NULL, NULL),
(2, '[\"0.2\",\"1\",\"5\"]', 'Sub Kriteria 1', NULL, 2, NULL, NULL),
(3, '[\"0.14\",\"0.2\",\"1\"]', 'Sub Kriteria 1', NULL, 3, NULL, NULL),
(4, '[\"1\",\"3\",\"7\"]', 'Sub Kriteria 2', NULL, 4, NULL, NULL),
(5, '[\"0.33\",\"1\",\"3\"]', 'Sub Kriteria 2', NULL, 5, NULL, NULL),
(6, '[\"0.14\",\"0.33\",\"1\"]', 'Sub Kriteria 2', NULL, 6, NULL, NULL),
(7, '[\"1\",\"6\",\"8\"]', 'Sub Kriteria 3', NULL, 7, NULL, NULL),
(8, '[\"0.167\",\"1\",\"6\"]', 'Sub Kriteria 3', NULL, 8, NULL, NULL),
(9, '[\"0.125\",\"0.167\",\"1\"]', 'Sub Kriteria 3', NULL, 9, NULL, NULL),
(10, '[\"1\",\"3\",\"5\"]', 'Sub Kriteria 4', NULL, 10, NULL, NULL),
(11, '[\"0.33\",\"1\",\"3\"]', 'Sub Kriteria 4', NULL, 11, NULL, NULL),
(12, '[\"0.2\",\"0.33\",\"1\"]', 'Sub Kriteria 4', NULL, 12, NULL, NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `consistency_criteria`
--

CREATE TABLE `consistency_criteria` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `criteria_ids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`criteria_ids`)),
  `lambda_max` decimal(10,9) NOT NULL,
  `CI` decimal(10,9) NOT NULL,
  `CR` decimal(10,9) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `consistency_subcriteria`
--

CREATE TABLE `consistency_subcriteria` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `subcriteria_ids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`subcriteria_ids`)),
  `lambda_max` decimal(10,9) NOT NULL,
  `CI` decimal(10,9) NOT NULL,
  `CR` decimal(10,9) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `consistency_subcriteria`
--

INSERT INTO `consistency_subcriteria` (`id`, `subcriteria_ids`, `lambda_max`, `CI`, `CR`, `created_at`, `updated_at`) VALUES
(1, '[1,2,3]', '1.432192880', '-0.783903560', '-1.351557862', NULL, '2025-03-29 09:11:34'),
(2, '[4,5,6]', '1.332537381', '-0.833731309', '-1.437467775', NULL, '2025-03-29 09:16:29'),
(3, '[7,8,9]', '1.484959163', '-0.757520418', '-1.306069687', NULL, '2025-03-29 09:18:48'),
(4, '[10,11,12]', '1.349964880', '-0.825017560', '-1.422444069', NULL, '2025-03-29 09:21:37');

-- --------------------------------------------------------

--
-- Struktur dari tabel `criteria`
--

CREATE TABLE `criteria` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `kode_criteria` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nilai_prioritas` decimal(4,3) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `criteria`
--

INSERT INTO `criteria` (`id`, `kode_criteria`, `nama`, `nilai_prioritas`, `created_at`, `updated_at`) VALUES
(1, 'K1', 'Usia', '0.414', NULL, NULL),
(2, 'K2', 'Pekerjaan', '0.159', NULL, NULL),
(3, 'K3', 'Pendapatan', '0.058', NULL, NULL),
(4, 'K4', 'Jumlah Tanggungan Anak', '0.260', NULL, NULL),
(5, 'K5', 'Kondisi Kepemilikan Rumah', '0.110', NULL, NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_reset_tokens_table', 1),
(3, '2014_10_12_100000_create_password_resets_table', 1),
(4, '2019_08_19_000000_create_failed_jobs_table', 1),
(5, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(6, '2025_02_26_064043_create_warga_table', 1),
(7, '2025_02_27_083406_create_criteria_table', 1),
(8, '2025_02_27_083455_create_subcriteria_table', 1),
(9, '2025_03_02_142359_create_perhitungan_table', 1),
(10, '2025_03_15_164329_create_settings_table', 1),
(11, '2025_03_16_160716_create_analisa_table', 1),
(12, '2025_03_27_225009_create_consistency_criteria_table', 1),
(13, '2025_03_27_225015_create_consistency_subcriteria_table', 1);

-- --------------------------------------------------------

--
-- Struktur dari tabel `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `perhitungan`
--

CREATE TABLE `perhitungan` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `warga_id` bigint(20) UNSIGNED NOT NULL,
  `k1` decimal(10,9) NOT NULL,
  `k2` decimal(10,9) NOT NULL,
  `k3` decimal(10,9) NOT NULL,
  `k4` decimal(10,9) NOT NULL,
  `k5` decimal(10,9) NOT NULL,
  `nilai_akhir` decimal(10,9) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `perhitungan`
--

INSERT INTO `perhitungan` (`id`, `warga_id`, `k1`, `k2`, `k3`, `k4`, `k5`, `nilai_akhir`, `created_at`, `updated_at`) VALUES
(1, 2, '0.414000000', '0.159000000', '0.058000000', '0.260000000', '0.110000000', '1.000000000', NULL, NULL),
(2, 3, '0.137623652', '0.057616023', '0.018087944', '0.106620599', '0.049091547', '0.369039765', NULL, NULL),
(3, 4, '0.042299139', '0.020759231', '0.005049945', '0.043475979', '0.049091547', '0.160675841', NULL, NULL),
(4, 5, '0.137623652', '0.020759231', '0.005049945', '0.260000000', '0.110000000', '0.533432828', NULL, NULL),
(5, 6, '0.414000000', '0.057616023', '0.018087944', '0.106620599', '0.021655893', '0.617980459', NULL, NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `settings`
--

CREATE TABLE `settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `settings`
--

INSERT INTO `settings` (`id`, `key`, `value`, `created_at`, `updated_at`) VALUES
(1, 'rekomendasi_limit', '10', NULL, NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `subcriteria`
--

CREATE TABLE `subcriteria` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `criteria_id` bigint(20) UNSIGNED NOT NULL,
  `sub_criteria` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `bobot` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nilai_prioritas` decimal(10,9) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `subcriteria`
--

INSERT INTO `subcriteria` (`id`, `criteria_id`, `sub_criteria`, `bobot`, `nilai_prioritas`, `created_at`, `updated_at`) VALUES
(1, 1, '>50 tahun', 'Sangat Baik (A)', '1.000000000', NULL, NULL),
(2, 1, '30-50 tahun', 'Baik (B)', '0.332424200', NULL, NULL),
(3, 1, '<30 tahun', 'Cukup (C)', '0.102171294', NULL, NULL),
(4, 2, 'Tidak Bekerja', 'Sangat Baik (A)', '1.000000000', NULL, NULL),
(5, 2, 'Pekerjaan tidak tetap', 'Baik (B)', '0.362365019', NULL, NULL),
(6, 2, 'Pekerjaan tetap', 'Cukup (C)', '0.130561204', NULL, NULL),
(7, 3, 'Tidak memiliki pendapatan', 'Sangat Baik (A)', '1.000000000', NULL, NULL),
(8, 3, '<Rp. 1.000.000', 'Baik (B)', '0.311861104', NULL, NULL),
(9, 3, '>Rp. 1.000.000', 'Cukup (C)', '0.087068012', NULL, NULL),
(10, 4, '>3 Anak', 'Sangat Baik (A)', '1.000000000', NULL, NULL),
(11, 4, '1-3 Anak', 'Baik (B)', '0.410078965', NULL, NULL),
(12, 4, 'Tidak Memiliki tanggungan', 'Cukup (C)', '0.167215587', NULL, NULL),
(13, 5, 'Menumpang', 'Sangat Baik (A)', '1.000000000', NULL, NULL),
(14, 5, 'Kontrak / Sewa', 'Baik (B)', '0.446286790', NULL, NULL),
(15, 5, 'Milik Sendiri', 'Cukup (C)', '0.196871755', NULL, NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `alamat` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `RT` varchar(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `RW` varchar(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ACTIVE',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `name`, `username`, `role`, `alamat`, `RT`, `RW`, `email`, `email_verified_at`, `password`, `status`, `remember_token`, `created_at`, `updated_at`) VALUES
(2, 'Slamet Riyadi', 'RT 01', 'RT', 'Jl Wilis Tama III No. 07', '001', '005', 'rt1@gmail.com', NULL, '$2y$12$r8sXs9kJuxsqNOqAgab6nuqK.9vI6MpEF2.AAFlIBFzD5h58wipXe', 'ACTIVE', NULL, NULL, NULL),
(4, 'Budi Santoso', 'RT 02', 'RT', 'Jl Wilis Mulya VI No 30', '002', '005', 'rt2@gmail.com', NULL, '$2y$12$MA3yLGCt26TwR7nydD3fRuaUi9I9rJ8y7567JQt8ADj6HnyfvV.pO', 'ACTIVE', NULL, NULL, NULL),
(5, 'Agus Supriyadi', 'RT 03', 'RT', 'Jl Wilis Arum No. 05', '003', '005', 'rt3@gmail.com', NULL, '$2y$12$d/jfqgFTXbborEGYSZ0dwezzXrqlHhSsYGIG7qJBcYYt34gPKqVHm', 'ACTIVE', NULL, NULL, NULL),
(6, 'Joko Susilo', 'RT 04', 'RT', 'Jl Wilis Tama IV No 23', '004', '005', 'rt4@gmail.com', NULL, '$2y$12$pwT9TpkiZdU/DJnj2wfLFeHh4Io33ChDGmTMGUMzogbU6.w/qrY/K', 'ACTIVE', NULL, NULL, NULL),
(7, 'Hendro Wahyudi', 'RT 05', 'RT', 'JL', '005', '005', 'rt5@gmail.com', NULL, '$2y$12$4HzEQ5wEDsh.wszWSk5GMuU6MMTPOrL/vkr67K7TMeOpEIcY.e0FS', 'ACTIVE', NULL, NULL, NULL),
(8, 'Dhany Adi Projo', 'staff kelurahan 1', 'Staff Kelurahan', NULL, NULL, NULL, 'staffkelurahan1@gmail.com', NULL, '$2y$12$XlU0b/34OfYF3xC0ObZDUOEBH0hal.rd.Ytm5D3NXdp0.BoouA3ne', 'ACTIVE', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `warga`
--

CREATE TABLE `warga` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `NIK` varchar(16) COLLATE utf8mb4_unicode_ci NOT NULL,
  `alamat` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kelurahan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kecamatan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `RT` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL,
  `RW` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL,
  `usia` int(11) NOT NULL,
  `status_pekerjaan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pendapatan` int(11) NOT NULL,
  `jumlah_tanggungan_anak` int(11) NOT NULL,
  `kepemilikan_rumah` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `warga`
--

INSERT INTO `warga` (`id`, `nama`, `NIK`, `alamat`, `kelurahan`, `kecamatan`, `RT`, `RW`, `usia`, `status_pekerjaan`, `pendapatan`, `jumlah_tanggungan_anak`, `kepemilikan_rumah`, `created_at`, `updated_at`) VALUES
(2, 'Siti Halimah', '3573019876543209', 'Jl Wilis Mulya VI No 31', 'Campurejo', 'Mojoroto', '001', '005', 53, 'Tidak Bekerja', 0, 5, 'Menumpang', NULL, NULL),
(3, 'Yuliasari', '3573019876543210', 'Jl Wilis Mulya VI No 05', 'Campurejo', 'Mojoroto', '001', '005', 39, 'Pekerjaan Tidak Tetap', 350000, 2, 'Sewa', NULL, NULL),
(4, 'Lambang Basuki', '3573019876543212', 'Jl Wilis Tama III No. 01', 'Campurejo', 'Mojoroto', '002', '005', 25, 'Pekerjaan Tetap', 3500000, 0, 'Sewa', NULL, NULL),
(5, 'Aji Rohman', '3573019876543213', 'Jl Wilis Tama III No. 33', 'Campurejo', 'Mojoroto', '002', '005', 37, 'Pekerjaan Tetap', 2500000, 4, 'Menumpang', NULL, NULL),
(6, 'Nurkholis', '3573019876543215', 'Jl Wilis Tama III No. 09', 'Campurejo', 'Mojoroto', '002', '005', 75, 'Pekerjaan Tidak Tetap', 500000, 3, 'Milik Sendiri', NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `analisa`
--
ALTER TABLE `analisa`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `consistency_criteria`
--
ALTER TABLE `consistency_criteria`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `consistency_subcriteria`
--
ALTER TABLE `consistency_subcriteria`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `criteria`
--
ALTER TABLE `criteria`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `criteria_kode_criteria_unique` (`kode_criteria`);

--
-- Indeks untuk tabel `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indeks untuk tabel `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Indeks untuk tabel `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indeks untuk tabel `perhitungan`
--
ALTER TABLE `perhitungan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `perhitungan_warga_id_foreign` (`warga_id`);

--
-- Indeks untuk tabel `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indeks untuk tabel `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `settings_key_unique` (`key`);

--
-- Indeks untuk tabel `subcriteria`
--
ALTER TABLE `subcriteria`
  ADD PRIMARY KEY (`id`),
  ADD KEY `subcriteria_criteria_id_foreign` (`criteria_id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- Indeks untuk tabel `warga`
--
ALTER TABLE `warga`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `warga_nik_unique` (`NIK`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `analisa`
--
ALTER TABLE `analisa`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT untuk tabel `consistency_criteria`
--
ALTER TABLE `consistency_criteria`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `consistency_subcriteria`
--
ALTER TABLE `consistency_subcriteria`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `criteria`
--
ALTER TABLE `criteria`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT untuk tabel `perhitungan`
--
ALTER TABLE `perhitungan`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `settings`
--
ALTER TABLE `settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `subcriteria`
--
ALTER TABLE `subcriteria`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT untuk tabel `warga`
--
ALTER TABLE `warga`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `perhitungan`
--
ALTER TABLE `perhitungan`
  ADD CONSTRAINT `perhitungan_warga_id_foreign` FOREIGN KEY (`warga_id`) REFERENCES `warga` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `subcriteria`
--
ALTER TABLE `subcriteria`
  ADD CONSTRAINT `subcriteria_criteria_id_foreign` FOREIGN KEY (`criteria_id`) REFERENCES `criteria` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
