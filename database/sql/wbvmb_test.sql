-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th10 23, 2025 lúc 08:48 AM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `wbvmb_test`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `booking`
--

CREATE TABLE `booking` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `id_nguoi_dung` bigint(20) UNSIGNED NOT NULL,
  `ma_booking` varchar(20) NOT NULL COMMENT 'Mã Booking',
  `tong_tien` decimal(10,2) NOT NULL,
  `trang_thai` enum('pending','confirmed','cancelled','paid','thanh_cong','da_huy','dat_thanh_cong') NOT NULL DEFAULT 'pending',
  `phuong_thuc_tt` varchar(50) DEFAULT NULL,
  `id_khuyen_mai` bigint(20) UNSIGNED DEFAULT NULL,
  `ngay_dat` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `chi_tiet_hoa_don`
--

CREATE TABLE `chi_tiet_hoa_don` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `id_hoa_don` bigint(20) UNSIGNED NOT NULL,
  `id_ve` bigint(20) UNSIGNED NOT NULL,
  `so_luong` smallint(5) UNSIGNED NOT NULL DEFAULT 1,
  `gia` decimal(10,2) NOT NULL COMMENT 'Giá gốc của vé/dịch vụ tại thời điểm thanh toán'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `chuyen_bay`
--

CREATE TABLE `chuyen_bay` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ma_chuyen_bay` varchar(10) NOT NULL,
  `id_may_bay` bigint(20) UNSIGNED NOT NULL,
  `id_san_bay_di` bigint(20) UNSIGNED NOT NULL,
  `id_san_bay_den` bigint(20) UNSIGNED NOT NULL,
  `thoi_gian_di` datetime NOT NULL,
  `thoi_gian_den` datetime NOT NULL,
  `gia_ve` decimal(10,2) NOT NULL,
  `trang_thai` varchar(20) NOT NULL DEFAULT 'dang_ban',
  `ngay_tao` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `danh_muc_bai_viet`
--

CREATE TABLE `danh_muc_bai_viet` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ten_danh_muc` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `trang_thai` enum('active','inactive') NOT NULL DEFAULT 'active',
  `ngay_tao` timestamp NOT NULL DEFAULT current_timestamp(),
  `id_danh_muc_cha` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `doanh_thu`
--

CREATE TABLE `doanh_thu` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `id_hoa_don` bigint(20) UNSIGNED NOT NULL,
  `tong_doanh_thu` decimal(15,2) NOT NULL COMMENT 'Tổng doanh thu đã trừ giảm giá',
  `ngay_ghi_nhan` date NOT NULL,
  `loai_doanh_thu` varchar(50) NOT NULL COMMENT 'Phân loại doanh thu',
  `thang` tinyint(3) UNSIGNED DEFAULT NULL COMMENT 'Tháng thống kê',
  `nam` year(4) DEFAULT NULL COMMENT 'Năm thống kê',
  `doanh_thu` decimal(15,2) DEFAULT NULL COMMENT 'Giá trị doanh thu khác (Có thể là giá trị gốc)',
  `ngay_cap_nhat` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `ghe`
--

CREATE TABLE `ghe` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `id_may_bay` bigint(20) UNSIGNED NOT NULL,
  `so_ghe` varchar(5) NOT NULL,
  `loai_ghe` enum('Business','Economy') NOT NULL,
  `trang_thai` enum('available','booked','sold') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `hoa_don`
--

CREATE TABLE `hoa_don` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `id_booking` bigint(20) UNSIGNED NOT NULL,
  `tong_tien` decimal(15,2) NOT NULL COMMENT 'Tổng tiền phải trả sau giảm giá',
  `trang_thai` enum('da_thanh_toan','chua_thanh_toan','da_huy') NOT NULL DEFAULT 'chua_thanh_toan',
  `phuong_thuc_tt` varchar(50) DEFAULT NULL,
  `ngay_tao` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `khuyen_mai`
--

CREATE TABLE `khuyen_mai` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ma_khuyen_mai` varchar(20) NOT NULL,
  `mo_ta` text DEFAULT NULL,
  `loai_gia_tri` enum('phan_tram','gia_tri_co_dinh') NOT NULL,
  `gia_tri` decimal(10,2) NOT NULL,
  `ngay_bat_dau` date NOT NULL,
  `ngay_ket_thuc` date NOT NULL,
  `trang_thai` enum('active','inactive','expired') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `may_bay`
--

CREATE TABLE `may_bay` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ma_may_bay` varchar(10) NOT NULL COMMENT 'Mã định danh máy bay',
  `ten_may_bay` varchar(100) NOT NULL COMMENT 'Tên hoặc số hiệu máy bay',
  `hang_san_xuat` varchar(50) NOT NULL COMMENT 'Hãng sản xuất (ví dụ: Boeing, Airbus)',
  `so_ghe` smallint(5) UNSIGNED NOT NULL COMMENT 'Tổng số ghế',
  `trang_thai` enum('hoat_dong','bao_tri','ngung_su_dung','active') NOT NULL DEFAULT 'hoat_dong',
  `ngay_tao` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_resets_table', 1),
(3, '2019_08_19_000000_create_failed_jobs_table', 1),
(4, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(5, '2025_11_18_000003_MayBay', 1),
(6, '2025_11_18_000004_SanBay', 1),
(7, '2025_11_18_000005_ChuyenBay', 1),
(8, '2025_11_19_000005_create_khuyen_mai_table', 1),
(9, '2025_11_19_000006_create_booking_table', 1),
(10, '2025_11_19_000007_create_ve_table', 1),
(11, '2025_11_19_000008_create_thong_tin_nguoi_di_table', 1),
(12, '2025_11_19_081110_add_dia_chi_to_san_bay_table', 1),
(13, '2025_11_19_091736_create_ghe_table', 1),
(14, '2025_11_19_100000_create_danh_muc_bai_viet_table', 1),
(15, '2025_11_19_130847_create_hoa_don_table', 1),
(16, '2025_11_19_131415_create_chi_tiet_hoa_don_table', 1),
(17, '2025_11_19_133136_create_doanh_thu_table', 1),
(18, '2025_11_20_150035_update_chuyen_bay_trang_thai_column', 1);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `nguoi_dung`
--

CREATE TABLE `nguoi_dung` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ho_ten` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `mat_khau` varchar(255) NOT NULL,
  `so_dien_thoai` varchar(255) DEFAULT NULL,
  `dia_chi` varchar(255) DEFAULT NULL,
  `vai_tro` enum('admin','nhan_vien','khach_hang') NOT NULL DEFAULT 'khach_hang',
  `trang_thai` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1: Kích hoạt, 0: Khóa',
  `remember_token` varchar(100) DEFAULT NULL,
  `ngay_tao` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `san_bay`
--

CREATE TABLE `san_bay` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ma_san_bay` varchar(5) NOT NULL COMMENT 'Mã sân bay (ví dụ: SGN, HAN)',
  `ten_san_bay` varchar(150) NOT NULL,
  `tinh_thanh` varchar(50) DEFAULT NULL,
  `quoc_gia` varchar(50) NOT NULL,
  `ngay_tao` timestamp NOT NULL DEFAULT current_timestamp(),
  `dia_chi` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `thong_tin_nguoi_di`
--

CREATE TABLE `thong_tin_nguoi_di` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `id_ve` bigint(20) UNSIGNED NOT NULL,
  `ho_ten` varchar(100) NOT NULL,
  `so_dien_thoai` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `dia_chi` varchar(255) DEFAULT NULL,
  `ghi_chu` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `ve`
--

CREATE TABLE `ve` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `id_booking` bigint(20) UNSIGNED NOT NULL,
  `id_chuyen_bay` bigint(20) UNSIGNED NOT NULL,
  `so_ghe` varchar(10) DEFAULT NULL COMMENT 'Số ghế',
  `gia_ve` decimal(10,2) NOT NULL,
  `trang_thai` enum('da_dat','da_thanh_toan','da_huy','da_xuat','booked','checked_in','used','cancelled') NOT NULL DEFAULT 'da_dat'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `booking`
--
ALTER TABLE `booking`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `booking_ma_booking_unique` (`ma_booking`),
  ADD KEY `booking_id_nguoi_dung_foreign` (`id_nguoi_dung`),
  ADD KEY `booking_id_khuyen_mai_foreign` (`id_khuyen_mai`);

--
-- Chỉ mục cho bảng `chi_tiet_hoa_don`
--
ALTER TABLE `chi_tiet_hoa_don`
  ADD PRIMARY KEY (`id`),
  ADD KEY `chi_tiet_hoa_don_id_hoa_don_foreign` (`id_hoa_don`),
  ADD KEY `chi_tiet_hoa_don_id_ve_foreign` (`id_ve`);

--
-- Chỉ mục cho bảng `chuyen_bay`
--
ALTER TABLE `chuyen_bay`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `chuyen_bay_ma_chuyen_bay_unique` (`ma_chuyen_bay`),
  ADD KEY `chuyen_bay_id_may_bay_foreign` (`id_may_bay`),
  ADD KEY `chuyen_bay_id_san_bay_di_foreign` (`id_san_bay_di`),
  ADD KEY `chuyen_bay_id_san_bay_den_foreign` (`id_san_bay_den`);

--
-- Chỉ mục cho bảng `danh_muc_bai_viet`
--
ALTER TABLE `danh_muc_bai_viet`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `danh_muc_bai_viet_slug_unique` (`slug`),
  ADD KEY `danh_muc_bai_viet_id_danh_muc_cha_foreign` (`id_danh_muc_cha`);

--
-- Chỉ mục cho bảng `doanh_thu`
--
ALTER TABLE `doanh_thu`
  ADD PRIMARY KEY (`id`),
  ADD KEY `doanh_thu_id_hoa_don_foreign` (`id_hoa_don`);

--
-- Chỉ mục cho bảng `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Chỉ mục cho bảng `ghe`
--
ALTER TABLE `ghe`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ghe_id_may_bay_foreign` (`id_may_bay`);

--
-- Chỉ mục cho bảng `hoa_don`
--
ALTER TABLE `hoa_don`
  ADD PRIMARY KEY (`id`),
  ADD KEY `hoa_don_id_booking_foreign` (`id_booking`);

--
-- Chỉ mục cho bảng `khuyen_mai`
--
ALTER TABLE `khuyen_mai`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `khuyen_mai_ma_khuyen_mai_unique` (`ma_khuyen_mai`);

--
-- Chỉ mục cho bảng `may_bay`
--
ALTER TABLE `may_bay`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `may_bay_ma_may_bay_unique` (`ma_may_bay`);

--
-- Chỉ mục cho bảng `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `nguoi_dung`
--
ALTER TABLE `nguoi_dung`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nguoi_dung_email_unique` (`email`);

--
-- Chỉ mục cho bảng `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`email`);

--
-- Chỉ mục cho bảng `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Chỉ mục cho bảng `san_bay`
--
ALTER TABLE `san_bay`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `san_bay_ma_san_bay_unique` (`ma_san_bay`);

--
-- Chỉ mục cho bảng `thong_tin_nguoi_di`
--
ALTER TABLE `thong_tin_nguoi_di`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `thong_tin_nguoi_di_id_ve_unique` (`id_ve`);

--
-- Chỉ mục cho bảng `ve`
--
ALTER TABLE `ve`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ve_id_booking_foreign` (`id_booking`),
  ADD KEY `ve_id_chuyen_bay_foreign` (`id_chuyen_bay`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `booking`
--
ALTER TABLE `booking`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `chi_tiet_hoa_don`
--
ALTER TABLE `chi_tiet_hoa_don`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `chuyen_bay`
--
ALTER TABLE `chuyen_bay`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `danh_muc_bai_viet`
--
ALTER TABLE `danh_muc_bai_viet`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `doanh_thu`
--
ALTER TABLE `doanh_thu`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `ghe`
--
ALTER TABLE `ghe`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `hoa_don`
--
ALTER TABLE `hoa_don`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `khuyen_mai`
--
ALTER TABLE `khuyen_mai`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `may_bay`
--
ALTER TABLE `may_bay`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT cho bảng `nguoi_dung`
--
ALTER TABLE `nguoi_dung`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `san_bay`
--
ALTER TABLE `san_bay`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT cho bảng `thong_tin_nguoi_di`
--
ALTER TABLE `thong_tin_nguoi_di`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `ve`
--
ALTER TABLE `ve`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `booking`
--
ALTER TABLE `booking`
  ADD CONSTRAINT `booking_id_khuyen_mai_foreign` FOREIGN KEY (`id_khuyen_mai`) REFERENCES `khuyen_mai` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `booking_id_nguoi_dung_foreign` FOREIGN KEY (`id_nguoi_dung`) REFERENCES `nguoi_dung` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `chi_tiet_hoa_don`
--
ALTER TABLE `chi_tiet_hoa_don`
  ADD CONSTRAINT `chi_tiet_hoa_don_id_hoa_don_foreign` FOREIGN KEY (`id_hoa_don`) REFERENCES `hoa_don` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `chi_tiet_hoa_don_id_ve_foreign` FOREIGN KEY (`id_ve`) REFERENCES `ve` (`id`);

--
-- Các ràng buộc cho bảng `chuyen_bay`
--
ALTER TABLE `chuyen_bay`
  ADD CONSTRAINT `chuyen_bay_id_may_bay_foreign` FOREIGN KEY (`id_may_bay`) REFERENCES `may_bay` (`id`),
  ADD CONSTRAINT `chuyen_bay_id_san_bay_den_foreign` FOREIGN KEY (`id_san_bay_den`) REFERENCES `san_bay` (`id`),
  ADD CONSTRAINT `chuyen_bay_id_san_bay_di_foreign` FOREIGN KEY (`id_san_bay_di`) REFERENCES `san_bay` (`id`);

--
-- Các ràng buộc cho bảng `danh_muc_bai_viet`
--
ALTER TABLE `danh_muc_bai_viet`
  ADD CONSTRAINT `danh_muc_bai_viet_id_danh_muc_cha_foreign` FOREIGN KEY (`id_danh_muc_cha`) REFERENCES `danh_muc_bai_viet` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `doanh_thu`
--
ALTER TABLE `doanh_thu`
  ADD CONSTRAINT `doanh_thu_id_hoa_don_foreign` FOREIGN KEY (`id_hoa_don`) REFERENCES `hoa_don` (`id`);

--
-- Các ràng buộc cho bảng `ghe`
--
ALTER TABLE `ghe`
  ADD CONSTRAINT `ghe_id_may_bay_foreign` FOREIGN KEY (`id_may_bay`) REFERENCES `may_bay` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `hoa_don`
--
ALTER TABLE `hoa_don`
  ADD CONSTRAINT `hoa_don_id_booking_foreign` FOREIGN KEY (`id_booking`) REFERENCES `booking` (`id`);

--
-- Các ràng buộc cho bảng `thong_tin_nguoi_di`
--
ALTER TABLE `thong_tin_nguoi_di`
  ADD CONSTRAINT `thong_tin_nguoi_di_id_ve_foreign` FOREIGN KEY (`id_ve`) REFERENCES `ve` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `ve`
--
ALTER TABLE `ve`
  ADD CONSTRAINT `ve_id_booking_foreign` FOREIGN KEY (`id_booking`) REFERENCES `booking` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ve_id_chuyen_bay_foreign` FOREIGN KEY (`id_chuyen_bay`) REFERENCES `chuyen_bay` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
