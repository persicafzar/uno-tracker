-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Aug 06, 2026 at 06:59 AM
-- Server version: 8.3.0
-- PHP Version: 8.1.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `uno_tracker`
--

-- --------------------------------------------------------

--
-- Table structure for table `achievements`
--

DROP TABLE IF EXISTS `achievements`;
CREATE TABLE IF NOT EXISTS `achievements` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'کد یکتا برای شناسایی',
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'نام نشان',
  `description` text COLLATE utf8mb4_unicode_ci COMMENT 'توضیحات',
  `icon` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 0xF09F8F85 COMMENT 'ایموجی یا آیکون',
  `category` enum('general','winning','streak','team','special') COLLATE utf8mb4_unicode_ci DEFAULT 'general' COMMENT 'دسته‌بندی',
  `rarity` enum('common','rare','epic','legendary') COLLATE utf8mb4_unicode_ci DEFAULT 'common' COMMENT 'کمیابی',
  `xp_reward` int UNSIGNED DEFAULT '10' COMMENT 'XP پاداش',
  `condition_type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'نوع شرط (total_games, total_wins, win_streak, etc)',
  `condition_value` int UNSIGNED NOT NULL COMMENT 'مقدار شرط',
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`),
  KEY `idx_code` (`code`),
  KEY `idx_category` (`category`),
  KEY `idx_rarity` (`rarity`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `achievements`
--

INSERT INTO `achievements` (`id`, `code`, `name`, `description`, `icon`, `category`, `rarity`, `xp_reward`, `condition_type`, `condition_value`, `is_active`, `created_at`) VALUES
(1, 'first_game', 'اولین قدم', 'اولین بازی خود را انجام دهید', '🎯', 'general', 'common', 10, 'total_games', 1, 1, '2026-07-04 14:32:34'),
(2, 'games_10', 'عاشق بازی', '۱۰ بازی انجام دهید', '🎮', 'general', 'common', 20, 'total_games', 10, 1, '2026-07-04 14:32:34'),
(3, 'games_50', 'بازیکن جدی', '۵۰ بازی انجام دهید', '🕹️', 'general', 'rare', 50, 'total_games', 50, 1, '2026-07-04 14:32:34'),
(4, 'games_100', 'کهنه‌کار', '۱۰۰ بازی انجام دهید', '🏅', 'general', 'epic', 100, 'total_games', 100, 1, '2026-07-04 14:32:34'),
(5, 'first_win', 'اولین پیروزی', 'اولین برد خود را کسب کنید', '🏆', 'winning', 'common', 15, 'total_wins', 1, 1, '2026-07-04 14:32:34'),
(6, 'wins_10', 'برنده ده‌تایی', '۱۰ برد کسب کنید', '🥇', 'winning', 'common', 30, 'total_wins', 10, 1, '2026-07-04 14:32:34'),
(7, 'wins_50', 'فاتح میدان', '۵۰ برد کسب کنید', '⚔️', 'winning', 'rare', 75, 'total_wins', 50, 1, '2026-07-04 14:32:34'),
(8, 'wins_100', 'افسانه پیروزی', '۱۰۰ برد کسب کنید', '👑', 'winning', 'epic', 150, 'total_wins', 100, 1, '2026-07-04 14:32:34'),
(9, 'streak_3', 'آتشین', '۳ برد متوالی', '🔥', 'streak', 'common', 25, 'best_streak', 3, 1, '2026-07-04 14:32:34'),
(10, 'streak_5', 'طوفانی', '۵ برد متوالی', '⚡', 'streak', 'rare', 50, 'best_streak', 5, 1, '2026-07-04 14:32:34'),
(11, 'streak_10', 'شکست‌ناپذیر', '۱۰ برد متوالی', '💥', 'streak', 'epic', 100, 'best_streak', 10, 1, '2026-07-04 14:32:34'),
(12, 'team_player', 'تیم‌باز', '۱۰ بازی تیمی انجام دهید', '👥', 'team', 'common', 20, 'team_games', 10, 1, '2026-07-04 14:32:34'),
(13, 'team_winner', 'رهبر تیم', '۱۰ برد تیمی کسب کنید', '🤝', 'team', 'rare', 40, 'team_wins', 10, 1, '2026-07-04 14:32:34'),
(14, 'points_100', 'امتیازآور', '۱۰۰ امتیاز کسب کنید', '💯', 'special', 'common', 15, 'total_points', 100, 1, '2026-07-04 14:32:34'),
(15, 'points_500', 'ستاره امتیاز', '۵۰۰ امتیاز کسب کنید', '⭐', 'special', 'rare', 50, 'total_points', 500, 1, '2026-07-04 14:32:34'),
(16, 'points_1000', 'نابغه', '۱۰۰۰ امتیاز کسب کنید', '🧠', 'special', 'epic', 100, 'total_points', 1000, 1, '2026-07-04 14:32:34');

-- --------------------------------------------------------

--
-- Table structure for table `admin_logs`
--

DROP TABLE IF EXISTS `admin_logs`;
CREATE TABLE IF NOT EXISTS `admin_logs` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `admin_id` int UNSIGNED NOT NULL,
  `action_type` enum('user_ban','user_unban','user_delete','user_role_change','game_delete','game_edit','achievement_create','achievement_edit','achievement_delete','challenge_create','challenge_edit','challenge_delete','setting_change','login','logout') COLLATE utf8mb4_unicode_ci NOT NULL,
  `target_type` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'نوع هدف (user, game, achievement, ...)',
  `target_id` int UNSIGNED DEFAULT NULL COMMENT 'شناسه هدف',
  `description` text COLLATE utf8mb4_unicode_ci,
  `old_data` json DEFAULT NULL COMMENT 'داده‌های قبلی',
  `new_data` json DEFAULT NULL COMMENT 'داده‌های جدید',
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_admin` (`admin_id`),
  KEY `idx_action` (`action_type`),
  KEY `idx_target` (`target_type`,`target_id`),
  KEY `idx_created` (`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=868 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admin_logs`
--

INSERT INTO `admin_logs` (`id`, `admin_id`, `action_type`, `target_type`, `target_id`, `description`, `old_data`, `new_data`, `ip_address`, `user_agent`, `created_at`) VALUES
(848, 1, '', 'admin_log', NULL, 'حذف همه لاگ‌های ادمین (64 مورد)', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-22 09:39:38'),
(849, 1, 'user_role_change', 'user', 2, 'تغییر نقش کاربر شمپاق از admin به user', '{\"role\": \"admin\"}', '{\"role\": \"user\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 06:52:16'),
(850, 1, '', 'user', 2, 'سلب مجوز ساخت بازی از شمپاق', NULL, '{\"can_create_game\": 0}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 07:02:44'),
(851, 1, '', 'user', 2, 'اعطای مجوز ساخت بازی به شمپاق', NULL, '{\"can_create_game\": 1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 07:11:03'),
(852, 1, '', 'user', 2, 'سلب مجوز ساخت بازی از شمپاق', NULL, '{\"can_create_game\": 0}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 07:11:18'),
(853, 1, '', 'user', 2, 'اعطای مجوز ساخت بازی به شمپاق', NULL, '{\"can_create_game\": 1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 07:11:27'),
(854, 1, '', 'user', 2, 'سلب مجوز ساخت بازی از شمپاق', NULL, '{\"can_create_game\": 0}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 07:15:59'),
(855, 1, '', 'user', 2, 'اعطای مجوز ساخت بازی به شمپاق', NULL, '{\"can_create_game\": 1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 07:17:00'),
(856, 1, '', 'user', 2, 'سلب مجوز ساخت بازی از شمپاق', NULL, '{\"can_create_game\": 0}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 07:17:36'),
(857, 1, '', 'user', 2, 'اعطای مجوز ساخت بازی به شمپاق', NULL, '{\"can_create_game\": 1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 07:40:11'),
(858, 1, '', 'user', 2, 'سلب مجوز ساخت بازی از شمپاق', NULL, '{\"can_create_game\": 0}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 08:14:11'),
(859, 1, '', 'user', 2, 'اعطای مجوز ساخت بازی به شمپاق', NULL, '{\"can_create_game\": 1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 08:15:13'),
(860, 1, '', 'user', 2, 'سلب مجوز ساخت بازی از شمپاق', NULL, '{\"can_create_game\": 0}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 08:16:49'),
(861, 1, '', 'user', 2, 'اعطای مجوز ساخت بازی به شمپاق', NULL, '{\"can_create_game\": 1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 08:18:34'),
(862, 1, '', 'user', 2, 'سلب مجوز ساخت بازی از شمپاق', NULL, '{\"can_create_game\": 0}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 08:26:44'),
(863, 1, '', 'user', 2, 'اعطای مجوز ساخت بازی به شمپاق', NULL, '{\"can_create_game\": 1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 08:27:08'),
(864, 1, '', 'user', 2, 'سلب مجوز ساخت بازی از شمپاق', NULL, '{\"can_create_game\": 0}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 08:27:24'),
(865, 7, 'user_role_change', 'user', 2, 'تغییر نقش کاربر شمپاق از user به admin', '{\"role\": \"user\"}', '{\"role\": \"admin\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:153.0) Gecko/20100101 Firefox/153.0', '2026-07-23 11:14:32'),
(866, 1, '', 'title', 1, 'ویرایش عنوان: تازه‌کار', '{\"id\": 1, \"code\": \"newbie\", \"icon\": \"🌱\", \"name\": \"تازه‌کار\", \"priority\": 10, \"is_active\": 1, \"created_at\": \"2026-07-04 18:02:34\", \"description\": \"اولین بازی خود را انجام داده\", \"bonus_points\": 0, \"condition_type\": \"total_games\", \"condition_value\": 1}', '{\"code\": \"newbie\", \"icon\": \"🌱\", \"name\": \"تازه‌کار\", \"priority\": 0, \"is_active\": 1, \"description\": \"اولین بازی خود را انجام داده\", \"bonus_points\": 50, \"condition_type\": \"total_games\", \"condition_value\": 1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-24 16:34:02'),
(867, 1, '', 'title', 1, 'ویرایش عنوان: تازه‌کار', '{\"id\": 1, \"code\": \"newbie\", \"icon\": \"🌱\", \"name\": \"تازه‌کار\", \"priority\": 0, \"is_active\": 1, \"created_at\": \"2026-07-04 18:02:34\", \"description\": \"اولین بازی خود را انجام داده\", \"bonus_points\": 50, \"condition_type\": \"total_games\", \"condition_value\": 1}', '{\"code\": \"newbie\", \"icon\": \"🌱\", \"name\": \"تازه‌کار\", \"priority\": 0, \"is_active\": 1, \"description\": \"اولین بازی خود را انجام داده\", \"bonus_points\": 0, \"condition_type\": \"total_games\", \"condition_value\": 1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-24 16:34:13');

-- --------------------------------------------------------

--
-- Table structure for table `cards`
--

DROP TABLE IF EXISTS `cards`;
CREATE TABLE IF NOT EXISTS `cards` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `icon_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `emoji` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `rarity` enum('common','rare','legendary') COLLATE utf8mb4_unicode_ci DEFAULT 'common',
  `score_multiplier` decimal(4,2) DEFAULT '1.00',
  `is_action_card` tinyint(1) DEFAULT '0',
  `is_active` tinyint(1) DEFAULT '1',
  `sort_order` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `idx_cards_rarity` (`rarity`),
  KEY `idx_cards_active` (`is_active`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cards`
--

INSERT INTO `cards` (`id`, `name`, `slug`, `icon_path`, `emoji`, `description`, `rarity`, `score_multiplier`, `is_action_card`, `is_active`, `sort_order`, `created_at`) VALUES
(1, 'کارت عددی', 'number', NULL, '🔢', 'کارت‌های معمولی با اعداد ۰ تا ۹', 'common', 2.00, 0, 1, 0, '2026-06-30 14:56:11'),
(2, 'پرش یکی', 'skip', NULL, '⏭️', 'نفر بعدی بازی نمی‌کند', 'common', 1.50, 1, 1, 2, '2026-06-30 14:56:11'),
(3, 'دوربرگردان', 'reverse', NULL, '🔄', 'جهت بازی عوض می‌شود', 'common', 1.50, 1, 1, 3, '2026-06-30 14:56:11'),
(4, 'جریمه دوتایی', 'draw_two', NULL, '+2', 'نفر بعدی ۲ کارت می‌کشد', 'common', 2.00, 1, 1, 4, '2026-06-30 14:56:11'),
(5, 'تغییر رنگ', 'wild_color', NULL, '🌈', 'رنگ بازی را تغییر می‌دهد', 'common', 1.50, 1, 1, 5, '2026-06-30 14:56:11'),
(6, 'جریمه ۴ تایی', 'wild_draw_four', NULL, '+4', 'نفر بعدی ۴ کارت می‌کشد و رنگ عوض می‌شود', 'rare', 3.00, 1, 1, 6, '2026-06-30 14:56:11'),
(7, 'سپر', 'shield', NULL, '🛡️', 'جریمه‌ها را خنثی می‌کند', 'rare', 2.50, 1, 1, 7, '2026-06-30 14:56:11'),
(8, 'دید زدن', 'peek', NULL, '👁️', 'کارت‌های یک نفر را می‌بینید', 'rare', 3.00, 1, 1, 8, '2026-06-30 14:56:11'),
(9, 'هدیه', 'gift', NULL, '🎁', '۲ کارت خود را به یک نفر بدهید', 'rare', 2.00, 1, 1, 9, '2026-06-30 14:56:11'),
(10, 'تعویض', 'swap', NULL, '🔀', 'کارت‌هایتان را با یک نفر عوض کنید', 'rare', 2.50, 1, 1, 10, '2026-06-30 14:56:11'),
(11, 'پرش دوتایی', 'double_skip', NULL, '⏩', 'دو نفر بعدی بازی نمی‌کنند', 'rare', 2.50, 1, 1, 11, '2026-06-30 14:56:11'),
(12, 'شافل', 'shuffle', NULL, '🌀', 'همه کارت‌ها جمع و دوباره پخش می‌شوند', 'rare', 3.00, 1, 1, 12, '2026-06-30 14:56:11'),
(13, 'قفل کردن', 'lock', NULL, '🔒', 'یک نفر تا ۳ دور از کارت عملیاتی محروم می‌شود', 'rare', 3.00, 1, 1, 13, '2026-06-30 14:56:11'),
(14, 'جریمه دوتایی انتخابی', 'targeted_draw_two', NULL, '🎯', 'یک نفر را انتخاب و ۲ کارت جریمه کنید', 'rare', 2.50, 1, 1, 14, '2026-06-30 14:56:11'),
(15, 'کینگ', 'king', NULL, '👑', 'کارت‌های همه را ببینید، یک کارت عملیاتی بدزدید، همه ۴ تا جریمه', 'legendary', 5.00, 1, 1, 15, '2026-06-30 14:56:11');

-- --------------------------------------------------------

--
-- Table structure for table `card_mastery`
--

DROP TABLE IF EXISTS `card_mastery`;
CREATE TABLE IF NOT EXISTS `card_mastery` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int UNSIGNED NOT NULL,
  `card_id` int UNSIGNED NOT NULL,
  `total_wins` int UNSIGNED DEFAULT '0',
  `current_streak` int DEFAULT '0',
  `max_streak` int DEFAULT '0',
  `last_won_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_user_card` (`user_id`,`card_id`),
  KEY `card_id` (`card_id`)
) ENGINE=InnoDB AUTO_INCREMENT=72 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `card_mastery`
--

INSERT INTO `card_mastery` (`id`, `user_id`, `card_id`, `total_wins`, `current_streak`, `max_streak`, `last_won_at`, `updated_at`) VALUES
(1, 1, 9, 6, 6, 6, '2026-07-16 13:47:44', '2026-07-16 13:47:44'),
(2, 1, 10, 3, 3, 3, '2026-07-16 12:28:45', '2026-07-16 12:28:45'),
(3, 1, 6, 2, 2, 2, '2026-07-24 18:58:54', '2026-07-24 18:58:54'),
(4, 1, 13, 11, 11, 11, '2026-07-24 17:35:54', '2026-07-24 17:35:54'),
(5, 1, 2, 9, 9, 9, '2026-07-16 07:04:44', '2026-07-16 07:04:44'),
(6, 6, 13, 2, 2, 2, '2026-07-12 17:38:57', '2026-07-12 17:38:57'),
(7, 6, 7, 5, 5, 5, '2026-07-03 09:00:33', '2026-07-03 09:00:33'),
(8, 1, 7, 6, 6, 6, '2026-07-03 09:04:53', '2026-07-03 09:04:53'),
(9, 1, 11, 2, 2, 2, '2026-07-16 13:17:29', '2026-07-16 13:17:29'),
(10, 1, 14, 4, 4, 4, '2026-07-16 12:44:32', '2026-07-16 12:44:32'),
(11, 6, 1, 2, 2, 2, '2026-07-11 18:20:09', '2026-07-11 18:20:09'),
(12, 1, 1, 10, 10, 10, '2026-07-23 10:33:55', '2026-07-23 10:33:55'),
(13, 7, 11, 2, 2, 2, '2026-07-03 10:33:35', '2026-07-03 10:33:35'),
(14, 7, 9, 5, 5, 5, '2026-07-24 17:37:16', '2026-07-24 17:37:16'),
(15, 7, 5, 3, 3, 3, '2026-07-15 11:58:11', '2026-07-15 11:58:11'),
(16, 2, 12, 2, 2, 2, '2026-07-24 11:17:49', '2026-07-24 11:17:49'),
(17, 6, 14, 2, 2, 2, '2026-07-12 17:09:17', '2026-07-12 17:09:17'),
(18, 1, 15, 9, 9, 9, '2026-07-23 10:37:01', '2026-07-23 10:37:01'),
(19, 3, 14, 1, 1, 1, '2026-07-03 11:38:41', '2026-07-03 11:38:41'),
(20, 2, 7, 3, 3, 3, '2026-07-24 13:30:16', '2026-07-24 13:30:16'),
(21, 1, 4, 2, 2, 2, '2026-07-12 19:31:17', '2026-07-12 19:31:17'),
(22, 6, 15, 1, 1, 1, '2026-07-04 13:50:07', '2026-07-04 13:50:07'),
(23, 7, 15, 7, 7, 7, '2026-07-23 10:54:14', '2026-07-23 10:54:14'),
(24, 7, 13, 1, 1, 1, '2026-07-04 15:08:25', '2026-07-04 15:08:25'),
(25, 7, 6, 2, 2, 2, '2026-07-12 16:53:38', '2026-07-12 16:53:38'),
(26, 4, 13, 1, 1, 1, '2026-07-06 18:29:37', '2026-07-06 18:29:37'),
(27, 4, 11, 1, 1, 1, '2026-07-06 18:29:42', '2026-07-06 18:29:42'),
(28, 4, 5, 1, 1, 1, '2026-07-06 18:29:50', '2026-07-06 18:29:50'),
(29, 1, 3, 4, 4, 4, '2026-07-16 07:04:06', '2026-07-16 07:04:06'),
(30, 8, 4, 1, 1, 1, '2026-07-07 11:06:21', '2026-07-07 11:06:21'),
(31, 2, 9, 2, 2, 2, '2026-07-12 11:54:59', '2026-07-12 11:54:59'),
(32, 2, 1, 3, 3, 3, '2026-07-23 10:54:39', '2026-07-23 10:54:39'),
(33, 2, 4, 1, 1, 1, '2026-07-07 11:20:37', '2026-07-07 11:20:37'),
(34, 7, 1, 2, 2, 2, '2026-07-24 19:02:18', '2026-07-24 19:02:18'),
(35, 12, 15, 1, 1, 1, '2026-07-11 12:25:50', '2026-07-11 12:25:50'),
(36, 13, 9, 5, 5, 5, '2026-07-16 13:01:24', '2026-07-16 13:01:24'),
(37, 12, 10, 2, 2, 2, '2026-07-16 13:53:12', '2026-07-16 13:53:12'),
(38, 12, 9, 2, 2, 2, '2026-07-16 13:55:17', '2026-07-16 13:55:17'),
(39, 12, 4, 1, 1, 1, '2026-07-11 18:57:51', '2026-07-11 18:57:51'),
(40, 12, 2, 2, 2, 2, '2026-07-12 09:23:34', '2026-07-12 09:23:34'),
(41, 13, 2, 2, 2, 2, '2026-07-12 17:39:19', '2026-07-12 17:39:19'),
(42, 6, 10, 1, 1, 1, '2026-07-12 09:38:04', '2026-07-12 09:38:04'),
(43, 7, 3, 3, 3, 3, '2026-07-12 18:06:31', '2026-07-12 18:06:31'),
(44, 8, 10, 1, 1, 1, '2026-07-12 16:53:02', '2026-07-12 16:53:02'),
(45, 8, 8, 1, 1, 1, '2026-07-12 16:53:29', '2026-07-12 16:53:29'),
(46, 13, 3, 1, 1, 1, '2026-07-12 16:54:10', '2026-07-12 16:54:10'),
(47, 6, 4, 3, 3, 3, '2026-07-15 18:52:58', '2026-07-15 18:52:58'),
(48, 6, 2, 2, 2, 2, '2026-07-12 17:51:41', '2026-07-12 17:51:41'),
(49, 6, 12, 2, 2, 2, '2026-07-16 15:22:22', '2026-07-16 15:22:22'),
(50, 13, 5, 3, 3, 3, '2026-07-16 11:45:08', '2026-07-16 11:45:08'),
(51, 7, 7, 1, 1, 1, '2026-07-12 17:54:16', '2026-07-12 17:54:16'),
(52, 8, 5, 1, 1, 1, '2026-07-12 17:54:24', '2026-07-12 17:54:24'),
(53, 2, 3, 1, 1, 1, '2026-07-15 12:17:19', '2026-07-15 12:17:19'),
(54, 2, 15, 2, 2, 2, '2026-07-24 06:56:06', '2026-07-24 06:56:06'),
(55, 12, 1, 7, 7, 7, '2026-07-16 13:38:24', '2026-07-16 13:38:24'),
(56, 13, 1, 4, 4, 4, '2026-07-16 13:10:35', '2026-07-16 13:10:35'),
(57, 12, 5, 1, 1, 1, '2026-07-16 12:39:28', '2026-07-16 12:39:28'),
(58, 13, 11, 1, 1, 1, '2026-07-16 13:00:35', '2026-07-16 13:00:35'),
(59, 12, 11, 1, 1, 1, '2026-07-16 13:18:48', '2026-07-16 13:18:48'),
(60, 12, 13, 1, 1, 1, '2026-07-16 13:23:11', '2026-07-16 13:23:11'),
(61, 12, 7, 1, 1, 1, '2026-07-16 13:34:17', '2026-07-16 13:34:17'),
(62, 13, 7, 1, 1, 1, '2026-07-16 13:49:56', '2026-07-16 13:49:56'),
(63, 3, 15, 1, 1, 1, '2026-07-23 10:32:12', '2026-07-23 10:32:12'),
(64, 2, 2, 1, 1, 1, '2026-07-23 10:34:35', '2026-07-23 10:34:35'),
(65, 3, 1, 1, 1, 1, '2026-07-23 12:31:35', '2026-07-23 12:31:35'),
(66, 3, 2, 1, 1, 1, '2026-07-23 12:32:22', '2026-07-23 12:32:22'),
(67, 3, 5, 1, 1, 1, '2026-07-24 07:48:50', '2026-07-24 07:48:50'),
(68, 7, 8, 1, 1, 1, '2026-07-24 11:09:39', '2026-07-24 11:09:39'),
(69, 3, 11, 1, 1, 1, '2026-07-24 13:33:14', '2026-07-24 13:33:14'),
(70, 7, 2, 1, 1, 1, '2026-07-24 17:33:57', '2026-07-24 17:33:57'),
(71, 7, 12, 1, 1, 1, '2026-07-24 17:35:37', '2026-07-24 17:35:37');

-- --------------------------------------------------------

--
-- Table structure for table `games`
--

DROP TABLE IF EXISTS `games`;
CREATE TABLE IF NOT EXISTS `games` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `referee_id` int UNSIGNED NOT NULL,
  `name` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `game_mode` enum('solo','friendly') COLLATE utf8mb4_unicode_ci DEFAULT 'solo',
  `target_wins` int UNSIGNED NOT NULL DEFAULT '10',
  `status` enum('pending','active','paused','finished','cancelled') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `winner_participant_id` int UNSIGNED DEFAULT NULL,
  `team_builder_algorithm` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `first_player_participant_id` int UNSIGNED DEFAULT NULL,
  `total_rounds_played` int UNSIGNED DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `started_at` timestamp NULL DEFAULT NULL,
  `finished_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_games_status` (`status`),
  KEY `idx_games_referee` (`referee_id`)
) ENGINE=InnoDB AUTO_INCREMENT=151 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `games`
--

INSERT INTO `games` (`id`, `referee_id`, `name`, `game_mode`, `target_wins`, `status`, `winner_participant_id`, `team_builder_algorithm`, `first_player_participant_id`, `total_rounds_played`, `created_at`, `started_at`, `finished_at`) VALUES
(1, 1, NULL, 'solo', 10, 'pending', NULL, 'manual', NULL, 0, '2026-07-01 14:46:16', NULL, NULL),
(2, 1, NULL, 'solo', 10, 'pending', NULL, 'manual', NULL, 0, '2026-07-01 15:02:21', NULL, NULL),
(3, 1, NULL, 'friendly', 10, 'pending', NULL, 'random', NULL, 0, '2026-07-01 15:05:04', NULL, NULL),
(4, 1, NULL, 'solo', 10, 'cancelled', NULL, 'manual', NULL, 0, '2026-07-01 15:17:26', NULL, '2026-07-01 15:24:33'),
(5, 2, NULL, 'solo', 10, 'pending', NULL, 'manual', NULL, 0, '2026-07-01 15:23:19', NULL, NULL),
(6, 2, NULL, 'solo', 10, 'pending', NULL, 'manual', NULL, 0, '2026-07-01 15:26:34', NULL, NULL),
(7, 2, NULL, 'friendly', 5, 'pending', NULL, 'random', NULL, 0, '2026-07-01 15:31:30', NULL, NULL),
(8, 2, NULL, 'friendly', 10, 'pending', NULL, 'manual', NULL, 0, '2026-07-01 15:33:05', NULL, NULL),
(9, 2, NULL, 'solo', 5, 'pending', NULL, 'manual', NULL, 0, '2026-07-01 16:08:13', NULL, NULL),
(10, 2, NULL, 'friendly', 10, 'pending', NULL, 'manual', NULL, 0, '2026-07-01 16:13:24', NULL, NULL),
(11, 2, NULL, 'friendly', 10, 'pending', NULL, 'manual', NULL, 0, '2026-07-01 16:42:35', NULL, NULL),
(12, 2, 'بازی لیگ', 'solo', 5, 'pending', NULL, 'manual', NULL, 0, '2026-07-01 17:21:22', NULL, NULL),
(13, 1, 'لیگ دوم', 'friendly', 10, 'cancelled', NULL, 'manual', NULL, 0, '2026-07-01 17:30:49', NULL, '2026-07-01 17:31:02'),
(14, 1, 'باازی یک', 'friendly', 10, 'pending', NULL, 'manual', NULL, 0, '2026-07-01 17:43:51', NULL, NULL),
(15, 1, 'لیگ دوم', 'solo', 10, 'pending', NULL, 'manual', NULL, 0, '2026-07-01 17:45:50', NULL, NULL),
(16, 1, 'لیگ', 'solo', 10, 'pending', NULL, 'manual', NULL, 0, '2026-07-01 18:01:33', NULL, NULL),
(17, 1, 'لیگ دوم', 'solo', 6, 'pending', NULL, 'manual', NULL, 0, '2026-07-01 18:02:29', NULL, NULL),
(18, 1, 'تیم', 'friendly', 5, 'pending', NULL, 'manual', NULL, 0, '2026-07-01 18:04:51', NULL, NULL),
(19, 1, 'زظطزشسزی', 'solo', 10, 'pending', NULL, 'manual', NULL, 0, '2026-07-01 18:34:48', NULL, NULL),
(20, 1, 'یسیشس', 'solo', 10, 'pending', NULL, 'manual', NULL, 0, '2026-07-01 18:57:04', NULL, NULL),
(21, 1, 'بازی جدید', 'solo', 12, 'pending', NULL, 'manual', NULL, 0, '2026-07-01 20:14:09', NULL, NULL),
(22, 1, 'تیم', 'friendly', 10, 'pending', NULL, 'manual', NULL, 0, '2026-07-01 20:49:19', NULL, NULL),
(23, 1, 'تیم', 'friendly', 14, 'cancelled', NULL, 'manual', NULL, 0, '2026-07-01 21:11:52', NULL, '2026-07-01 21:12:00'),
(24, 1, 'یک چیزی', 'solo', 15, 'pending', NULL, 'manual', NULL, 0, '2026-07-01 21:28:01', NULL, NULL),
(25, 1, 'بازی جدید', 'friendly', 7, 'cancelled', NULL, 'manual', NULL, 0, '2026-07-01 21:33:56', NULL, '2026-07-01 21:34:39'),
(26, 1, 'asdas', 'friendly', 10, 'pending', NULL, 'manual', NULL, 0, '2026-07-01 21:39:01', NULL, NULL),
(27, 1, 'سلام', 'solo', 10, 'cancelled', NULL, 'manual', 120, 26, '2026-07-02 12:44:52', '2026-07-03 08:44:46', '2026-07-03 09:54:47'),
(28, 1, 'بازی', 'friendly', 10, 'pending', NULL, 'manual', NULL, 0, '2026-07-02 13:03:07', NULL, NULL),
(29, 1, 'بازی جدید 🆕', 'friendly', 13, 'cancelled', NULL, 'manual', NULL, 0, '2026-07-02 16:54:37', NULL, '2026-07-02 16:54:53'),
(30, 1, 'بازی', 'friendly', 10, 'pending', NULL, 'manual', NULL, 0, '2026-07-02 17:13:36', NULL, NULL),
(31, 1, 'بازی جدید', 'solo', 10, 'pending', NULL, 'manual', NULL, 0, '2026-07-02 17:46:21', NULL, NULL),
(32, 1, 'بازی', 'solo', 5, 'pending', NULL, 'manual', NULL, 0, '2026-07-02 18:00:40', NULL, NULL),
(33, 1, 'تیم', 'friendly', 10, 'paused', NULL, 'manual', 146, 3, '2026-07-02 19:02:56', '2026-07-03 15:03:24', NULL),
(34, 1, 'گروهی', 'friendly', 5, 'cancelled', NULL, 'manual', 152, 0, '2026-07-03 09:56:00', '2026-07-03 09:56:08', '2026-07-03 09:59:42'),
(38, 1, 'انفرادی', 'solo', 3, 'finished', 171, 'manual', 171, 5, '2026-07-03 11:10:37', '2026-07-03 11:10:42', '2026-07-03 11:18:06'),
(46, 1, 'جدید انفرادی', 'solo', 10, 'finished', 200, 'manual', 200, 10, '2026-07-04 11:43:57', '2026-07-04 11:44:03', '2026-07-04 11:44:39'),
(54, 1, 'طزظطزظط', 'friendly', 3, 'cancelled', NULL, 'manual', NULL, 0, '2026-07-05 11:48:42', NULL, '2026-07-05 12:33:21'),
(55, 1, 'تیم', 'solo', 3, 'pending', NULL, 'manual', NULL, 0, '2026-07-05 12:07:46', NULL, NULL),
(56, 1, 'czxczc', 'solo', 10, 'cancelled', NULL, 'manual', NULL, 0, '2026-07-05 12:28:53', NULL, '2026-07-05 12:28:56'),
(57, 1, 'dedqqw', 'friendly', 3, 'cancelled', NULL, 'manual', NULL, 0, '2026-07-05 12:29:47', NULL, '2026-07-05 12:29:50'),
(60, 1, 'لیگ انفرادی f - 1405/04/14', 'solo', 10, 'cancelled', NULL, 'manual', NULL, 0, '2026-07-05 13:14:12', NULL, '2026-07-05 13:14:15'),
(66, 1, 'لیگ دوستانه - 1405/04/14', 'friendly', 3, 'cancelled', NULL, 'manual', NULL, 0, '2026-07-05 14:19:20', NULL, '2026-07-05 14:19:30'),
(79, 1, 'لیگ دوستانه - 1405/04/15', 'friendly', 3, 'finished', 322, 'manual', 322, 3, '2026-07-06 19:16:01', '2026-07-06 19:16:05', '2026-07-06 19:16:26'),
(80, 1, 'لیگ انفرادی - 1405/04/16', 'solo', 3, 'finished', 325, 'manual', 325, 5, '2026-07-07 08:14:59', '2026-07-07 08:15:38', '2026-07-07 10:43:39'),
(93, 1, 'لیگ انفرادی - 1405/04/19', 'solo', 10, 'cancelled', NULL, 'manual', 371, 0, '2026-07-10 09:20:22', '2026-07-10 09:20:31', NULL),
(94, 1, 'لیگ انفرادی - 1405/04/19', 'solo', 10, 'finished', 375, 'manual', 375, 25, '2026-07-10 11:25:17', '2026-07-10 11:26:25', '2026-07-10 12:38:23'),
(95, 1, 'لیگ انفرادی - 1405/04/20', 'solo', 15, 'cancelled', NULL, 'manual', 380, 34, '2026-07-11 08:13:59', '2026-07-11 08:14:07', '2026-07-23 10:30:04'),
(96, 1, 'لیگ دوستانه - 1405/04/20', 'friendly', 15, 'paused', NULL, 'manual', 382, 18, '2026-07-11 08:28:28', '2026-07-11 08:28:33', NULL),
(104, 1, 'لیگ انفرادی - 1405/04/25', 'solo', 10, 'paused', NULL, 'manual', 412, 21, '2026-07-16 11:22:29', '2026-07-16 11:24:54', NULL),
(106, 1, 'لیگ انفرادی - 1405/04/26', 'solo', 10, 'cancelled', NULL, 'manual', 418, 0, '2026-07-17 06:38:20', '2026-07-17 06:38:34', '2026-07-17 06:39:11'),
(107, 1, 'لیگ انفرادی - 1405/04/26', 'solo', 10, 'paused', NULL, 'manual', 422, 10, '2026-07-17 06:40:03', '2026-07-17 06:40:31', NULL),
(145, 2, 'لیگ انفرادی - 1405/04/28', 'solo', 10, 'cancelled', NULL, 'manual', 609, 0, '2026-07-19 16:04:11', '2026-07-19 16:04:15', '2026-07-19 16:04:19'),
(146, 1, 'لیگ دوستانه - 1405/05/01', 'friendly', 10, 'paused', NULL, 'random', 613, 6, '2026-07-23 10:30:52', '2026-07-23 10:31:27', NULL),
(147, 1, 'لیگ انفرادی - 1405/05/01', 'solo', 10, 'paused', NULL, 'manual', 617, 13, '2026-07-23 10:36:13', '2026-07-23 10:36:39', NULL),
(148, 1, 'لیگ انفرادی - 1405/05/01', 'solo', 10, 'finished', 620, 'manual', 622, 31, '2026-07-23 19:34:17', '2026-07-23 19:46:03', '2026-07-24 21:34:59'),
(149, 1, 'لیگ انفرادی - 1405/05/01', 'solo', 20, 'active', NULL, 'manual', 625, 41, '2026-07-23 19:34:17', '2026-07-25 10:33:33', NULL),
(150, 1, 'لیگ دوستانه - 1405/05/02', 'friendly', 3, 'finished', 629, 'random', 632, 5, '2026-07-24 17:26:40', '2026-07-24 17:26:47', '2026-07-24 21:34:21');

-- --------------------------------------------------------

--
-- Table structure for table `game_participants`
--

DROP TABLE IF EXISTS `game_participants`;
CREATE TABLE IF NOT EXISTS `game_participants` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `game_id` int UNSIGNED NOT NULL,
  `user_id` int UNSIGNED DEFAULT NULL,
  `team_id` int UNSIGNED DEFAULT NULL,
  `guest_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `wins_count` int UNSIGNED DEFAULT '0',
  `total_score` decimal(10,2) DEFAULT '0.00',
  `is_winner` tinyint(1) DEFAULT '0',
  `joined_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_participants_game` (`game_id`),
  KEY `idx_participants_user` (`user_id`),
  KEY `idx_participants_team` (`team_id`)
) ENGINE=InnoDB AUTO_INCREMENT=633 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `game_participants`
--

INSERT INTO `game_participants` (`id`, `game_id`, `user_id`, `team_id`, `guest_name`, `wins_count`, `total_score`, `is_winner`, `joined_at`) VALUES
(1, 1, 1, NULL, NULL, 0, 0.00, 0, '2026-07-01 14:46:16'),
(2, 2, 1, NULL, NULL, 0, 0.00, 0, '2026-07-01 15:02:21'),
(3, 3, 1, 1, NULL, 0, 0.00, 0, '2026-07-01 15:05:04'),
(4, 4, 5, NULL, NULL, 0, 0.00, 0, '2026-07-01 15:17:26'),
(5, 4, 1, NULL, NULL, 0, 0.00, 0, '2026-07-01 15:17:26'),
(6, 4, 6, NULL, NULL, 0, 0.00, 0, '2026-07-01 15:17:26'),
(7, 4, NULL, NULL, 'حسن', 0, 0.00, 0, '2026-07-01 15:17:26'),
(8, 5, 5, NULL, NULL, 0, 0.00, 0, '2026-07-01 15:23:19'),
(9, 5, 1, NULL, NULL, 0, 0.00, 0, '2026-07-01 15:23:19'),
(10, 5, 3, NULL, NULL, 0, 0.00, 0, '2026-07-01 15:23:19'),
(11, 5, 4, NULL, NULL, 0, 0.00, 0, '2026-07-01 15:23:19'),
(12, 5, 2, NULL, NULL, 0, 0.00, 0, '2026-07-01 15:23:19'),
(13, 6, 5, NULL, NULL, 0, 0.00, 0, '2026-07-01 15:26:34'),
(14, 6, 1, NULL, NULL, 0, 0.00, 0, '2026-07-01 15:26:34'),
(15, 6, 2, NULL, NULL, 0, 0.00, 0, '2026-07-01 15:26:34'),
(16, 6, 2, NULL, NULL, 0, 0.00, 0, '2026-07-01 15:26:34'),
(17, 7, 1, 5, NULL, 0, 0.00, 0, '2026-07-01 15:31:30'),
(18, 7, 6, 2, NULL, 0, 0.00, 0, '2026-07-01 15:31:30'),
(19, 7, 5, 2, NULL, 0, 0.00, 0, '2026-07-01 15:31:30'),
(20, 7, 3, 4, NULL, 0, 0.00, 0, '2026-07-01 15:31:30'),
(21, 7, 2, 3, NULL, 0, 0.00, 0, '2026-07-01 15:31:30'),
(22, 7, 4, 4, NULL, 0, 0.00, 0, '2026-07-01 15:31:30'),
(23, 7, 2, 3, NULL, 0, 0.00, 0, '2026-07-01 15:31:30'),
(24, 7, NULL, NULL, 'مهمان 1', 0, 0.00, 0, '2026-07-01 15:31:30'),
(25, 7, NULL, NULL, 'مهمان 2', 0, 0.00, 0, '2026-07-01 15:31:30'),
(26, 8, 1, 6, NULL, 0, 0.00, 0, '2026-07-01 15:33:05'),
(27, 8, 6, 7, NULL, 0, 0.00, 0, '2026-07-01 15:33:05'),
(28, 8, 5, 6, NULL, 0, 0.00, 0, '2026-07-01 15:33:05'),
(29, 8, 3, 7, NULL, 0, 0.00, 0, '2026-07-01 15:33:05'),
(30, 8, 2, 6, NULL, 0, 0.00, 0, '2026-07-01 15:33:05'),
(31, 8, 4, 7, NULL, 0, 0.00, 0, '2026-07-01 15:33:05'),
(32, 8, NULL, NULL, 'مهمان 7', 0, 0.00, 0, '2026-07-01 15:33:05'),
(33, 8, NULL, NULL, 'مهمان 8', 0, 0.00, 0, '2026-07-01 15:33:05'),
(34, 9, 1, NULL, NULL, 0, 0.00, 0, '2026-07-01 16:08:13'),
(35, 9, 6, NULL, NULL, 0, 0.00, 0, '2026-07-01 16:08:13'),
(36, 9, 5, NULL, NULL, 0, 0.00, 0, '2026-07-01 16:08:13'),
(37, 9, NULL, NULL, 'مهمان 2', 0, 0.00, 0, '2026-07-01 16:08:13'),
(38, 10, 1, 8, NULL, 0, 0.00, 0, '2026-07-01 16:13:24'),
(39, 10, 6, 9, NULL, 0, 0.00, 0, '2026-07-01 16:13:24'),
(40, 10, 5, 10, NULL, 0, 0.00, 0, '2026-07-01 16:13:24'),
(41, 10, 3, 8, NULL, 0, 0.00, 0, '2026-07-01 16:13:24'),
(42, 10, 2, 9, NULL, 0, 0.00, 0, '2026-07-01 16:13:24'),
(43, 10, 4, 10, NULL, 0, 0.00, 0, '2026-07-01 16:13:24'),
(44, 11, 1, 11, NULL, 0, 0.00, 0, '2026-07-01 16:42:35'),
(45, 11, 6, 12, NULL, 0, 0.00, 0, '2026-07-01 16:42:35'),
(46, 11, 5, 13, NULL, 0, 0.00, 0, '2026-07-01 16:42:35'),
(47, 11, 2, 11, NULL, 0, 0.00, 0, '2026-07-01 16:42:35'),
(48, 11, NULL, NULL, 'مهمان', 0, 0.00, 0, '2026-07-01 16:42:35'),
(49, 12, 3, NULL, NULL, 0, 0.00, 0, '2026-07-01 17:21:22'),
(50, 12, 2, NULL, NULL, 0, 0.00, 0, '2026-07-01 17:21:22'),
(51, 12, NULL, NULL, 'مهمان', 0, 0.00, 0, '2026-07-01 17:21:22'),
(52, 13, 1, NULL, NULL, 0, 0.00, 0, '2026-07-01 17:30:49'),
(53, 13, 6, NULL, NULL, 0, 0.00, 0, '2026-07-01 17:30:49'),
(54, 13, 5, NULL, NULL, 0, 0.00, 0, '2026-07-01 17:30:49'),
(55, 13, 3, NULL, NULL, 0, 0.00, 0, '2026-07-01 17:30:49'),
(56, 13, 2, NULL, NULL, 0, 0.00, 0, '2026-07-01 17:30:49'),
(57, 13, 4, NULL, NULL, 0, 0.00, 0, '2026-07-01 17:30:49'),
(58, 13, NULL, NULL, 'مهمان یک', 0, 0.00, 0, '2026-07-01 17:30:49'),
(59, 13, NULL, NULL, 'مهمان 2', 0, 0.00, 0, '2026-07-01 17:30:49'),
(60, 14, 1, NULL, NULL, 0, 0.00, 0, '2026-07-01 17:43:51'),
(61, 14, 6, NULL, NULL, 0, 0.00, 0, '2026-07-01 17:43:51'),
(62, 14, 5, NULL, NULL, 0, 0.00, 0, '2026-07-01 17:43:51'),
(63, 14, 3, NULL, NULL, 0, 0.00, 0, '2026-07-01 17:43:51'),
(64, 15, 1, NULL, NULL, 0, 0.00, 0, '2026-07-01 17:45:50'),
(65, 15, 6, NULL, NULL, 0, 0.00, 0, '2026-07-01 17:45:50'),
(66, 15, 5, NULL, NULL, 0, 0.00, 0, '2026-07-01 17:45:50'),
(67, 16, 6, NULL, NULL, 0, 0.00, 0, '2026-07-01 18:01:33'),
(68, 16, 5, NULL, NULL, 0, 0.00, 0, '2026-07-01 18:01:33'),
(69, 17, 1, NULL, NULL, 0, 0.00, 0, '2026-07-01 18:02:29'),
(70, 17, 6, NULL, NULL, 0, 0.00, 0, '2026-07-01 18:02:29'),
(71, 17, 5, NULL, NULL, 0, 0.00, 0, '2026-07-01 18:02:29'),
(72, 17, NULL, NULL, 'مهمان 1', 0, 0.00, 0, '2026-07-01 18:02:29'),
(73, 18, 1, 20, NULL, 0, 0.00, 0, '2026-07-01 18:04:51'),
(74, 18, 6, 21, NULL, 0, 0.00, 0, '2026-07-01 18:04:51'),
(75, 18, 5, 22, NULL, 0, 0.00, 0, '2026-07-01 18:04:51'),
(76, 18, NULL, 20, 'مهمان 1', 0, 0.00, 0, '2026-07-01 18:04:51'),
(77, 18, NULL, 21, 'مهمان 2', 0, 0.00, 0, '2026-07-01 18:04:51'),
(78, 19, 1, NULL, NULL, 0, 0.00, 0, '2026-07-01 18:34:48'),
(79, 19, 6, NULL, NULL, 0, 0.00, 0, '2026-07-01 18:34:48'),
(80, 19, 5, NULL, NULL, 0, 0.00, 0, '2026-07-01 18:34:48'),
(81, 19, NULL, NULL, 'یشسیشسی', 0, 0.00, 0, '2026-07-01 18:34:48'),
(82, 20, NULL, NULL, 'یشسیشس', 0, 0.00, 0, '2026-07-01 18:57:04'),
(83, 21, 1, NULL, NULL, 0, 0.00, 0, '2026-07-01 20:14:09'),
(84, 21, 3, NULL, NULL, 0, 0.00, 0, '2026-07-01 20:14:09'),
(85, 21, 2, NULL, NULL, 0, 0.00, 0, '2026-07-01 20:14:09'),
(86, 21, NULL, NULL, 'مهمان 1', 0, 0.00, 0, '2026-07-01 20:14:09'),
(87, 21, NULL, NULL, 'مهمان 2', 0, 0.00, 0, '2026-07-01 20:14:09'),
(88, 22, 1, 26, NULL, 0, 0.00, 0, '2026-07-01 20:49:19'),
(89, 22, 6, 24, NULL, 0, 0.00, 0, '2026-07-01 20:49:19'),
(90, 22, 5, 25, NULL, 0, 0.00, 0, '2026-07-01 20:49:19'),
(91, 22, 3, 26, NULL, 0, 0.00, 0, '2026-07-01 20:49:19'),
(92, 22, 2, 23, NULL, 0, 0.00, 0, '2026-07-01 20:49:19'),
(93, 22, 4, 24, NULL, 0, 0.00, 0, '2026-07-01 20:49:19'),
(94, 22, NULL, 25, 'مهمان', 0, 0.00, 0, '2026-07-01 20:49:19'),
(95, 22, NULL, 26, 'مهمان 2', 0, 0.00, 0, '2026-07-01 20:49:19'),
(96, 23, 1, 27, NULL, 0, 0.00, 0, '2026-07-01 21:11:52'),
(97, 23, 6, 27, NULL, 0, 0.00, 0, '2026-07-01 21:11:52'),
(98, 23, 5, 28, NULL, 0, 0.00, 0, '2026-07-01 21:11:52'),
(99, 23, 3, 29, NULL, 0, 0.00, 0, '2026-07-01 21:11:52'),
(100, 23, 2, 29, NULL, 0, 0.00, 0, '2026-07-01 21:11:52'),
(101, 23, 4, 30, NULL, 0, 0.00, 0, '2026-07-01 21:11:52'),
(102, 23, NULL, 27, 'مهمان 1', 0, 0.00, 0, '2026-07-01 21:11:52'),
(103, 23, NULL, 28, 'مهمان 2', 0, 0.00, 0, '2026-07-01 21:11:52'),
(104, 24, 1, NULL, NULL, 0, 0.00, 0, '2026-07-01 21:28:01'),
(105, 24, 6, NULL, NULL, 0, 0.00, 0, '2026-07-01 21:28:01'),
(106, 24, NULL, NULL, 'مهمان', 0, 0.00, 0, '2026-07-01 21:28:01'),
(107, 25, 1, 33, NULL, 0, 0.00, 0, '2026-07-01 21:33:56'),
(108, 25, 6, 32, NULL, 0, 0.00, 0, '2026-07-01 21:33:56'),
(109, 25, 5, 33, NULL, 0, 0.00, 0, '2026-07-01 21:33:56'),
(110, 25, 3, 31, NULL, 0, 0.00, 0, '2026-07-01 21:33:56'),
(111, 25, 2, 34, NULL, 0, 0.00, 0, '2026-07-01 21:33:56'),
(112, 25, 4, 32, NULL, 0, 0.00, 0, '2026-07-01 21:33:56'),
(113, 25, NULL, 33, 'مه', 0, 0.00, 0, '2026-07-01 21:33:56'),
(114, 25, NULL, 34, 'ییشس', 0, 0.00, 0, '2026-07-01 21:33:56'),
(115, 26, 1, 36, NULL, 0, 0.00, 0, '2026-07-01 21:39:01'),
(116, 26, 6, 36, NULL, 0, 0.00, 0, '2026-07-01 21:39:01'),
(117, 26, 5, 35, NULL, 0, 0.00, 0, '2026-07-01 21:39:01'),
(118, 26, NULL, 36, 'lilhk', 0, 0.00, 0, '2026-07-01 21:39:01'),
(119, 27, 1, NULL, NULL, 16, 16.00, 0, '2026-07-02 12:44:52'),
(120, 27, 6, NULL, NULL, 6, 6.00, 0, '2026-07-02 12:44:52'),
(121, 27, NULL, NULL, 'مهمان', 4, 4.00, 0, '2026-07-02 12:44:52'),
(122, 28, 1, 37, NULL, 0, 0.00, 0, '2026-07-02 13:03:07'),
(123, 28, 6, 38, NULL, 0, 0.00, 0, '2026-07-02 13:03:07'),
(124, 28, 5, 39, NULL, 0, 0.00, 0, '2026-07-02 13:03:07'),
(125, 28, 3, 37, NULL, 0, 0.00, 0, '2026-07-02 13:03:07'),
(126, 28, 2, 38, NULL, 0, 0.00, 0, '2026-07-02 13:03:07'),
(127, 28, 4, 39, NULL, 0, 0.00, 0, '2026-07-02 13:03:07'),
(128, 29, 1, 42, NULL, 0, 0.00, 0, '2026-07-02 16:54:37'),
(129, 29, 6, 41, NULL, 0, 0.00, 0, '2026-07-02 16:54:37'),
(130, 29, 5, 42, NULL, 0, 0.00, 0, '2026-07-02 16:54:37'),
(131, 29, 2, 40, NULL, 0, 0.00, 0, '2026-07-02 16:54:37'),
(132, 29, NULL, 40, 'الر', 0, 0.00, 0, '2026-07-02 16:54:37'),
(133, 30, 1, 43, NULL, 0, 0.00, 0, '2026-07-02 17:13:36'),
(134, 30, 6, 44, NULL, 0, 0.00, 0, '2026-07-02 17:13:36'),
(135, 30, 2, 45, NULL, 0, 0.00, 0, '2026-07-02 17:13:36'),
(136, 30, 4, 43, NULL, 0, 0.00, 0, '2026-07-02 17:13:36'),
(137, 30, NULL, 44, 'مهمان', 0, 0.00, 0, '2026-07-02 17:13:36'),
(138, 30, NULL, 45, 'مهمان 2', 0, 0.00, 0, '2026-07-02 17:13:36'),
(139, 31, 1, NULL, NULL, 0, 0.00, 0, '2026-07-02 17:46:21'),
(140, 31, 6, NULL, NULL, 0, 0.00, 0, '2026-07-02 17:46:21'),
(141, 31, 7, NULL, NULL, 0, 0.00, 0, '2026-07-02 17:46:21'),
(142, 32, 1, NULL, NULL, 0, 0.00, 0, '2026-07-02 18:00:40'),
(143, 32, 6, NULL, NULL, 0, 0.00, 0, '2026-07-02 18:00:40'),
(144, 32, 7, NULL, NULL, 0, 0.00, 0, '2026-07-02 18:00:40'),
(145, 32, NULL, NULL, 'مهمان', 0, 0.00, 0, '2026-07-02 18:00:40'),
(146, 33, 1, 46, NULL, 1, 1.00, 0, '2026-07-02 19:02:56'),
(147, 33, 6, 47, NULL, 0, 0.00, 0, '2026-07-02 19:02:56'),
(148, 33, 7, 48, NULL, 1, 1.00, 0, '2026-07-02 19:02:56'),
(149, 33, 2, 46, NULL, 0, 0.00, 0, '2026-07-02 19:02:56'),
(150, 33, NULL, 47, 'بیسبیس', 1, 1.00, 0, '2026-07-02 19:02:56'),
(151, 34, 1, 51, NULL, 0, 0.00, 0, '2026-07-03 09:56:00'),
(152, 34, 6, 49, NULL, 0, 0.00, 0, '2026-07-03 09:56:00'),
(153, 34, 7, 50, NULL, 0, 0.00, 0, '2026-07-03 09:56:00'),
(154, 34, 2, 51, NULL, 0, 0.00, 0, '2026-07-03 09:56:00'),
(155, 34, NULL, 49, 'مهمان', 0, 0.00, 0, '2026-07-03 09:56:00'),
(156, 34, NULL, 50, 'مهمان 2', 0, 0.00, 0, '2026-07-03 09:56:00'),
(171, 38, 1, NULL, NULL, 3, 3.00, 1, '2026-07-03 11:10:37'),
(172, 38, 6, NULL, NULL, 0, 0.00, 0, '2026-07-03 11:10:37'),
(173, 38, 7, NULL, NULL, 0, 0.00, 0, '2026-07-03 11:10:37'),
(174, 38, NULL, NULL, 'مهمان', 2, 2.00, 0, '2026-07-03 11:10:37'),
(199, 46, 7, NULL, NULL, 0, 0.00, 0, '2026-07-04 11:43:57'),
(200, 46, 5, NULL, NULL, 10, 10.00, 1, '2026-07-04 11:43:57'),
(201, 46, 2, NULL, NULL, 0, 0.00, 0, '2026-07-04 11:43:57'),
(202, 46, 4, NULL, NULL, 0, 0.00, 0, '2026-07-04 11:43:57'),
(229, 54, 1, 67, NULL, 0, 0.00, 0, '2026-07-05 11:48:42'),
(230, 54, 6, 68, NULL, 0, 0.00, 0, '2026-07-05 11:48:42'),
(231, 54, 7, 67, NULL, 0, 0.00, 0, '2026-07-05 11:48:42'),
(232, 54, NULL, 68, 'بسیب', 0, 0.00, 0, '2026-07-05 11:48:42'),
(233, 55, 1, NULL, NULL, 0, 0.00, 0, '2026-07-05 12:07:46'),
(234, 55, 6, NULL, NULL, 0, 0.00, 0, '2026-07-05 12:07:46'),
(235, 55, 7, NULL, NULL, 0, 0.00, 0, '2026-07-05 12:07:46'),
(236, 55, NULL, NULL, 'مهمان', 0, 0.00, 0, '2026-07-05 12:07:46'),
(237, 56, 1, NULL, NULL, 0, 0.00, 0, '2026-07-05 12:28:53'),
(238, 56, 6, NULL, NULL, 0, 0.00, 0, '2026-07-05 12:28:53'),
(239, 56, NULL, NULL, 'dasdsa', 0, 0.00, 0, '2026-07-05 12:28:53'),
(240, 57, 1, 69, NULL, 0, 0.00, 0, '2026-07-05 12:29:47'),
(241, 57, 6, 70, NULL, 0, 0.00, 0, '2026-07-05 12:29:47'),
(242, 57, NULL, 69, 'dsdasd', 0, 0.00, 0, '2026-07-05 12:29:47'),
(243, 57, NULL, 70, 'dasdas', 0, 0.00, 0, '2026-07-05 12:29:47'),
(252, 60, 1, NULL, NULL, 0, 0.00, 0, '2026-07-05 13:14:12'),
(253, 60, NULL, NULL, 'fsdfsd', 0, 0.00, 0, '2026-07-05 13:14:12'),
(269, 66, 1, 76, NULL, 0, 0.00, 0, '2026-07-05 14:19:20'),
(270, 66, 6, 77, NULL, 0, 0.00, 0, '2026-07-05 14:19:20'),
(271, 66, 7, 76, NULL, 0, 0.00, 0, '2026-07-05 14:19:20'),
(320, 79, 1, 92, NULL, 0, 0.00, 0, '2026-07-06 19:16:01'),
(321, 79, 6, 92, NULL, 0, 0.00, 0, '2026-07-06 19:16:01'),
(322, 79, 7, 93, NULL, 1, 1.00, 1, '2026-07-06 19:16:01'),
(323, 79, NULL, 93, 'عمو منصور', 2, 2.00, 0, '2026-07-06 19:16:01'),
(324, 80, 1, NULL, NULL, 2, 2.00, 0, '2026-07-07 08:14:59'),
(325, 80, 6, NULL, NULL, 3, 3.00, 1, '2026-07-07 08:14:59'),
(368, 93, 9, NULL, NULL, 0, 0.00, 0, '2026-07-10 09:20:22'),
(369, 93, 2, NULL, NULL, 0, 0.00, 0, '2026-07-10 09:20:22'),
(370, 93, NULL, NULL, 'مهمان', 0, 0.00, 0, '2026-07-10 09:20:22'),
(371, 93, NULL, NULL, 'مهمان', 0, 0.00, 0, '2026-07-10 09:20:22'),
(372, 93, NULL, NULL, 'مهمان', 0, 0.00, 0, '2026-07-10 09:20:22'),
(373, 94, 1, NULL, NULL, 6, 6.00, 0, '2026-07-10 11:25:17'),
(374, 94, 13, NULL, NULL, 9, 9.00, 0, '2026-07-10 11:25:17'),
(375, 94, 12, NULL, NULL, 10, 10.00, 1, '2026-07-10 11:25:17'),
(377, 95, 1, NULL, NULL, 5, 61.05, 0, '2026-07-11 08:13:59'),
(378, 95, 7, NULL, NULL, 10, 179.70, 0, '2026-07-11 08:13:59'),
(379, 95, 8, NULL, NULL, 10, 92.15, 0, '2026-07-11 08:13:59'),
(380, 95, NULL, NULL, 'مهمان', 9, 33.00, 0, '2026-07-11 08:13:59'),
(381, 96, 1, 102, NULL, 6, 55.98, 0, '2026-07-11 08:28:28'),
(382, 96, 13, 103, NULL, 2, 79.29, 0, '2026-07-11 08:28:28'),
(383, 96, 12, 102, NULL, 3, 33.38, 0, '2026-07-11 08:28:28'),
(384, 96, 6, 103, NULL, 7, 135.79, 0, '2026-07-11 08:28:28'),
(411, 104, 1, NULL, NULL, 6, 42.00, 0, '2026-07-16 11:22:29'),
(412, 104, 13, NULL, NULL, 6, 23.00, 0, '2026-07-16 11:22:29'),
(413, 104, 12, NULL, NULL, 4, 17.00, 0, '2026-07-16 11:22:29'),
(414, 104, NULL, NULL, 'کماندو', 5, 13.00, 0, '2026-07-16 11:22:29'),
(418, 106, 1, NULL, NULL, 0, 0.00, 0, '2026-07-17 06:38:20'),
(419, 106, 6, NULL, NULL, 0, 0.00, 0, '2026-07-17 06:38:20'),
(420, 106, 7, NULL, NULL, 0, 0.00, 0, '2026-07-17 06:38:20'),
(421, 107, 6, NULL, NULL, 0, 0.00, 0, '2026-07-17 06:40:03'),
(422, 107, 7, NULL, NULL, 2, 60.00, 0, '2026-07-17 06:40:03'),
(423, 107, NULL, NULL, 'مهمان 1', 8, 40.00, 0, '2026-07-17 06:40:03'),
(608, 145, 9, NULL, NULL, 0, 0.00, 0, '2026-07-19 16:04:11'),
(609, 145, 2, NULL, NULL, 0, 0.00, 0, '2026-07-19 16:04:11'),
(610, 145, 4, NULL, NULL, 0, 0.00, 0, '2026-07-19 16:04:11'),
(611, 146, 1, 203, NULL, 2, 50.00, 0, '2026-07-23 10:30:52'),
(612, 146, 7, 203, NULL, 1, 40.00, 0, '2026-07-23 10:30:52'),
(613, 146, 3, 202, NULL, 1, 110.00, 0, '2026-07-23 10:30:52'),
(614, 146, 2, 202, NULL, 2, 102.50, 0, '2026-07-23 10:30:52'),
(615, 147, 1, NULL, NULL, 1, 50.00, 0, '2026-07-23 10:36:13'),
(616, 147, 7, NULL, NULL, 2, 30.00, 0, '2026-07-23 10:36:13'),
(617, 147, 3, NULL, NULL, 8, 47.50, 0, '2026-07-23 10:36:13'),
(618, 147, 2, NULL, NULL, 2, 15.00, 0, '2026-07-23 10:36:13'),
(619, 148, 1, NULL, NULL, 4, 20.00, 0, '2026-07-23 19:34:17'),
(620, 148, 7, NULL, NULL, 10, 70.00, 1, '2026-07-23 19:34:17'),
(621, 148, 3, NULL, NULL, 8, 50.00, 0, '2026-07-23 19:34:17'),
(622, 148, 2, NULL, NULL, 9, 90.00, 0, '2026-07-23 19:34:17'),
(623, 149, 1, NULL, NULL, 11, 55.00, 0, '2026-07-23 19:34:17'),
(624, 149, 7, NULL, NULL, 11, 55.00, 0, '2026-07-23 19:34:17'),
(625, 149, 3, NULL, NULL, 9, 45.00, 0, '2026-07-23 19:34:17'),
(626, 149, 2, NULL, NULL, 10, 50.00, 0, '2026-07-23 19:34:17'),
(627, 150, 1, 206, NULL, 0, 0.00, 0, '2026-07-24 17:26:40'),
(628, 150, 7, 205, NULL, 1, 25.00, 0, '2026-07-24 17:26:40'),
(629, 150, 5, 204, NULL, 1, 30.00, 1, '2026-07-24 17:26:40'),
(630, 150, 10, 204, NULL, 2, 30.00, 0, '2026-07-24 17:26:40'),
(631, 150, 3, 206, NULL, 0, 0.00, 0, '2026-07-24 17:26:40'),
(632, 150, 2, 205, NULL, 1, 25.00, 0, '2026-07-24 17:26:40');

-- --------------------------------------------------------

--
-- Table structure for table `game_rounds`
--

DROP TABLE IF EXISTS `game_rounds`;
CREATE TABLE IF NOT EXISTS `game_rounds` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `game_id` int UNSIGNED NOT NULL,
  `round_number` int UNSIGNED NOT NULL,
  `winner_participant_id` int UNSIGNED NOT NULL,
  `winner_team_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `winning_card_id` int UNSIGNED DEFAULT NULL,
  `win_type_id` int UNSIGNED DEFAULT NULL,
  `calculated_score` decimal(10,2) DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `winning_card_id` (`winning_card_id`),
  KEY `win_type_id` (`win_type_id`),
  KEY `idx_rounds_game` (`game_id`),
  KEY `idx_rounds_winner` (`winner_participant_id`)
) ENGINE=InnoDB AUTO_INCREMENT=717 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `game_rounds`
--

INSERT INTO `game_rounds` (`id`, `game_id`, `round_number`, `winner_participant_id`, `winner_team_name`, `winning_card_id`, `win_type_id`, `calculated_score`, `created_at`) VALUES
(2, 27, 1, 119, NULL, 9, 3, 1.00, '2026-07-03 08:56:38'),
(3, 27, 2, 119, NULL, 10, 1, 1.00, '2026-07-03 08:56:45'),
(4, 27, 3, 119, NULL, 6, 2, 1.00, '2026-07-03 08:56:56'),
(5, 27, 4, 119, NULL, 13, 4, 1.00, '2026-07-03 08:57:01'),
(6, 27, 5, 119, NULL, 13, 4, 1.00, '2026-07-03 08:57:04'),
(7, 27, 6, 119, NULL, 13, 4, 1.00, '2026-07-03 08:57:04'),
(8, 27, 7, 119, NULL, 13, 4, 1.00, '2026-07-03 08:57:05'),
(9, 27, 8, 119, NULL, 13, 4, 1.00, '2026-07-03 08:57:05'),
(10, 27, 9, 119, NULL, 2, 3, 1.00, '2026-07-03 08:57:13'),
(11, 27, 10, 120, NULL, 13, 3, 1.00, '2026-07-03 09:00:10'),
(12, 27, 11, 121, NULL, 8, 2, 1.00, '2026-07-03 09:00:18'),
(13, 27, 12, 121, NULL, 11, 2, 1.00, '2026-07-03 09:00:21'),
(14, 27, 13, 121, NULL, 11, 2, 1.00, '2026-07-03 09:00:23'),
(15, 27, 14, 121, NULL, 11, 2, 1.00, '2026-07-03 09:00:23'),
(16, 27, 15, 120, NULL, 7, 4, 1.00, '2026-07-03 09:00:31'),
(17, 27, 16, 120, NULL, 7, 4, 1.00, '2026-07-03 09:00:32'),
(18, 27, 17, 120, NULL, 7, 4, 1.00, '2026-07-03 09:00:32'),
(19, 27, 18, 120, NULL, 7, 4, 1.00, '2026-07-03 09:00:33'),
(20, 27, 19, 120, NULL, 7, 4, 1.00, '2026-07-03 09:00:33'),
(21, 27, 20, 119, NULL, 7, 4, 1.00, '2026-07-03 09:00:39'),
(22, 27, 21, 119, NULL, 7, 4, 1.00, '2026-07-03 09:01:20'),
(24, 27, 22, 119, NULL, 7, 4, 1.00, '2026-07-03 09:04:52'),
(25, 27, 23, 119, NULL, 7, 4, 1.00, '2026-07-03 09:04:52'),
(26, 27, 24, 119, NULL, 7, 4, 1.00, '2026-07-03 09:04:53'),
(27, 27, 25, 119, NULL, NULL, NULL, 1.00, '2026-07-03 09:08:25'),
(28, 27, 26, 119, NULL, NULL, NULL, 1.00, '2026-07-03 09:09:02'),
(49, 38, 1, 174, NULL, 1, 1, 1.00, '2026-07-03 11:11:00'),
(50, 38, 2, 174, NULL, 14, 1, 1.00, '2026-07-03 11:11:59'),
(52, 38, 3, 171, NULL, NULL, NULL, 1.00, '2026-07-03 11:17:47'),
(53, 38, 4, 171, NULL, NULL, NULL, 1.00, '2026-07-03 11:17:53'),
(54, 38, 5, 171, NULL, NULL, NULL, 1.00, '2026-07-03 11:18:00'),
(91, 33, 1, 146, 'سیبسی', 4, 2, 1.00, '2026-07-03 15:03:34'),
(92, 33, 2, 150, 'سیبسی', NULL, NULL, 1.00, '2026-07-03 15:03:45'),
(93, 33, 3, 148, 'سیبسی', NULL, NULL, 1.00, '2026-07-03 15:03:56'),
(102, 46, 1, 200, NULL, NULL, NULL, 1.00, '2026-07-04 11:44:09'),
(103, 46, 2, 200, NULL, NULL, NULL, 1.00, '2026-07-04 11:44:13'),
(104, 46, 3, 200, NULL, NULL, NULL, 1.00, '2026-07-04 11:44:16'),
(105, 46, 4, 200, NULL, NULL, NULL, 1.00, '2026-07-04 11:44:18'),
(106, 46, 5, 200, NULL, NULL, NULL, 1.00, '2026-07-04 11:44:20'),
(107, 46, 6, 200, NULL, NULL, NULL, 1.00, '2026-07-04 11:44:23'),
(108, 46, 7, 200, NULL, NULL, NULL, 1.00, '2026-07-04 11:44:26'),
(109, 46, 8, 200, NULL, NULL, NULL, 1.00, '2026-07-04 11:44:29'),
(110, 46, 9, 200, NULL, NULL, NULL, 1.00, '2026-07-04 11:44:32'),
(111, 46, 10, 200, NULL, NULL, NULL, 1.00, '2026-07-04 11:44:35'),
(153, 79, 1, 323, 'تیم 2', NULL, NULL, 1.00, '2026-07-06 19:16:08'),
(154, 79, 2, 323, 'تیم 2', NULL, NULL, 1.00, '2026-07-06 19:16:10'),
(155, 79, 3, 322, 'تیم 2', NULL, NULL, 1.00, '2026-07-06 19:16:17'),
(156, 80, 1, 324, NULL, NULL, NULL, 1.00, '2026-07-07 08:15:51'),
(165, 80, 2, 324, NULL, NULL, NULL, 1.00, '2026-07-07 10:18:07'),
(171, 80, 3, 325, NULL, NULL, NULL, 1.00, '2026-07-07 10:41:18'),
(172, 80, 4, 325, NULL, NULL, NULL, 1.00, '2026-07-07 10:41:22'),
(173, 80, 5, 325, NULL, NULL, NULL, 1.00, '2026-07-07 10:43:33'),
(349, 94, 1, 373, NULL, NULL, NULL, 1.00, '2026-07-10 11:32:45'),
(350, 94, 2, 373, NULL, 1, 1, 1.00, '2026-07-10 11:34:58'),
(351, 94, 3, 374, NULL, NULL, NULL, 1.00, '2026-07-10 11:38:17'),
(352, 94, 4, 375, NULL, NULL, NULL, 1.00, '2026-07-10 11:43:56'),
(353, 94, 5, 373, NULL, NULL, NULL, 1.00, '2026-07-10 11:46:21'),
(354, 94, 6, 373, NULL, NULL, NULL, 1.00, '2026-07-10 11:51:20'),
(355, 94, 7, 375, NULL, NULL, NULL, 1.00, '2026-07-10 11:54:57'),
(356, 94, 8, 374, NULL, NULL, NULL, 1.00, '2026-07-10 11:56:53'),
(357, 94, 9, 375, NULL, NULL, NULL, 1.00, '2026-07-10 11:59:13'),
(358, 94, 10, 375, NULL, NULL, 2, 1.00, '2026-07-10 12:03:18'),
(359, 94, 11, 375, NULL, NULL, NULL, 1.00, '2026-07-10 12:05:14'),
(360, 94, 12, 375, NULL, NULL, NULL, 1.00, '2026-07-10 12:08:19'),
(361, 94, 13, 374, NULL, NULL, NULL, 1.00, '2026-07-10 12:10:45'),
(362, 94, 14, 374, NULL, NULL, NULL, 1.00, '2026-07-10 12:13:16'),
(363, 94, 15, 373, NULL, NULL, NULL, 1.00, '2026-07-10 12:14:22'),
(364, 94, 16, 375, NULL, NULL, NULL, 1.00, '2026-07-10 12:15:51'),
(365, 94, 17, 375, NULL, NULL, NULL, 1.00, '2026-07-10 12:17:45'),
(366, 94, 18, 374, NULL, NULL, NULL, 1.00, '2026-07-10 12:20:00'),
(367, 94, 19, 375, NULL, NULL, NULL, 1.00, '2026-07-10 12:21:36'),
(368, 94, 20, 373, NULL, NULL, NULL, 1.00, '2026-07-10 12:22:41'),
(369, 94, 21, 374, NULL, NULL, NULL, 1.00, '2026-07-10 12:24:59'),
(370, 94, 22, 374, NULL, NULL, NULL, 1.00, '2026-07-10 12:29:25'),
(371, 94, 23, 374, NULL, NULL, NULL, 1.00, '2026-07-10 12:31:50'),
(372, 94, 24, 374, NULL, NULL, NULL, 1.00, '2026-07-10 12:34:35'),
(373, 94, 25, 375, NULL, NULL, NULL, 1.00, '2026-07-10 12:38:14'),
(390, 96, 1, 381, 'تیم 1', 15, 2, 11.25, '2026-07-11 12:24:49'),
(391, 96, 2, 383, 'تیم 1', 15, 2, 11.25, '2026-07-11 12:25:50'),
(392, 96, 3, 383, 'تیم 1', NULL, NULL, 1.50, '2026-07-11 12:26:55'),
(393, 96, 4, 383, 'تیم 1', NULL, NULL, 1.50, '2026-07-11 12:39:16'),
(418, 96, 5, 381, 'تیم 1', NULL, NULL, 1.50, '2026-07-11 18:12:50'),
(419, 96, 6, 381, 'تیم 1', 15, 2, 11.25, '2026-07-11 18:14:14'),
(420, 96, 7, 384, 'تیم 2', 1, 1, 3.00, '2026-07-11 18:20:09'),
(421, 96, 8, 381, 'تیم 1', 13, 2, 6.75, '2026-07-11 18:47:54'),
(422, 96, 9, 381, 'تیم 1', 13, 2, 6.75, '2026-07-11 18:55:02'),
(428, 96, 10, 382, 'تیم 2', 2, 4, 2.93, '2026-07-12 09:34:46'),
(429, 96, 11, 384, 'تیم 2', 10, 5, 5.63, '2026-07-12 09:38:04'),
(442, 96, 12, 381, 'تیم 1', 10, 2, 5.63, '2026-07-12 13:41:27'),
(444, 95, 1, 378, NULL, NULL, NULL, 2.00, '2026-07-12 16:52:49'),
(446, 95, 2, 379, NULL, 8, 5, 4.50, '2026-07-12 16:53:29'),
(447, 95, 3, 378, NULL, 6, 4, 4.90, '2026-07-12 16:53:38'),
(448, 96, 13, 382, 'تیم 2', 3, 2, 3.38, '2026-07-12 16:54:10'),
(450, 96, 14, 384, 'تیم 2', 4, 2, 4.50, '2026-07-12 17:17:58'),
(452, 96, 15, 384, 'تیم 2', 13, 2, 6.75, '2026-07-12 17:38:57'),
(459, 95, 4, 378, NULL, 7, 3, 3.00, '2026-07-12 17:54:16'),
(460, 95, 5, 379, NULL, 5, 3, 1.20, '2026-07-12 17:54:24'),
(461, 95, 6, 378, NULL, 3, 3, 2.20, '2026-07-12 18:06:14'),
(462, 95, 7, 378, NULL, 3, 5, 3.25, '2026-07-12 18:06:31'),
(464, 95, 8, 377, NULL, 15, 5, 7.50, '2026-07-12 18:07:28'),
(466, 95, 9, 377, NULL, 2, 2, 4.25, '2026-07-12 18:55:41'),
(469, 95, 10, 377, NULL, 1, 2, 6.00, '2026-07-12 19:29:32'),
(486, 96, 16, 384, 'تیم 2', 4, 5, 4.50, '2026-07-15 18:52:58'),
(509, 95, 11, 378, NULL, NULL, NULL, 2.00, '2026-07-16 06:36:31'),
(510, 95, 12, 380, NULL, NULL, NULL, 1.00, '2026-07-16 06:36:44'),
(511, 95, 13, 380, NULL, NULL, NULL, 1.00, '2026-07-16 06:38:06'),
(512, 95, 14, 378, NULL, NULL, NULL, 2.00, '2026-07-16 06:41:57'),
(513, 95, 15, 380, NULL, NULL, NULL, 1.00, '2026-07-16 06:42:13'),
(514, 95, 16, 377, NULL, NULL, NULL, 1.00, '2026-07-16 06:42:24'),
(515, 95, 17, 377, NULL, 3, 5, 2.25, '2026-07-16 07:04:06'),
(517, 95, 18, 379, NULL, NULL, NULL, 1.00, '2026-07-16 07:05:20'),
(518, 104, 1, 414, NULL, 9, 1, 2.00, '2026-07-16 11:28:05'),
(519, 104, 2, 412, NULL, 9, 1, 2.00, '2026-07-16 11:30:06'),
(520, 104, 3, 412, NULL, 9, 1, 2.00, '2026-07-16 11:33:24'),
(521, 104, 4, 411, NULL, 1, 1, 2.00, '2026-07-16 11:37:40'),
(522, 104, 5, 413, NULL, 1, 5, 3.00, '2026-07-16 11:41:27'),
(523, 104, 6, 412, NULL, 5, NULL, 1.50, '2026-07-16 11:45:08'),
(524, 104, 7, 412, NULL, 1, NULL, 2.00, '2026-07-16 11:48:16'),
(525, 104, 8, 412, NULL, 1, NULL, 2.00, '2026-07-16 11:55:46'),
(526, 104, 9, 412, NULL, 9, NULL, 2.00, '2026-07-16 11:57:38'),
(527, 104, 10, 411, NULL, 1, NULL, 2.00, '2026-07-16 12:01:33'),
(528, 104, 11, 411, NULL, 1, NULL, 2.00, '2026-07-16 12:07:44'),
(529, 104, 12, 414, NULL, 15, NULL, 5.00, '2026-07-16 12:11:58'),
(530, 104, 13, 414, NULL, 1, NULL, 2.00, '2026-07-16 12:20:20'),
(531, 104, 14, 413, NULL, 1, NULL, 2.00, '2026-07-16 12:24:11'),
(532, 104, 15, 411, NULL, 10, NULL, 2.50, '2026-07-16 12:28:45'),
(533, 104, 16, 414, NULL, NULL, NULL, 1.00, '2026-07-16 12:32:04'),
(534, 104, 17, 414, NULL, 6, NULL, 3.00, '2026-07-16 12:37:44'),
(535, 104, 18, 413, NULL, 5, NULL, 1.50, '2026-07-16 12:39:28'),
(536, 104, 19, 411, NULL, 14, NULL, 2.50, '2026-07-16 12:44:32'),
(537, 104, 20, 413, NULL, 1, NULL, 2.00, '2026-07-16 12:50:01'),
(560, 96, 17, 384, 'تیم 2', 12, 1, 30.00, '2026-07-16 15:22:22'),
(561, 107, 1, 423, NULL, NULL, NULL, 5.00, '2026-07-18 11:02:29'),
(562, 107, 2, 423, NULL, NULL, NULL, 5.00, '2026-07-18 11:09:44'),
(563, 107, 3, 423, NULL, NULL, NULL, 5.00, '2026-07-18 11:12:43'),
(564, 107, 4, 423, NULL, NULL, NULL, 5.00, '2026-07-18 16:43:26'),
(565, 95, 19, 379, NULL, NULL, NULL, 5.00, '2026-07-19 19:09:41'),
(566, 95, 20, 378, NULL, NULL, NULL, 15.00, '2026-07-19 19:09:52'),
(567, 95, 21, 379, NULL, NULL, NULL, 5.00, '2026-07-19 19:11:03'),
(568, 95, 22, 380, NULL, NULL, NULL, 5.00, '2026-07-19 19:40:00'),
(569, 95, 23, 380, NULL, NULL, NULL, 5.00, '2026-07-19 19:40:58'),
(570, 95, 24, 378, NULL, NULL, NULL, 15.00, '2026-07-19 19:41:12'),
(571, 95, 25, 379, NULL, NULL, NULL, 5.00, '2026-07-19 19:41:25'),
(573, 95, 26, 378, NULL, NULL, NULL, 15.00, '2026-07-19 19:43:10'),
(574, 95, 27, 380, NULL, NULL, NULL, 5.00, '2026-07-19 19:45:04'),
(575, 95, 28, 379, NULL, NULL, NULL, 5.00, '2026-07-19 19:45:19'),
(576, 95, 29, 380, NULL, NULL, NULL, 5.00, '2026-07-19 19:45:31'),
(577, 95, 30, 380, NULL, NULL, NULL, 5.00, '2026-07-19 19:45:47'),
(578, 95, 31, 379, NULL, NULL, NULL, 5.00, '2026-07-19 19:49:32'),
(579, 95, 32, 379, NULL, NULL, NULL, 5.00, '2026-07-19 19:52:08'),
(580, 95, 33, 379, NULL, NULL, NULL, 5.00, '2026-07-19 19:54:05'),
(581, 95, 34, 380, NULL, NULL, NULL, 5.00, '2026-07-19 19:54:27'),
(583, 107, 5, 422, NULL, NULL, NULL, 15.00, '2026-07-20 07:14:14'),
(584, 96, 18, 384, 'تیم 2', NULL, NULL, 15.00, '2026-07-20 07:33:47'),
(585, 107, 6, 423, NULL, NULL, NULL, 5.00, '2026-07-20 07:50:26'),
(587, 107, 7, 423, NULL, NULL, NULL, 5.00, '2026-07-20 15:09:45'),
(588, 107, 8, 422, NULL, NULL, NULL, 15.00, '2026-07-20 15:09:50'),
(589, 104, 21, 411, NULL, NULL, NULL, 10.00, '2026-07-20 17:45:32'),
(590, 107, 9, 423, NULL, NULL, NULL, 5.00, '2026-07-22 09:45:53'),
(591, 107, 10, 423, NULL, NULL, NULL, 5.00, '2026-07-22 09:46:15'),
(592, 146, 1, 613, 'تیم 1', 15, NULL, 50.00, '2026-07-23 10:32:12'),
(593, 146, 2, 611, 'تیم 2', 1, NULL, 20.00, '2026-07-23 10:33:55'),
(594, 146, 3, 614, 'تیم 1', 2, NULL, 15.00, '2026-07-23 10:34:35'),
(595, 146, 4, 614, 'تیم 1', 1, NULL, 20.00, '2026-07-23 10:35:08'),
(596, 147, 1, 615, NULL, 15, NULL, 25.00, '2026-07-23 10:37:01'),
(597, 147, 2, 616, NULL, 15, NULL, 25.00, '2026-07-23 10:54:14'),
(598, 147, 3, 618, NULL, 1, NULL, 10.00, '2026-07-23 10:54:39'),
(599, 147, 4, 617, NULL, 1, NULL, 10.00, '2026-07-23 12:31:35'),
(600, 147, 5, 617, NULL, 2, NULL, 7.50, '2026-07-23 12:32:22'),
(601, 147, 6, 617, NULL, NULL, NULL, 5.00, '2026-07-23 15:37:22'),
(602, 147, 7, 617, NULL, NULL, NULL, 5.00, '2026-07-23 15:39:44'),
(603, 147, 8, 617, NULL, NULL, NULL, 5.00, '2026-07-23 15:40:08'),
(604, 147, 9, 617, NULL, NULL, NULL, 5.00, '2026-07-23 15:57:56'),
(605, 147, 10, 617, NULL, NULL, NULL, 5.00, '2026-07-23 15:58:59'),
(606, 147, 11, 617, NULL, NULL, NULL, 5.00, '2026-07-23 16:25:32'),
(607, 147, 12, 618, NULL, NULL, NULL, 5.00, '2026-07-23 16:25:40'),
(608, 147, 13, 616, NULL, NULL, NULL, 5.00, '2026-07-23 16:25:45'),
(609, 146, 5, 612, 'تیم 2', NULL, NULL, 10.00, '2026-07-23 16:28:41'),
(610, 146, 6, 611, 'تیم 2', NULL, NULL, 10.00, '2026-07-23 16:28:55'),
(611, 148, 1, 621, NULL, NULL, NULL, 5.00, '2026-07-23 19:47:03'),
(612, 148, 2, 622, NULL, NULL, NULL, 5.00, '2026-07-23 19:54:29'),
(613, 148, 3, 620, NULL, NULL, NULL, 5.00, '2026-07-23 19:59:36'),
(614, 148, 4, 622, NULL, NULL, NULL, 5.00, '2026-07-23 20:03:05'),
(615, 148, 5, 621, NULL, NULL, NULL, 5.00, '2026-07-23 20:12:24'),
(616, 148, 6, 620, NULL, NULL, NULL, 5.00, '2026-07-23 20:12:33'),
(617, 148, 7, 619, NULL, NULL, NULL, 5.00, '2026-07-23 20:12:47'),
(618, 148, 8, 622, NULL, NULL, NULL, 5.00, '2026-07-23 20:14:37'),
(619, 148, 9, 621, NULL, NULL, NULL, 5.00, '2026-07-23 20:14:43'),
(620, 148, 10, 620, NULL, NULL, NULL, 5.00, '2026-07-23 20:14:52'),
(621, 148, 11, 619, NULL, NULL, NULL, 5.00, '2026-07-23 20:14:58'),
(622, 148, 12, 620, NULL, NULL, NULL, 5.00, '2026-07-23 20:19:08'),
(623, 148, 13, 622, NULL, NULL, NULL, 5.00, '2026-07-23 20:21:31'),
(624, 148, 14, 621, NULL, NULL, NULL, 5.00, '2026-07-23 20:21:53'),
(625, 148, 15, 621, NULL, NULL, NULL, 5.00, '2026-07-23 20:24:13'),
(626, 148, 16, 620, NULL, NULL, NULL, 5.00, '2026-07-23 20:24:32'),
(627, 148, 17, 619, NULL, NULL, NULL, 5.00, '2026-07-23 20:24:44'),
(628, 148, 18, 622, NULL, 15, NULL, 25.00, '2026-07-24 06:56:06'),
(629, 148, 19, 620, NULL, 9, 4, 10.00, '2026-07-24 07:34:28'),
(630, 148, 20, 621, NULL, 5, 5, 7.50, '2026-07-24 07:48:50'),
(631, 148, 21, 622, NULL, 7, 3, 12.50, '2026-07-24 07:51:49'),
(632, 148, 22, 620, NULL, 8, 2, 15.00, '2026-07-24 11:09:39'),
(633, 148, 23, 622, NULL, 12, 4, 15.00, '2026-07-24 11:17:49'),
(634, 148, 24, 620, NULL, 9, NULL, 10.00, '2026-07-24 13:29:41'),
(635, 148, 25, 622, NULL, 7, NULL, 12.50, '2026-07-24 13:30:16'),
(636, 148, 26, 621, NULL, 11, NULL, 12.50, '2026-07-24 13:33:14'),
(637, 150, 1, 628, 'تیم 2', 2, NULL, 15.00, '2026-07-24 17:33:57'),
(644, 150, 2, 632, 'تیم 2', NULL, NULL, 10.00, '2026-07-24 17:42:13'),
(660, 148, 27, 620, NULL, NULL, NULL, 5.00, '2026-07-24 21:15:54'),
(662, 148, 28, 622, NULL, NULL, NULL, 5.00, '2026-07-24 21:16:25'),
(663, 148, 29, 619, NULL, NULL, NULL, 5.00, '2026-07-24 21:16:32'),
(664, 148, 30, 621, NULL, NULL, NULL, 5.00, '2026-07-24 21:16:57'),
(668, 150, 3, 629, 'تیم 1', NULL, NULL, 10.00, '2026-07-24 21:33:29'),
(669, 150, 4, 630, 'تیم 1', NULL, NULL, 10.00, '2026-07-24 21:33:56'),
(670, 150, 5, 630, 'تیم 1', NULL, NULL, 10.00, '2026-07-24 21:34:11'),
(671, 148, 31, 620, NULL, NULL, NULL, 5.00, '2026-07-24 21:34:48'),
(672, 149, 1, 625, NULL, NULL, NULL, 5.00, '2026-07-25 10:33:39'),
(673, 149, 2, 624, NULL, NULL, NULL, 5.00, '2026-07-25 10:33:47'),
(674, 149, 3, 625, NULL, NULL, NULL, 5.00, '2026-07-25 11:02:09'),
(675, 149, 4, 625, NULL, NULL, NULL, 5.00, '2026-07-25 11:02:23'),
(676, 149, 5, 625, NULL, NULL, NULL, 5.00, '2026-07-25 11:03:15'),
(677, 149, 6, 625, NULL, NULL, NULL, 5.00, '2026-07-25 17:00:51'),
(678, 149, 7, 624, NULL, NULL, NULL, 5.00, '2026-07-25 17:00:59'),
(679, 149, 8, 623, NULL, NULL, NULL, 5.00, '2026-07-25 17:01:07'),
(680, 149, 9, 624, NULL, NULL, NULL, 5.00, '2026-07-25 17:01:46'),
(681, 149, 10, 623, NULL, NULL, NULL, 5.00, '2026-07-25 17:44:01'),
(682, 149, 11, 623, NULL, NULL, NULL, 5.00, '2026-07-25 17:44:09'),
(683, 149, 12, 624, NULL, NULL, NULL, 5.00, '2026-07-25 17:44:14'),
(685, 149, 13, 624, NULL, NULL, NULL, 5.00, '2026-07-25 17:55:20'),
(686, 149, 14, 624, NULL, NULL, NULL, 5.00, '2026-07-25 17:55:52'),
(687, 149, 15, 623, NULL, NULL, NULL, 5.00, '2026-07-25 18:04:51'),
(688, 149, 16, 624, NULL, NULL, NULL, 5.00, '2026-07-25 18:05:43'),
(689, 149, 17, 625, NULL, NULL, NULL, 5.00, '2026-07-25 18:05:50'),
(690, 149, 18, 624, NULL, NULL, NULL, 5.00, '2026-07-25 18:06:06'),
(691, 149, 19, 623, NULL, NULL, NULL, 5.00, '2026-07-25 18:06:13'),
(692, 149, 20, 626, NULL, NULL, NULL, 5.00, '2026-07-25 18:06:34'),
(693, 149, 21, 624, NULL, NULL, NULL, 5.00, '2026-07-25 18:13:21'),
(694, 149, 22, 625, NULL, NULL, NULL, 5.00, '2026-07-25 18:18:02'),
(695, 149, 23, 626, NULL, NULL, NULL, 5.00, '2026-07-25 18:19:49'),
(697, 149, 24, 626, NULL, NULL, NULL, 5.00, '2026-07-25 18:37:47'),
(698, 149, 25, 626, NULL, NULL, NULL, 5.00, '2026-07-25 18:48:12'),
(699, 149, 26, 626, NULL, NULL, NULL, 5.00, '2026-07-25 18:49:16'),
(700, 149, 27, 626, NULL, NULL, NULL, 5.00, '2026-07-25 18:50:13'),
(701, 149, 28, 626, NULL, NULL, NULL, 5.00, '2026-07-25 18:54:31'),
(702, 149, 29, 626, NULL, NULL, NULL, 5.00, '2026-07-25 19:15:28'),
(703, 149, 30, 623, NULL, NULL, NULL, 5.00, '2026-07-25 19:15:46'),
(704, 149, 31, 625, NULL, NULL, NULL, 5.00, '2026-07-25 19:15:50'),
(706, 149, 32, 626, NULL, NULL, NULL, 5.00, '2026-07-25 19:29:10'),
(707, 149, 33, 623, NULL, NULL, NULL, 5.00, '2026-07-25 19:33:19'),
(708, 149, 34, 626, NULL, NULL, NULL, 5.00, '2026-07-25 19:33:26'),
(709, 149, 35, 624, NULL, NULL, NULL, 5.00, '2026-07-25 19:42:26'),
(710, 149, 36, 623, NULL, NULL, NULL, 5.00, '2026-07-25 19:42:43'),
(711, 149, 37, 623, NULL, NULL, NULL, 5.00, '2026-07-25 19:46:41'),
(712, 149, 38, 624, NULL, NULL, NULL, 5.00, '2026-07-25 19:48:09'),
(713, 149, 39, 623, NULL, NULL, NULL, 5.00, '2026-07-25 19:48:17'),
(714, 149, 40, 625, NULL, NULL, NULL, 5.00, '2026-07-25 19:48:22'),
(716, 149, 41, 623, NULL, NULL, NULL, 5.00, '2026-07-25 19:52:44');

-- --------------------------------------------------------

--
-- Table structure for table `leaderboard_cache`
--

DROP TABLE IF EXISTS `leaderboard_cache`;
CREATE TABLE IF NOT EXISTS `leaderboard_cache` (
  `user_id` int UNSIGNED NOT NULL,
  `total_games` int UNSIGNED DEFAULT '0',
  `total_wins` int UNSIGNED DEFAULT '0',
  `total_losses` int UNSIGNED DEFAULT '0',
  `total_points` decimal(10,2) DEFAULT '0.00',
  `win_rate` decimal(5,2) DEFAULT '0.00',
  `points_per_game` decimal(10,2) DEFAULT '0.00',
  `confidence_factor` decimal(3,2) DEFAULT '0.00',
  `final_rank_score` decimal(8,2) DEFAULT '0.00',
  `current_streak` int DEFAULT '0',
  `best_streak` int DEFAULT '0',
  `last_updated` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`),
  KEY `idx_leaderboard_rank` (`final_rank_score` DESC),
  KEY `idx_leaderboard_wins` (`total_wins` DESC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `leaderboard_cache`
--

INSERT INTO `leaderboard_cache` (`user_id`, `total_games`, `total_wins`, `total_losses`, `total_points`, `win_rate`, `points_per_game`, `confidence_factor`, `final_rank_score`, `current_streak`, `best_streak`, `last_updated`) VALUES
(1, 18, 1, 6, 91.00, 16.67, 5.17, 0.00, 0.00, 0, 2, '2026-07-25 19:52:44'),
(2, 13, 0, 4, 165.00, 0.00, 38.33, 0.00, 0.00, 0, 0, '2026-07-25 19:33:26'),
(3, 12, 0, 3, 100.00, 0.00, 25.00, 0.00, 0.00, 0, 0, '2026-07-25 19:48:22'),
(4, 1, 0, 4, 0.00, 0.00, 0.75, 0.00, 0.00, 0, 0, '2026-07-20 20:43:05'),
(5, 2, 2, 1, 40.00, 100.00, 20.00, 0.00, 0.00, 0, 0, '2026-07-24 21:34:21'),
(6, 3, 1, 19, 3.00, 33.33, 1.52, 0.00, 0.00, 0, 0, '2026-07-20 20:43:14'),
(7, 18, 2, 4, 161.00, 40.00, 19.20, 0.00, 0.00, 0, 0, '2026-07-25 19:48:27'),
(8, 0, 0, 4, 0.00, 0.00, 0.67, 0.00, 0.00, 0, 0, '2026-07-20 20:43:05'),
(9, 0, 0, 0, 0.00, 0.00, 1.00, 0.00, 0.00, 0, 0, '2026-07-20 20:43:14'),
(10, 1, 0, 2, 30.00, 0.00, 30.00, 0.00, 0.00, 0, 0, '2026-07-24 21:34:21'),
(12, 1, 1, 1, 10.00, 100.00, 21.83, 0.00, 0.00, 0, 0, '2026-07-20 20:43:14'),
(13, 1, 0, 4, 9.00, 0.00, 13.33, 0.00, 0.00, 0, 0, '2026-07-20 20:43:14');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
CREATE TABLE IF NOT EXISTS `notifications` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int UNSIGNED NOT NULL,
  `type` enum('achievement','title','level_up','challenge','streak','system') COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci,
  `icon` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 0xF09F9494,
  `link` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'لینک مرتبط',
  `is_read` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user_unread` (`user_id`,`is_read`),
  KEY `idx_created` (`created_at`),
  KEY `idx_type` (`type`)
) ENGINE=InnoDB AUTO_INCREMENT=2111 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `type`, `title`, `message`, `icon`, `link`, `is_read`, `created_at`) VALUES
(2109, 10, 'title', '🏆 عنوان جدید!', 'تبریک! شما عنوان «تازه‌کار» را کسب کردید.', '🌱', '/achievements', 0, '2026-07-24 21:34:21'),
(2110, 3, 'title', '🏆 عنوان جدید!', 'تبریک! شما عنوان «تازه‌کار» را کسب کردید.', '🌱', '/achievements', 0, '2026-07-24 21:34:21');

-- --------------------------------------------------------

--
-- Table structure for table `notification_templates`
--

DROP TABLE IF EXISTS `notification_templates`;
CREATE TABLE IF NOT EXISTS `notification_templates` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `event_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `text_template` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `color_hex` varchar(7) COLLATE utf8mb4_unicode_ci DEFAULT '#3B82F6',
  `sound_file` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `animation_type` enum('bounce','shake','slide','fade') COLLATE utf8mb4_unicode_ci DEFAULT 'fade',
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `event_name` (`event_name`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notification_templates`
--

INSERT INTO `notification_templates` (`id`, `event_name`, `text_template`, `color_hex`, `sound_file`, `animation_type`, `is_active`, `created_at`) VALUES
(1, 'round_won', '🏆 تبریک {player}! شما با کارت {card} برنده دور {round} شدید!', '#10B981', 'win.mp3', 'bounce', 1, '2026-06-30 14:56:11'),
(2, 'round_lost', '💔 متأسفانه دور {round} را واگذار کردید. دفعه بعد!', '#EF4444', 'lose.mp3', 'shake', 1, '2026-06-30 14:56:11'),
(3, 'match_point', '🔥 {player} در آستانه پیروزی! ({wins}/{target})', '#F59E0B', 'matchpoint.mp3', 'bounce', 1, '2026-06-30 14:56:11'),
(4, 'game_finished', '👑 بازی تمام شد! برنده: {winner}', '#8B5CF6', 'fanfare.mp3', 'fade', 1, '2026-06-30 14:56:11'),
(5, 'title_stolen', '🚨 {new_holder} لقب «{title}» را از {old_holder} دزدید!', '#EF4444', 'steal.mp3', 'shake', 1, '2026-06-30 14:56:11'),
(6, 'achievement_unlocked', '🏅 نشان جدید: {achievement}!', '#10B981', 'achievement.mp3', 'bounce', 1, '2026-06-30 14:56:11');

-- --------------------------------------------------------

--
-- Table structure for table `otp_codes`
--

DROP TABLE IF EXISTS `otp_codes`;
CREATE TABLE IF NOT EXISTS `otp_codes` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `phone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `purpose` enum('register','login','reset_password') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `expires_at` timestamp NOT NULL,
  `used` tinyint(1) DEFAULT '0',
  `attempts` tinyint UNSIGNED DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_phone_purpose` (`phone`,`purpose`),
  KEY `idx_expires` (`expires_at`),
  KEY `idx_code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `otp_codes`
--

INSERT INTO `otp_codes` (`id`, `phone`, `code`, `purpose`, `ip_address`, `expires_at`, `used`, `attempts`, `created_at`) VALUES
(1, '09019177577', '255010', 'login', '127.0.0.1', '2026-07-21 15:01:54', 1, 1, '2026-07-21 14:56:54'),
(2, '09019177577', '687816', 'login', '127.0.0.1', '2026-07-21 15:15:06', 1, 1, '2026-07-21 15:10:06'),
(3, '09019177577', '190681', 'login', '127.0.0.1', '2026-07-21 15:16:44', 1, 1, '2026-07-21 15:11:44'),
(4, '09019177577', '848333', 'login', '127.0.0.1', '2026-07-21 15:19:57', 1, 0, '2026-07-21 15:14:57'),
(5, '09019177577', '776507', 'login', '127.0.0.1', '2026-07-21 15:24:54', 1, 1, '2026-07-21 15:19:54'),
(6, '09019177577', '786822', 'login', '192.168.2.13', '2026-07-21 15:28:01', 1, 1, '2026-07-21 15:23:01'),
(7, '09019177577', '359610', 'login', '127.0.0.1', '2026-07-21 15:34:35', 0, 0, '2026-07-21 15:29:35'),
(8, '09927049228', '303173', 'register', '127.0.0.1', '2026-07-21 15:37:44', 1, 1, '2026-07-21 15:32:44'),
(9, '09927049228', '529919', 'register', '127.0.0.1', '2026-07-21 16:02:46', 1, 1, '2026-07-21 15:57:46');

-- --------------------------------------------------------

--
-- Table structure for table `player_levels`
--

DROP TABLE IF EXISTS `player_levels`;
CREATE TABLE IF NOT EXISTS `player_levels` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `level` int UNSIGNED NOT NULL,
  `min_xp` int UNSIGNED NOT NULL COMMENT 'حداقل XP برای این سطح',
  `max_xp` int UNSIGNED NOT NULL COMMENT 'حداکثر XP برای این سطح',
  `title` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'عنوان سطح',
  `color` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT '#6366f1' COMMENT 'رنگ سطح',
  `icon` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT '⭐' COMMENT 'آیکون سطح',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_level` (`level`),
  KEY `idx_xp_range` (`min_xp`,`max_xp`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `player_levels`
--

INSERT INTO `player_levels` (`id`, `level`, `min_xp`, `max_xp`, `title`, `color`, `icon`) VALUES
(1, 1, 0, 99, 'تازه‌کار', '#94a3b8', '🌱'),
(2, 2, 100, 299, 'مبتدی', '#60a5fa', '🎮'),
(3, 3, 300, 599, 'بازیکن', '#3b82f6', '⭐'),
(4, 4, 600, 999, 'حرفه‌ای', '#8b5cf6', '🌟'),
(5, 5, 100000, 999999, 'ماهر', '#a855f7', '💫'),
(6, 6, 1000000, 1999999, 'استاد', '#d946ef', '✨'),
(7, 7, 2000000, 2999999, 'افسانه', '#ec4899', '🔥'),
(8, 8, 3000000, 3999999, 'اسطوره', '#f43f5e', '⚡'),
(9, 9, 4000000, 4999999, 'قهرمان', '#f59e0b', '🏆'),
(10, 10, 5000000, 6999999, 'پادشاه', '#eab308', '👑'),
(11, 11, 7000000, 8999999, 'امپراطور', '#dc2626', '💎'),
(12, 12, 9999999, 20000000, 'خدای UNO', '#7c2d12', '🌠');

-- --------------------------------------------------------

--
-- Table structure for table `referee_actions_log`
--

DROP TABLE IF EXISTS `referee_actions_log`;
CREATE TABLE IF NOT EXISTS `referee_actions_log` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `game_id` int UNSIGNED NOT NULL,
  `referee_id` int UNSIGNED NOT NULL,
  `action_type` enum('create','start','pause','resume','finish','cancel','score_edit','handover','round_add','round_edit') COLLATE utf8mb4_unicode_ci NOT NULL,
  `target_type` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `target_id` int UNSIGNED DEFAULT NULL,
  `old_value` json DEFAULT NULL,
  `new_value` json DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_reflog_game` (`game_id`),
  KEY `idx_reflog_referee` (`referee_id`)
) ENGINE=InnoDB AUTO_INCREMENT=742 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `referee_actions_log`
--

INSERT INTO `referee_actions_log` (`id`, `game_id`, `referee_id`, `action_type`, `target_type`, `target_id`, `old_value`, `new_value`, `ip_address`, `created_at`) VALUES
(713, 148, 1, '', 'game_round', 638, '{\"id\": 638, \"game_id\": 148, \"created_at\": \"2026-07-24 21:05:37\", \"win_type_id\": 1, \"round_number\": 27, \"winning_card_id\": 12, \"calculated_score\": \"15.00\", \"winner_team_name\": null, \"winner_participant_id\": 620}', NULL, NULL, '2026-07-24 17:35:44'),
(714, 148, 1, '', 'game_round', 641, '{\"id\": 641, \"game_id\": 148, \"created_at\": \"2026-07-24 21:07:16\", \"win_type_id\": null, \"round_number\": 29, \"winning_card_id\": 9, \"calculated_score\": \"10.00\", \"winner_team_name\": null, \"winner_participant_id\": 620}', NULL, NULL, '2026-07-24 17:40:52'),
(715, 148, 1, '', 'game_round', 640, '{\"id\": 640, \"game_id\": 148, \"created_at\": \"2026-07-24 21:06:32\", \"win_type_id\": null, \"round_number\": 28, \"winning_card_id\": null, \"calculated_score\": \"5.00\", \"winner_team_name\": null, \"winner_participant_id\": 620}', NULL, NULL, '2026-07-24 17:41:10'),
(716, 148, 1, '', 'game_round', 643, '{\"id\": 643, \"game_id\": 148, \"created_at\": \"2026-07-24 21:11:28\", \"win_type_id\": null, \"round_number\": 29, \"winning_card_id\": null, \"calculated_score\": \"5.00\", \"winner_team_name\": null, \"winner_participant_id\": 620}', NULL, NULL, '2026-07-24 17:41:37'),
(717, 150, 1, '', 'game_round', 645, '{\"id\": 645, \"game_id\": 150, \"created_at\": \"2026-07-24 21:12:26\", \"win_type_id\": null, \"round_number\": 3, \"winning_card_id\": null, \"calculated_score\": \"10.00\", \"winner_team_name\": \"تیم 2\", \"winner_participant_id\": 632}', NULL, NULL, '2026-07-24 17:42:33'),
(718, 148, 1, '', 'game_round', 647, '{\"id\": 647, \"game_id\": 148, \"created_at\": \"2026-07-24 22:32:18\", \"win_type_id\": null, \"round_number\": 30, \"winning_card_id\": 1, \"calculated_score\": \"10.00\", \"winner_team_name\": null, \"winner_participant_id\": 620}', NULL, NULL, '2026-07-24 19:10:11'),
(719, 148, 1, '', 'game_round', 648, '{\"id\": 648, \"game_id\": 148, \"created_at\": \"2026-07-24 22:40:27\", \"win_type_id\": null, \"round_number\": 30, \"winning_card_id\": null, \"calculated_score\": \"5.00\", \"winner_team_name\": null, \"winner_participant_id\": 620}', NULL, NULL, '2026-07-24 19:10:47'),
(720, 148, 1, '', 'game_round', 649, '{\"id\": 649, \"game_id\": 148, \"created_at\": \"2026-07-24 22:40:53\", \"win_type_id\": null, \"round_number\": 30, \"winning_card_id\": null, \"calculated_score\": \"5.00\", \"winner_team_name\": null, \"winner_participant_id\": 621}', NULL, NULL, '2026-07-24 19:27:04'),
(721, 148, 1, '', 'game_round', 650, '{\"id\": 650, \"game_id\": 148, \"created_at\": \"2026-07-24 23:02:24\", \"win_type_id\": null, \"round_number\": 30, \"winning_card_id\": null, \"calculated_score\": \"5.00\", \"winner_team_name\": null, \"winner_participant_id\": 621}', NULL, NULL, '2026-07-24 19:36:32'),
(722, 148, 1, '', 'game_round', 646, '{\"id\": 646, \"game_id\": 148, \"created_at\": \"2026-07-24 22:28:54\", \"win_type_id\": null, \"round_number\": 29, \"winning_card_id\": 6, \"calculated_score\": \"15.00\", \"winner_team_name\": null, \"winner_participant_id\": 619}', NULL, NULL, '2026-07-24 19:38:01'),
(723, 148, 1, '', 'game_round', 642, '{\"id\": 642, \"game_id\": 148, \"created_at\": \"2026-07-24 21:11:21\", \"win_type_id\": null, \"round_number\": 28, \"winning_card_id\": null, \"calculated_score\": \"5.00\", \"winner_team_name\": null, \"winner_participant_id\": 620}', NULL, NULL, '2026-07-24 19:38:53'),
(724, 148, 1, '', 'game_round', 639, '{\"id\": 639, \"game_id\": 148, \"created_at\": \"2026-07-24 21:05:54\", \"win_type_id\": null, \"round_number\": 27, \"winning_card_id\": 13, \"calculated_score\": \"15.00\", \"winner_team_name\": null, \"winner_participant_id\": 619}', NULL, NULL, '2026-07-24 19:41:09'),
(725, 148, 1, '', 'game_round', 656, '{\"id\": 656, \"game_id\": 148, \"created_at\": \"2026-07-25 00:26:12\", \"win_type_id\": null, \"round_number\": 32, \"winning_card_id\": null, \"calculated_score\": \"5.00\", \"winner_team_name\": null, \"winner_participant_id\": 619}', NULL, NULL, '2026-07-24 21:02:14'),
(726, 148, 1, '', 'game_round', 655, '{\"id\": 655, \"game_id\": 148, \"created_at\": \"2026-07-25 00:22:19\", \"win_type_id\": null, \"round_number\": 31, \"winning_card_id\": null, \"calculated_score\": \"5.00\", \"winner_team_name\": null, \"winner_participant_id\": 619}', NULL, NULL, '2026-07-24 21:04:54'),
(727, 148, 1, '', 'game_round', 654, '{\"id\": 654, \"game_id\": 148, \"created_at\": \"2026-07-25 00:03:33\", \"win_type_id\": null, \"round_number\": 30, \"winning_card_id\": null, \"calculated_score\": \"5.00\", \"winner_team_name\": null, \"winner_participant_id\": 619}', NULL, NULL, '2026-07-24 21:05:23'),
(728, 148, 1, '', 'game_round', 658, '{\"id\": 658, \"game_id\": 148, \"created_at\": \"2026-07-25 00:36:52\", \"win_type_id\": null, \"round_number\": 31, \"winning_card_id\": null, \"calculated_score\": \"5.00\", \"winner_team_name\": null, \"winner_participant_id\": 621}', NULL, NULL, '2026-07-24 21:07:05'),
(729, 148, 1, '', 'game_round', 657, '{\"id\": 657, \"game_id\": 148, \"created_at\": \"2026-07-25 00:36:09\", \"win_type_id\": null, \"round_number\": 30, \"winning_card_id\": null, \"calculated_score\": \"5.00\", \"winner_team_name\": null, \"winner_participant_id\": 619}', NULL, NULL, '2026-07-24 21:07:10'),
(730, 148, 1, '', 'game_round', 653, '{\"id\": 653, \"game_id\": 148, \"created_at\": \"2026-07-24 23:58:41\", \"win_type_id\": null, \"round_number\": 29, \"winning_card_id\": null, \"calculated_score\": \"5.00\", \"winner_team_name\": null, \"winner_participant_id\": 619}', NULL, NULL, '2026-07-24 21:07:14'),
(731, 148, 1, '', 'game_round', 652, '{\"id\": 652, \"game_id\": 148, \"created_at\": \"2026-07-24 23:26:45\", \"win_type_id\": null, \"round_number\": 28, \"winning_card_id\": null, \"calculated_score\": \"5.00\", \"winner_team_name\": null, \"winner_participant_id\": 621}', NULL, NULL, '2026-07-24 21:07:17'),
(732, 148, 1, '', 'game_round', 659, '{\"id\": 659, \"game_id\": 148, \"created_at\": \"2026-07-25 00:41:04\", \"win_type_id\": null, \"round_number\": 28, \"winning_card_id\": null, \"calculated_score\": \"5.00\", \"winner_team_name\": null, \"winner_participant_id\": 620}', NULL, NULL, '2026-07-24 21:15:32'),
(733, 148, 1, '', 'game_round', 651, '{\"id\": 651, \"game_id\": 148, \"created_at\": \"2026-07-24 23:24:06\", \"win_type_id\": null, \"round_number\": 27, \"winning_card_id\": null, \"calculated_score\": \"5.00\", \"winner_team_name\": null, \"winner_participant_id\": 619}', NULL, NULL, '2026-07-24 21:15:41'),
(734, 148, 1, '', 'game_round', 661, '{\"id\": 661, \"game_id\": 148, \"created_at\": \"2026-07-25 00:46:09\", \"win_type_id\": null, \"round_number\": 28, \"winning_card_id\": null, \"calculated_score\": \"5.00\", \"winner_team_name\": null, \"winner_participant_id\": 620}', NULL, NULL, '2026-07-24 21:16:20'),
(735, 148, 1, '', 'game_round', 665, '{\"id\": 665, \"game_id\": 148, \"created_at\": \"2026-07-25 00:48:55\", \"win_type_id\": null, \"round_number\": 31, \"winning_card_id\": null, \"calculated_score\": \"5.00\", \"winner_team_name\": null, \"winner_participant_id\": 620}', NULL, NULL, '2026-07-24 21:19:12'),
(736, 148, 1, '', 'game_round', 666, '{\"id\": 666, \"game_id\": 148, \"created_at\": \"2026-07-25 00:54:02\", \"win_type_id\": null, \"round_number\": 31, \"winning_card_id\": null, \"calculated_score\": \"5.00\", \"winner_team_name\": null, \"winner_participant_id\": 620}', NULL, NULL, '2026-07-24 21:29:41'),
(737, 148, 1, '', 'game_round', 667, '{\"id\": 667, \"game_id\": 148, \"created_at\": \"2026-07-25 01:00:18\", \"win_type_id\": null, \"round_number\": 31, \"winning_card_id\": null, \"calculated_score\": \"5.00\", \"winner_team_name\": null, \"winner_participant_id\": 620}', NULL, NULL, '2026-07-24 21:32:44'),
(738, 149, 1, '', 'game_round', 684, '{\"id\": 684, \"game_id\": 149, \"created_at\": \"2026-07-25 21:14:17\", \"win_type_id\": null, \"round_number\": 13, \"winning_card_id\": null, \"calculated_score\": \"5.00\", \"winner_team_name\": null, \"winner_participant_id\": 625}', NULL, NULL, '2026-07-25 17:44:24'),
(739, 149, 1, '', 'game_round', 696, '{\"id\": 696, \"game_id\": 149, \"created_at\": \"2026-07-25 21:49:58\", \"win_type_id\": null, \"round_number\": 24, \"winning_card_id\": null, \"calculated_score\": \"5.00\", \"winner_team_name\": null, \"winner_participant_id\": 624}', NULL, NULL, '2026-07-25 18:36:38'),
(740, 149, 1, '', 'game_round', 705, '{\"id\": 705, \"game_id\": 149, \"created_at\": \"2026-07-25 22:52:56\", \"win_type_id\": null, \"round_number\": 32, \"winning_card_id\": null, \"calculated_score\": \"5.00\", \"winner_team_name\": null, \"winner_participant_id\": 623}', NULL, NULL, '2026-07-25 19:28:09'),
(741, 149, 1, '', 'game_round', 715, '{\"id\": 715, \"game_id\": 149, \"created_at\": \"2026-07-25 23:18:27\", \"win_type_id\": null, \"round_number\": 41, \"winning_card_id\": null, \"calculated_score\": \"5.00\", \"winner_team_name\": null, \"winner_participant_id\": 624}', NULL, NULL, '2026-07-25 19:48:33');

-- --------------------------------------------------------

--
-- Table structure for table `sse_events`
--

DROP TABLE IF EXISTS `sse_events`;
CREATE TABLE IF NOT EXISTS `sse_events` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `channel` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'کانال رویداد (game_1, user_5, ...)',
  `event_type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'نوع رویداد',
  `data` json NOT NULL COMMENT 'داده‌های رویداد',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_channel_time` (`channel`,`created_at`),
  KEY `idx_event_type` (`event_type`),
  KEY `idx_created` (`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=3161 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sse_events`
--

INSERT INTO `sse_events` (`id`, `channel`, `event_type`, `data`, `created_at`) VALUES
(2977, 'game_147', 'round_recorded', '{\"card\": null, \"score\": 5, \"winner\": {\"id\": 3, \"name\": \"سنتری\", \"participant_id\": 617}, \"game_id\": 147, \"round_id\": 604, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-23 19:27:56\", \"round_number\": 9, \"source_user_id\": 1}', '2026-07-23 15:57:56'),
(2978, 'game_147', 'round_recorded', '{\"card\": null, \"score\": 5, \"winner\": {\"id\": 3, \"name\": \"سنتری\", \"participant_id\": 617}, \"game_id\": 147, \"round_id\": 605, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-23 19:28:59\", \"round_number\": 10, \"source_user_id\": 1}', '2026-07-23 15:58:59'),
(2979, 'game_147', 'round_recorded', '{\"card\": null, \"score\": 5, \"winner\": {\"id\": 3, \"name\": \"سنتری\", \"participant_id\": 617}, \"game_id\": 147, \"round_id\": 606, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-23 19:55:32\", \"round_number\": 11, \"source_user_id\": 1}', '2026-07-23 16:25:32'),
(2980, 'game_147', 'round_recorded', '{\"card\": null, \"score\": 5, \"winner\": {\"id\": 2, \"name\": \"شمپاق\", \"participant_id\": 618}, \"game_id\": 147, \"round_id\": 607, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-23 19:55:40\", \"round_number\": 12, \"source_user_id\": 1}', '2026-07-23 16:25:40'),
(2981, 'game_147', 'round_recorded', '{\"card\": null, \"score\": 5, \"winner\": {\"id\": 7, \"name\": \"امپراطور\", \"participant_id\": 616}, \"game_id\": 147, \"round_id\": 608, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-23 19:55:45\", \"round_number\": 13, \"source_user_id\": 1}', '2026-07-23 16:25:45'),
(2982, 'game_147', 'game_status_changed', '{\"status\": \"paused\", \"game_id\": 147, \"source_user_id\": 1}', '2026-07-23 16:26:49'),
(2983, 'game_146', 'game_status_changed', '{\"status\": \"active\", \"game_id\": 146, \"source_user_id\": 1}', '2026-07-23 16:28:36'),
(2984, 'game_146', 'round_recorded', '{\"card\": null, \"score\": 10, \"winner\": {\"id\": 7, \"name\": \"امپراطور\", \"participant_id\": 612}, \"game_id\": 146, \"round_id\": 609, \"win_type\": null, \"team_name\": \"تیم 2\", \"recorded_at\": \"2026-07-23 19:58:41\", \"round_number\": 5, \"source_user_id\": 1}', '2026-07-23 16:28:41'),
(2985, 'game_146', 'round_recorded', '{\"card\": null, \"score\": 10, \"winner\": {\"id\": 1, \"name\": \"Admin\", \"participant_id\": 611}, \"game_id\": 146, \"round_id\": 610, \"win_type\": null, \"team_name\": \"تیم 2\", \"recorded_at\": \"2026-07-23 19:58:55\", \"round_number\": 6, \"source_user_id\": 1}', '2026-07-23 16:28:55'),
(2986, 'game_146', 'game_status_changed', '{\"status\": \"paused\", \"game_id\": 146, \"source_user_id\": 1}', '2026-07-23 16:30:03'),
(2987, 'game_148', 'game_started', '{\"status\": \"active\", \"game_id\": 148, \"started_at\": \"2026-07-23 23:16:03\", \"first_player\": {\"id\": 622, \"name\": \"شمپاق\"}, \"source_user_id\": 1}', '2026-07-23 19:46:03'),
(2988, 'game_148', 'round_recorded', '{\"card\": null, \"score\": 5, \"winner\": {\"id\": 3, \"name\": \"سنتری\", \"participant_id\": 621}, \"game_id\": 148, \"round_id\": 611, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-23 23:17:03\", \"round_number\": 1, \"source_user_id\": 1}', '2026-07-23 19:47:03'),
(2989, 'game_148', 'round_recorded', '{\"card\": null, \"score\": 5, \"winner\": {\"id\": 2, \"name\": \"شمپاق\", \"participant_id\": 622}, \"game_id\": 148, \"round_id\": 612, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-23 23:24:29\", \"round_number\": 2, \"source_user_id\": 1}', '2026-07-23 19:54:29'),
(2990, 'game_148', 'round_recorded', '{\"card\": null, \"score\": 5, \"winner\": {\"id\": 7, \"name\": \"امپراطور\", \"participant_id\": 620}, \"game_id\": 148, \"round_id\": 613, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-23 23:29:36\", \"round_number\": 3, \"source_user_id\": 1}', '2026-07-23 19:59:36'),
(2991, 'game_148', 'round_recorded', '{\"card\": null, \"score\": 5, \"winner\": {\"id\": 2, \"name\": \"شمپاق\", \"participant_id\": 622}, \"game_id\": 148, \"round_id\": 614, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-23 23:33:05\", \"round_number\": 4, \"source_user_id\": 1}', '2026-07-23 20:03:05'),
(2992, 'game_148', 'round_recorded', '{\"card\": null, \"score\": 5, \"winner\": {\"id\": 3, \"name\": \"سنتری\", \"participant_id\": 621}, \"game_id\": 148, \"round_id\": 615, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-23 23:42:24\", \"round_number\": 5, \"source_user_id\": 1}', '2026-07-23 20:12:24'),
(2993, 'game_148', 'round_recorded', '{\"card\": null, \"score\": 5, \"winner\": {\"id\": 7, \"name\": \"امپراطور\", \"participant_id\": 620}, \"game_id\": 148, \"round_id\": 616, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-23 23:42:33\", \"round_number\": 6, \"source_user_id\": 1}', '2026-07-23 20:12:33'),
(2994, 'game_148', 'round_recorded', '{\"card\": null, \"score\": 5, \"winner\": {\"id\": 1, \"name\": \"Admin\", \"participant_id\": 619}, \"game_id\": 148, \"round_id\": 617, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-23 23:42:47\", \"round_number\": 7, \"source_user_id\": 1}', '2026-07-23 20:12:47'),
(2995, 'game_148', 'game_status_changed', '{\"status\": \"paused\", \"game_id\": 148, \"source_user_id\": 1}', '2026-07-23 20:13:03'),
(2996, 'game_148', 'game_status_changed', '{\"status\": \"active\", \"game_id\": 148, \"source_user_id\": 1}', '2026-07-23 20:13:10'),
(2997, 'game_148', 'game_status_changed', '{\"status\": \"paused\", \"game_id\": 148, \"source_user_id\": 1}', '2026-07-23 20:13:22'),
(2998, 'game_148', 'game_status_changed', '{\"status\": \"active\", \"game_id\": 148, \"source_user_id\": 1}', '2026-07-23 20:13:41'),
(2999, 'game_148', 'round_recorded', '{\"card\": null, \"score\": 5, \"winner\": {\"id\": 2, \"name\": \"شمپاق\", \"participant_id\": 622}, \"game_id\": 148, \"round_id\": 618, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-23 23:44:37\", \"round_number\": 8, \"source_user_id\": 1}', '2026-07-23 20:14:37'),
(3000, 'game_148', 'round_recorded', '{\"card\": null, \"score\": 5, \"winner\": {\"id\": 3, \"name\": \"سنتری\", \"participant_id\": 621}, \"game_id\": 148, \"round_id\": 619, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-23 23:44:43\", \"round_number\": 9, \"source_user_id\": 1}', '2026-07-23 20:14:43'),
(3001, 'game_148', 'round_recorded', '{\"card\": null, \"score\": 5, \"winner\": {\"id\": 7, \"name\": \"امپراطور\", \"participant_id\": 620}, \"game_id\": 148, \"round_id\": 620, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-23 23:44:52\", \"round_number\": 10, \"source_user_id\": 1}', '2026-07-23 20:14:52'),
(3002, 'game_148', 'round_recorded', '{\"card\": null, \"score\": 5, \"winner\": {\"id\": 1, \"name\": \"Admin\", \"participant_id\": 619}, \"game_id\": 148, \"round_id\": 621, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-23 23:44:58\", \"round_number\": 11, \"source_user_id\": 1}', '2026-07-23 20:14:58'),
(3003, 'game_148', 'round_recorded', '{\"card\": null, \"score\": 5, \"winner\": {\"id\": 7, \"name\": \"امپراطور\", \"participant_id\": 620}, \"game_id\": 148, \"round_id\": 622, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-23 23:49:08\", \"round_number\": 12, \"source_user_id\": 1}', '2026-07-23 20:19:08'),
(3004, 'game_148', 'round_recorded', '{\"card\": null, \"score\": 5, \"winner\": {\"id\": 2, \"name\": \"شمپاق\", \"participant_id\": 622}, \"game_id\": 148, \"round_id\": 623, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-23 23:51:31\", \"round_number\": 13, \"source_user_id\": 1}', '2026-07-23 20:21:31'),
(3005, 'game_148', 'round_recorded', '{\"card\": null, \"score\": 5, \"winner\": {\"id\": 3, \"name\": \"سنتری\", \"participant_id\": 621}, \"game_id\": 148, \"round_id\": 624, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-23 23:51:53\", \"round_number\": 14, \"source_user_id\": 1}', '2026-07-23 20:21:53'),
(3006, 'game_148', 'round_recorded', '{\"card\": null, \"score\": 5, \"winner\": {\"id\": 3, \"name\": \"سنتری\", \"participant_id\": 621}, \"game_id\": 148, \"round_id\": 625, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-23 23:54:13\", \"round_number\": 15, \"source_user_id\": 1}', '2026-07-23 20:24:13'),
(3007, 'game_148', 'round_recorded', '{\"card\": null, \"score\": 5, \"winner\": {\"id\": 7, \"name\": \"امپراطور\", \"participant_id\": 620}, \"game_id\": 148, \"round_id\": 626, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-23 23:54:32\", \"round_number\": 16, \"source_user_id\": 1}', '2026-07-23 20:24:32'),
(3008, 'game_148', 'round_recorded', '{\"card\": null, \"score\": 5, \"winner\": {\"id\": 1, \"name\": \"Admin\", \"participant_id\": 619}, \"game_id\": 148, \"round_id\": 627, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-23 23:54:44\", \"round_number\": 17, \"source_user_id\": 1}', '2026-07-23 20:24:44'),
(3009, 'game_148', 'game_status_changed', '{\"status\": \"paused\", \"game_id\": 148, \"source_user_id\": 1}', '2026-07-23 20:25:25'),
(3010, 'game_148', 'game_status_changed', '{\"status\": \"active\", \"game_id\": 148, \"source_user_id\": 1}', '2026-07-24 06:55:58'),
(3011, 'game_148', 'round_recorded', '{\"card\": {\"id\": 15, \"name\": \"کینگ\", \"emoji\": \"👑\", \"rarity\": \"legendary\", \"multiplier\": 5}, \"score\": 25, \"winner\": {\"id\": 2, \"name\": \"شمپاق\", \"participant_id\": 622}, \"game_id\": 148, \"round_id\": 628, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-24 10:26:06\", \"round_number\": 18, \"source_user_id\": 1}', '2026-07-24 06:56:06'),
(3012, 'game_148', 'round_recorded', '{\"card\": {\"id\": 9, \"name\": \"هدیه\", \"emoji\": \"🎁\", \"rarity\": \"rare\", \"multiplier\": 2}, \"score\": 10, \"winner\": {\"id\": 7, \"name\": \"امپراطور\", \"participant_id\": 620}, \"game_id\": 148, \"round_id\": 629, \"win_type\": {\"id\": 4, \"icon\": \"💪\", \"name\": \"سلطه‌گر\", \"multiplier\": 1}, \"team_name\": null, \"recorded_at\": \"2026-07-24 11:04:28\", \"round_number\": 19, \"source_user_id\": 1}', '2026-07-24 07:34:28'),
(3013, 'game_148', 'round_recorded', '{\"card\": {\"id\": 5, \"name\": \"تغییر رنگ\", \"emoji\": \"🌈\", \"rarity\": \"common\", \"multiplier\": 1.5}, \"score\": 7.5, \"winner\": {\"id\": 3, \"name\": \"سنتری\", \"participant_id\": 621}, \"game_id\": 148, \"round_id\": 630, \"win_type\": {\"id\": 5, \"icon\": \"💣\", \"name\": \"تاکتیکی\", \"multiplier\": 1}, \"team_name\": null, \"recorded_at\": \"2026-07-24 11:18:50\", \"round_number\": 20, \"source_user_id\": 1}', '2026-07-24 07:48:50'),
(3014, 'game_148', 'round_recorded', '{\"card\": {\"id\": 7, \"name\": \"سپر\", \"emoji\": \"🛡️\", \"rarity\": \"rare\", \"multiplier\": 2.5}, \"score\": 12.5, \"winner\": {\"id\": 2, \"name\": \"شمپاق\", \"participant_id\": 622}, \"game_id\": 148, \"round_id\": 631, \"win_type\": {\"id\": 3, \"icon\": \"🍀\", \"name\": \"شانسی\", \"multiplier\": 1}, \"team_name\": null, \"recorded_at\": \"2026-07-24 11:21:49\", \"round_number\": 21, \"source_user_id\": 1}', '2026-07-24 07:51:49'),
(3015, 'game_148', 'round_recorded', '{\"card\": {\"id\": 8, \"name\": \"دید زدن\", \"emoji\": \"👁️\", \"rarity\": \"rare\", \"multiplier\": 3}, \"score\": 15, \"winner\": {\"id\": 7, \"name\": \"امپراطور\", \"participant_id\": 620}, \"game_id\": 148, \"round_id\": 632, \"win_type\": {\"id\": 2, \"icon\": \"🔄\", \"name\": \"کامبک\", \"multiplier\": 1}, \"team_name\": null, \"recorded_at\": \"2026-07-24 14:39:39\", \"round_number\": 22, \"source_user_id\": 1}', '2026-07-24 11:09:39'),
(3016, 'game_148', 'round_recorded', '{\"card\": {\"id\": 12, \"name\": \"شافل\", \"emoji\": \"🌀\", \"rarity\": \"rare\", \"multiplier\": 3}, \"score\": 15, \"winner\": {\"id\": 2, \"name\": \"شمپاق\", \"participant_id\": 622}, \"game_id\": 148, \"round_id\": 633, \"win_type\": {\"id\": 4, \"icon\": \"💪\", \"name\": \"سلطه‌گر\", \"multiplier\": 1}, \"team_name\": null, \"recorded_at\": \"2026-07-24 14:47:49\", \"round_number\": 23, \"source_user_id\": 1}', '2026-07-24 11:17:49'),
(3017, 'game_148', 'round_recorded', '{\"card\": {\"id\": 9, \"name\": \"هدیه\", \"emoji\": \"🎁\", \"rarity\": \"rare\", \"multiplier\": 2}, \"score\": 10, \"winner\": {\"id\": 7, \"name\": \"امپراطور\", \"participant_id\": 620}, \"game_id\": 148, \"round_id\": 634, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-24 16:59:41\", \"round_number\": 24, \"source_user_id\": 1}', '2026-07-24 13:29:41'),
(3018, 'game_148', 'round_recorded', '{\"card\": {\"id\": 7, \"name\": \"سپر\", \"emoji\": \"🛡️\", \"rarity\": \"rare\", \"multiplier\": 2.5}, \"score\": 12.5, \"winner\": {\"id\": 2, \"name\": \"شمپاق\", \"participant_id\": 622}, \"game_id\": 148, \"round_id\": 635, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-24 17:00:16\", \"round_number\": 25, \"source_user_id\": 1}', '2026-07-24 13:30:16'),
(3019, 'game_148', 'round_recorded', '{\"card\": {\"id\": 11, \"name\": \"پرش دوتایی\", \"emoji\": \"⏩\", \"rarity\": \"rare\", \"multiplier\": 2.5}, \"score\": 12.5, \"winner\": {\"id\": 3, \"name\": \"سنتری\", \"participant_id\": 621}, \"game_id\": 148, \"round_id\": 636, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-24 17:03:14\", \"round_number\": 26, \"source_user_id\": 1}', '2026-07-24 13:33:14'),
(3020, 'game_148', 'game_status_changed', '{\"status\": \"paused\", \"game_id\": 148, \"source_user_id\": 1}', '2026-07-24 17:26:16'),
(3021, 'game_150', 'game_started', '{\"status\": \"active\", \"game_id\": 150, \"started_at\": \"2026-07-24 20:56:47\", \"first_player\": {\"id\": 632, \"name\": \"شمپاق\"}, \"source_user_id\": 1}', '2026-07-24 17:26:47'),
(3022, 'game_150', 'round_recorded', '{\"card\": {\"id\": 2, \"name\": \"پرش یکی\", \"emoji\": \"⏭️\", \"rarity\": \"common\", \"multiplier\": 1.5}, \"score\": 15, \"winner\": {\"id\": 7, \"name\": \"امپراطور\", \"participant_id\": 628}, \"game_id\": 150, \"round_id\": 637, \"win_type\": null, \"team_name\": \"تیم 2\", \"recorded_at\": \"2026-07-24 21:03:57\", \"round_number\": 1, \"source_user_id\": 1}', '2026-07-24 17:33:57'),
(3023, 'game_150', 'game_status_changed', '{\"status\": \"paused\", \"game_id\": 150, \"source_user_id\": 1}', '2026-07-24 17:35:20'),
(3024, 'game_148', 'game_status_changed', '{\"status\": \"active\", \"game_id\": 148, \"source_user_id\": 1}', '2026-07-24 17:35:25'),
(3025, 'game_148', 'round_recorded', '{\"card\": {\"id\": 12, \"name\": \"شافل\", \"emoji\": \"🌀\", \"rarity\": \"rare\", \"multiplier\": 3}, \"score\": 15, \"winner\": {\"id\": 7, \"name\": \"امپراطور\", \"participant_id\": 620}, \"game_id\": 148, \"round_id\": 638, \"win_type\": {\"id\": 1, \"icon\": \"✅\", \"name\": \"برد معمولی\", \"multiplier\": 1}, \"team_name\": null, \"recorded_at\": \"2026-07-24 21:05:37\", \"round_number\": 27, \"source_user_id\": 1}', '2026-07-24 17:35:37'),
(3026, 'game_148', 'round_undone', '{\"game_id\": 148, \"undone_at\": \"2026-07-24 21:05:44\", \"undone_round\": 27, \"source_user_id\": 1}', '2026-07-24 17:35:44'),
(3027, 'game_148', 'round_recorded', '{\"card\": {\"id\": 13, \"name\": \"قفل کردن\", \"emoji\": \"🔒\", \"rarity\": \"rare\", \"multiplier\": 3}, \"score\": 15, \"winner\": {\"id\": 1, \"name\": \"Admin\", \"participant_id\": 619}, \"game_id\": 148, \"round_id\": 639, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-24 21:05:54\", \"round_number\": 27, \"source_user_id\": 1}', '2026-07-24 17:35:54'),
(3028, 'game_148', 'round_recorded', '{\"card\": null, \"score\": 5, \"winner\": {\"id\": 7, \"name\": \"امپراطور\", \"participant_id\": 620}, \"game_id\": 148, \"round_id\": 640, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-24 21:06:32\", \"round_number\": 28, \"source_user_id\": 1}', '2026-07-24 17:36:32'),
(3029, 'game_148', 'round_recorded', '{\"card\": {\"id\": 9, \"name\": \"هدیه\", \"emoji\": \"🎁\", \"rarity\": \"rare\", \"multiplier\": 2}, \"score\": 10, \"winner\": {\"id\": 7, \"name\": \"امپراطور\", \"participant_id\": 620}, \"game_id\": 148, \"round_id\": 641, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-24 21:07:16\", \"round_number\": 29, \"source_user_id\": 1}', '2026-07-24 17:37:16'),
(3030, 'game_148', 'round_undone', '{\"game_id\": 148, \"undone_at\": \"2026-07-24 21:10:52\", \"undone_round\": 29, \"source_user_id\": 1}', '2026-07-24 17:40:52'),
(3031, 'game_148', 'round_undone', '{\"game_id\": 148, \"undone_at\": \"2026-07-24 21:11:10\", \"undone_round\": 28, \"source_user_id\": 1}', '2026-07-24 17:41:10'),
(3032, 'game_148', 'round_recorded', '{\"card\": null, \"score\": 5, \"winner\": {\"id\": 7, \"name\": \"امپراطور\", \"participant_id\": 620}, \"game_id\": 148, \"round_id\": 642, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-24 21:11:21\", \"round_number\": 28, \"source_user_id\": 1}', '2026-07-24 17:41:21'),
(3033, 'game_148', 'round_recorded', '{\"card\": null, \"score\": 5, \"winner\": {\"id\": 7, \"name\": \"امپراطور\", \"participant_id\": 620}, \"game_id\": 148, \"round_id\": 643, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-24 21:11:28\", \"round_number\": 29, \"source_user_id\": 1}', '2026-07-24 17:41:28'),
(3034, 'game_148', 'round_undone', '{\"game_id\": 148, \"undone_at\": \"2026-07-24 21:11:37\", \"undone_round\": 29, \"source_user_id\": 1}', '2026-07-24 17:41:37'),
(3035, 'game_150', 'game_target_changed', '{\"game_id\": 150, \"max_wins\": 1, \"changed_at\": \"2026-07-24 21:11:54\", \"min_target\": 3, \"new_target\": 3, \"old_target\": 10, \"source_user_id\": 1}', '2026-07-24 17:41:54'),
(3036, 'game_148', 'game_status_changed', '{\"status\": \"paused\", \"game_id\": 148, \"source_user_id\": 1}', '2026-07-24 17:42:03'),
(3037, 'game_150', 'game_status_changed', '{\"status\": \"active\", \"game_id\": 150, \"source_user_id\": 1}', '2026-07-24 17:42:06'),
(3038, 'game_150', 'round_recorded', '{\"card\": null, \"score\": 10, \"winner\": {\"id\": 2, \"name\": \"شمپاق\", \"participant_id\": 632}, \"game_id\": 150, \"round_id\": 644, \"win_type\": null, \"team_name\": \"تیم 2\", \"recorded_at\": \"2026-07-24 21:12:13\", \"round_number\": 2, \"source_user_id\": 1}', '2026-07-24 17:42:13'),
(3039, 'game_150', 'round_recorded', '{\"card\": null, \"score\": 10, \"winner\": {\"id\": 2, \"name\": \"شمپاق\", \"participant_id\": 632}, \"game_id\": 150, \"round_id\": 645, \"win_type\": null, \"team_name\": \"تیم 2\", \"recorded_at\": \"2026-07-24 21:12:26\", \"round_number\": 3, \"source_user_id\": 1}', '2026-07-24 17:42:26'),
(3040, 'game_150', 'round_undone', '{\"game_id\": 150, \"undone_at\": \"2026-07-24 21:12:33\", \"undone_round\": 3, \"source_user_id\": 1}', '2026-07-24 17:42:33'),
(3041, 'game_150', 'game_status_changed', '{\"status\": \"paused\", \"game_id\": 150, \"source_user_id\": 1}', '2026-07-24 17:42:39'),
(3042, 'game_148', 'game_status_changed', '{\"status\": \"active\", \"game_id\": 148, \"source_user_id\": 1}', '2026-07-24 18:58:18'),
(3043, 'game_148', 'round_recorded', '{\"card\": {\"id\": 6, \"name\": \"جریمه ۴ تایی\", \"emoji\": \"+4\", \"rarity\": \"rare\", \"multiplier\": 3}, \"score\": 15, \"winner\": {\"id\": 1, \"name\": \"Admin\", \"participant_id\": 619}, \"game_id\": 148, \"round_id\": 646, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-24 22:28:54\", \"round_number\": 29, \"source_user_id\": 1}', '2026-07-24 18:58:54'),
(3044, 'game_148', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 2}, \"score\": 10, \"winner\": {\"id\": 7, \"name\": \"امپراطور\", \"participant_id\": 620}, \"game_id\": 148, \"round_id\": 647, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-24 22:32:18\", \"round_number\": 30, \"source_user_id\": 1}', '2026-07-24 19:02:18'),
(3045, 'game_148', 'round_undone', '{\"game_id\": 148, \"undone_at\": \"2026-07-24 22:40:11\", \"undone_round\": 30, \"source_user_id\": 1}', '2026-07-24 19:10:11'),
(3046, 'game_148', 'round_recorded', '{\"card\": null, \"score\": 5, \"winner\": {\"id\": 7, \"name\": \"امپراطور\", \"participant_id\": 620}, \"game_id\": 148, \"round_id\": 648, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-24 22:40:27\", \"round_number\": 30, \"source_user_id\": 1}', '2026-07-24 19:10:27'),
(3047, 'game_148', 'round_undone', '{\"game_id\": 148, \"undone_at\": \"2026-07-24 22:40:47\", \"undone_round\": 30, \"source_user_id\": 1}', '2026-07-24 19:10:47'),
(3048, 'game_148', 'round_recorded', '{\"card\": null, \"score\": 5, \"winner\": {\"id\": 3, \"name\": \"سنتری\", \"participant_id\": 621}, \"game_id\": 148, \"round_id\": 649, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-24 22:40:53\", \"round_number\": 30, \"source_user_id\": 1}', '2026-07-24 19:10:53'),
(3049, 'game_148', 'round_undone', '{\"game_id\": 148, \"undone_at\": \"2026-07-24 22:57:04\", \"undone_round\": 30, \"source_user_id\": 1}', '2026-07-24 19:27:04'),
(3050, 'game_148', 'round_recorded', '{\"card\": null, \"score\": 5, \"winner\": {\"id\": 3, \"name\": \"سنتری\", \"participant_id\": 621}, \"game_id\": 148, \"round_id\": 650, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-24 23:02:24\", \"round_number\": 30, \"source_user_id\": 1}', '2026-07-24 19:32:24'),
(3051, 'game_148', 'round_undone', '{\"game_id\": 148, \"undone_at\": \"2026-07-24 23:06:32\", \"undone_round\": 30, \"source_user_id\": 1}', '2026-07-24 19:36:32'),
(3052, 'game_148', 'round_undone', '{\"game_id\": 148, \"undone_at\": \"2026-07-24 23:08:01\", \"undone_round\": 29, \"source_user_id\": 1}', '2026-07-24 19:38:01'),
(3053, 'game_148', 'round_undone', '{\"game_id\": 148, \"undone_at\": \"2026-07-24 23:08:53\", \"undone_round\": 28, \"source_user_id\": 1}', '2026-07-24 19:38:53'),
(3054, 'game_148', 'round_undone', '{\"game_id\": 148, \"undone_at\": \"2026-07-24 23:11:09\", \"undone_round\": 27, \"source_user_id\": 1}', '2026-07-24 19:41:09'),
(3055, 'game_148', 'round_recorded', '{\"card\": null, \"score\": 5, \"winner\": {\"id\": 1, \"name\": \"Admin\", \"participant_id\": 619}, \"game_id\": 148, \"round_id\": 651, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-24 23:24:06\", \"round_number\": 27, \"source_user_id\": 1}', '2026-07-24 19:54:06'),
(3056, 'game_148', 'round_recorded', '{\"card\": null, \"score\": 5, \"winner\": {\"id\": 3, \"name\": \"سنتری\", \"participant_id\": 621}, \"game_id\": 148, \"round_id\": 652, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-24 23:26:45\", \"round_number\": 28, \"source_user_id\": 1}', '2026-07-24 19:56:45'),
(3057, 'game_148', 'round_recorded', '{\"card\": null, \"score\": 5, \"winner\": {\"id\": 1, \"name\": \"Admin\", \"participant_id\": 619}, \"game_id\": 148, \"round_id\": 653, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-24 23:58:41\", \"round_number\": 29, \"source_user_id\": 1}', '2026-07-24 20:28:41'),
(3058, 'game_148', 'round_recorded', '{\"card\": null, \"score\": 5, \"winner\": {\"id\": 1, \"name\": \"Admin\", \"participant_id\": 619}, \"game_id\": 148, \"round_id\": 654, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-25 00:03:33\", \"round_number\": 30, \"source_user_id\": 1}', '2026-07-24 20:33:33'),
(3059, 'game_148', 'round_recorded', '{\"card\": null, \"score\": 5, \"winner\": {\"id\": 1, \"name\": \"Admin\", \"participant_id\": 619}, \"game_id\": 148, \"round_id\": 655, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-25 00:22:19\", \"round_number\": 31, \"source_user_id\": 1}', '2026-07-24 20:52:19'),
(3060, 'game_148', 'round_recorded', '{\"card\": null, \"score\": 5, \"winner\": {\"id\": 1, \"name\": \"Admin\", \"participant_id\": 619}, \"game_id\": 148, \"round_id\": 656, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-25 00:26:13\", \"round_number\": 32, \"source_user_id\": 1}', '2026-07-24 20:56:13'),
(3061, 'game_148', 'round_undone', '{\"game_id\": 148, \"undone_at\": \"2026-07-25 00:32:14\", \"undone_round\": 32, \"source_user_id\": 1}', '2026-07-24 21:02:14'),
(3062, 'game_148', 'round_undone', '{\"game_id\": 148, \"undone_at\": \"2026-07-25 00:34:54\", \"undone_round\": 31, \"source_user_id\": 1}', '2026-07-24 21:04:54'),
(3063, 'game_148', 'round_undone', '{\"game_id\": 148, \"undone_at\": \"2026-07-25 00:35:23\", \"undone_round\": 30, \"source_user_id\": 1}', '2026-07-24 21:05:23'),
(3064, 'game_148', 'round_recorded', '{\"card\": null, \"score\": 5, \"winner\": {\"id\": 1, \"name\": \"Admin\", \"participant_id\": 619}, \"game_id\": 148, \"round_id\": 657, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-25 00:36:09\", \"round_number\": 30, \"source_user_id\": 1}', '2026-07-24 21:06:09'),
(3065, 'game_148', 'round_recorded', '{\"card\": null, \"score\": 5, \"winner\": {\"id\": 3, \"name\": \"سنتری\", \"participant_id\": 621}, \"game_id\": 148, \"round_id\": 658, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-25 00:36:52\", \"round_number\": 31, \"source_user_id\": 1}', '2026-07-24 21:06:52'),
(3066, 'game_148', 'round_undone', '{\"game_id\": 148, \"undone_at\": \"2026-07-25 00:37:05\", \"undone_round\": 31, \"source_user_id\": 1}', '2026-07-24 21:07:05'),
(3067, 'game_148', 'round_undone', '{\"game_id\": 148, \"undone_at\": \"2026-07-25 00:37:10\", \"undone_round\": 30, \"source_user_id\": 1}', '2026-07-24 21:07:10'),
(3068, 'game_148', 'round_undone', '{\"game_id\": 148, \"undone_at\": \"2026-07-25 00:37:14\", \"undone_round\": 29, \"source_user_id\": 1}', '2026-07-24 21:07:14'),
(3069, 'game_148', 'round_undone', '{\"game_id\": 148, \"undone_at\": \"2026-07-25 00:37:17\", \"undone_round\": 28, \"source_user_id\": 1}', '2026-07-24 21:07:17'),
(3070, 'game_148', 'game_status_changed', '{\"status\": \"paused\", \"game_id\": 148, \"source_user_id\": 1}', '2026-07-24 21:07:34'),
(3071, 'game_148', 'game_status_changed', '{\"status\": \"active\", \"game_id\": 148, \"source_user_id\": 1}', '2026-07-24 21:07:43'),
(3072, 'game_148', 'round_recorded', '{\"card\": null, \"score\": 5, \"winner\": {\"id\": 7, \"name\": \"امپراطور\", \"participant_id\": 620}, \"game_id\": 148, \"round_id\": 659, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-25 00:41:04\", \"round_number\": 28, \"source_user_id\": 1}', '2026-07-24 21:11:04'),
(3073, 'game_148', 'round_undone', '{\"game_id\": 148, \"undone_at\": \"2026-07-25 00:45:32\", \"undone_round\": 28, \"source_user_id\": 1}', '2026-07-24 21:15:32'),
(3074, 'game_148', 'round_undone', '{\"game_id\": 148, \"undone_at\": \"2026-07-25 00:45:41\", \"undone_round\": 27, \"source_user_id\": 1}', '2026-07-24 21:15:41'),
(3075, 'game_148', 'round_recorded', '{\"card\": null, \"score\": 5, \"winner\": {\"id\": 7, \"name\": \"امپراطور\", \"participant_id\": 620}, \"game_id\": 148, \"round_id\": 660, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-25 00:45:54\", \"round_number\": 27, \"source_user_id\": 1}', '2026-07-24 21:15:54'),
(3076, 'game_148', 'round_recorded', '{\"card\": null, \"score\": 5, \"winner\": {\"id\": 7, \"name\": \"امپراطور\", \"participant_id\": 620}, \"game_id\": 148, \"round_id\": 661, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-25 00:46:09\", \"round_number\": 28, \"source_user_id\": 1}', '2026-07-24 21:16:09'),
(3077, 'game_148', 'round_undone', '{\"game_id\": 148, \"undone_at\": \"2026-07-25 00:46:20\", \"undone_round\": 28, \"source_user_id\": 1}', '2026-07-24 21:16:20'),
(3078, 'game_148', 'round_recorded', '{\"card\": null, \"score\": 5, \"winner\": {\"id\": 2, \"name\": \"شمپاق\", \"participant_id\": 622}, \"game_id\": 148, \"round_id\": 662, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-25 00:46:25\", \"round_number\": 28, \"source_user_id\": 1}', '2026-07-24 21:16:25'),
(3079, 'game_148', 'round_recorded', '{\"card\": null, \"score\": 5, \"winner\": {\"id\": 1, \"name\": \"Admin\", \"participant_id\": 619}, \"game_id\": 148, \"round_id\": 663, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-25 00:46:32\", \"round_number\": 29, \"source_user_id\": 1}', '2026-07-24 21:16:32'),
(3080, 'game_148', 'round_recorded', '{\"card\": null, \"score\": 5, \"winner\": {\"id\": 3, \"name\": \"سنتری\", \"participant_id\": 621}, \"game_id\": 148, \"round_id\": 664, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-25 00:46:57\", \"round_number\": 30, \"source_user_id\": 1}', '2026-07-24 21:16:57'),
(3081, 'game_148', 'round_recorded', '{\"card\": null, \"score\": 5, \"winner\": {\"id\": 7, \"name\": \"امپراطور\", \"participant_id\": 620}, \"game_id\": 148, \"round_id\": 665, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-25 00:48:55\", \"round_number\": 31, \"source_user_id\": 1}', '2026-07-24 21:18:55'),
(3082, 'game_148', 'round_undone', '{\"game_id\": 148, \"undone_at\": \"2026-07-25 00:49:12\", \"undone_round\": 31, \"source_user_id\": 1}', '2026-07-24 21:19:12'),
(3083, 'game_148', 'round_recorded', '{\"card\": null, \"score\": 5, \"winner\": {\"id\": 7, \"name\": \"امپراطور\", \"participant_id\": 620}, \"game_id\": 148, \"round_id\": 666, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-25 00:54:02\", \"round_number\": 31, \"source_user_id\": 1}', '2026-07-24 21:24:02'),
(3084, 'game_148', 'round_undone', '{\"game_id\": 148, \"undone_at\": \"2026-07-25 00:59:41\", \"undone_round\": 31, \"source_user_id\": 1}', '2026-07-24 21:29:41'),
(3085, 'game_148', 'round_recorded', '{\"card\": null, \"score\": 5, \"winner\": {\"id\": 7, \"name\": \"امپراطور\", \"participant_id\": 620}, \"game_id\": 148, \"round_id\": 667, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-25 01:00:18\", \"round_number\": 31, \"source_user_id\": 1}', '2026-07-24 21:30:18'),
(3086, 'game_148', 'round_undone', '{\"game_id\": 148, \"undone_at\": \"2026-07-25 01:02:44\", \"undone_round\": 31, \"source_user_id\": 1}', '2026-07-24 21:32:44'),
(3087, 'game_148', 'game_status_changed', '{\"status\": \"paused\", \"game_id\": 148, \"source_user_id\": 1}', '2026-07-24 21:33:02'),
(3088, 'game_150', 'game_status_changed', '{\"status\": \"active\", \"game_id\": 150, \"source_user_id\": 1}', '2026-07-24 21:33:13'),
(3089, 'game_150', 'round_recorded', '{\"card\": null, \"score\": 10, \"winner\": {\"id\": 5, \"name\": \"خسرو\", \"participant_id\": 629}, \"game_id\": 150, \"round_id\": 668, \"win_type\": null, \"team_name\": \"تیم 1\", \"recorded_at\": \"2026-07-25 01:03:29\", \"round_number\": 3, \"source_user_id\": 1}', '2026-07-24 21:33:29'),
(3090, 'game_150', 'round_recorded', '{\"card\": null, \"score\": 10, \"winner\": {\"id\": 10, \"name\": \"زینب\", \"participant_id\": 630}, \"game_id\": 150, \"round_id\": 669, \"win_type\": null, \"team_name\": \"تیم 1\", \"recorded_at\": \"2026-07-25 01:03:56\", \"round_number\": 4, \"source_user_id\": 1}', '2026-07-24 21:33:56'),
(3091, 'game_150', 'round_recorded', '{\"card\": null, \"score\": 10, \"winner\": {\"id\": 10, \"name\": \"زینب\", \"participant_id\": 630}, \"game_id\": 150, \"round_id\": 670, \"win_type\": null, \"team_name\": \"تیم 1\", \"recorded_at\": \"2026-07-25 01:04:11\", \"round_number\": 5, \"source_user_id\": 1}', '2026-07-24 21:34:11'),
(3092, 'game_150', 'game_finished', '{\"status\": \"finished\", \"winner\": {\"id\": 5, \"name\": \"تیم تیم 1\", \"participant_id\": 629}, \"game_id\": 150, \"finished_at\": \"2026-07-25 01:04:21\", \"total_rounds\": 5, \"source_user_id\": 1}', '2026-07-24 21:34:21'),
(3093, 'game_148', 'game_status_changed', '{\"status\": \"active\", \"game_id\": 148, \"source_user_id\": 1}', '2026-07-24 21:34:36'),
(3094, 'game_148', 'round_recorded', '{\"card\": null, \"score\": 5, \"winner\": {\"id\": 7, \"name\": \"امپراطور\", \"participant_id\": 620}, \"game_id\": 148, \"round_id\": 671, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-25 01:04:48\", \"round_number\": 31, \"source_user_id\": 1}', '2026-07-24 21:34:48'),
(3095, 'game_148', 'game_finished', '{\"status\": \"finished\", \"winner\": {\"id\": 7, \"name\": \"امپراطور\", \"participant_id\": 620}, \"game_id\": 148, \"finished_at\": \"2026-07-25 01:04:59\", \"total_rounds\": 31, \"source_user_id\": 1}', '2026-07-24 21:34:59'),
(3096, 'game_149', 'game_started', '{\"status\": \"active\", \"game_id\": 149, \"started_at\": \"2026-07-25 14:03:33\", \"first_player\": {\"id\": 625, \"name\": \"سنتری\"}, \"source_user_id\": 1}', '2026-07-25 10:33:33'),
(3097, 'game_149', 'round_recorded', '{\"card\": null, \"score\": 5, \"winner\": {\"id\": 3, \"name\": \"سنتری\", \"participant_id\": 625}, \"game_id\": 149, \"round_id\": 672, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-25 14:03:39\", \"round_number\": 1, \"source_user_id\": 1}', '2026-07-25 10:33:39'),
(3098, 'game_149', 'round_recorded', '{\"card\": null, \"score\": 5, \"winner\": {\"id\": 7, \"name\": \"امپراطور\", \"participant_id\": 624}, \"game_id\": 149, \"round_id\": 673, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-25 14:03:47\", \"round_number\": 2, \"source_user_id\": 1}', '2026-07-25 10:33:47'),
(3099, 'game_149', 'game_status_changed', '{\"status\": \"paused\", \"game_id\": 149, \"source_user_id\": 1}', '2026-07-25 10:33:55'),
(3100, 'game_149', 'game_status_changed', '{\"status\": \"active\", \"game_id\": 149, \"source_user_id\": 1}', '2026-07-25 10:34:46'),
(3101, 'game_149', 'game_status_changed', '{\"status\": \"paused\", \"game_id\": 149, \"source_user_id\": 1}', '2026-07-25 10:34:57'),
(3102, 'game_149', 'game_status_changed', '{\"status\": \"active\", \"game_id\": 149, \"source_user_id\": 1}', '2026-07-25 10:36:12'),
(3103, 'game_149', 'round_recorded', '{\"card\": null, \"score\": 5, \"winner\": {\"id\": 3, \"name\": \"سنتری\", \"participant_id\": 625}, \"game_id\": 149, \"round_id\": 674, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-25 14:32:09\", \"round_number\": 3, \"source_user_id\": 1}', '2026-07-25 11:02:09'),
(3104, 'game_149', 'round_recorded', '{\"card\": null, \"score\": 5, \"winner\": {\"id\": 3, \"name\": \"سنتری\", \"participant_id\": 625}, \"game_id\": 149, \"round_id\": 675, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-25 14:32:23\", \"round_number\": 4, \"source_user_id\": 1}', '2026-07-25 11:02:23'),
(3105, 'game_149', 'round_recorded', '{\"card\": null, \"score\": 5, \"winner\": {\"id\": 3, \"name\": \"سنتری\", \"participant_id\": 625}, \"game_id\": 149, \"round_id\": 676, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-25 14:33:15\", \"round_number\": 5, \"source_user_id\": 1}', '2026-07-25 11:03:15'),
(3106, 'game_149', 'game_status_changed', '{\"status\": \"paused\", \"game_id\": 149, \"source_user_id\": 1}', '2026-07-25 11:04:41'),
(3107, 'game_149', 'game_status_changed', '{\"status\": \"active\", \"game_id\": 149, \"source_user_id\": 1}', '2026-07-25 11:04:47'),
(3108, 'game_149', 'round_recorded', '{\"card\": null, \"score\": 5, \"winner\": {\"id\": 3, \"name\": \"سنتری\", \"participant_id\": 625}, \"game_id\": 149, \"round_id\": 677, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-25 20:30:51\", \"round_number\": 6, \"source_user_id\": 1}', '2026-07-25 17:00:51'),
(3109, 'game_149', 'round_recorded', '{\"card\": null, \"score\": 5, \"winner\": {\"id\": 7, \"name\": \"امپراطور\", \"participant_id\": 624}, \"game_id\": 149, \"round_id\": 678, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-25 20:30:59\", \"round_number\": 7, \"source_user_id\": 1}', '2026-07-25 17:00:59'),
(3110, 'game_149', 'round_recorded', '{\"card\": null, \"score\": 5, \"winner\": {\"id\": 1, \"name\": \"Admin\", \"participant_id\": 623}, \"game_id\": 149, \"round_id\": 679, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-25 20:31:07\", \"round_number\": 8, \"source_user_id\": 1}', '2026-07-25 17:01:07'),
(3111, 'game_149', 'round_recorded', '{\"card\": null, \"score\": 5, \"winner\": {\"id\": 7, \"name\": \"امپراطور\", \"participant_id\": 624}, \"game_id\": 149, \"round_id\": 680, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-25 20:31:46\", \"round_number\": 9, \"source_user_id\": 1}', '2026-07-25 17:01:46'),
(3112, 'game_149', 'round_recorded', '{\"card\": null, \"score\": 5, \"winner\": {\"id\": 1, \"name\": \"Admin\", \"participant_id\": 623}, \"game_id\": 149, \"round_id\": 681, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-25 21:14:01\", \"round_number\": 10, \"source_user_id\": 1}', '2026-07-25 17:44:01'),
(3113, 'game_149', 'round_recorded', '{\"card\": null, \"score\": 5, \"winner\": {\"id\": 1, \"name\": \"Admin\", \"participant_id\": 623}, \"game_id\": 149, \"round_id\": 682, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-25 21:14:09\", \"round_number\": 11, \"source_user_id\": 1}', '2026-07-25 17:44:09'),
(3114, 'game_149', 'round_recorded', '{\"card\": null, \"score\": 5, \"winner\": {\"id\": 7, \"name\": \"امپراطور\", \"participant_id\": 624}, \"game_id\": 149, \"round_id\": 683, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-25 21:14:14\", \"round_number\": 12, \"source_user_id\": 1}', '2026-07-25 17:44:14'),
(3115, 'game_149', 'round_recorded', '{\"card\": null, \"score\": 5, \"winner\": {\"id\": 3, \"name\": \"سنتری\", \"participant_id\": 625}, \"game_id\": 149, \"round_id\": 684, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-25 21:14:17\", \"round_number\": 13, \"source_user_id\": 1}', '2026-07-25 17:44:17'),
(3116, 'game_149', 'round_undone', '{\"game_id\": 149, \"undone_at\": \"2026-07-25 21:14:24\", \"undone_round\": 13, \"source_user_id\": 1}', '2026-07-25 17:44:24'),
(3117, 'game_149', 'game_status_changed', '{\"status\": \"paused\", \"game_id\": 149, \"source_user_id\": 1}', '2026-07-25 17:44:27'),
(3118, 'game_149', 'game_status_changed', '{\"status\": \"active\", \"game_id\": 149, \"source_user_id\": 1}', '2026-07-25 17:44:32'),
(3119, 'game_149', 'round_recorded', '{\"card\": null, \"score\": 5, \"winner\": {\"id\": 7, \"name\": \"امپراطور\", \"participant_id\": 624}, \"game_id\": 149, \"round_id\": 685, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-25 21:25:20\", \"round_number\": 13, \"source_user_id\": 1}', '2026-07-25 17:55:20'),
(3120, 'game_149', 'round_recorded', '{\"card\": null, \"score\": 5, \"winner\": {\"id\": 7, \"name\": \"امپراطور\", \"participant_id\": 624}, \"game_id\": 149, \"round_id\": 686, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-25 21:25:52\", \"round_number\": 14, \"source_user_id\": 1}', '2026-07-25 17:55:52'),
(3121, 'game_149', 'round_recorded', '{\"card\": null, \"score\": 5, \"winner\": {\"id\": 1, \"name\": \"Admin\", \"participant_id\": 623}, \"game_id\": 149, \"round_id\": 687, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-25 21:34:51\", \"round_number\": 15, \"source_user_id\": 1}', '2026-07-25 18:04:51'),
(3122, 'game_149', 'round_recorded', '{\"card\": null, \"score\": 5, \"winner\": {\"id\": 7, \"name\": \"امپراطور\", \"participant_id\": 624}, \"game_id\": 149, \"round_id\": 688, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-25 21:35:43\", \"round_number\": 16, \"source_user_id\": 1}', '2026-07-25 18:05:43'),
(3123, 'game_149', 'round_recorded', '{\"card\": null, \"score\": 5, \"winner\": {\"id\": 3, \"name\": \"سنتری\", \"participant_id\": 625}, \"game_id\": 149, \"round_id\": 689, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-25 21:35:50\", \"round_number\": 17, \"source_user_id\": 1}', '2026-07-25 18:05:50'),
(3124, 'game_149', 'round_recorded', '{\"card\": null, \"score\": 5, \"winner\": {\"id\": 7, \"name\": \"امپراطور\", \"participant_id\": 624}, \"game_id\": 149, \"round_id\": 690, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-25 21:36:06\", \"round_number\": 18, \"source_user_id\": 1}', '2026-07-25 18:06:06'),
(3125, 'game_149', 'round_recorded', '{\"card\": null, \"score\": 5, \"winner\": {\"id\": 1, \"name\": \"Admin\", \"participant_id\": 623}, \"game_id\": 149, \"round_id\": 691, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-25 21:36:13\", \"round_number\": 19, \"source_user_id\": 1}', '2026-07-25 18:06:13'),
(3126, 'game_149', 'round_recorded', '{\"card\": null, \"score\": 5, \"winner\": {\"id\": 2, \"name\": \"شمپاق\", \"participant_id\": 626}, \"game_id\": 149, \"round_id\": 692, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-25 21:36:34\", \"round_number\": 20, \"source_user_id\": 1}', '2026-07-25 18:06:34'),
(3127, 'game_149', 'round_recorded', '{\"card\": null, \"score\": 5, \"winner\": {\"id\": 7, \"name\": \"امپراطور\", \"participant_id\": 624}, \"game_id\": 149, \"round_id\": 693, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-25 21:43:21\", \"round_number\": 21, \"source_user_id\": 1}', '2026-07-25 18:13:21'),
(3128, 'game_149', 'round_recorded', '{\"card\": null, \"score\": 5, \"winner\": {\"id\": 3, \"name\": \"سنتری\", \"participant_id\": 625}, \"game_id\": 149, \"round_id\": 694, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-25 21:48:02\", \"round_number\": 22, \"source_user_id\": 1}', '2026-07-25 18:18:02'),
(3129, 'game_149', 'round_recorded', '{\"card\": null, \"score\": 5, \"winner\": {\"id\": 2, \"name\": \"شمپاق\", \"participant_id\": 626}, \"game_id\": 149, \"round_id\": 695, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-25 21:49:49\", \"round_number\": 23, \"source_user_id\": 1}', '2026-07-25 18:19:49'),
(3130, 'game_149', 'round_recorded', '{\"card\": null, \"score\": 5, \"winner\": {\"id\": 7, \"name\": \"امپراطور\", \"participant_id\": 624}, \"game_id\": 149, \"round_id\": 696, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-25 21:49:58\", \"round_number\": 24, \"source_user_id\": 1}', '2026-07-25 18:19:58'),
(3131, 'game_149', 'round_undone', '{\"game_id\": 149, \"undone_at\": \"2026-07-25 22:06:38\", \"undone_round\": 24, \"source_user_id\": 1}', '2026-07-25 18:36:38'),
(3132, 'game_149', 'round_recorded', '{\"card\": null, \"score\": 5, \"winner\": {\"id\": 2, \"name\": \"شمپاق\", \"participant_id\": 626}, \"game_id\": 149, \"round_id\": 697, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-25 22:07:47\", \"round_number\": 24, \"source_user_id\": 1}', '2026-07-25 18:37:47'),
(3133, 'game_149', 'round_recorded', '{\"card\": null, \"score\": 5, \"winner\": {\"id\": 2, \"name\": \"شمپاق\", \"participant_id\": 626}, \"game_id\": 149, \"round_id\": 698, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-25 22:18:12\", \"round_number\": 25, \"source_user_id\": 1}', '2026-07-25 18:48:12'),
(3134, 'game_149', 'round_recorded', '{\"card\": null, \"score\": 5, \"winner\": {\"id\": 2, \"name\": \"شمپاق\", \"participant_id\": 626}, \"game_id\": 149, \"round_id\": 699, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-25 22:19:16\", \"round_number\": 26, \"source_user_id\": 1}', '2026-07-25 18:49:16'),
(3135, 'game_149', 'round_recorded', '{\"card\": null, \"score\": 5, \"winner\": {\"id\": 2, \"name\": \"شمپاق\", \"participant_id\": 626}, \"game_id\": 149, \"round_id\": 700, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-25 22:20:13\", \"round_number\": 27, \"source_user_id\": 1}', '2026-07-25 18:50:13'),
(3136, 'game_149', 'round_recorded', '{\"card\": null, \"score\": 5, \"winner\": {\"id\": 2, \"name\": \"شمپاق\", \"participant_id\": 626}, \"game_id\": 149, \"round_id\": 701, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-25 22:24:31\", \"round_number\": 28, \"source_user_id\": 1}', '2026-07-25 18:54:31'),
(3137, 'game_149', 'round_recorded', '{\"card\": null, \"score\": 5, \"winner\": {\"id\": 2, \"name\": \"شمپاق\", \"participant_id\": 626}, \"game_id\": 149, \"round_id\": 702, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-25 22:45:28\", \"round_number\": 29, \"source_user_id\": 1}', '2026-07-25 19:15:28'),
(3138, 'game_149', 'round_recorded', '{\"card\": null, \"score\": 5, \"winner\": {\"id\": 1, \"name\": \"Admin\", \"participant_id\": 623}, \"game_id\": 149, \"round_id\": 703, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-25 22:45:46\", \"round_number\": 30, \"source_user_id\": 1}', '2026-07-25 19:15:46'),
(3139, 'game_149', 'round_recorded', '{\"card\": null, \"score\": 5, \"winner\": {\"id\": 3, \"name\": \"سنتری\", \"participant_id\": 625}, \"game_id\": 149, \"round_id\": 704, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-25 22:45:50\", \"round_number\": 31, \"source_user_id\": 1}', '2026-07-25 19:15:50'),
(3140, 'game_149', 'round_recorded', '{\"card\": null, \"score\": 5, \"winner\": {\"id\": 1, \"name\": \"Admin\", \"participant_id\": 623}, \"game_id\": 149, \"round_id\": 705, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-25 22:52:56\", \"round_number\": 32, \"source_user_id\": 1}', '2026-07-25 19:22:56'),
(3141, 'game_149', 'round_undone', '{\"game_id\": 149, \"undone_at\": \"2026-07-25 22:58:09\", \"undone_round\": 32, \"source_user_id\": 1}', '2026-07-25 19:28:09'),
(3142, 'game_149', 'round_recorded', '{\"card\": null, \"score\": 5, \"winner\": {\"id\": 2, \"name\": \"شمپاق\", \"participant_id\": 626}, \"game_id\": 149, \"round_id\": 706, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-25 22:59:10\", \"round_number\": 32, \"source_user_id\": 1}', '2026-07-25 19:29:10'),
(3143, 'game_149', 'game_target_changed', '{\"game_id\": 149, \"max_wins\": 9, \"changed_at\": \"2026-07-25 22:59:46\", \"min_target\": 9, \"new_target\": 20, \"old_target\": 10, \"source_user_id\": 1}', '2026-07-25 19:29:46'),
(3144, 'game_149', 'game_status_changed', '{\"status\": \"paused\", \"game_id\": 149, \"source_user_id\": 1}', '2026-07-25 19:29:55'),
(3145, 'game_149', 'game_status_changed', '{\"status\": \"active\", \"game_id\": 149, \"source_user_id\": 1}', '2026-07-25 19:30:11'),
(3146, 'game_149', 'round_recorded', '{\"card\": null, \"score\": 5, \"winner\": {\"id\": 1, \"name\": \"Admin\", \"participant_id\": 623}, \"game_id\": 149, \"round_id\": 707, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-25 23:03:19\", \"round_number\": 33, \"source_user_id\": 1}', '2026-07-25 19:33:19'),
(3147, 'game_149', 'round_recorded', '{\"card\": null, \"score\": 5, \"winner\": {\"id\": 2, \"name\": \"شمپاق\", \"participant_id\": 626}, \"game_id\": 149, \"round_id\": 708, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-25 23:03:26\", \"round_number\": 34, \"source_user_id\": 1}', '2026-07-25 19:33:26'),
(3148, 'game_149', 'game_status_changed', '{\"status\": \"paused\", \"game_id\": 149, \"source_user_id\": 1}', '2026-07-25 19:42:04'),
(3149, 'game_149', 'game_status_changed', '{\"status\": \"active\", \"game_id\": 149, \"source_user_id\": 1}', '2026-07-25 19:42:20'),
(3150, 'game_149', 'round_recorded', '{\"card\": null, \"score\": 5, \"winner\": {\"id\": 7, \"name\": \"امپراطور\", \"participant_id\": 624}, \"game_id\": 149, \"round_id\": 709, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-25 23:12:26\", \"round_number\": 35, \"source_user_id\": 1}', '2026-07-25 19:42:26'),
(3151, 'game_149', 'round_recorded', '{\"card\": null, \"score\": 5, \"winner\": {\"id\": 1, \"name\": \"Admin\", \"participant_id\": 623}, \"game_id\": 149, \"round_id\": 710, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-25 23:12:43\", \"round_number\": 36, \"source_user_id\": 1}', '2026-07-25 19:42:43'),
(3152, 'game_149', 'round_recorded', '{\"card\": null, \"score\": 5, \"winner\": {\"id\": 1, \"name\": \"Admin\", \"participant_id\": 623}, \"game_id\": 149, \"round_id\": 711, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-25 23:16:41\", \"round_number\": 37, \"source_user_id\": 1}', '2026-07-25 19:46:41'),
(3153, 'game_149', 'round_recorded', '{\"card\": null, \"score\": 5, \"winner\": {\"id\": 7, \"name\": \"امپراطور\", \"participant_id\": 624}, \"game_id\": 149, \"round_id\": 712, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-25 23:18:09\", \"round_number\": 38, \"source_user_id\": 1}', '2026-07-25 19:48:09'),
(3154, 'game_149', 'round_recorded', '{\"card\": null, \"score\": 5, \"winner\": {\"id\": 1, \"name\": \"Admin\", \"participant_id\": 623}, \"game_id\": 149, \"round_id\": 713, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-25 23:18:17\", \"round_number\": 39, \"source_user_id\": 1}', '2026-07-25 19:48:17'),
(3155, 'game_149', 'round_recorded', '{\"card\": null, \"score\": 5, \"winner\": {\"id\": 3, \"name\": \"سنتری\", \"participant_id\": 625}, \"game_id\": 149, \"round_id\": 714, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-25 23:18:22\", \"round_number\": 40, \"source_user_id\": 1}', '2026-07-25 19:48:22'),
(3156, 'game_149', 'round_recorded', '{\"card\": null, \"score\": 5, \"winner\": {\"id\": 7, \"name\": \"امپراطور\", \"participant_id\": 624}, \"game_id\": 149, \"round_id\": 715, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-25 23:18:27\", \"round_number\": 41, \"source_user_id\": 1}', '2026-07-25 19:48:27'),
(3157, 'game_149', 'round_undone', '{\"game_id\": 149, \"undone_at\": \"2026-07-25 23:18:33\", \"undone_round\": 41, \"source_user_id\": 1}', '2026-07-25 19:48:33'),
(3158, 'game_149', 'game_status_changed', '{\"status\": \"paused\", \"game_id\": 149, \"source_user_id\": 1}', '2026-07-25 19:48:37'),
(3159, 'game_149', 'game_status_changed', '{\"status\": \"active\", \"game_id\": 149, \"source_user_id\": 1}', '2026-07-25 19:48:47');
INSERT INTO `sse_events` (`id`, `channel`, `event_type`, `data`, `created_at`) VALUES
(3160, 'game_149', 'round_recorded', '{\"card\": null, \"score\": 5, \"winner\": {\"id\": 1, \"name\": \"Admin\", \"participant_id\": 623}, \"game_id\": 149, \"round_id\": 716, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-25 23:22:44\", \"round_number\": 41, \"source_user_id\": 1}', '2026-07-25 19:52:44');

-- --------------------------------------------------------

--
-- Table structure for table `suspicious_games`
--

DROP TABLE IF EXISTS `suspicious_games`;
CREATE TABLE IF NOT EXISTS `suspicious_games` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `game_id` int UNSIGNED NOT NULL,
  `user_id` int UNSIGNED DEFAULT NULL,
  `cheat_types` json NOT NULL COMMENT 'انواع تقلب شناسایی شده',
  `risk_level` enum('low','medium','high','critical') COLLATE utf8mb4_unicode_ci NOT NULL,
  `details` json DEFAULT NULL COMMENT 'جزئیات بیشتر',
  `is_reviewed` tinyint(1) DEFAULT '0',
  `reviewed_by` int UNSIGNED DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_game_id` (`game_id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_risk_level` (`risk_level`),
  KEY `idx_is_reviewed` (`is_reviewed`),
  KEY `idx_created` (`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=55 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `suspicious_games`
--

INSERT INTO `suspicious_games` (`id`, `game_id`, `user_id`, `cheat_types`, `risk_level`, `details`, `is_reviewed`, `reviewed_by`, `reviewed_at`, `created_at`) VALUES
(54, 150, 1, '[\"low_target_wins\"]', 'low', '{\"low_target\": {\"threshold\": 5, \"target_wins\": 3, \"is_suspicious\": true}}', 0, NULL, NULL, '2026-07-24 21:34:21');

-- --------------------------------------------------------

--
-- Table structure for table `system_settings`
--

DROP TABLE IF EXISTS `system_settings`;
CREATE TABLE IF NOT EXISTS `system_settings` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `setting_value` text COLLATE utf8mb4_unicode_ci,
  `setting_type` enum('string','integer','boolean','json') COLLATE utf8mb4_unicode_ci DEFAULT 'string',
  `description` text COLLATE utf8mb4_unicode_ci,
  `category` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'general',
  `updated_by` int UNSIGNED DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `setting_key` (`setting_key`),
  KEY `updated_by` (`updated_by`),
  KEY `idx_key` (`setting_key`),
  KEY `idx_category` (`category`)
) ENGINE=InnoDB AUTO_INCREMENT=136 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `system_settings`
--

INSERT INTO `system_settings` (`id`, `setting_key`, `setting_value`, `setting_type`, `description`, `category`, `updated_by`, `updated_at`) VALUES
(1, 'site_name', 'UNO Tracker', 'string', 'نام سایت', 'general', 1, '2026-07-07 17:36:29'),
(2, 'site_description', 'سیستم ثبت و آنالیز بازی‌های UNO', 'string', 'توضیحات سایت', 'general', NULL, '2026-07-25 17:02:37'),
(3, 'maintenance_mode', '0', 'integer', 'حالت تعمیر و نگهداری', 'general', NULL, '2026-07-14 16:21:44'),
(4, 'registration_enabled', '1', 'integer', 'فعال بودن ثبت‌نام', 'general', NULL, '2026-07-17 05:04:19'),
(5, 'max_guest_players', '10', 'integer', 'حداکثر تعداد بازیکن مهمان در هر بازی', 'game', NULL, '2026-07-06 11:20:16'),
(6, 'default_target_wins', '10', 'integer', 'هدف برد پیش‌فرض', 'game', NULL, '2026-07-17 05:05:08'),
(7, 'min_players_solo', '2', 'integer', 'حداقل بازیکن برای بازی انفرادی', 'game', NULL, '2026-07-06 11:20:16'),
(8, 'min_players_team', '4', 'integer', 'حداقل بازیکن برای بازی تیمی', 'game', NULL, '2026-07-06 11:20:16'),
(9, 'players_per_team', '2', 'integer', 'تعداد بازیکن در هر تیم', 'game', NULL, '2026-07-06 11:20:16'),
(10, 'xp_game_played', '5', 'integer', 'XP برای شرکت در بازی', 'gamification', NULL, '2026-07-06 11:20:16'),
(11, 'xp_win_solo', '15', 'integer', 'XP برای برد انفرادی', 'gamification', NULL, '2026-07-06 11:20:16'),
(12, 'xp_win_team', '20', 'integer', 'XP برای برد تیمی', 'gamification', NULL, '2026-07-06 11:20:16'),
(13, 'streak_reset_hours', '24', 'integer', 'ساعت برای ریست خودکار زنجیره پیروزی', 'gamification', NULL, '2026-07-06 11:20:16'),
(14, 'max_upload_size', '5242880', 'integer', 'حداکثر حجم آپلود (بایت)', 'upload', NULL, '2026-07-06 11:20:16'),
(15, 'allowed_image_types', 'jpg,jpeg,png,gif,webp', 'string', 'فرمت‌های مجاز تصویر', 'upload', NULL, '2026-07-08 12:28:17'),
(54, 'site_logo', '/assets/images/logo.svg', 'string', 'مسیر لوگوی سایت', 'general', NULL, '2026-07-08 12:28:17'),
(55, 'session_lifetime', '43200', 'integer', 'زمان انقضای Session (دقیقه)', 'session', NULL, '2026-07-08 18:00:00'),
(56, 'session_idle_timeout', '43200', 'integer', 'زمان بیکاری قبل از خروج (دقیقه)', 'session', NULL, '2026-07-18 17:33:58'),
(57, 'session_warn_before_expire', '300', 'integer', 'هشدار قبل از انقضا (ثانیه)', 'session', NULL, '2026-07-08 12:28:17'),
(58, 'session_regenerate_interval', '300', 'integer', 'بازسازی Session ID (ثانیه)', 'session', NULL, '2026-07-08 12:28:17'),
(59, 'max_login_attempts', '5', 'integer', 'حداکثر تلاش ناموفق برای ورود', 'security', NULL, '2026-07-08 12:28:17'),
(60, 'lockout_duration', '15', 'integer', 'مدت زمان قفل شدن حساب (دقیقه)', 'security', NULL, '2026-07-08 12:28:17'),
(61, 'password_min_length', '6', 'integer', 'حداقل طول رمز عبور', 'security', NULL, '2026-07-08 12:28:17'),
(62, 'require_special_char', '0', 'integer', 'نیاز به کاراکتر خاص در رمز', 'security', NULL, '2026-07-21 08:11:34'),
(63, 'require_number', '0', 'integer', 'نیاز به عدد در رمز', 'security', NULL, '2026-07-21 08:11:34'),
(64, 'max_players_per_game', '20', 'integer', 'حداکثر بازیکن در یک بازی', 'game', NULL, '2026-07-08 12:28:17'),
(65, 'xp_achievement_unlock', '50', 'integer', 'XP برای کسب نشان', 'gamification', NULL, '2026-07-08 12:28:17'),
(66, 'xp_challenge_complete', '30', 'integer', 'XP برای تکمیل ماموریت', 'gamification', NULL, '2026-07-08 12:28:17'),
(67, 'avatar_max_width', '500', 'integer', 'حداکثر عرض آواتار (پیکسل)', 'upload', NULL, '2026-07-08 12:28:17'),
(68, 'avatar_max_height', '500', 'integer', 'حداکثر ارتفاع آواتار (پیکسل)', 'upload', NULL, '2026-07-08 12:28:17'),
(69, 'default_theme', 'light', 'string', 'تم پیش‌فرض (light/dark)', 'display', NULL, '2026-07-08 12:28:17'),
(70, 'default_language', 'fa', 'string', 'زبان پیش‌فرض', 'display', NULL, '2026-07-08 12:28:17'),
(71, 'items_per_page', '20', 'integer', 'تعداد آیتم در هر صفحه', 'display', NULL, '2026-07-08 12:28:17'),
(72, 'enable_animations', '1', 'integer', 'فعال بودن انیمیشن‌ها', 'display', NULL, '2026-07-25 17:03:19'),
(73, 'enable_notifications', '1', 'integer', 'فعال بودن اعلان‌های سیستم', 'notification', 1, '2026-07-25 19:52:12'),
(74, 'notification_sound_enabled', '1', 'integer', 'فعال بودن صدای اعلان', 'notification', 1, '2026-07-25 19:52:12'),
(75, 'default_notification_duration', '3000', 'integer', 'مدت نمایش اعلان پیش‌فرض (میلی‌ثانیه)', 'notification', 1, '2026-07-25 19:52:12'),
(76, 'notification_position', 'top-end', 'string', 'موقعیت اعلان‌ها', 'notification', 1, '2026-07-25 19:52:12'),
(77, 'scoring_base_score', '5', 'integer', 'امتیاز پایه هر برد', 'scoring', NULL, '2026-07-25 17:03:43'),
(78, 'scoring_xp_multiplier', '2.0', '', 'ضریب تبدیل امتیاز به XP', 'scoring', NULL, '2026-07-10 13:31:13'),
(79, 'scoring_win_bonus', '15', 'integer', 'XP اضافی برای برد', 'scoring', NULL, '2026-07-10 13:31:13'),
(80, 'scoring_game_bonus', '5', 'integer', 'XP اضافی برای شرکت در بازی', 'scoring', NULL, '2026-07-10 13:31:13'),
(81, 'scoring_team_multiplier', '2', 'integer', 'ضریب امتیاز بازی تیمی', 'scoring', NULL, '2026-07-16 15:05:32'),
(82, 'scoring_winner_bonus', '50', 'integer', 'XP اضافی برنده بازی', 'scoring', NULL, '2026-07-10 13:31:13'),
(83, 'scoring_min_target_wins', '3', 'integer', 'حداقل هدف برد مجاز', 'scoring', NULL, '2026-07-10 13:31:13'),
(84, 'anticheat_enabled', '1', 'integer', 'فعال بودن سیستم ضدتقلب', 'anticheat', NULL, '2026-07-14 16:21:50'),
(85, 'anticheat_min_round_duration', '60', 'integer', 'حداقل زمان هر دور (ثانیه)', 'anticheat', NULL, '2026-07-15 12:02:09'),
(86, 'anticheat_min_players', '3', 'integer', 'حداقل تعداد بازیکنان', 'anticheat', NULL, '2026-07-13 17:06:53'),
(87, 'anticheat_max_guests', '2', 'integer', 'حداکثر تعداد بازیکنان مهمان', 'anticheat', NULL, '2026-07-13 17:06:53'),
(88, 'anticheat_max_guest_ratio', '1.0', '', 'حداکثر نسبت مهمان به عضو', 'anticheat', NULL, '2026-07-13 17:06:53'),
(89, 'anticheat_min_members', '2', 'integer', 'حداقل تعداد بازیکنان عضو', 'anticheat', NULL, '2026-07-13 17:06:53'),
(90, 'anticheat_max_win_percentage', '100', 'integer', 'حداکثر درصد برد یک بازیکن', 'anticheat', NULL, '2026-07-14 16:42:08'),
(91, 'anticheat_max_games_per_hour', '2', 'integer', 'حداکثر تعداد بازی در ساعت', 'anticheat', NULL, '2026-07-13 17:06:53'),
(92, 'anticheat_min_target_wins_threshold', '5', 'integer', 'آستانه هدف برد کم', 'anticheat', NULL, '2026-07-13 17:06:53'),
(93, 'anticheat_max_low_target_games', '3', 'integer', 'حداکثر بازی‌های با هدف کم در روز', 'anticheat', NULL, '2026-07-13 17:06:53'),
(94, 'anticheat_new_account_hours', '24', 'integer', 'ساعت‌های محدودیت حساب تازه', 'anticheat', NULL, '2026-07-13 17:06:53'),
(95, 'anticheat_max_accounts_per_ip', '2', 'integer', 'حداکثر اکانت برای هر IP', 'anticheat', NULL, '2026-07-13 17:06:53'),
(96, 'anticheat_max_games_created_per_day', '3', 'integer', 'حداکثر بازی‌های ایجاد شده در روز', 'anticheat', NULL, '2026-07-13 17:06:53'),
(97, 'anticheat_max_solo_games_per_day', '2', 'integer', 'حداکثر بازی‌های انفرادی در روز', 'anticheat', NULL, '2026-07-13 17:06:53'),
(98, 'anticheat_max_friendly_games_per_day', '2', 'integer', 'حداکثر بازی‌های دوستانه در روز', 'anticheat', NULL, '2026-07-13 17:06:53'),
(99, 'anticheat_collusion_min_games', '3', 'integer', 'حداقل تعداد بازی در ۱ ساعت برای تشخیص تبانی', 'anticheat', NULL, '2026-07-14 15:51:15'),
(100, 'anticheat_collusion_max_opponents', '1', 'integer', 'حداکثر تعداد حریف یکتا (کمتر = مشکوک‌تر)', 'anticheat', NULL, '2026-07-14 15:51:15'),
(101, 'first_player_selection', 'random', 'string', 'نحوه انتخاب بازیکن شروع‌کننده بازی (random=تصادفی، by_score=بر اساس امتیاز، by_xp=بر اساس XP)', 'game', NULL, '2026-07-17 06:40:42'),
(119, 'auth_method', 'password', 'string', 'روش احراز هویت (password=رمز عبور، sms=پیامک)', 'security', NULL, '2026-07-23 06:51:02'),
(120, 'sms_enabled', '1', 'integer', 'فعال‌سازی سیستم پیامک', 'security', NULL, '2026-07-21 14:56:36'),
(121, 'sms_otp_length', '6', 'integer', 'طول کد تایید پیامکی', 'security', NULL, '2026-07-21 11:38:47'),
(122, 'sms_otp_expiry', '5', 'integer', 'زمان انقضای کد (دقیقه)', 'security', NULL, '2026-07-21 11:38:47'),
(123, 'sms_daily_limit', '10', 'integer', 'حداکثر پیامک روزانه برای هر شماره', 'security', NULL, '2026-07-21 11:38:47'),
(124, 'sms_otp_attempt_limit', '5', 'integer', 'حداکثر تلاش برای وارد کردن کد', 'security', NULL, '2026-07-21 11:38:47'),
(125, 'sse_sound_settings', '{\"game_started\":{\"enabled\":true,\"sound\":\"game-start.mp3\"},\"round_recorded\":{\"enabled\":true,\"sound\":\"round-recorded.mp3\"},\"round_winner\":{\"enabled\":true,\"sound\":\"round-win.mp3\"},\"round_loser\":{\"enabled\":true,\"sound\":\"round-lose.mp3\"},\"round_undone\":{\"enabled\":true,\"sound\":\"round-lose-2.mp3\"},\"game_finished\":{\"enabled\":true,\"sound\":\"default.mp3\"},\"game_winner\":{\"enabled\":true,\"sound\":\"game-win.mp3\"},\"game_loser\":{\"enabled\":true,\"sound\":\"round-lose-3.mp3\"},\"score_updated\":{\"enabled\":true,\"sound\":\"game-pause.mp3\"},\"notification\":{\"enabled\":true,\"sound\":\"default.mp3\"},\"system_message\":{\"enabled\":true,\"sound\":\"default.mp3\"},\"game_status_changed\":{\"paused\":{\"enabled\":true,\"sound\":\"game-pause.mp3\"},\"resumed\":{\"enabled\":true,\"sound\":\"game-resume.mp3\"}}}', 'json', 'تنظیمات صدای رویدادهای SSE (هر رویداد دارای enabled و sound)', 'notification', 1, '2026-07-25 19:52:12');

-- --------------------------------------------------------

--
-- Table structure for table `teammate_history`
--

DROP TABLE IF EXISTS `teammate_history`;
CREATE TABLE IF NOT EXISTS `teammate_history` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id_1` int UNSIGNED NOT NULL,
  `user_id_2` int UNSIGNED NOT NULL,
  `games_together` int UNSIGNED DEFAULT '1',
  `wins_together` int UNSIGNED DEFAULT '0',
  `last_played_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_teammates` (`user_id_1`,`user_id_2`),
  KEY `user_id_2` (`user_id_2`)
) ;

-- --------------------------------------------------------

--
-- Table structure for table `teams`
--

DROP TABLE IF EXISTS `teams`;
CREATE TABLE IF NOT EXISTS `teams` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `game_id` int UNSIGNED NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `color_hex` varchar(7) COLLATE utf8mb4_unicode_ci DEFAULT '#3B82F6',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_teams_game` (`game_id`)
) ENGINE=InnoDB AUTO_INCREMENT=207 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `teams`
--

INSERT INTO `teams` (`id`, `game_id`, `name`, `color_hex`, `created_at`) VALUES
(1, 3, 'بلسیببلیس', '#3B82F6', '2026-07-01 15:05:04'),
(2, 7, 'تیم اول ', '#3B82F6', '2026-07-01 15:31:30'),
(3, 7, 'تیم دوم', '#EF4444', '2026-07-01 15:31:30'),
(4, 7, 'تیم 3', '#10B981', '2026-07-01 15:31:30'),
(5, 7, 'تیم 4', '#F59E0B', '2026-07-01 15:31:30'),
(6, 8, '', '#3B82F6', '2026-07-01 15:33:05'),
(7, 8, '', '#EF4444', '2026-07-01 15:33:05'),
(8, 10, 'تیم یک', '#3B82F6', '2026-07-01 16:13:24'),
(9, 10, 'تیم دوم', '#EF4444', '2026-07-01 16:13:24'),
(10, 10, 'تیم سوم', '#10B981', '2026-07-01 16:13:24'),
(11, 11, 'بلسیببلیس', '#3B82F6', '2026-07-01 16:42:35'),
(12, 11, 'بسبسی', '#EF4444', '2026-07-01 16:42:35'),
(13, 11, 'بسیبسیبس', '#10B981', '2026-07-01 16:42:35'),
(14, 13, 'اول', '#3B82F6', '2026-07-01 17:30:49'),
(15, 13, 'دوم', '#EF4444', '2026-07-01 17:30:49'),
(16, 13, 'سوم', '#10B981', '2026-07-01 17:30:49'),
(17, 13, 'چهارم', '#F59E0B', '2026-07-01 17:30:49'),
(18, 14, 'تیم یک', '#3B82F6', '2026-07-01 17:43:51'),
(19, 14, 'تیم دو', '#EF4444', '2026-07-01 17:43:51'),
(20, 18, 'بلسیببلیس', '#3B82F6', '2026-07-01 18:04:51'),
(21, 18, 'بلسیببلیس', '#EF4444', '2026-07-01 18:04:51'),
(22, 18, 'بلسیببلیس', '#10B981', '2026-07-01 18:04:51'),
(23, 22, 'تیم اول', '#3B82F6', '2026-07-01 20:49:19'),
(24, 22, 'تیم دوم', '#EF4444', '2026-07-01 20:49:19'),
(25, 22, 'تیم سوم', '#10B981', '2026-07-01 20:49:19'),
(26, 22, 'تیم چهارم', '#F59E0B', '2026-07-01 20:49:19'),
(27, 23, 'تیم یک', '#3B82F6', '2026-07-01 21:11:52'),
(28, 23, 'تیم دوم', '#EF4444', '2026-07-01 21:11:52'),
(29, 23, 'تیم سوم', '#10B981', '2026-07-01 21:11:52'),
(30, 23, 'تیم چهارم', '#F59E0B', '2026-07-01 21:11:52'),
(31, 25, 'تیم ا', '#3B82F6', '2026-07-01 21:33:56'),
(32, 25, 'تیم', '#EF4444', '2026-07-01 21:33:56'),
(33, 25, 'ییشسی', '#10B981', '2026-07-01 21:33:56'),
(34, 25, 'یشسیشس', '#F59E0B', '2026-07-01 21:33:56'),
(35, 26, 'jdl d;', '#3B82F6', '2026-07-01 21:39:01'),
(36, 26, 'jdl ;;', '#EF4444', '2026-07-01 21:39:01'),
(37, 28, 'تبم', '#3B82F6', '2026-07-02 13:03:07'),
(38, 28, 'تیم ملی', '#EF4444', '2026-07-02 13:03:07'),
(39, 28, 'سلام', '#10B981', '2026-07-02 13:03:07'),
(40, 29, 'تیم اول', '#3B82F6', '2026-07-02 16:54:37'),
(41, 29, 'تیم دوم', '#EF4444', '2026-07-02 16:54:37'),
(42, 29, 'تیم سوم', '#10B981', '2026-07-02 16:54:37'),
(43, 30, 'تیم اول', '#3B82F6', '2026-07-02 17:13:36'),
(44, 30, 'تیم دوم', '#EF4444', '2026-07-02 17:13:36'),
(45, 30, 'تیم سوم', '#10B981', '2026-07-02 17:13:36'),
(46, 33, 'سیبسی', '#3B82F6', '2026-07-02 19:02:56'),
(47, 33, 'سیبسی', '#EF4444', '2026-07-02 19:02:56'),
(48, 33, 'سیبسی', '#10B981', '2026-07-02 19:02:56'),
(49, 34, 'تیم حسن', '#3B82F6', '2026-07-03 09:56:00'),
(50, 34, 'تیم مهدی', '#EF4444', '2026-07-03 09:56:00'),
(51, 34, 'تیم شمپاق', '#10B981', '2026-07-03 09:56:00'),
(67, 54, 'زظطز', '#3B82F6', '2026-07-05 11:48:42'),
(68, 54, 'زظطزظطز', '#EF4444', '2026-07-05 11:48:42'),
(69, 57, 'dasd', '#3B82F6', '2026-07-05 12:29:47'),
(70, 57, 'asdas', '#EF4444', '2026-07-05 12:29:47'),
(76, 66, 'تیم تنتنتن', '#3B82F6', '2026-07-05 14:19:20'),
(77, 66, 'تیم 2', '#EF4444', '2026-07-05 14:19:20'),
(92, 79, 'تیم 1', '#3B82F6', '2026-07-06 19:16:01'),
(93, 79, 'تیم 2', '#EF4444', '2026-07-06 19:16:01'),
(102, 96, 'تیم 1', '#3B82F6', '2026-07-11 08:28:28'),
(103, 96, 'تیم 2', '#EF4444', '2026-07-11 08:28:28'),
(202, 146, 'تیم 1', '#3B82F6', '2026-07-23 10:30:52'),
(203, 146, 'تیم 2', '#EF4444', '2026-07-23 10:30:52'),
(204, 150, 'تیم 1', '#3B82F6', '2026-07-24 17:26:40'),
(205, 150, 'تیم 2', '#EF4444', '2026-07-24 17:26:40'),
(206, 150, 'تیم 3', '#10B981', '2026-07-24 17:26:40');

-- --------------------------------------------------------

--
-- Table structure for table `titles`
--

DROP TABLE IF EXISTS `titles`;
CREATE TABLE IF NOT EXISTS `titles` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `icon` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 0xF09F8E96EFB88F,
  `condition_type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `condition_value` int UNSIGNED NOT NULL,
  `priority` int DEFAULT '0' COMMENT 'اولویت نمایش (بالا = مهم‌تر)',
  `bonus_points` int UNSIGNED DEFAULT '0' COMMENT 'امتیاز بونوس برای هر برد',
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`),
  KEY `idx_code` (`code`),
  KEY `idx_priority` (`priority`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `titles`
--

INSERT INTO `titles` (`id`, `code`, `name`, `description`, `icon`, `condition_type`, `condition_value`, `priority`, `bonus_points`, `is_active`, `created_at`) VALUES
(1, 'newbie', 'تازه‌کار', 'اولین بازی خود را انجام داده', '🌱', 'total_games', 1, 0, 0, 1, '2026-07-04 14:32:34'),
(2, 'active', 'بازیکن فعال', '۱۰ بازی انجام داده', '🎯', 'total_games', 10, 50, 5, 1, '2026-07-04 14:32:34'),
(3, 'winner', 'برنده', '۱۰ برد کسب کرده', '🏆', 'total_wins', 10, 60, 10, 1, '2026-07-04 14:32:34'),
(4, 'champion', 'قهرمان', '200 برد کسب کرده', '👑', 'total_wins', 200, 90, 25, 1, '2026-07-04 14:32:34'),
(5, 'legend', 'افسانه', '10 برد متوالی داشته', '👽', 'best_streak', 10, 100, 100, 1, '2026-07-04 14:32:34'),
(6, 'unstoppable', 'سرسخت', '۵ برد متوالی داشته', '💀', 'best_streak', 5, 80, 20, 1, '2026-07-04 14:32:34'),
(7, 'team_leader', 'رهبر تیم', '۱۰ برد تیمی داشته', '🤝', 'team_wins', 10, 70, 15, 1, '2026-07-04 14:32:34');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `real_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nickname` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password_hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `avatar_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tagline` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role` enum('user','admin','super_admin') COLLATE utf8mb4_unicode_ci DEFAULT 'user',
  `status` enum('active','banned','pending') COLLATE utf8mb4_unicode_ci DEFAULT 'active',
  `can_create_game` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'مجوز ساخت بازی',
  `can_join_game` tinyint(1) DEFAULT '1' COMMENT 'مجوز شرکت در بازی',
  `is_online` tinyint(1) DEFAULT '0',
  `last_seen_at` timestamp NULL DEFAULT NULL,
  `last_ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `registration_ip` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `playstyle_id` int UNSIGNED DEFAULT NULL,
  `current_title_id` int UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nickname` (`nickname`),
  UNIQUE KEY `phone` (`phone`),
  KEY `idx_users_nickname` (`nickname`),
  KEY `idx_users_status` (`status`),
  KEY `idx_users_online` (`is_online`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `real_name`, `nickname`, `phone`, `password_hash`, `avatar_path`, `tagline`, `role`, `status`, `can_create_game`, `can_join_game`, `is_online`, `last_seen_at`, `last_ip_address`, `registration_ip`, `playstyle_id`, `current_title_id`, `created_at`, `updated_at`) VALUES
(1, 'حسن حمیدی قمر', 'Admin', '09019177577', '$2y$10$HTvY4k4Ozhf5QHX4PbgFfuofDDruw8INYvk2uVYoOfkmAYZxwXeES', 'user_1_1784804195.jpg', 'یک چیزی', 'super_admin', 'active', 1, 1, 1, '2026-08-05 13:18:04', '192.168.2.11', NULL, NULL, 1, '2026-06-30 14:56:11', '2026-08-05 13:18:04'),
(2, 'محمد عليزاده', 'شمپاق', '09000000002', '$2y$10$XE7HOvq5fqG2aT5HKTwiaeVzfTDy3fQTC7cV5OFGMjzktKqIk0mdq', 'user_2_1784804466.jpg', 'من بهترین بازیکن UNO هستم!', 'admin', 'active', 0, 1, 1, '2026-07-23 09:33:55', '192.168.2.13', NULL, NULL, 1, '2026-07-01 12:47:30', '2026-07-23 11:14:32'),
(3, 'علی احمدی', 'سنتری', '09000000003', '$2y$10$wjVdQbLpHdM09mksUe1Gt.zvZEvv1ruKwcC4vyVOi9osYtAgK4F5q', NULL, 'من بهترین بازیکن UNO هستم!', 'user', 'active', 1, 1, 0, '2026-07-01 13:40:58', NULL, NULL, NULL, 1, '2026-07-01 13:08:05', '2026-07-24 21:34:21'),
(4, 'حسین', 'حسین', '09000000004', '$2y$10$sSu5HtEt3pKhctkRSyKtU.MssFejFjCLN7PyJj/4co1O6bFgHNxD6', NULL, '', 'user', 'active', 1, 1, 0, '2026-07-01 13:11:46', NULL, NULL, NULL, 1, '2026-07-01 13:11:41', '2026-07-20 18:40:14'),
(5, 'خسرو', 'خسرو', '09000000005', '$2y$10$Tep0aY6BUkEDNJjLMPjR6OZBbtgxLRMBg1se3Rp/FR1yKeiEPS9BS', NULL, '', 'user', 'active', 1, 1, 0, '2026-07-07 14:52:51', NULL, NULL, NULL, 1, '2026-07-01 13:12:28', '2026-07-19 16:06:17'),
(6, 'مهدی', 'RNGER', '09000000010', '$2y$10$LYc4hQGHQcOedO9/kg8iGe1l0WcXaybN7l5zQ4WmihWWKTI86u9gS', 'user_6_1783423501.png', 'من بهترینم', 'user', 'active', 1, 1, 0, '2026-07-12 11:52:25', NULL, NULL, NULL, 1, '2026-07-01 14:29:39', '2026-07-20 20:43:14'),
(7, 'شاهین', 'امپراطور', '09000000011', '$2y$10$/Si1jsSCi9UMQfuI7J0j3OP8Ood.bi8rqSRPZD975lvqLs8CyqkuO', NULL, 'شعار', 'admin', 'active', 1, 1, 1, '2026-07-24 20:29:51', '127.0.0.1', NULL, NULL, 1, '2026-07-02 17:18:54', '2026-07-24 20:29:51'),
(8, 'عباس', 'باس باس', '09000000009', '$2y$10$SdPC7ZsfXnf4DIzd3oK66.wkoLBucjnwA9AHt0Ux9i/VoN4fhDcdG', NULL, '', 'user', 'active', 1, 1, 1, '2026-07-07 14:39:23', NULL, NULL, 3, NULL, '2026-07-04 11:46:38', '2026-07-20 20:43:05'),
(9, 'جعفر', 'جعفر', '09111111111', '$2y$10$zUwKle2GtHEXPfRs0UGD9OFNoup6LUwp76wmXM7Gq5ryKyMkdgFp.', NULL, '', 'user', 'active', 1, 1, 0, '2026-07-04 11:50:07', NULL, NULL, NULL, NULL, '2026-07-04 11:50:04', '2026-07-20 20:43:14'),
(10, 'زینب', 'زینب', '09222222222', '$2y$10$.7986Mu7hNu8jPhjJmgBDehEx45nkoKyAThe0xLnZ2vDJoguT4ww2', NULL, '', 'user', 'active', 1, 1, 0, '2026-07-04 11:50:33', NULL, NULL, NULL, 1, '2026-07-04 11:50:31', '2026-07-24 21:34:21'),
(12, 'مهدی', 'RANGER', '09373056591', '$2y$10$meNL.0bpXeqL1vLuVK1Jf.aLzvcSRCgqc0ugr/.e0w7rkdM21.xne', 'user_12_1783774038.jpg', 'من بهترینم', 'user', 'active', 1, 1, 1, '2026-07-16 11:25:57', NULL, NULL, NULL, 1, '2026-07-10 11:16:41', '2026-07-16 11:25:57'),
(13, 'ABBAS', 'AVANGR', '09305586873', '$2y$10$z8kOVS4hIeDaOWY1Yfb1Ru.btSdG1dW2ssuRZ8cY9mHAxk.HreIBe', 'user_13_1783774152.jpg', 'ابو کینگ', 'user', 'active', 1, 1, 1, '2026-07-16 11:24:40', NULL, NULL, NULL, 1, '2026-07-10 11:16:45', '2026-07-20 20:14:30'),
(15, 'تست otp', 'otp', '09927049228', '$2y$10$qA4.OWtMpxahvPrbOmFPoeJHyXVA4.RSuHQ56QKPIxdgr4n2ePpRG', NULL, '', 'user', 'active', 1, 1, 1, '2026-07-21 15:58:21', '127.0.0.1', '127.0.0.1', NULL, NULL, '2026-07-21 15:58:21', '2026-07-21 16:55:47');

-- --------------------------------------------------------

--
-- Table structure for table `user_achievements`
--

DROP TABLE IF EXISTS `user_achievements`;
CREATE TABLE IF NOT EXISTS `user_achievements` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int UNSIGNED NOT NULL,
  `achievement_id` int UNSIGNED NOT NULL,
  `progress` int UNSIGNED DEFAULT '0' COMMENT 'پیشرفت فعلی',
  `is_completed` tinyint(1) DEFAULT '0' COMMENT 'آیا تکمیل شده؟',
  `unlocked_at` timestamp NULL DEFAULT NULL COMMENT 'زمان کسب',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_user_achievement` (`user_id`,`achievement_id`),
  KEY `achievement_id` (`achievement_id`),
  KEY `idx_user_completed` (`user_id`,`is_completed`),
  KEY `idx_unlocked` (`unlocked_at`)
) ENGINE=InnoDB AUTO_INCREMENT=461 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_achievements`
--

INSERT INTO `user_achievements` (`id`, `user_id`, `achievement_id`, `progress`, `is_completed`, `unlocked_at`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 4, 1, '2026-07-20 20:43:14', '2026-07-04 14:47:30', '2026-07-20 20:43:14'),
(2, 1, 2, 46, 1, '2026-07-23 10:33:55', '2026-07-04 14:47:30', '2026-07-23 10:33:55'),
(3, 1, 3, 50, 1, '2026-07-24 17:35:54', '2026-07-04 14:47:30', '2026-07-24 17:35:54'),
(4, 1, 4, 50, 0, NULL, '2026-07-04 14:47:30', '2026-07-24 17:35:54'),
(5, 1, 5, 1, 1, '2026-07-20 20:43:14', '2026-07-04 14:47:30', '2026-07-20 20:43:14'),
(6, 1, 6, 1, 0, NULL, '2026-07-04 14:47:30', '2026-07-20 20:43:14'),
(7, 1, 7, 1, 0, NULL, '2026-07-04 14:47:30', '2026-07-20 20:43:14'),
(8, 1, 8, 1, 0, NULL, '2026-07-04 14:47:30', '2026-07-20 20:43:14'),
(9, 1, 9, 2, 0, NULL, '2026-07-04 14:47:30', '2026-07-04 14:59:32'),
(10, 1, 10, 2, 0, NULL, '2026-07-04 14:47:30', '2026-07-04 14:59:32'),
(11, 1, 11, 2, 0, NULL, '2026-07-04 14:47:30', '2026-07-04 14:59:32'),
(12, 1, 12, 23, 1, '2026-07-23 10:33:55', '2026-07-04 14:47:30', '2026-07-23 10:33:55'),
(13, 1, 13, 0, 0, NULL, '2026-07-04 14:47:30', '2026-07-20 20:43:14'),
(14, 1, 14, 217, 1, '2026-07-23 10:33:55', '2026-07-04 14:47:30', '2026-07-23 10:33:55'),
(15, 1, 15, 362, 0, NULL, '2026-07-04 14:47:30', '2026-07-25 19:52:44'),
(16, 1, 16, 362, 0, NULL, '2026-07-04 14:47:30', '2026-07-25 19:52:44'),
(17, 6, 1, 3, 1, '2026-07-20 20:43:14', '2026-07-04 15:08:44', '2026-07-20 20:43:14'),
(18, 6, 2, 3, 0, NULL, '2026-07-04 15:08:44', '2026-07-20 20:43:14'),
(19, 6, 3, 3, 0, NULL, '2026-07-04 15:08:44', '2026-07-20 20:43:14'),
(20, 6, 4, 3, 0, NULL, '2026-07-04 15:08:44', '2026-07-20 20:43:14'),
(21, 6, 12, 0, 0, NULL, '2026-07-04 15:08:44', '2026-07-20 19:19:27'),
(22, 6, 14, 3, 0, NULL, '2026-07-04 15:08:44', '2026-07-20 20:43:14'),
(23, 6, 15, 3, 0, NULL, '2026-07-04 15:08:44', '2026-07-20 20:43:14'),
(24, 6, 16, 3, 0, NULL, '2026-07-04 15:08:44', '2026-07-20 20:43:14'),
(25, 7, 1, 3, 1, '2026-07-20 20:43:14', '2026-07-04 15:08:44', '2026-07-20 20:43:14'),
(26, 7, 2, 15, 1, '2026-07-23 10:54:14', '2026-07-04 15:08:44', '2026-07-23 10:54:14'),
(27, 7, 3, 18, 0, NULL, '2026-07-04 15:08:44', '2026-07-24 17:33:57'),
(28, 7, 4, 18, 0, NULL, '2026-07-04 15:08:44', '2026-07-24 17:33:57'),
(29, 7, 5, 1, 1, '2026-07-20 20:43:14', '2026-07-04 15:08:44', '2026-07-20 20:43:14'),
(30, 7, 6, 2, 0, NULL, '2026-07-04 15:08:44', '2026-07-24 21:34:59'),
(31, 7, 7, 2, 0, NULL, '2026-07-04 15:08:44', '2026-07-24 21:34:59'),
(32, 7, 8, 2, 0, NULL, '2026-07-04 15:08:44', '2026-07-24 21:34:59'),
(33, 7, 12, 7, 0, NULL, '2026-07-04 15:08:44', '2026-07-24 17:33:57'),
(34, 7, 13, 1, 0, NULL, '2026-07-04 15:08:44', '2026-07-20 20:43:14'),
(35, 7, 14, 286, 1, '2026-07-23 10:54:14', '2026-07-04 15:08:44', '2026-07-23 10:54:14'),
(36, 7, 15, 466, 0, NULL, '2026-07-04 15:08:44', '2026-07-25 19:48:27'),
(37, 7, 16, 466, 0, NULL, '2026-07-04 15:08:44', '2026-07-25 19:48:27'),
(38, 2, 1, 1, 1, '2026-07-20 20:43:14', '2026-07-04 15:44:36', '2026-07-20 20:43:14'),
(39, 2, 2, 21, 1, '2026-07-23 10:34:35', '2026-07-04 15:44:36', '2026-07-23 10:34:35'),
(40, 2, 3, 25, 0, NULL, '2026-07-04 15:44:36', '2026-07-24 17:42:13'),
(41, 2, 4, 25, 0, NULL, '2026-07-04 15:44:36', '2026-07-24 17:42:13'),
(42, 2, 12, 14, 1, '2026-07-23 10:34:35', '2026-07-04 15:44:36', '2026-07-23 10:34:35'),
(43, 2, 14, 102, 1, '2026-07-23 10:35:08', '2026-07-04 15:44:36', '2026-07-23 10:35:08'),
(44, 2, 15, 282, 0, NULL, '2026-07-04 15:44:36', '2026-07-25 19:33:26'),
(45, 2, 16, 282, 0, NULL, '2026-07-04 15:44:36', '2026-07-25 19:33:26'),
(46, 4, 1, 1, 1, '2026-07-20 20:43:05', '2026-07-06 18:41:33', '2026-07-20 20:43:05'),
(47, 4, 2, 1, 0, NULL, '2026-07-06 18:41:33', '2026-07-20 20:43:05'),
(48, 4, 3, 1, 0, NULL, '2026-07-06 18:41:33', '2026-07-20 20:43:05'),
(49, 4, 4, 1, 0, NULL, '2026-07-06 18:41:33', '2026-07-20 20:43:05'),
(50, 4, 5, 0, 0, NULL, '2026-07-06 18:41:33', '2026-07-20 20:43:05'),
(51, 4, 6, 0, 0, NULL, '2026-07-06 18:41:33', '2026-07-20 20:43:05'),
(52, 4, 7, 0, 0, NULL, '2026-07-06 18:41:33', '2026-07-20 20:43:05'),
(53, 4, 8, 0, 0, NULL, '2026-07-06 18:41:33', '2026-07-20 20:43:05'),
(54, 4, 14, 0, 0, NULL, '2026-07-06 18:41:33', '2026-07-20 20:43:05'),
(55, 4, 15, 0, 0, NULL, '2026-07-06 18:41:33', '2026-07-20 20:43:05'),
(56, 4, 16, 0, 0, NULL, '2026-07-06 18:41:33', '2026-07-20 20:43:05'),
(57, 8, 1, 0, 0, NULL, '2026-07-07 12:37:04', '2026-07-20 20:43:05'),
(58, 8, 2, 0, 0, NULL, '2026-07-07 12:37:04', '2026-07-20 20:43:05'),
(59, 8, 3, 0, 0, NULL, '2026-07-07 12:37:04', '2026-07-20 20:43:05'),
(60, 8, 4, 0, 0, NULL, '2026-07-07 12:37:04', '2026-07-20 20:43:05'),
(61, 8, 14, 0, 0, NULL, '2026-07-07 12:37:04', '2026-07-20 20:43:04'),
(62, 8, 15, 0, 0, NULL, '2026-07-07 12:37:04', '2026-07-20 20:43:04'),
(63, 8, 16, 0, 0, NULL, '2026-07-07 12:37:04', '2026-07-20 20:43:04'),
(64, 3, 1, 13, 1, '2026-07-23 10:32:12', '2026-07-07 16:27:43', '2026-07-23 10:32:12'),
(65, 3, 2, 13, 1, '2026-07-23 10:32:12', '2026-07-07 16:27:43', '2026-07-23 10:32:12'),
(66, 3, 3, 17, 0, NULL, '2026-07-07 16:27:43', '2026-07-24 19:10:53'),
(67, 3, 4, 17, 0, NULL, '2026-07-07 16:27:43', '2026-07-24 19:10:53'),
(68, 3, 12, 10, 1, '2026-07-23 10:32:12', '2026-07-07 16:27:43', '2026-07-23 10:32:12'),
(69, 3, 14, 120, 1, '2026-07-23 12:31:35', '2026-07-07 16:27:43', '2026-07-23 12:31:35'),
(70, 3, 15, 252, 0, NULL, '2026-07-07 16:27:43', '2026-07-25 19:48:22'),
(71, 3, 16, 252, 0, NULL, '2026-07-07 16:27:43', '2026-07-25 19:48:22'),
(72, 13, 1, 1, 1, '2026-07-20 20:43:14', '2026-07-10 12:38:23', '2026-07-20 20:43:14'),
(73, 13, 2, 1, 0, NULL, '2026-07-10 12:38:23', '2026-07-20 20:43:14'),
(74, 13, 3, 1, 0, NULL, '2026-07-10 12:38:23', '2026-07-20 20:43:14'),
(75, 13, 4, 1, 0, NULL, '2026-07-10 12:38:23', '2026-07-20 20:43:14'),
(76, 13, 14, 9, 0, NULL, '2026-07-10 12:38:23', '2026-07-20 20:43:14'),
(77, 13, 15, 9, 0, NULL, '2026-07-10 12:38:23', '2026-07-20 20:43:14'),
(78, 13, 16, 9, 0, NULL, '2026-07-10 12:38:23', '2026-07-20 20:43:14'),
(79, 12, 1, 1, 1, '2026-07-20 20:43:14', '2026-07-10 12:38:23', '2026-07-20 20:43:14'),
(80, 12, 2, 1, 0, NULL, '2026-07-10 12:38:23', '2026-07-20 20:43:14'),
(81, 12, 3, 1, 0, NULL, '2026-07-10 12:38:23', '2026-07-20 20:43:14'),
(82, 12, 4, 1, 0, NULL, '2026-07-10 12:38:23', '2026-07-20 20:43:14'),
(83, 12, 5, 1, 1, '2026-07-20 20:43:14', '2026-07-10 12:38:23', '2026-07-20 20:43:14'),
(84, 12, 6, 1, 0, NULL, '2026-07-10 12:38:23', '2026-07-20 20:43:14'),
(85, 12, 7, 1, 0, NULL, '2026-07-10 12:38:23', '2026-07-20 20:43:14'),
(86, 12, 8, 1, 0, NULL, '2026-07-10 12:38:23', '2026-07-20 20:43:14'),
(87, 12, 14, 10, 0, NULL, '2026-07-10 12:38:23', '2026-07-20 20:43:14'),
(88, 12, 15, 10, 0, NULL, '2026-07-10 12:38:23', '2026-07-20 20:43:14'),
(89, 12, 16, 10, 0, NULL, '2026-07-10 12:38:23', '2026-07-20 20:43:14'),
(90, 5, 1, 1, 1, '2026-07-20 20:43:04', '2026-07-12 11:59:20', '2026-07-20 20:43:04'),
(91, 5, 2, 23, 1, '2026-07-24 21:33:29', '2026-07-12 11:59:20', '2026-07-24 21:33:29'),
(92, 5, 3, 23, 0, NULL, '2026-07-12 11:59:20', '2026-07-24 21:33:29'),
(93, 5, 4, 23, 0, NULL, '2026-07-12 11:59:20', '2026-07-24 21:33:29'),
(94, 5, 5, 1, 1, '2026-07-20 20:43:04', '2026-07-12 11:59:20', '2026-07-20 20:43:04'),
(95, 5, 6, 2, 0, NULL, '2026-07-12 11:59:20', '2026-07-24 21:34:21'),
(96, 5, 7, 2, 0, NULL, '2026-07-12 11:59:20', '2026-07-24 21:34:21'),
(97, 5, 8, 2, 0, NULL, '2026-07-12 11:59:20', '2026-07-24 21:34:21'),
(98, 5, 12, 14, 1, '2026-07-24 21:33:29', '2026-07-12 11:59:20', '2026-07-24 21:33:29'),
(99, 5, 14, 40, 0, NULL, '2026-07-12 11:59:20', '2026-07-24 21:34:21'),
(100, 5, 15, 40, 0, NULL, '2026-07-12 11:59:20', '2026-07-24 21:34:21'),
(101, 5, 16, 40, 0, NULL, '2026-07-12 11:59:20', '2026-07-24 21:34:21'),
(102, 6, 5, 1, 1, '2026-07-20 20:43:14', '2026-07-12 17:09:17', '2026-07-20 20:43:14'),
(103, 6, 6, 1, 0, NULL, '2026-07-12 17:09:17', '2026-07-20 20:43:14'),
(104, 6, 7, 1, 0, NULL, '2026-07-12 17:09:17', '2026-07-20 20:43:14'),
(105, 6, 8, 1, 0, NULL, '2026-07-12 17:09:17', '2026-07-20 20:43:14'),
(106, 6, 9, 0, 0, NULL, '2026-07-12 17:09:17', '2026-07-12 17:09:17'),
(107, 6, 10, 0, 0, NULL, '2026-07-12 17:09:17', '2026-07-12 17:09:17'),
(108, 6, 11, 0, 0, NULL, '2026-07-12 17:09:17', '2026-07-12 17:09:17'),
(109, 6, 13, 0, 0, NULL, '2026-07-12 17:09:17', '2026-07-20 20:43:05'),
(110, 13, 5, 0, 0, NULL, '2026-07-12 17:39:19', '2026-07-12 17:39:19'),
(111, 13, 6, 0, 0, NULL, '2026-07-12 17:39:19', '2026-07-12 17:39:19'),
(112, 13, 7, 0, 0, NULL, '2026-07-12 17:39:19', '2026-07-12 17:39:19'),
(113, 13, 8, 0, 0, NULL, '2026-07-12 17:39:19', '2026-07-12 17:39:19'),
(114, 13, 9, 0, 0, NULL, '2026-07-12 17:39:19', '2026-07-12 17:39:19'),
(115, 13, 10, 0, 0, NULL, '2026-07-12 17:39:19', '2026-07-12 17:39:19'),
(116, 13, 11, 0, 0, NULL, '2026-07-12 17:39:19', '2026-07-12 17:39:19'),
(117, 13, 12, 0, 0, NULL, '2026-07-12 17:39:19', '2026-07-20 20:43:13'),
(118, 13, 13, 0, 0, NULL, '2026-07-12 17:39:19', '2026-07-12 17:39:19'),
(119, 7, 9, 0, 0, NULL, '2026-07-12 17:54:16', '2026-07-12 17:54:16'),
(120, 7, 10, 0, 0, NULL, '2026-07-12 17:54:16', '2026-07-12 17:54:16'),
(121, 7, 11, 0, 0, NULL, '2026-07-12 17:54:16', '2026-07-12 17:54:16'),
(122, 8, 5, 0, 0, NULL, '2026-07-12 17:54:24', '2026-07-12 17:54:24'),
(123, 8, 6, 0, 0, NULL, '2026-07-12 17:54:24', '2026-07-12 17:54:24'),
(124, 8, 7, 0, 0, NULL, '2026-07-12 17:54:24', '2026-07-12 17:54:24'),
(125, 8, 8, 0, 0, NULL, '2026-07-12 17:54:24', '2026-07-12 17:54:24'),
(126, 8, 9, 0, 0, NULL, '2026-07-12 17:54:24', '2026-07-12 17:54:24'),
(127, 8, 10, 0, 0, NULL, '2026-07-12 17:54:24', '2026-07-12 17:54:24'),
(128, 8, 11, 0, 0, NULL, '2026-07-12 17:54:24', '2026-07-12 17:54:24'),
(129, 8, 12, 0, 0, NULL, '2026-07-12 17:54:24', '2026-07-12 17:54:24'),
(130, 8, 13, 0, 0, NULL, '2026-07-12 17:54:24', '2026-07-12 17:54:24'),
(131, 2, 5, 0, 0, NULL, '2026-07-15 11:58:40', '2026-07-19 16:25:15'),
(132, 2, 6, 0, 0, NULL, '2026-07-15 11:58:40', '2026-07-19 16:25:15'),
(133, 2, 7, 0, 0, NULL, '2026-07-15 11:58:40', '2026-07-19 16:25:15'),
(134, 2, 8, 0, 0, NULL, '2026-07-15 11:58:40', '2026-07-19 16:25:15'),
(135, 2, 9, 0, 0, NULL, '2026-07-15 11:58:40', '2026-07-15 11:58:40'),
(136, 2, 10, 0, 0, NULL, '2026-07-15 11:58:40', '2026-07-15 11:58:40'),
(137, 2, 11, 0, 0, NULL, '2026-07-15 11:58:40', '2026-07-15 11:58:40'),
(138, 2, 13, 0, 0, NULL, '2026-07-15 11:58:40', '2026-07-15 11:58:40'),
(139, 5, 9, 0, 0, NULL, '2026-07-15 11:58:46', '2026-07-15 11:58:46'),
(140, 5, 10, 0, 0, NULL, '2026-07-15 11:58:46', '2026-07-15 11:58:46'),
(141, 5, 11, 0, 0, NULL, '2026-07-15 11:58:46', '2026-07-15 11:58:46'),
(142, 5, 13, 1, 0, NULL, '2026-07-15 11:58:46', '2026-07-24 21:34:21'),
(154, 12, 9, 0, 0, NULL, '2026-07-16 11:41:27', '2026-07-16 11:41:27'),
(155, 12, 10, 0, 0, NULL, '2026-07-16 11:41:27', '2026-07-16 11:41:27'),
(156, 12, 11, 0, 0, NULL, '2026-07-16 11:41:27', '2026-07-16 11:41:27'),
(157, 12, 12, 0, 0, NULL, '2026-07-16 11:41:27', '2026-07-20 20:43:13'),
(158, 12, 13, 0, 0, NULL, '2026-07-16 11:41:27', '2026-07-16 11:41:27'),
(172, 9, 1, 0, 0, NULL, '2026-07-18 12:48:48', '2026-07-20 20:43:14'),
(173, 9, 5, 0, 0, NULL, '2026-07-18 12:48:48', '2026-07-20 20:43:14'),
(174, 10, 1, 1, 1, '2026-07-24 21:33:56', '2026-07-18 12:48:48', '2026-07-24 21:33:56'),
(438, 3, 5, 0, 0, NULL, '2026-07-23 10:32:12', '2026-07-23 10:32:12'),
(439, 3, 6, 0, 0, NULL, '2026-07-23 10:32:12', '2026-07-23 10:32:12'),
(440, 3, 7, 0, 0, NULL, '2026-07-23 10:32:12', '2026-07-23 10:32:12'),
(441, 3, 8, 0, 0, NULL, '2026-07-23 10:32:12', '2026-07-23 10:32:12'),
(442, 3, 9, 0, 0, NULL, '2026-07-23 10:32:12', '2026-07-23 10:32:12'),
(443, 3, 10, 0, 0, NULL, '2026-07-23 10:32:12', '2026-07-23 10:32:12'),
(444, 3, 11, 0, 0, NULL, '2026-07-23 10:32:12', '2026-07-23 10:32:12'),
(445, 3, 13, 0, 0, NULL, '2026-07-23 10:32:12', '2026-07-23 10:32:12'),
(446, 10, 2, 1, 0, NULL, '2026-07-24 21:33:56', '2026-07-24 21:33:56'),
(447, 10, 3, 1, 0, NULL, '2026-07-24 21:33:56', '2026-07-24 21:33:56'),
(448, 10, 4, 1, 0, NULL, '2026-07-24 21:33:56', '2026-07-24 21:33:56'),
(449, 10, 5, 0, 0, NULL, '2026-07-24 21:33:56', '2026-07-24 21:33:56'),
(450, 10, 6, 0, 0, NULL, '2026-07-24 21:33:56', '2026-07-24 21:33:56'),
(451, 10, 7, 0, 0, NULL, '2026-07-24 21:33:56', '2026-07-24 21:33:56'),
(452, 10, 8, 0, 0, NULL, '2026-07-24 21:33:56', '2026-07-24 21:33:56'),
(453, 10, 9, 0, 0, NULL, '2026-07-24 21:33:56', '2026-07-24 21:33:56'),
(454, 10, 10, 0, 0, NULL, '2026-07-24 21:33:56', '2026-07-24 21:33:56'),
(455, 10, 11, 0, 0, NULL, '2026-07-24 21:33:56', '2026-07-24 21:33:56'),
(456, 10, 12, 1, 0, NULL, '2026-07-24 21:33:56', '2026-07-24 21:33:56'),
(457, 10, 13, 0, 0, NULL, '2026-07-24 21:33:56', '2026-07-24 21:33:56'),
(458, 10, 14, 30, 0, NULL, '2026-07-24 21:33:56', '2026-07-24 21:34:11'),
(459, 10, 15, 30, 0, NULL, '2026-07-24 21:33:56', '2026-07-24 21:34:11'),
(460, 10, 16, 30, 0, NULL, '2026-07-24 21:33:56', '2026-07-24 21:34:11');

-- --------------------------------------------------------

--
-- Table structure for table `user_ip_tracking`
--

DROP TABLE IF EXISTS `user_ip_tracking`;
CREATE TABLE IF NOT EXISTS `user_ip_tracking` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int UNSIGNED NOT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `first_seen_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `last_seen_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `login_count` int UNSIGNED DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_user_ip` (`user_id`,`ip_address`),
  KEY `idx_ip_address` (`ip_address`),
  KEY `idx_user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_ip_tracking`
--

INSERT INTO `user_ip_tracking` (`id`, `user_id`, `ip_address`, `user_agent`, `first_seen_at`, `last_seen_at`, `login_count`) VALUES
(1, 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-21 14:58:17', '2026-07-24 20:29:29', 5),
(2, 1, '192.168.2.13', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Mobile Safari/537.36', '2026-07-21 15:23:17', '2026-07-23 17:10:52', 2),
(3, 14, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-21 15:33:09', '2026-07-21 15:33:09', 1),
(4, 15, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-21 15:58:21', '2026-07-21 15:58:21', 1),
(5, 2, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 06:51:48', '2026-07-23 06:51:48', 1),
(6, 2, '192.168.2.13', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Mobile Safari/537.36', '2026-07-23 09:33:55', '2026-07-23 09:33:55', 1),
(7, 1, '192.168.2.10', 'Mozilla/5.0 (Linux; NetCast; U) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.3945.79 Safari/537.36 SmartTV/10.0 Colt/2.0', '2026-07-23 19:43:43', '2026-07-23 19:43:43', 1),
(8, 7, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:153.0) Gecko/20100101 Firefox/153.0', '2026-07-24 20:29:51', '2026-07-24 20:29:51', 1),
(9, 1, '192.168.2.11', 'Mozilla/5.0 (Linux; NetCast; U) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.3945.79 Safari/537.36 SmartTV/10.0 Colt/2.0', '2026-08-05 13:18:04', '2026-08-05 13:18:04', 1);

-- --------------------------------------------------------

--
-- Table structure for table `user_notification_settings`
--

DROP TABLE IF EXISTS `user_notification_settings`;
CREATE TABLE IF NOT EXISTS `user_notification_settings` (
  `user_id` int UNSIGNED NOT NULL,
  `enable_game_updates` tinyint(1) DEFAULT '1',
  `enable_achievements` tinyint(1) DEFAULT '1',
  `enable_challenges` tinyint(1) DEFAULT '1',
  `enable_system` tinyint(1) DEFAULT '1',
  `enable_push` tinyint(1) DEFAULT '0',
  `quiet_hours_start` time DEFAULT NULL,
  `quiet_hours_end` time DEFAULT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_sessions`
--

DROP TABLE IF EXISTS `user_sessions`;
CREATE TABLE IF NOT EXISTS `user_sessions` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int UNSIGNED NOT NULL,
  `session_id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `page` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'صفحه فعلی',
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `last_activity` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user` (`user_id`),
  KEY `idx_session` (`session_id`),
  KEY `idx_activity` (`last_activity`)
) ENGINE=InnoDB AUTO_INCREMENT=72 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_sessions`
--

INSERT INTO `user_sessions` (`id`, `user_id`, `session_id`, `page`, `ip_address`, `user_agent`, `last_activity`) VALUES
(1, 1, 'neok8evvm2idcsbnrnk3ev01so', '/games', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-07-07 09:32:59'),
(2, 1, 'b95n7l1p5ckdr0mkrtgk3ur0qe', '/achievements', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-07-07 18:18:48'),
(3, 6, 'oqiro97l3u9bnuqg4rbknna2v3', '/game/86', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-07-07 17:04:59'),
(4, 1, 'le1eavohdudmrsbfoakdo39oi2', '/achievements', '192.168.2.15', 'Mozilla/5.0 (Linux; NetCast; U) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.3945.79 Safari/537.36 SmartTV/10.0 Colt/2.0', '2026-07-07 11:35:19'),
(5, 1, '6lgqnuull40i34ptlbdabmido0', '/games', '192.168.2.13', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Mobile Safari/537.36', '2026-07-07 12:12:59'),
(6, 6, 'culbivld5gfjtk9ctlr314to4s', '/dashboard', '192.168.2.14', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Mobile Safari/537.36', '2026-07-07 11:35:28'),
(7, 5, '033142lodqe76j68g702br28pa', '/game/59', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:152.0) Gecko/20100101 Firefox/152.0', '2026-07-07 14:52:48'),
(8, 8, 'rc4t7347cg5tuqunacpldgj8p0', '/game/83', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:152.0) Gecko/20100101 Firefox/152.0', '2026-07-07 16:42:12'),
(9, 7, 'nsshcbb5tsa0nos9k1r29hbada', '/game/83', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:152.0) Gecko/20100101 Firefox/152.0', '2026-07-07 16:41:25'),
(10, 7, 'rrb82vtcgj6s60ks72vvmmng0u', '/game/86', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-07-07 17:02:02'),
(11, 6, 'l68431cvn4j10ornjud43iq8np', '/game/88', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-07-07 17:22:38'),
(12, 1, 'b9h45v62gc7mmpod6eav7a5mbk', '/dashboard', '192.168.2.13', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Mobile Safari/537.36', '2026-07-07 17:52:55'),
(13, 1, 'd71orar9hkrg1ss7idt2vcaufc', '/dashboard', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-07-08 12:11:53'),
(14, 1, '8pc03qihf66i43mhakvhlf90ac', '/game/89', '192.168.2.13', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Mobile Safari/537.36', '2026-07-08 11:54:40'),
(15, 6, '37p7liaq9jd4ha2sndn50pgfa9', '/game/create', '192.168.2.14', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Mobile Safari/537.36', '2026-07-08 11:06:32'),
(16, 1, '9djtcrd9no51a8j73tg6r7av8v', '/game/90', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-07-09 19:39:05'),
(17, 6, 'ptnphsb627jgs0uooneiedo3dp', '/game/90', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-07-09 19:39:07'),
(18, 7, 'kvsvl8bkr85l5s9uvlrugn0nuk', '/game/90', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-07-09 19:39:08'),
(19, 1, '7e08ne4frtbmj9un55luboi1on', '/game/68', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-07-10 18:59:43'),
(20, 1, 'hedjdl1ue6eslsrp3gcgsrni0j', '/dashboard', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-07-10 08:00:46'),
(21, 1, 'g0jvr0p1gd8gdnii8al2vs7tf2', '/game/94', '192.168.2.13', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Mobile Safari/537.36', '2026-07-10 15:07:16'),
(22, 1, 'osqantnlae1ims5tajhcp34grn', '/games', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:152.0) Gecko/20100101 Firefox/152.0', '2026-07-10 19:00:13'),
(23, 13, '6j6hiken2m71gn41i5758n4lsj', '/dashboard', '192.168.2.10', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Mobile Safari/537.36', '2026-07-10 12:39:46'),
(24, 12, 'bclcumm8hctr4gvctnhqkuhses', '/games', '192.168.2.14', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Mobile Safari/537.36', '2026-07-10 12:39:44'),
(25, 1, '6jfgt8r0aehg93g5rcn3inouqq', '/game/97', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-11 21:27:44'),
(26, 1, 'itc4dn8lfir12o7jn1o4c9fj7u', '/game/96', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:152.0) Gecko/20100101 Firefox/152.0', '2026-07-11 13:02:14'),
(27, 1, '5hv3ddl19ppt2meniocj6277s3', '/game/97', '192.168.2.13', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Mobile Safari/537.36', '2026-07-11 13:00:49'),
(28, 13, '23o56bchi32ct1i6iqkjqb0ov7', '/game/97', '192.168.2.10', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Mobile Safari/537.36', '2026-07-11 13:01:30'),
(29, 12, '15s2n3iadel0k3251ok4grt6m6', '/game/97', '192.168.2.14', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Mobile Safari/537.36', '2026-07-11 13:01:34'),
(30, 1, '82nhd6eej45qgpgmm4q19elppv', '/dashboard', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-12 19:35:50'),
(31, 1, 'fpc57dpk303i6l59gbd0upvpgj', '/game/96', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:152.0) Gecko/20100101 Firefox/152.0', '2026-07-12 20:04:49'),
(32, 7, 'bgb7fcvrlieoe5nsqdns1nrnb6', '/game/98', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-12 12:28:38'),
(33, 7, 'l3lr4itamlqlfj1m89bk81f7vt', '/game/96', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-12 20:04:52'),
(34, 1, 'asbam9jimttqragvfgaogqo8l0', '/dashboard', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-13 19:31:40'),
(35, 1, '9iff5ftb5joofbhh3bnunlh7tj', '/game/96', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-14 17:24:35'),
(36, 1, 'co0adomi0bvtjk3157an7v5rkm', '/dashboard', '192.168.2.13', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Mobile Safari/537.36', '2026-07-14 13:19:09'),
(37, 1, 'v7nigmon22urckrhd94lpvcitp', '/dashboard', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-15 20:52:46'),
(38, 2, 'jv1i2n6h42uupt2ei1kg7fhmu0', '/game/98', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-15 10:52:29'),
(39, 1, 'om5u7erl7m8alqcm3ktdg3huoa', '/game/create', '192.168.2.13', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Mobile Safari/537.36', '2026-07-15 11:57:35'),
(40, 13, 'tvhktsmp1lau0vhu1p3cg57ehs', '/dashboard', '192.168.2.11', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Mobile Safari/537.36', '2026-07-15 11:16:47'),
(41, 2, '9j7av2rkt0vvargnomad72it7q', '/dashboard', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-15 18:54:28'),
(42, 7, '9ol8mu00vqoqpud9gcl34nc2ir', '/game/95', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-15 21:04:49'),
(43, 1, '2dti34bh5m4slamupsjajq3qp7', '/dashboard', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:152.0) Gecko/20100101 Firefox/152.0', '2026-07-15 21:04:35'),
(44, 7, 'iit52hq64e4gfp6blav54rnaho', '/game/95', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:152.0) Gecko/20100101 Firefox/152.0', '2026-07-15 21:04:30'),
(45, 1, '6hogiafmff5sbouf0htbrci0mh', '/dashboard', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-16 19:33:58'),
(46, 7, 'u8oiv2a35gte6r7omgjehi607l', '/dashboard', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-16 07:06:11'),
(47, 1, '451bbr8qfnqiqlr1qhthn45s4j', '/game/105', '192.168.2.13', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Mobile Safari/537.36', '2026-07-16 13:59:41'),
(48, 13, '72hdld7qf0dd94tb92k87s30iv', '/game/105', '192.168.2.11', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Mobile Safari/537.36', '2026-07-16 14:03:27'),
(49, 12, 'fss7sokvlntl7hvtt2gbh3lfhk', '/dashboard', '192.168.2.14', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Mobile Safari/537.36', '2026-07-16 17:24:01'),
(50, 1, '3kbnvqsmf9bqe3fv1nihe1vvja', '/dashboard', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-17 16:10:03'),
(51, 1, 'kuqg8p9dbp96bkjkmiggclnn96', '/dashboard', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-18 16:51:25'),
(52, 7, 'ctskkti9bmukmq1d514v8gpruc', '/game/107', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-18 11:28:44'),
(53, 1, 'va99k8bhe8v08ddpmqj8siknpn', '/dashboard', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:152.0) Gecko/20100101 Firefox/152.0', '2026-07-18 17:38:38'),
(54, 1, 'clquglk7h1qvaj992r5qisvroq', '/game/create', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:152.0) Gecko/20100101 Firefox/152.0', '2026-07-18 19:11:25'),
(55, 1, '44sn67fvkn45s0bgdimo7r9dmv', '/games', '192.168.2.13', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Mobile Safari/537.36', '2026-07-20 08:04:22'),
(56, 1, 'g1rti1oruhh55hcg5vpuk3i12k', '/game/150', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-24 20:28:41'),
(57, 7, 'norq4biag05mirk9rlgou2p01q', '/dashboard', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-19 14:08:24'),
(58, 7, 'mu6p7dh1mvlkuue4r6bn5096n3', '/dashboard', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-19 14:13:16'),
(59, 7, '6bnqpf9e9k08nmde4l1s5k20do', '/dashboard', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-19 15:34:42'),
(60, 2, 'f998m683n7ta48uche7bihlhpm', '/game/create', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:152.0) Gecko/20100101 Firefox/152.0', '2026-07-19 15:55:07'),
(61, 7, '921t4tsmog7e9ctd8mbch7q3ng', '/dashboard', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-19 17:05:32'),
(62, 2, '3ia6hll1lan0pknlg583lq00c4', '/games', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:152.0) Gecko/20100101 Firefox/152.0', '2026-07-19 18:13:29'),
(63, 1, 'irv9cqbtlc6jdfr7uccaqosnae', '/dashboard', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:152.0) Gecko/20100101 Firefox/152.0', '2026-07-19 19:39:30'),
(64, 7, '2v281moguscdmj1jf19g63u2ve', '/game/95', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-19 19:39:57'),
(65, 2, 'akl69l4dbaijprbpafhs3ap2v6', '/game/148', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 20:14:21'),
(66, 7, '36u8inaqg4kenq8uvgrl9equ30', '/profile', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:153.0) Gecko/20100101 Firefox/153.0', '2026-07-24 20:28:43'),
(67, 1, '04eqdnk18sjungd94pkeoqhbqn', '/dashboard', '192.168.2.13', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Mobile Safari/537.36', '2026-07-24 20:27:01'),
(68, 1, 'fbq5nkti61vdlpa9lke0eehf8f', 'http://192.168.2.12/game/148', '192.168.2.10', 'Mozilla/5.0 (Linux; NetCast; U) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.3945.79 Safari/537.36 SmartTV/10.0 Colt/2.0', '2026-07-24 11:48:04'),
(69, 7, 'e5jd6dtne6crdcmc86nr14hr44', '/game/149', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:153.0) Gecko/20100101 Firefox/153.0', '2026-07-25 19:52:02'),
(70, 1, '1vg60dl6rgkclt3eoc3rb6savm', '/dashboard', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-08-05 16:28:20'),
(71, 1, 'seuhn9fdqg6j5m718hnoqqkc5k', '/tv/149', '192.168.2.11', 'Mozilla/5.0 (Linux; NetCast; U) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.3945.79 Safari/537.36 SmartTV/10.0 Colt/2.0', '2026-08-05 14:09:50');

-- --------------------------------------------------------

--
-- Table structure for table `user_streaks`
--

DROP TABLE IF EXISTS `user_streaks`;
CREATE TABLE IF NOT EXISTS `user_streaks` (
  `user_id` int UNSIGNED NOT NULL,
  `current_streak` int UNSIGNED DEFAULT '0' COMMENT 'استریک فعلی',
  `best_streak` int UNSIGNED DEFAULT '0' COMMENT 'بهترین استریک',
  `last_win_at` timestamp NULL DEFAULT NULL COMMENT 'زمان آخرین برد',
  `streak_broken_at` timestamp NULL DEFAULT NULL COMMENT 'زمان شکستن استریک',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_streaks`
--

INSERT INTO `user_streaks` (`user_id`, `current_streak`, `best_streak`, `last_win_at`, `streak_broken_at`, `updated_at`) VALUES
(1, 0, 2, '2026-07-04 11:29:32', '2026-07-04 15:44:36', '2026-07-04 15:44:36'),
(2, 0, 0, NULL, NULL, '2026-07-04 15:44:36'),
(3, 0, 0, NULL, NULL, '2026-07-07 16:27:43'),
(4, 0, 0, NULL, NULL, '2026-07-06 18:41:33'),
(5, 0, 0, NULL, NULL, '2026-07-12 11:59:20'),
(6, 0, 0, NULL, NULL, '2026-07-04 15:08:44'),
(7, 0, 0, NULL, NULL, '2026-07-04 15:08:44'),
(8, 0, 0, NULL, NULL, '2026-07-07 12:37:04'),
(10, 0, 0, NULL, NULL, '2026-07-24 21:34:21'),
(12, 0, 0, NULL, NULL, '2026-07-10 12:38:23'),
(13, 0, 0, NULL, NULL, '2026-07-10 12:38:23');

-- --------------------------------------------------------

--
-- Table structure for table `user_titles`
--

DROP TABLE IF EXISTS `user_titles`;
CREATE TABLE IF NOT EXISTS `user_titles` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int UNSIGNED NOT NULL,
  `title_id` int UNSIGNED NOT NULL,
  `is_active` tinyint(1) DEFAULT '0' COMMENT 'لقب فعال فعلی',
  `unlocked_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_user_title` (`user_id`,`title_id`),
  KEY `title_id` (`title_id`),
  KEY `idx_user_active` (`user_id`,`is_active`)
) ENGINE=InnoDB AUTO_INCREMENT=146 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_titles`
--

INSERT INTO `user_titles` (`id`, `user_id`, `title_id`, `is_active`, `unlocked_at`) VALUES
(4, 4, 1, 1, '2026-07-20 20:43:05'),
(5, 5, 1, 1, '2026-07-20 20:43:04'),
(11, 12, 1, 1, '2026-07-20 20:43:14'),
(12, 13, 1, 1, '2026-07-20 20:43:14'),
(13, 7, 1, 1, '2026-07-20 20:43:14'),
(16, 6, 1, 1, '2026-07-20 20:43:14'),
(18, 1, 1, 1, '2026-07-20 20:43:14'),
(20, 2, 1, 1, '2026-07-20 20:43:14'),
(144, 10, 1, 1, '2026-07-24 21:34:21'),
(145, 3, 1, 1, '2026-07-24 21:34:21');

-- --------------------------------------------------------

--
-- Table structure for table `user_xp`
--

DROP TABLE IF EXISTS `user_xp`;
CREATE TABLE IF NOT EXISTS `user_xp` (
  `user_id` int UNSIGNED NOT NULL,
  `total_xp` int UNSIGNED DEFAULT '0' COMMENT 'کل XP',
  `current_level` int UNSIGNED DEFAULT '1' COMMENT 'سطح فعلی',
  `xp_to_next_level` int UNSIGNED DEFAULT '100' COMMENT 'XP مورد نیاز برای سطح بعدی',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_xp`
--

INSERT INTO `user_xp` (`user_id`, `total_xp`, `current_level`, `xp_to_next_level`, `updated_at`) VALUES
(1, 222, 2, 78, '2026-07-24 21:34:59'),
(2, 310, 3, 290, '2026-07-24 21:34:59'),
(3, 185, 2, 115, '2026-07-24 21:34:59'),
(4, 5, 1, 100, '2026-07-20 20:43:05'),
(5, 215, 2, 85, '2026-07-24 21:34:21'),
(6, 36, 1, 100, '2026-07-20 20:43:14'),
(7, 342, 3, 258, '2026-07-24 21:34:59'),
(8, 0, 1, 100, '2026-07-20 20:43:05'),
(10, 80, 1, 20, '2026-07-24 21:34:21'),
(12, 40, 1, 100, '2026-07-20 20:43:14'),
(13, 23, 1, 100, '2026-07-20 20:43:14');

-- --------------------------------------------------------

--
-- Table structure for table `win_types`
--

DROP TABLE IF EXISTS `win_types`;
CREATE TABLE IF NOT EXISTS `win_types` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `icon` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `score_multiplier` decimal(4,2) DEFAULT '1.00',
  `is_active` tinyint(1) DEFAULT '1',
  `sort_order` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `win_types`
--

INSERT INTO `win_types` (`id`, `name`, `slug`, `icon`, `description`, `score_multiplier`, `is_active`, `sort_order`, `created_at`) VALUES
(1, 'برد معمولی', 'normal', '✅', 'برد استاندارد', 1.00, 1, 0, '2026-06-30 14:56:11'),
(2, 'کامبک', 'comeback', '🔄', 'برد پس از عقب بودن زیاد', 1.00, 1, 0, '2026-06-30 14:56:11'),
(3, 'شانسی', 'lucky', '🍀', 'برد با شانس زیاد', 1.00, 1, 0, '2026-06-30 14:56:11'),
(4, 'سلطه‌گر', 'domination', '💪', 'برد با اختلاف زیاد', 1.00, 1, 0, '2026-06-30 14:56:11'),
(5, 'تاکتیکی', 'tactical', '💣', 'برد با نقشه', 1.00, 1, 0, '2026-07-10 14:58:08');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admin_logs`
--
ALTER TABLE `admin_logs`
  ADD CONSTRAINT `admin_logs_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `card_mastery`
--
ALTER TABLE `card_mastery`
  ADD CONSTRAINT `card_mastery_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `card_mastery_ibfk_2` FOREIGN KEY (`card_id`) REFERENCES `cards` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `games`
--
ALTER TABLE `games`
  ADD CONSTRAINT `games_ibfk_1` FOREIGN KEY (`referee_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `game_participants`
--
ALTER TABLE `game_participants`
  ADD CONSTRAINT `game_participants_ibfk_1` FOREIGN KEY (`game_id`) REFERENCES `games` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `game_participants_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `game_participants_ibfk_3` FOREIGN KEY (`team_id`) REFERENCES `teams` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `game_rounds`
--
ALTER TABLE `game_rounds`
  ADD CONSTRAINT `game_rounds_ibfk_1` FOREIGN KEY (`game_id`) REFERENCES `games` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `game_rounds_ibfk_2` FOREIGN KEY (`winner_participant_id`) REFERENCES `game_participants` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `game_rounds_ibfk_3` FOREIGN KEY (`winning_card_id`) REFERENCES `cards` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `game_rounds_ibfk_4` FOREIGN KEY (`win_type_id`) REFERENCES `win_types` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `leaderboard_cache`
--
ALTER TABLE `leaderboard_cache`
  ADD CONSTRAINT `leaderboard_cache_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `referee_actions_log`
--
ALTER TABLE `referee_actions_log`
  ADD CONSTRAINT `referee_actions_log_ibfk_1` FOREIGN KEY (`game_id`) REFERENCES `games` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `referee_actions_log_ibfk_2` FOREIGN KEY (`referee_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `system_settings`
--
ALTER TABLE `system_settings`
  ADD CONSTRAINT `system_settings_ibfk_1` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `teammate_history`
--
ALTER TABLE `teammate_history`
  ADD CONSTRAINT `teammate_history_ibfk_1` FOREIGN KEY (`user_id_1`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `teammate_history_ibfk_2` FOREIGN KEY (`user_id_2`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `teams`
--
ALTER TABLE `teams`
  ADD CONSTRAINT `teams_ibfk_1` FOREIGN KEY (`game_id`) REFERENCES `games` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_achievements`
--
ALTER TABLE `user_achievements`
  ADD CONSTRAINT `user_achievements_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_achievements_ibfk_2` FOREIGN KEY (`achievement_id`) REFERENCES `achievements` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_notification_settings`
--
ALTER TABLE `user_notification_settings`
  ADD CONSTRAINT `user_notification_settings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_sessions`
--
ALTER TABLE `user_sessions`
  ADD CONSTRAINT `user_sessions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_streaks`
--
ALTER TABLE `user_streaks`
  ADD CONSTRAINT `user_streaks_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_titles`
--
ALTER TABLE `user_titles`
  ADD CONSTRAINT `user_titles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_titles_ibfk_2` FOREIGN KEY (`title_id`) REFERENCES `titles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_xp`
--
ALTER TABLE `user_xp`
  ADD CONSTRAINT `user_xp_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
