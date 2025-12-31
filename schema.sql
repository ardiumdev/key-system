-- Veritabanı Şeması - Webui Key System
-- Bu dosya veritabanı tablolarını oluşturur ve varsayılan verileri ekler.

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Veritabanı: `key_system_db`
--
-- Eğer veritabanı yoksa oluşturulmalıdır (hosting panelinden manuel oluşturmanız gerekebilir)
-- CREATE DATABASE IF NOT EXISTS `key_system_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
-- USE `key_system_db`;

-- --------------------------------------------------------

--
-- Tablo yapısı: `access_keys`
--

CREATE TABLE IF NOT EXISTS `access_keys` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `key_code` varchar(255) NOT NULL,
  `target_url` text NOT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `key_code` (`key_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tablo yapısı: `admins`
--

CREATE TABLE IF NOT EXISTS `admins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi: `admins`
--
-- Varsayılan admin hesabı: Kullanıcı Adı: admin, Şifre: admin123
-- Şifre hash'i bcrypt ile oluşturulmuştur.
-- Eğer bu şifre çalışmazsa setup.php dosyasını çalıştırarak sıfırlayabilirsiniz.

INSERT INTO `admins` (`id`, `username`, `password`, `created_at`) VALUES
(1, 'admin', '$2y$10$uVc//dPMwKLE.b2afFl6cuk7cjRyPpVGYW/Sp3EQdcNLIUxpivF7e', '2025-01-01 00:00:00'); 
-- Not: Yukarıdaki hash örnektir, Lütfen hash kismini farklı birşey yapın yoksa admin panele başkaları giriş yapabilir.

-- --------------------------------------------------------

--
-- Tablo yapısı: `settings`
--

CREATE TABLE IF NOT EXISTS `settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(50) NOT NULL,
  `setting_value` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `setting_key` (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi: `settings`
--

INSERT IGNORE INTO `settings` (`setting_key`, `setting_value`) VALUES
('footer_text', 'Yapımcı <strong>Webui</strong>'),
('site_title', 'Webui Key System'),
('site_desc', 'Gelişmiş key doğrulama ve yönlendirme sistemi.');

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

