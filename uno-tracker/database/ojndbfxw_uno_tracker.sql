-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Aug 08, 2026 at 10:01 PM
-- Server version: 8.0.44-cll-lve
-- PHP Version: 8.4.23

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ojndbfxw_uno_tracker`
--

-- --------------------------------------------------------

--
-- Table structure for table `achievements`
--

CREATE TABLE `achievements` (
  `id` int UNSIGNED NOT NULL,
  `code` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'کد یکتا برای شناسایی',
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'نام نشان',
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'توضیحات',
  `icon` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 0xF09F8F85 COMMENT 'ایموجی یا آیکون',
  `category` enum('general','winning','streak','team','special') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'general' COMMENT 'دسته‌بندی',
  `rarity` enum('common','rare','epic','legendary') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'common' COMMENT 'کمیابی',
  `xp_reward` int UNSIGNED DEFAULT '10' COMMENT 'XP پاداش',
  `condition_type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'نوع شرط (total_games, total_wins, win_streak, etc)',
  `condition_value` int UNSIGNED NOT NULL COMMENT 'مقدار شرط',
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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

CREATE TABLE `admin_logs` (
  `id` int UNSIGNED NOT NULL,
  `admin_id` int UNSIGNED NOT NULL,
  `action_type` enum('user_ban','user_unban','user_delete','user_role_change','game_delete','game_edit','achievement_create','achievement_edit','achievement_delete','challenge_create','challenge_edit','challenge_delete','setting_change','login','logout') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `target_type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'نوع هدف (user, game, achievement, ...)',
  `target_id` int UNSIGNED DEFAULT NULL COMMENT 'شناسه هدف',
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `old_data` json DEFAULT NULL COMMENT 'داده‌های قبلی',
  `new_data` json DEFAULT NULL COMMENT 'داده‌های جدید',
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admin_logs`
--

INSERT INTO `admin_logs` (`id`, `admin_id`, `action_type`, `target_type`, `target_id`, `description`, `old_data`, `new_data`, `ip_address`, `user_agent`, `created_at`) VALUES
(849, 1, '', 'title', 5, 'ویرایش عنوان: افسانه', '{\"id\": 5, \"code\": \"legend\", \"icon\": \"👽\", \"name\": \"افسانه\", \"priority\": 100, \"is_active\": 1, \"created_at\": \"2026-07-04 14:32:34\", \"description\": \"10 برد متوالی داشته\", \"bonus_points\": 100, \"condition_type\": \"best_streak\", \"condition_value\": 10}', '{\"code\": \"legend\", \"icon\": \"👽\", \"name\": \"افسانه\", \"priority\": 0, \"is_active\": 1, \"description\": \"10 برد متوالی داشته\", \"bonus_points\": 1000, \"condition_type\": \"best_streak\", \"condition_value\": 10}', '113.203.11.53', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Mobile Safari/537.36', '2026-07-22 11:18:54'),
(850, 1, '', 'user', 18, 'اعطای مجوز ساخت بازی به امپراطور', NULL, '{\"can_create_game\": 1}', '89.45.156.251', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Mobile Safari/537.36', '2026-07-22 19:28:40'),
(851, 1, '', 'user', 18, 'سلب مجوز ساخت بازی از امپراطور', NULL, '{\"can_create_game\": 0}', '89.45.156.251', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Mobile Safari/537.36', '2026-07-22 19:28:55'),
(852, 1, 'user_role_change', 'user', 18, 'تغییر نقش کاربر امپراطور از user به admin', '{\"role\": \"user\"}', '{\"role\": \"admin\"}', '89.45.156.251', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Mobile Safari/537.36', '2026-07-22 19:32:41'),
(853, 1, 'user_role_change', 'user', 18, 'تغییر نقش کاربر امپراطور از admin به user', '{\"role\": \"admin\"}', '{\"role\": \"user\"}', '89.45.156.251', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Mobile Safari/537.36', '2026-07-22 19:34:00'),
(854, 1, '', 'user', 21, 'اعطای مجوز ساخت بازی به کماندو', NULL, '{\"can_create_game\": 1}', '217.219.119.181', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Mobile Safari/537.36', '2026-07-24 11:58:19'),
(855, 1, '', 'user', 21, 'سلب مجوز ساخت بازی از کماندو', NULL, '{\"can_create_game\": 0}', '217.219.119.181', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Mobile Safari/537.36', '2026-07-24 11:58:31'),
(856, 1, 'user_ban', 'user', 20, 'مسدود کردن کاربر: فرانکلین', NULL, '{\"status\": \"banned\"}', '217.219.119.181', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Mobile Safari/537.36', '2026-07-24 12:00:26'),
(857, 1, 'user_unban', 'user', 20, 'فعال‌سازی کاربر: فرانکلین', NULL, '{\"status\": \"active\"}', '217.219.119.181', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Mobile Safari/537.36', '2026-07-24 12:03:47'),
(858, 1, '', 'card', 12, 'ویرایش کارت: شافل', '{\"id\": 12, \"name\": \"شافل\", \"slug\": \"shuffle\", \"emoji\": \"🌀\", \"rarity\": \"rare\", \"icon_path\": null, \"is_active\": 1, \"created_at\": \"2026-06-30 14:56:11\", \"sort_order\": 12, \"description\": \"همه کارت‌ها جمع و دوباره پخش می‌شوند\", \"is_action_card\": 1, \"score_multiplier\": \"3.00\"}', '{\"name\": \"شافل\", \"slug\": \"shuffle\", \"emoji\": \"🌀\", \"rarity\": \"rare\", \"is_active\": 1, \"description\": \"همه کارت‌ها جمع و دوباره پخش می‌شوند\", \"score_multiplier\": 5}', '217.219.119.181', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Mobile Safari/537.36', '2026-07-24 15:22:24'),
(859, 1, '', 'title', 1, 'ویرایش عنوان: تازه‌کار', '{\"id\": 1, \"code\": \"newbie\", \"icon\": \"🌱\", \"name\": \"تازه‌کار\", \"priority\": 10, \"is_active\": 1, \"created_at\": \"2026-07-04 14:32:34\", \"description\": \"اولین بازی خود را انجام داده\", \"bonus_points\": 0, \"condition_type\": \"total_games\", \"condition_value\": 1}', '{\"code\": \"newbie\", \"icon\": \"🧑‍🏫\", \"name\": \"کارآموز\", \"priority\": 0, \"is_active\": 1, \"description\": \"اولین بازی خود را انجام داده\", \"bonus_points\": 0, \"condition_type\": \"total_games\", \"condition_value\": 1}', '217.219.119.181', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Mobile Safari/537.36', '2026-07-24 15:54:33'),
(860, 1, '', 'title', 1, 'ویرایش عنوان: کارآموز', '{\"id\": 1, \"code\": \"newbie\", \"icon\": \"🧑‍🏫\", \"name\": \"کارآموز\", \"priority\": 0, \"is_active\": 1, \"created_at\": \"2026-07-04 14:32:34\", \"description\": \"اولین بازی خود را انجام داده\", \"bonus_points\": 0, \"condition_type\": \"total_games\", \"condition_value\": 1}', '{\"code\": \"newbie\", \"icon\": \"👨‍🏫\", \"name\": \"کارآموز\", \"priority\": 0, \"is_active\": 1, \"description\": \"اولین بازی خود را انجام داده\", \"bonus_points\": 0, \"condition_type\": \"total_games\", \"condition_value\": 1}', '217.219.119.181', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Mobile Safari/537.36', '2026-07-24 15:55:45'),
(861, 1, '', 'title', 4, 'ویرایش عنوان: قهرمان', '{\"id\": 4, \"code\": \"champion\", \"icon\": \"👑\", \"name\": \"قهرمان\", \"priority\": 90, \"is_active\": 1, \"created_at\": \"2026-07-04 14:32:34\", \"description\": \"200 برد کسب کرده\", \"bonus_points\": 25, \"condition_type\": \"total_wins\", \"condition_value\": 200}', '{\"code\": \"champion\", \"icon\": \"👑\", \"name\": \"قهرمان\", \"priority\": 0, \"is_active\": 1, \"description\": \"100 برد کسب کرده\", \"bonus_points\": 100, \"condition_type\": \"total_wins\", \"condition_value\": 100}', '217.219.119.181', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Mobile Safari/537.36', '2026-07-24 16:00:18'),
(862, 1, '', 'title', 6, 'ویرایش عنوان: سرسخت', '{\"id\": 6, \"code\": \"unstoppable\", \"icon\": \"💀\", \"name\": \"سرسخت\", \"priority\": 80, \"is_active\": 1, \"created_at\": \"2026-07-04 14:32:34\", \"description\": \"۵ برد متوالی داشته\", \"bonus_points\": 20, \"condition_type\": \"best_streak\", \"condition_value\": 5}', '{\"code\": \"unstoppable\", \"icon\": \"💀\", \"name\": \"سرسخت\", \"priority\": 0, \"is_active\": 1, \"description\": \"۵ برد متوالی داشته\", \"bonus_points\": 50, \"condition_type\": \"best_streak\", \"condition_value\": 5}', '217.219.119.181', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Mobile Safari/537.36', '2026-07-24 16:01:20'),
(863, 1, '', 'title', 7, 'ویرایش عنوان: رهبر تیم', '{\"id\": 7, \"code\": \"team_leader\", \"icon\": \"🤝\", \"name\": \"رهبر تیم\", \"priority\": 70, \"is_active\": 1, \"created_at\": \"2026-07-04 14:32:34\", \"description\": \"۱۰ برد تیمی داشته\", \"bonus_points\": 15, \"condition_type\": \"team_wins\", \"condition_value\": 10}', '{\"code\": \"team_leader\", \"icon\": \"🤝\", \"name\": \"رهبر تیم\", \"priority\": 0, \"is_active\": 1, \"description\": \"۱۰ برد تیمی داشته\", \"bonus_points\": 50, \"condition_type\": \"team_wins\", \"condition_value\": 10}', '217.219.119.181', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Mobile Safari/537.36', '2026-07-24 16:12:11'),
(864, 1, '', 'title', 2, 'ویرایش عنوان: بازیکن فعال', '{\"id\": 2, \"code\": \"active\", \"icon\": \"🎯\", \"name\": \"بازیکن فعال\", \"priority\": 50, \"is_active\": 1, \"created_at\": \"2026-07-04 14:32:34\", \"description\": \"۱۰ بازی انجام داده\", \"bonus_points\": 5, \"condition_type\": \"total_games\", \"condition_value\": 10}', '{\"code\": \"active\", \"icon\": \"🎯\", \"name\": \"بازیکن فعال\", \"priority\": 0, \"is_active\": 1, \"description\": \"۱۰ بازی انجام داده\", \"bonus_points\": 10, \"condition_type\": \"total_games\", \"condition_value\": 10}', '217.219.119.181', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Mobile Safari/537.36', '2026-07-24 16:12:26'),
(865, 1, '', 'title', 6, 'ویرایش عنوان: سرسخت', '{\"id\": 6, \"code\": \"unstoppable\", \"icon\": \"💀\", \"name\": \"سرسخت\", \"priority\": 0, \"is_active\": 1, \"created_at\": \"2026-07-04 14:32:34\", \"description\": \"۵ برد متوالی داشته\", \"bonus_points\": 50, \"condition_type\": \"best_streak\", \"condition_value\": 5}', '{\"code\": \"unstoppable\", \"icon\": \"💀\", \"name\": \"سرسخت\", \"priority\": 0, \"is_active\": 1, \"description\": \"۵ برد متوالی داشته\", \"bonus_points\": 100, \"condition_type\": \"best_streak\", \"condition_value\": 5}', '217.219.119.181', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Mobile Safari/537.36', '2026-07-24 16:13:13'),
(866, 1, '', 'title', 5, 'ویرایش عنوان: افسانه', '{\"id\": 5, \"code\": \"legend\", \"icon\": \"👽\", \"name\": \"افسانه\", \"priority\": 0, \"is_active\": 1, \"created_at\": \"2026-07-04 14:32:34\", \"description\": \"10 برد متوالی داشته\", \"bonus_points\": 1000, \"condition_type\": \"best_streak\", \"condition_value\": 10}', '{\"code\": \"legend\", \"icon\": \"👽\", \"name\": \"افسانه\", \"priority\": 0, \"is_active\": 1, \"description\": \"10 برد متوالی داشته\", \"bonus_points\": 10000, \"condition_type\": \"best_streak\", \"condition_value\": 10}', '217.219.119.181', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Mobile Safari/537.36', '2026-07-24 16:15:04'),
(867, 1, '', 'title', 6, 'ویرایش عنوان: سرسخت', '{\"id\": 6, \"code\": \"unstoppable\", \"icon\": \"💀\", \"name\": \"سرسخت\", \"priority\": 0, \"is_active\": 1, \"created_at\": \"2026-07-04 14:32:34\", \"description\": \"۵ برد متوالی داشته\", \"bonus_points\": 100, \"condition_type\": \"best_streak\", \"condition_value\": 5}', '{\"code\": \"unstoppable\", \"icon\": \"💀\", \"name\": \"سرسخت\", \"priority\": 0, \"is_active\": 1, \"description\": \"۵ برد متوالی داشته\", \"bonus_points\": 1000, \"condition_type\": \"best_streak\", \"condition_value\": 5}', '217.219.119.181', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Mobile Safari/537.36', '2026-07-24 16:16:38'),
(868, 1, '', 'title', 3, 'ویرایش عنوان: برنده', '{\"id\": 3, \"code\": \"winner\", \"icon\": \"🏆\", \"name\": \"برنده\", \"priority\": 60, \"is_active\": 1, \"created_at\": \"2026-07-04 14:32:34\", \"description\": \"۱۰ برد کسب کرده\", \"bonus_points\": 10, \"condition_type\": \"total_wins\", \"condition_value\": 10}', '{\"code\": \"winner\", \"icon\": \"🏆\", \"name\": \"برنده\", \"priority\": 0, \"is_active\": 1, \"description\": \"۱۰ برد کسب کرده\", \"bonus_points\": 100, \"condition_type\": \"total_wins\", \"condition_value\": 10}', '217.219.119.181', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-24 16:29:07'),
(869, 1, '', 'title', 4, 'ویرایش عنوان: قهرمان', '{\"id\": 4, \"code\": \"champion\", \"icon\": \"👑\", \"name\": \"قهرمان\", \"priority\": 0, \"is_active\": 1, \"created_at\": \"2026-07-04 14:32:34\", \"description\": \"100 برد کسب کرده\", \"bonus_points\": 100, \"condition_type\": \"total_wins\", \"condition_value\": 100}', '{\"code\": \"champion\", \"icon\": \"👑\", \"name\": \"قهرمان\", \"priority\": 0, \"is_active\": 1, \"description\": \"100 برد کسب کرده\", \"bonus_points\": 500, \"condition_type\": \"total_wins\", \"condition_value\": 100}', '217.219.119.181', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-24 16:29:41'),
(870, 1, '', 'title', 7, 'ویرایش عنوان: رهبر تیم', '{\"id\": 7, \"code\": \"team_leader\", \"icon\": \"🤝\", \"name\": \"رهبر تیم\", \"priority\": 0, \"is_active\": 1, \"created_at\": \"2026-07-04 14:32:34\", \"description\": \"۱۰ برد تیمی داشته\", \"bonus_points\": 50, \"condition_type\": \"team_wins\", \"condition_value\": 10}', '{\"code\": \"team_leader\", \"icon\": \"🤝\", \"name\": \"رهبر تیم\", \"priority\": 0, \"is_active\": 1, \"description\": \"۱۰ برد تیمی داشته\", \"bonus_points\": 200, \"condition_type\": \"team_wins\", \"condition_value\": 10}', '217.219.119.181', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-24 16:35:18'),
(871, 1, '', 'card', 11, 'ویرایش کارت: پرش دوتایی', '{\"id\": 11, \"name\": \"پرش دوتایی\", \"slug\": \"double_skip\", \"emoji\": \"⏩\", \"rarity\": \"rare\", \"icon_path\": null, \"is_active\": 1, \"created_at\": \"2026-06-30 14:56:11\", \"sort_order\": 11, \"description\": \"دو نفر بعدی بازی نمی‌کنند\", \"is_action_card\": 1, \"score_multiplier\": \"5.00\"}', '{\"name\": \"پرش دوتایی\", \"slug\": \"double_skip\", \"emoji\": \"⏩\", \"rarity\": \"rare\", \"is_active\": 1, \"description\": \"دو نفر بعدی بازی نمی‌کنند\", \"score_multiplier\": 2}', '217.219.119.181', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-24 16:40:43'),
(872, 1, '', 'card', 15, 'ویرایش کارت: کینگ', '{\"id\": 15, \"name\": \"کینگ\", \"slug\": \"king\", \"emoji\": \"👑\", \"rarity\": \"legendary\", \"icon_path\": null, \"is_active\": 1, \"created_at\": \"2026-06-30 14:56:11\", \"sort_order\": 15, \"description\": \"کارت‌های همه را ببینید، یک کارت عملیاتی بدزدید، همه ۴ تا جریمه\", \"is_action_card\": 1, \"score_multiplier\": \"10.00\"}', '{\"name\": \"کینگ\", \"slug\": \"king\", \"emoji\": \"👑\", \"rarity\": \"legendary\", \"is_active\": 1, \"description\": \"کارت‌های همه را ببینید، یک کارت عملیاتی بدزدید، همه ۴ تا جریمه\", \"score_multiplier\": 20}', '217.219.119.181', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-24 16:42:47'),
(873, 1, '', 'card', 12, 'ویرایش کارت: شافل', '{\"id\": 12, \"name\": \"شافل\", \"slug\": \"shuffle\", \"emoji\": \"🌀\", \"rarity\": \"rare\", \"icon_path\": null, \"is_active\": 1, \"created_at\": \"2026-06-30 14:56:11\", \"sort_order\": 12, \"description\": \"همه کارت‌ها جمع و دوباره پخش می‌شوند\", \"is_action_card\": 1, \"score_multiplier\": \"5.00\"}', '{\"name\": \"شافل\", \"slug\": \"shuffle\", \"emoji\": \"🌀\", \"rarity\": \"rare\", \"is_active\": 1, \"description\": \"همه کارت‌ها جمع و دوباره پخش می‌شوند\", \"score_multiplier\": 10}', '217.219.119.181', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-24 16:42:59'),
(874, 1, '', 'card', 13, 'ویرایش کارت: قفل کردن', '{\"id\": 13, \"name\": \"قفل کردن\", \"slug\": \"lock\", \"emoji\": \"🔒\", \"rarity\": \"rare\", \"icon_path\": null, \"is_active\": 1, \"created_at\": \"2026-06-30 14:56:11\", \"sort_order\": 13, \"description\": \"یک نفر تا ۳ دور از کارت عملیاتی محروم می‌شود\", \"is_action_card\": 1, \"score_multiplier\": \"5.00\"}', '{\"name\": \"قفل کردن\", \"slug\": \"lock\", \"emoji\": \"🔒\", \"rarity\": \"rare\", \"is_active\": 1, \"description\": \"یک نفر تا ۳ دور از کارت عملیاتی محروم می‌شود\", \"score_multiplier\": 10}', '217.219.119.181', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-24 16:43:09'),
(875, 1, '', 'card', 14, 'ویرایش کارت: جریمه دوتایی انتخابی', '{\"id\": 14, \"name\": \"جریمه دوتایی انتخابی\", \"slug\": \"targeted_draw_two\", \"emoji\": \"🎯\", \"rarity\": \"rare\", \"icon_path\": null, \"is_active\": 1, \"created_at\": \"2026-06-30 14:56:11\", \"sort_order\": 14, \"description\": \"یک نفر را انتخاب و ۲ کارت جریمه کنید\", \"is_action_card\": 1, \"score_multiplier\": \"3.00\"}', '{\"name\": \"جریمه دوتایی انتخابی\", \"slug\": \"targeted_draw_two\", \"emoji\": \"🎯\", \"rarity\": \"rare\", \"is_active\": 1, \"description\": \"یک نفر را انتخاب و ۲ کارت جریمه کنید\", \"score_multiplier\": 5}', '217.219.119.181', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-24 16:46:12'),
(876, 1, '', 'card', 7, 'ویرایش کارت: سپر', '{\"id\": 7, \"name\": \"سپر\", \"slug\": \"shield\", \"emoji\": \"🛡️\", \"rarity\": \"rare\", \"icon_path\": null, \"is_active\": 1, \"created_at\": \"2026-06-30 14:56:11\", \"sort_order\": 7, \"description\": \"جریمه‌ها را خنثی می‌کند\", \"is_action_card\": 1, \"score_multiplier\": \"3.00\"}', '{\"name\": \"سپر\", \"slug\": \"shield\", \"emoji\": \"🛡️\", \"rarity\": \"rare\", \"is_active\": 1, \"description\": \"جریمه‌ها را خنثی می‌کند\", \"score_multiplier\": 5}', '217.219.119.181', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-24 16:46:29'),
(877, 1, '', 'card', 8, 'ویرایش کارت: دید زدن', '{\"id\": 8, \"name\": \"دید زدن\", \"slug\": \"peek\", \"emoji\": \"👁️\", \"rarity\": \"rare\", \"icon_path\": null, \"is_active\": 1, \"created_at\": \"2026-06-30 14:56:11\", \"sort_order\": 8, \"description\": \"کارت‌های یک نفر را می‌بینید\", \"is_action_card\": 1, \"score_multiplier\": \"2.00\"}', '{\"name\": \"دید زدن\", \"slug\": \"peek\", \"emoji\": \"👁️\", \"rarity\": \"common\", \"is_active\": 1, \"description\": \"کارت‌های یک نفر را می‌بینید\", \"score_multiplier\": 2}', '217.219.119.181', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Mobile Safari/537.36', '2026-07-24 21:44:06'),
(878, 1, '', 'card', 6, 'ویرایش کارت: جریمه ۴ تایی', '{\"id\": 6, \"name\": \"جریمه ۴ تایی\", \"slug\": \"wild_draw_four\", \"emoji\": \"+4\", \"rarity\": \"rare\", \"icon_path\": null, \"is_active\": 1, \"created_at\": \"2026-06-30 14:56:11\", \"sort_order\": 6, \"description\": \"نفر بعدی ۴ کارت می‌کشد و رنگ عوض می‌شود\", \"is_action_card\": 1, \"score_multiplier\": \"3.00\"}', '{\"name\": \"جریمه ۴ تایی\", \"slug\": \"wild_draw_four\", \"emoji\": \"+4\", \"rarity\": \"common\", \"is_active\": 1, \"description\": \"نفر بعدی ۴ کارت می‌کشد و رنگ عوض می‌شود\", \"score_multiplier\": 3}', '217.219.119.181', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Mobile Safari/537.36', '2026-07-24 21:44:21'),
(879, 1, '', 'card', 9, 'ویرایش کارت: هدیه', '{\"id\": 9, \"name\": \"هدیه\", \"slug\": \"gift\", \"emoji\": \"🎁\", \"rarity\": \"rare\", \"icon_path\": null, \"is_active\": 1, \"created_at\": \"2026-06-30 14:56:11\", \"sort_order\": 9, \"description\": \"۲ کارت خود را به یک نفر بدهید\", \"is_action_card\": 1, \"score_multiplier\": \"2.00\"}', '{\"name\": \"هدیه\", \"slug\": \"gift\", \"emoji\": \"🎁\", \"rarity\": \"common\", \"is_active\": 1, \"description\": \"۲ کارت خود را به یک نفر بدهید\", \"score_multiplier\": 2}', '217.219.119.181', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Mobile Safari/537.36', '2026-07-24 21:44:37'),
(880, 1, '', 'card', 11, 'ویرایش کارت: پرش دوتایی', '{\"id\": 11, \"name\": \"پرش دوتایی\", \"slug\": \"double_skip\", \"emoji\": \"⏩\", \"rarity\": \"rare\", \"icon_path\": null, \"is_active\": 1, \"created_at\": \"2026-06-30 14:56:11\", \"sort_order\": 11, \"description\": \"دو نفر بعدی بازی نمی‌کنند\", \"is_action_card\": 1, \"score_multiplier\": \"2.00\"}', '{\"name\": \"پرش دوتایی\", \"slug\": \"double_skip\", \"emoji\": \"⏩\", \"rarity\": \"common\", \"is_active\": 1, \"description\": \"دو نفر بعدی بازی نمی‌کنند\", \"score_multiplier\": 2}', '217.219.119.181', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Mobile Safari/537.36', '2026-07-24 21:45:11'),
(881, 1, '', 'user', 18, 'اعطای مجوز ساخت بازی به امپراطور', NULL, '{\"can_create_game\": 1}', '217.219.119.181', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Mobile Safari/537.36', '2026-07-26 19:22:27'),
(882, 1, '', 'user', 17, 'اعطای مجوز ساخت بازی به RANGER', NULL, '{\"can_create_game\": 1}', '217.219.119.181', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Mobile Safari/537.36', '2026-07-26 19:23:15'),
(883, 1, '', 'user', 17, 'سلب مجوز ساخت بازی از RANGER', NULL, '{\"can_create_game\": 0}', '217.219.119.181', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Mobile Safari/537.36', '2026-07-26 19:23:23'),
(884, 1, 'user_ban', 'user', 23, 'مسدود کردن کاربر: mohammadshirdel68', NULL, '{\"status\": \"banned\"}', '2.183.10.215', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Mobile Safari/537.36', '2026-08-01 17:31:39'),
(885, 1, '', 'user', 23, 'سلب مجوز شرکت در بازی از mohammadshirdel68 (ساخت بازی هم سلب شد)', NULL, '{\"can_join_game\": 0, \"can_create_game\": 0}', '2.183.10.215', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Mobile Safari/537.36', '2026-08-01 18:21:48'),
(886, 1, '', 'user', 16, 'اعطای مجوز ساخت بازی به KiNG', NULL, '{\"can_create_game\": 1}', '91.92.192.160', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Mobile Safari/537.36', '2026-08-02 18:59:34'),
(887, 1, '', 'card', 2, 'ویرایش کارت: پرش یکی', '{\"id\": 2, \"name\": \"پرش یکی\", \"slug\": \"skip\", \"emoji\": \"⏭️\", \"rarity\": \"common\", \"icon_path\": null, \"is_active\": 1, \"created_at\": \"2026-06-30 14:56:11\", \"sort_order\": 2, \"description\": \"نفر بعدی بازی نمی‌کند\", \"is_action_card\": 1, \"score_multiplier\": \"1.00\"}', '{\"name\": \"پرش یکی\", \"slug\": \"skip\", \"emoji\": \"▶️\", \"rarity\": \"common\", \"is_active\": 1, \"description\": \"نفر بعدی بازی نمی‌کند\", \"score_multiplier\": 1}', '91.133.199.176', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Mobile Safari/537.36', '2026-08-04 12:06:59'),
(888, 1, '', 'user', 17, 'اعطای مجوز ساخت بازی به RANGER', NULL, '{\"can_create_game\": 1}', '89.45.154.233', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-08-04 18:48:22'),
(889, 1, 'game_delete', 'game', 174, 'حذف بازی تقلبی/مشکوک و باز محاسبه خودکار آمار، XP، مدال و القاب تمام شرکت‌کنندگان - بازی: لیگ انفرادی - 1405/05/14', '{\"id\": 174, \"name\": \"لیگ انفرادی - 1405/05/14\", \"status\": \"cancelled\", \"game_mode\": \"solo\", \"created_at\": \"2026-08-05 17:34:40\", \"referee_id\": 16, \"started_at\": \"2026-08-05 17:34:56\", \"finished_at\": \"2026-08-05 17:44:02\", \"target_wins\": 3, \"total_rounds_played\": 5, \"winner_participant_id\": null, \"team_builder_algorithm\": \"manual\", \"first_player_participant_id\": 718}', NULL, '2.184.120.111', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Mobile Safari/537.36', '2026-08-05 14:14:48'),
(890, 1, '', 'user', 16, 'بازمحاسبه آمار و XP کاربر به دلیل احتمال تقلب', NULL, '{\"new_xp\": 3435, \"total_wins\": 5, \"total_games\": 18, \"total_points\": 1635}', '2.184.120.111', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Mobile Safari/537.36', '2026-08-05 14:15:37'),
(891, 1, '', 'user', 17, 'بازمحاسبه آمار و XP کاربر به دلیل احتمال تقلب', NULL, '{\"new_xp\": 7675, \"total_wins\": 10, \"total_games\": 23, \"total_points\": 3705}', '2.184.120.111', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Mobile Safari/537.36', '2026-08-05 14:16:04'),
(892, 1, '', 'level', 12, 'ویرایش سطح 12: جاودانه', '{\"id\": 12, \"icon\": \"🌠\", \"color\": \"#7c2d12\", \"level\": 12, \"title\": \"خدای UNO\", \"max_xp\": 20000000, \"min_xp\": 9999999}', '{\"icon\": \"🌠\", \"color\": \"#7c2d12\", \"title\": \"جاودانه\", \"max_xp\": 20000000, \"min_xp\": 9999999}', '5.119.36.55', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-08-07 07:01:29'),
(893, 1, '', 'user', NULL, 'باز محاسبه گروهی آمار: 9 موفق، 0 ناموفق از 9 کاربر (0.42 ثانیه)', NULL, '{\"total\": 9, \"failed\": 0, \"success\": 9, \"duration\": 0.42, \"failed_users\": []}', '91.92.192.168', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Mobile Safari/537.36', '2026-08-07 11:17:40'),
(894, 1, '', 'user', NULL, 'باز محاسبه گروهی آمار: 9 موفق، 0 ناموفق از 9 کاربر (0.45 ثانیه)', NULL, '{\"total\": 9, \"failed\": 0, \"success\": 9, \"duration\": 0.45, \"failed_users\": []}', '91.92.192.168', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Mobile Safari/537.36', '2026-08-07 12:56:40');

-- --------------------------------------------------------

--
-- Table structure for table `cards`
--

CREATE TABLE `cards` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `icon_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `emoji` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `rarity` enum('common','rare','legendary') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'common',
  `score_multiplier` decimal(4,2) DEFAULT '1.00',
  `is_action_card` tinyint(1) DEFAULT '0',
  `is_active` tinyint(1) DEFAULT '1',
  `sort_order` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cards`
--

INSERT INTO `cards` (`id`, `name`, `slug`, `icon_path`, `emoji`, `description`, `rarity`, `score_multiplier`, `is_action_card`, `is_active`, `sort_order`, `created_at`) VALUES
(1, 'کارت عددی', 'number', NULL, '🔢', 'کارت‌های معمولی با اعداد ۰ تا ۹', 'common', 1.00, 0, 1, 0, '2026-06-30 11:26:11'),
(2, 'پرش یکی', 'skip', NULL, '▶️', 'نفر بعدی بازی نمی‌کند', 'common', 1.00, 1, 1, 2, '2026-06-30 11:26:11'),
(3, 'دوربرگردان', 'reverse', NULL, '🔄', 'جهت بازی عوض می‌شود', 'common', 1.00, 1, 1, 3, '2026-06-30 11:26:11'),
(4, 'جریمه دوتایی', 'draw_two', NULL, '+2', 'نفر بعدی ۲ کارت می‌کشد', 'common', 2.00, 1, 1, 4, '2026-06-30 11:26:11'),
(5, 'تغییر رنگ', 'wild_color', NULL, '🌈', 'رنگ بازی را تغییر می‌دهد', 'common', 2.00, 1, 1, 5, '2026-06-30 11:26:11'),
(6, 'جریمه ۴ تایی', 'wild_draw_four', NULL, '+4', 'نفر بعدی ۴ کارت می‌کشد و رنگ عوض می‌شود', 'common', 3.00, 1, 1, 6, '2026-06-30 11:26:11'),
(7, 'سپر', 'shield', NULL, '🛡️', 'جریمه‌ها را خنثی می‌کند', 'rare', 5.00, 1, 1, 7, '2026-06-30 11:26:11'),
(8, 'دید زدن', 'peek', NULL, '👁️', 'کارت‌های یک نفر را می‌بینید', 'common', 2.00, 1, 1, 8, '2026-06-30 11:26:11'),
(9, 'هدیه', 'gift', NULL, '🎁', '۲ کارت خود را به یک نفر بدهید', 'common', 2.00, 1, 1, 9, '2026-06-30 11:26:11'),
(10, 'تعویض', 'swap', NULL, '🔀', 'کارت‌هایتان را با یک نفر عوض کنید', 'rare', 4.00, 1, 0, 10, '2026-06-30 11:26:11'),
(11, 'پرش دوتایی', 'double_skip', NULL, '⏩', 'دو نفر بعدی بازی نمی‌کنند', 'common', 2.00, 1, 1, 11, '2026-06-30 11:26:11'),
(12, 'شافل', 'shuffle', NULL, '🌀', 'همه کارت‌ها جمع و دوباره پخش می‌شوند', 'rare', 10.00, 1, 1, 12, '2026-06-30 11:26:11'),
(13, 'قفل کردن', 'lock', NULL, '🔒', 'یک نفر تا ۳ دور از کارت عملیاتی محروم می‌شود', 'rare', 10.00, 1, 1, 13, '2026-06-30 11:26:11'),
(14, 'جریمه دوتایی انتخابی', 'targeted_draw_two', NULL, '🎯', 'یک نفر را انتخاب و ۲ کارت جریمه کنید', 'rare', 5.00, 1, 1, 14, '2026-06-30 11:26:11'),
(15, 'کینگ', 'king', NULL, '👑', 'کارت‌های همه را ببینید، یک کارت عملیاتی بدزدید، همه ۴ تا جریمه', 'legendary', 20.00, 1, 1, 15, '2026-06-30 11:26:11');

-- --------------------------------------------------------

--
-- Table structure for table `card_mastery`
--

CREATE TABLE `card_mastery` (
  `id` int UNSIGNED NOT NULL,
  `user_id` int UNSIGNED NOT NULL,
  `card_id` int UNSIGNED NOT NULL,
  `total_wins` int UNSIGNED DEFAULT '0',
  `current_streak` int DEFAULT '0',
  `max_streak` int DEFAULT '0',
  `last_won_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `card_mastery`
--

INSERT INTO `card_mastery` (`id`, `user_id`, `card_id`, `total_wins`, `current_streak`, `max_streak`, `last_won_at`, `updated_at`) VALUES
(63, 18, 1, 29, 29, 29, '2026-08-07 12:37:12', '2026-08-07 12:37:12'),
(64, 18, 9, 13, 13, 13, '2026-08-06 11:36:47', '2026-08-06 11:36:47'),
(65, 17, 1, 46, 46, 46, '2026-08-07 12:48:20', '2026-08-07 12:48:20'),
(66, 1, 9, 30, 30, 30, '2026-08-07 12:45:02', '2026-08-07 12:45:02'),
(67, 1, 1, 44, 44, 44, '2026-08-07 11:41:17', '2026-08-07 11:41:17'),
(68, 17, 9, 21, 21, 21, '2026-08-07 12:42:24', '2026-08-07 12:42:24'),
(69, 16, 9, 24, 24, 24, '2026-08-06 13:06:47', '2026-08-06 13:06:47'),
(70, 17, 14, 3, 3, 3, '2026-08-05 15:09:08', '2026-08-05 15:09:08'),
(71, 1, 7, 11, 11, 11, '2026-08-04 15:01:23', '2026-08-04 15:01:23'),
(72, 16, 1, 30, 30, 30, '2026-08-07 12:34:50', '2026-08-07 12:34:50'),
(73, 17, 2, 2, 2, 2, '2026-08-03 20:53:43', '2026-08-03 20:53:43'),
(74, 1, 5, 6, 6, 6, '2026-08-07 12:32:17', '2026-08-07 12:32:17'),
(75, 17, 10, 1, 1, 1, '2026-07-23 12:48:47', '2026-07-23 12:48:47'),
(76, 16, 14, 5, 5, 5, '2026-08-07 12:39:34', '2026-08-07 12:39:34'),
(77, 1, 6, 4, 4, 4, '2026-08-06 12:52:50', '2026-08-06 12:52:50'),
(78, 16, 3, 3, 3, 3, '2026-08-06 13:14:03', '2026-08-06 13:14:03'),
(79, 16, 11, 3, 3, 3, '2026-08-05 15:05:16', '2026-08-05 15:05:16'),
(80, 17, 5, 4, 4, 4, '2026-08-03 19:49:12', '2026-08-03 19:49:12'),
(81, 17, 4, 4, 4, 4, '2026-08-03 20:02:43', '2026-08-03 20:02:43'),
(82, 17, 11, 6, 6, 6, '2026-08-05 14:06:42', '2026-08-05 14:06:42'),
(83, 21, 1, 2, 2, 2, '2026-08-07 11:09:40', '2026-08-07 11:09:40'),
(84, 20, 7, 1, 1, 1, '2026-07-24 13:13:52', '2026-07-24 13:13:52'),
(85, 20, 1, 2, 2, 2, '2026-07-28 15:09:47', '2026-07-28 15:09:47'),
(86, 20, 5, 2, 2, 2, '2026-07-28 14:41:50', '2026-07-28 14:41:50'),
(87, 20, 14, 2, 2, 2, '2026-07-24 13:42:56', '2026-07-24 13:42:56'),
(88, 19, 15, 1, 1, 1, '2026-07-24 13:45:45', '2026-07-24 13:45:45'),
(89, 20, 9, 2, 2, 2, '2026-07-28 14:51:36', '2026-07-28 14:51:36'),
(90, 21, 4, 1, 1, 1, '2026-07-24 14:29:07', '2026-07-24 14:29:07'),
(91, 16, 2, 1, 1, 1, '2026-07-24 15:04:32', '2026-07-24 15:04:32'),
(92, 16, 5, 3, 3, 3, '2026-08-06 12:41:36', '2026-08-06 12:41:36'),
(93, 16, 13, 6, 6, 6, '2026-08-07 11:13:43', '2026-08-07 11:13:43'),
(94, 16, 12, 5, 5, 5, '2026-08-07 12:55:20', '2026-08-07 12:55:20'),
(95, 1, 13, 6, 6, 6, '2026-08-07 11:51:28', '2026-08-07 11:51:28'),
(96, 1, 15, 8, 8, 8, '2026-08-05 16:01:44', '2026-08-05 16:01:44'),
(97, 17, 7, 2, 2, 2, '2026-08-07 12:16:29', '2026-08-07 12:16:29'),
(98, 1, 12, 6, 6, 6, '2026-08-06 10:47:15', '2026-08-06 10:47:15'),
(99, 17, 15, 6, 6, 6, '2026-08-05 14:30:02', '2026-08-05 14:30:02'),
(100, 1, 11, 6, 6, 6, '2026-08-05 14:35:28', '2026-08-05 14:35:28'),
(101, 17, 12, 6, 6, 6, '2026-08-05 15:43:52', '2026-08-05 15:43:52'),
(102, 16, 6, 1, 1, 1, '2026-07-25 13:14:53', '2026-07-25 13:14:53'),
(103, 1, 3, 1, 1, 1, '2026-07-25 13:36:26', '2026-07-25 13:36:26'),
(104, 18, 7, 5, 5, 5, '2026-08-06 11:52:29', '2026-08-06 11:52:29'),
(105, 18, 13, 2, 2, 2, '2026-08-03 19:39:14', '2026-08-03 19:39:14'),
(106, 17, 6, 2, 2, 2, '2026-07-25 15:25:12', '2026-07-25 15:25:12'),
(107, 16, 7, 5, 5, 5, '2026-08-07 11:33:21', '2026-08-07 11:33:21'),
(108, 24, 4, 1, 1, 1, '2026-07-28 14:59:27', '2026-07-28 14:59:27'),
(109, 18, 12, 4, 4, 4, '2026-08-06 12:01:56', '2026-08-06 12:01:56'),
(110, 24, 1, 3, 3, 3, '2026-08-01 14:57:46', '2026-08-01 14:57:46'),
(111, 24, 9, 2, 2, 2, '2026-08-01 15:06:54', '2026-08-01 15:06:54'),
(112, 24, 5, 1, 1, 1, '2026-07-31 12:02:57', '2026-07-31 12:02:57'),
(113, 18, 6, 5, 5, 5, '2026-08-06 13:16:09', '2026-08-06 13:16:09'),
(114, 24, 12, 1, 1, 1, '2026-08-01 15:16:51', '2026-08-01 15:16:51'),
(115, 18, 4, 1, 1, 1, '2026-08-03 20:25:11', '2026-08-03 20:25:11'),
(116, 16, 15, 4, 4, 4, '2026-08-07 12:05:40', '2026-08-07 12:05:40'),
(117, 18, 15, 2, 2, 2, '2026-08-04 14:55:04', '2026-08-04 14:55:04'),
(118, 18, 14, 1, 1, 1, '2026-08-04 14:32:36', '2026-08-04 14:32:36'),
(119, 1, 14, 1, 1, 1, '2026-08-05 11:47:33', '2026-08-05 11:47:33'),
(120, 16, 4, 2, 2, 2, '2026-08-05 15:39:40', '2026-08-05 15:39:40'),
(121, 1, 8, 1, 1, 1, '2026-08-06 10:55:42', '2026-08-06 10:55:42'),
(122, 18, 8, 1, 1, 1, '2026-08-06 11:11:47', '2026-08-06 11:11:47'),
(123, 21, 6, 1, 1, 1, '2026-08-07 11:11:15', '2026-08-07 11:11:15'),
(124, 21, 15, 1, 1, 1, '2026-08-07 11:22:39', '2026-08-07 11:22:39'),
(125, 21, 9, 1, 1, 1, '2026-08-07 11:46:05', '2026-08-07 11:46:05');

-- --------------------------------------------------------

--
-- Table structure for table `games`
--

CREATE TABLE `games` (
  `id` int UNSIGNED NOT NULL,
  `referee_id` int UNSIGNED NOT NULL,
  `name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `game_mode` enum('solo','friendly') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'solo',
  `target_wins` int UNSIGNED NOT NULL DEFAULT '10',
  `status` enum('pending','active','paused','finished','cancelled') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `winner_participant_id` int UNSIGNED DEFAULT NULL,
  `winner_team_id` int UNSIGNED DEFAULT NULL,
  `team_builder_algorithm` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `first_player_participant_id` int UNSIGNED DEFAULT NULL,
  `total_rounds_played` int UNSIGNED DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `started_at` timestamp NULL DEFAULT NULL,
  `finished_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `games`
--

INSERT INTO `games` (`id`, `referee_id`, `name`, `game_mode`, `target_wins`, `status`, `winner_participant_id`, `winner_team_id`, `team_builder_algorithm`, `first_player_participant_id`, `total_rounds_played`, `created_at`, `started_at`, `finished_at`) VALUES
(146, 1, 'لیگ انفرادی - 1405/04/31', 'solo', 10, 'finished', 612, NULL, 'manual', 611, 22, '2026-07-22 13:37:02', '2026-07-23 14:15:06', '2026-07-23 15:17:08'),
(147, 1, 'لیگ انفرادی - 1405/04/31', 'solo', 5, 'finished', 615, NULL, 'manual', 616, 16, '2026-07-22 13:41:23', '2026-07-22 13:42:00', '2026-07-22 15:31:06'),
(148, 1, 'لیگ انفرادی - 1405/04/31', 'solo', 5, 'finished', 619, NULL, 'manual', 618, 10, '2026-07-22 18:46:11', '2026-07-22 18:46:43', '2026-07-22 19:22:32'),
(149, 1, 'لیگ انفرادی - 1405/05/01', 'solo', 5, 'finished', 622, NULL, 'manual', 622, 9, '2026-07-23 12:34:12', '2026-07-23 12:35:15', '2026-07-23 13:15:32'),
(150, 1, 'لیگ دوستانه - 1405/05/01', 'friendly', 5, 'finished', 625, 202, 'balanced', 628, 9, '2026-07-23 13:17:08', '2026-07-23 13:17:31', '2026-07-23 13:51:08'),
(151, 1, 'لیگ دوستانه - 1405/05/01', 'friendly', 3, 'finished', 631, 205, 'manual', 630, 5, '2026-07-23 13:51:13', '2026-07-23 13:51:18', '2026-07-23 14:13:45'),
(152, 1, 'لیگ انفرادی - 1405/05/02', 'solo', 3, 'finished', 635, NULL, 'manual', 636, 6, '2026-07-24 12:55:51', '2026-07-24 12:57:43', '2026-07-24 13:22:28'),
(153, 18, 'لیگ انفرادی - 1405/05/02', 'solo', 3, 'finished', 637, NULL, 'manual', 637, 7, '2026-07-24 13:22:43', '2026-07-24 13:22:56', '2026-07-24 13:54:30'),
(154, 1, 'لیگ انفرادی - 1405/05/02', 'solo', 3, 'finished', 644, NULL, 'manual', 643, 7, '2026-07-24 13:55:54', '2026-07-24 13:56:04', '2026-07-24 14:34:03'),
(155, 1, 'لیگ انفرادی - 1405/05/02', 'solo', 10, 'finished', 649, NULL, 'manual', 647, 20, '2026-07-24 14:35:46', '2026-07-24 14:35:53', '2026-07-24 15:48:04'),
(156, 1, 'لیگ انفرادی - 1405/05/03', 'solo', 10, 'finished', 652, NULL, 'manual', 651, 24, '2026-07-25 11:58:11', '2026-07-25 11:59:31', '2026-07-25 12:59:32'),
(157, 1, 'لیگ انفرادی - 1405/05/03', 'solo', 3, 'finished', 653, NULL, 'manual', 655, 7, '2026-07-25 13:00:25', '2026-07-25 13:00:35', '2026-07-25 13:19:02'),
(158, 1, 'لیگ انفرادی - 1405/05/03', 'solo', 3, 'finished', 659, NULL, 'manual', 657, 7, '2026-07-25 13:20:14', '2026-07-25 13:20:21', '2026-07-25 13:50:48'),
(159, 1, 'لیگ دوستانه - 1405/05/03', 'friendly', 5, 'finished', 662, 207, 'random', 663, 7, '2026-07-25 13:52:39', '2026-07-25 13:52:44', '2026-07-25 14:18:18'),
(160, 1, 'لیگ دوستانه - 1405/05/03', 'friendly', 5, 'finished', 664, 208, 'random', 667, 8, '2026-07-25 14:19:20', '2026-07-25 14:19:25', '2026-07-25 14:49:17'),
(161, 1, 'لیگ دوستانه - 1405/05/03', 'friendly', 3, 'finished', 668, 210, 'manual', 668, 3, '2026-07-25 14:50:08', '2026-07-25 14:50:12', '2026-07-25 15:32:18'),
(162, 1, 'لیگ انفرادی - 1405/05/03', 'solo', 5, 'finished', 673, NULL, 'manual', 673, 11, '2026-07-25 14:51:24', '2026-07-25 14:51:37', '2026-07-25 15:17:32'),
(163, 18, 'لیگ انفرادی - 1405/05/06', 'solo', 3, 'finished', 678, NULL, 'manual', 677, 7, '2026-07-28 14:36:04', '2026-07-28 14:36:33', '2026-07-28 15:09:57'),
(164, 18, 'لیگ انفرادی - 1405/05/09', 'solo', 3, 'cancelled', NULL, NULL, 'manual', 681, 0, '2026-07-31 11:40:32', '2026-07-31 11:40:47', '2026-07-31 11:45:00'),
(165, 18, 'لیگ انفرادی - 1405/05/09', 'solo', 3, 'finished', 688, NULL, 'manual', 689, 4, '2026-07-31 11:45:35', '2026-07-31 11:45:54', '2026-07-31 12:03:10'),
(166, 18, 'لیگ انفرادی - 1405/05/10', 'solo', 3, 'finished', 690, NULL, 'manual', 692, 7, '2026-08-01 14:45:57', '2026-08-01 14:46:04', '2026-08-01 15:11:44'),
(167, 18, 'لیگ دوستانه - 1405/05/10', 'friendly', 3, 'finished', 693, 213, 'manual', 694, 4, '2026-08-01 15:13:43', '2026-08-01 15:13:53', '2026-08-01 15:28:08'),
(168, 1, 'لیگ انفرادی - 1405/05/12', 'solo', 10, 'finished', 697, NULL, 'manual', 697, 25, '2026-08-03 19:09:20', '2026-08-03 19:15:19', '2026-08-03 20:27:47'),
(169, 1, 'لیگ انفرادی - 1405/05/12', 'solo', 5, 'finished', 700, NULL, 'manual', 700, 12, '2026-08-03 20:28:57', '2026-08-03 20:29:02', '2026-08-03 21:03:45'),
(170, 1, 'لیگ انفرادی - 1405/05/13', 'solo', 10, 'finished', 705, NULL, 'manual', 704, 20, '2026-08-04 12:41:53', '2026-08-04 12:41:59', '2026-08-04 15:13:03'),
(171, 1, 'لیگ انفرادی - 1405/05/13', 'solo', 5, 'finished', 707, NULL, 'manual', 708, 6, '2026-08-04 13:27:04', '2026-08-04 13:27:20', '2026-08-04 13:44:01'),
(172, 1, 'لیگ دوستانه - 1405/05/13', 'friendly', 10, 'finished', 711, 215, 'balanced', 713, 17, '2026-08-04 13:45:24', '2026-08-04 13:45:30', '2026-08-04 14:58:44'),
(173, 1, 'لیگ انفرادی - 1405/05/14', 'solo', 5, 'finished', 715, NULL, 'manual', 716, 13, '2026-08-05 11:26:56', '2026-08-05 11:27:06', '2026-08-05 12:12:59'),
(175, 1, 'لیگ انفرادی - 1405/05/14', 'solo', 10, 'finished', 721, NULL, 'manual', 719, 23, '2026-08-05 14:17:21', '2026-08-05 14:17:31', '2026-08-05 15:03:44'),
(176, 1, 'لیگ انفرادی - 1405/05/14', 'solo', 5, 'finished', 722, NULL, 'manual', 724, 8, '2026-08-05 15:04:13', '2026-08-05 15:04:19', '2026-08-05 15:21:23'),
(177, 1, 'لیگ دوستانه - 1405/05/14', 'friendly', 5, 'finished', 727, 216, 'random', 725, 9, '2026-08-05 15:22:22', '2026-08-05 15:22:31', '2026-08-05 16:09:52'),
(178, 1, 'لیگ انفرادی - 1405/05/15', 'solo', 5, 'finished', 731, NULL, 'manual', 731, 11, '2026-08-06 10:39:56', '2026-08-06 10:40:01', '2026-08-06 11:31:39'),
(179, 1, 'لیگ انفرادی - 1405/05/15', 'solo', 5, 'finished', 736, NULL, 'manual', 734, 15, '2026-08-06 11:33:53', '2026-08-06 11:33:57', '2026-08-06 12:28:10'),
(180, 1, 'لیگ دوستانه - 1405/05/15', 'friendly', 5, 'finished', 737, 219, 'random', 739, 6, '2026-08-06 12:29:49', '2026-08-06 12:29:55', '2026-08-06 12:49:56'),
(181, 1, 'لیگ دوستانه - 1405/05/15', 'friendly', 5, 'finished', 741, 220, 'random', 742, 9, '2026-08-06 12:50:44', '2026-08-06 12:50:50', '2026-08-06 13:20:08'),
(182, 17, 'لیگ انفرادی - 1405/05/16', 'solo', 3, 'finished', 748, NULL, 'manual', 746, 4, '2026-08-07 11:05:50', '2026-08-07 11:05:57', '2026-08-07 11:22:44'),
(183, 1, 'لیگ انفرادی - 1405/05/16', 'solo', 3, 'finished', 752, NULL, 'manual', 753, 5, '2026-08-07 11:24:46', '2026-08-07 11:25:10', '2026-08-07 11:51:35'),
(184, 1, 'لیگ دوستانه - 1405/05/16', 'friendly', 5, 'finished', NULL, 222, 'balanced', 755, 6, '2026-08-07 11:53:29', '2026-08-07 11:53:46', '2026-08-07 12:20:38'),
(185, 1, 'لیگ دوستانه - 1405/05/16', 'friendly', 5, 'finished', NULL, 225, 'balanced', 758, 9, '2026-08-07 12:22:16', '2026-08-07 12:22:23', '2026-08-07 12:55:27');

-- --------------------------------------------------------

--
-- Table structure for table `game_participants`
--

CREATE TABLE `game_participants` (
  `id` int UNSIGNED NOT NULL,
  `game_id` int UNSIGNED NOT NULL,
  `user_id` int UNSIGNED DEFAULT NULL,
  `team_id` int UNSIGNED DEFAULT NULL,
  `guest_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `wins_count` int UNSIGNED DEFAULT '0',
  `total_score` decimal(10,2) DEFAULT '0.00',
  `is_winner` tinyint(1) DEFAULT '0',
  `joined_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `game_participants`
--

INSERT INTO `game_participants` (`id`, `game_id`, `user_id`, `team_id`, `guest_name`, `wins_count`, `total_score`, `is_winner`, `joined_at`) VALUES
(611, 146, 16, NULL, NULL, 5, 120.00, 0, '2026-07-22 13:37:02'),
(612, 146, 17, NULL, NULL, 10, 220.00, 1, '2026-07-22 13:37:02'),
(613, 146, 1, NULL, NULL, 7, 130.00, 0, '2026-07-22 13:37:02'),
(614, 147, 16, NULL, NULL, 3, 50.00, 0, '2026-07-22 13:41:23'),
(615, 147, 17, NULL, NULL, 5, 90.00, 1, '2026-07-22 13:41:23'),
(616, 147, 18, NULL, NULL, 4, 50.00, 0, '2026-07-22 13:41:23'),
(617, 147, 1, NULL, NULL, 4, 70.00, 0, '2026-07-22 13:41:23'),
(618, 148, 17, NULL, NULL, 3, 30.00, 0, '2026-07-22 18:46:11'),
(619, 148, 18, NULL, NULL, 5, 70.00, 1, '2026-07-22 18:46:11'),
(620, 148, 1, NULL, NULL, 2, 40.00, 0, '2026-07-22 18:46:11'),
(621, 149, 16, NULL, NULL, 0, 0.00, 0, '2026-07-23 12:34:12'),
(622, 149, 17, NULL, NULL, 5, 95.00, 1, '2026-07-23 12:34:12'),
(623, 149, 18, NULL, NULL, 1, 10.00, 0, '2026-07-23 12:34:12'),
(624, 149, 1, NULL, NULL, 3, 60.00, 0, '2026-07-23 12:34:12'),
(625, 150, 16, 202, NULL, 5, 105.00, 1, '2026-07-23 13:17:08'),
(626, 150, 17, 202, NULL, 0, 70.00, 1, '2026-07-23 13:17:08'),
(627, 150, 18, 203, NULL, 0, 70.00, 0, '2026-07-23 13:17:08'),
(628, 150, 1, 203, NULL, 4, 105.00, 0, '2026-07-23 13:17:08'),
(629, 151, 16, 204, NULL, 1, 25.00, 0, '2026-07-23 13:51:13'),
(630, 151, 17, 204, NULL, 1, 25.00, 0, '2026-07-23 13:51:13'),
(631, 151, 18, 205, NULL, 0, 30.00, 1, '2026-07-23 13:51:13'),
(632, 151, 1, 205, NULL, 3, 45.00, 1, '2026-07-23 13:51:13'),
(633, 152, 18, NULL, NULL, 0, 0.00, 0, '2026-07-24 12:55:51'),
(634, 152, 1, NULL, NULL, 2, 15.00, 0, '2026-07-24 12:55:51'),
(635, 152, 20, NULL, NULL, 3, 30.00, 1, '2026-07-24 12:55:51'),
(636, 152, 21, NULL, NULL, 1, 5.00, 0, '2026-07-24 12:55:51'),
(637, 153, 16, NULL, NULL, 3, 30.00, 1, '2026-07-24 13:22:43'),
(638, 153, 19, NULL, NULL, 1, 50.00, 0, '2026-07-24 13:22:43'),
(639, 153, 18, NULL, NULL, 1, 5.00, 0, '2026-07-24 13:22:43'),
(640, 153, 20, NULL, NULL, 2, 30.00, 0, '2026-07-24 13:22:43'),
(641, 153, 21, NULL, NULL, 0, 0.00, 0, '2026-07-24 13:22:43'),
(642, 154, 16, NULL, NULL, 2, 15.00, 0, '2026-07-24 13:55:54'),
(643, 154, 18, NULL, NULL, 0, 0.00, 0, '2026-07-24 13:55:54'),
(644, 154, 1, NULL, NULL, 3, 20.00, 1, '2026-07-24 13:55:54'),
(645, 154, 20, NULL, NULL, 1, 10.00, 0, '2026-07-24 13:55:54'),
(646, 154, 21, NULL, NULL, 1, 10.00, 0, '2026-07-24 13:55:54'),
(647, 155, 16, NULL, NULL, 7, 90.00, 0, '2026-07-24 14:35:46'),
(648, 155, 18, NULL, NULL, 3, 20.00, 0, '2026-07-24 14:35:46'),
(649, 155, 1, NULL, NULL, 10, 90.00, 1, '2026-07-24 14:35:46'),
(650, 156, 16, NULL, NULL, 7, 155.00, 0, '2026-07-25 11:58:11'),
(651, 156, 17, NULL, NULL, 7, 170.00, 0, '2026-07-25 11:58:11'),
(652, 156, 1, NULL, NULL, 10, 270.00, 1, '2026-07-25 11:58:11'),
(653, 157, 16, NULL, NULL, 3, 35.00, 1, '2026-07-25 13:00:25'),
(654, 157, 17, NULL, NULL, 2, 55.00, 0, '2026-07-25 13:00:25'),
(655, 157, 1, NULL, NULL, 2, 145.00, 0, '2026-07-25 13:00:25'),
(656, 158, 16, NULL, NULL, 1, 20.00, 0, '2026-07-25 13:20:14'),
(657, 158, 17, NULL, NULL, 1, 50.00, 0, '2026-07-25 13:20:15'),
(658, 158, 18, NULL, NULL, 2, 35.00, 0, '2026-07-25 13:20:15'),
(659, 158, 1, NULL, NULL, 3, 50.00, 1, '2026-07-25 13:20:15'),
(660, 159, 16, 206, NULL, 1, 40.00, 0, '2026-07-25 13:52:39'),
(661, 159, 17, 206, NULL, 1, 40.00, 0, '2026-07-25 13:52:39'),
(662, 159, 18, 207, NULL, 1, 440.00, 1, '2026-07-25 13:52:39'),
(663, 159, 1, 207, NULL, 4, 440.00, 1, '2026-07-25 13:52:39'),
(664, 160, 16, 208, NULL, 2, 300.00, 1, '2026-07-25 14:19:20'),
(665, 160, 17, 208, NULL, 3, 300.00, 1, '2026-07-25 14:19:20'),
(666, 160, 18, 209, NULL, 0, 60.00, 0, '2026-07-25 14:19:20'),
(667, 160, 1, 209, NULL, 3, 60.00, 0, '2026-07-25 14:19:20'),
(668, 161, 16, 210, NULL, 1, 120.00, 1, '2026-07-25 14:50:08'),
(669, 161, 17, 210, NULL, 2, 120.00, 1, '2026-07-25 14:50:08'),
(670, 161, 18, 211, NULL, 0, 0.00, 0, '2026-07-25 14:50:08'),
(671, 161, 1, 211, NULL, 0, 0.00, 0, '2026-07-25 14:50:08'),
(672, 162, 16, NULL, NULL, 1, 20.00, 0, '2026-07-25 14:51:24'),
(673, 162, 17, NULL, NULL, 5, 90.00, 1, '2026-07-25 14:51:24'),
(674, 162, 1, NULL, NULL, 5, 305.00, 0, '2026-07-25 14:51:24'),
(675, 163, 17, NULL, NULL, 1, 15.00, 0, '2026-07-28 14:36:04'),
(676, 163, 18, NULL, NULL, 1, 60.00, 0, '2026-07-28 14:36:04'),
(677, 163, 24, NULL, NULL, 2, 15.00, 0, '2026-07-28 14:36:04'),
(678, 163, 20, NULL, NULL, 3, 25.00, 1, '2026-07-28 14:36:04'),
(679, 163, 21, NULL, NULL, 0, 0.00, 0, '2026-07-28 14:36:04'),
(680, 164, 17, NULL, NULL, 0, 0.00, 0, '2026-07-31 11:40:32'),
(681, 164, 18, NULL, NULL, 0, 0.00, 0, '2026-07-31 11:40:32'),
(682, 164, 24, NULL, NULL, 0, 0.00, 0, '2026-07-31 11:40:32'),
(683, 164, 1, NULL, NULL, 0, 0.00, 0, '2026-07-31 11:40:32'),
(684, 164, 20, NULL, NULL, 0, 0.00, 0, '2026-07-31 11:40:32'),
(685, 164, 21, NULL, NULL, 0, 0.00, 0, '2026-07-31 11:40:32'),
(686, 165, 17, NULL, NULL, 1, 15.00, 0, '2026-07-31 11:45:35'),
(687, 165, 18, NULL, NULL, 0, 0.00, 0, '2026-07-31 11:45:35'),
(688, 165, 24, NULL, NULL, 3, 25.00, 1, '2026-07-31 11:45:35'),
(689, 165, 20, NULL, NULL, 0, 0.00, 0, '2026-07-31 11:45:35'),
(690, 166, 17, NULL, NULL, 3, 45.00, 1, '2026-08-01 14:45:57'),
(691, 166, 18, NULL, NULL, 2, 40.00, 0, '2026-08-01 14:45:57'),
(692, 166, 24, NULL, NULL, 2, 15.00, 0, '2026-08-01 14:45:57'),
(693, 167, 17, 213, NULL, 1, 270.00, 1, '2026-08-01 15:13:43'),
(694, 167, 18, 213, NULL, 2, 270.00, 1, '2026-08-01 15:13:43'),
(695, 167, 24, 212, NULL, 1, 100.00, 0, '2026-08-01 15:13:43'),
(696, 167, 21, 212, NULL, 0, 100.00, 0, '2026-08-01 15:13:43'),
(697, 168, 17, NULL, NULL, 10, 315.00, 1, '2026-08-03 19:09:20'),
(698, 168, 18, NULL, NULL, 9, 225.00, 0, '2026-08-03 19:09:20'),
(699, 168, 1, NULL, NULL, 6, 230.00, 0, '2026-08-03 19:09:20'),
(700, 169, 17, NULL, NULL, 5, 85.00, 1, '2026-08-03 20:28:57'),
(701, 169, 18, NULL, NULL, 4, 65.00, 0, '2026-08-03 20:28:57'),
(702, 169, 1, NULL, NULL, 3, 75.00, 0, '2026-08-03 20:28:57'),
(703, 170, 16, NULL, NULL, 7, 270.00, 0, '2026-08-04 12:41:53'),
(704, 170, 17, NULL, NULL, 3, 140.00, 0, '2026-08-04 12:41:53'),
(705, 170, 1, NULL, NULL, 10, 195.00, 1, '2026-08-04 12:41:53'),
(706, 171, 16, NULL, NULL, 0, 0.00, 0, '2026-08-04 13:27:04'),
(707, 171, 17, NULL, NULL, 5, 90.00, 1, '2026-08-04 13:27:04'),
(708, 171, 18, NULL, NULL, 0, 0.00, 0, '2026-08-04 13:27:04'),
(709, 171, 1, NULL, NULL, 1, 60.00, 0, '2026-08-04 13:27:04'),
(710, 172, 16, 214, NULL, 4, 240.00, 0, '2026-08-04 13:45:24'),
(711, 172, 17, 215, NULL, 1, 810.00, 1, '2026-08-04 13:45:24'),
(712, 172, 18, 215, NULL, 9, 810.00, 1, '2026-08-04 13:45:24'),
(713, 172, 1, 214, NULL, 3, 240.00, 0, '2026-08-04 13:45:24'),
(714, 173, 17, NULL, NULL, 4, 565.00, 0, '2026-08-05 11:26:56'),
(715, 173, 18, NULL, NULL, 5, 90.00, 1, '2026-08-05 11:26:56'),
(716, 173, 1, NULL, NULL, 4, 135.00, 0, '2026-08-05 11:26:56'),
(719, 175, 16, NULL, NULL, 7, 115.00, 0, '2026-08-05 14:17:21'),
(720, 175, 17, NULL, NULL, 6, 755.00, 0, '2026-08-05 14:17:21'),
(721, 175, 1, NULL, NULL, 10, 365.00, 1, '2026-08-05 14:17:21'),
(722, 176, 16, NULL, NULL, 5, 130.00, 1, '2026-08-05 15:04:13'),
(723, 176, 17, NULL, NULL, 2, 230.00, 0, '2026-08-05 15:04:13'),
(724, 176, 1, NULL, NULL, 1, 110.00, 0, '2026-08-05 15:04:13'),
(725, 177, 16, 217, NULL, 3, 320.00, 0, '2026-08-05 15:22:22'),
(726, 177, 17, 217, NULL, 1, 320.00, 0, '2026-08-05 15:22:22'),
(727, 177, 18, 216, NULL, 1, 320.00, 1, '2026-08-05 15:22:22'),
(728, 177, 1, 216, NULL, 4, 320.00, 1, '2026-08-05 15:22:22'),
(729, 178, 16, NULL, NULL, 1, 15.00, 0, '2026-08-06 10:39:56'),
(730, 178, 17, NULL, NULL, 2, 210.00, 0, '2026-08-06 10:39:56'),
(731, 178, 18, NULL, NULL, 5, 85.00, 1, '2026-08-06 10:39:56'),
(732, 178, 1, NULL, NULL, 3, 100.00, 0, '2026-08-06 10:39:56'),
(733, 179, 16, NULL, NULL, 4, 95.00, 0, '2026-08-06 11:33:53'),
(734, 179, 17, NULL, NULL, 2, 210.00, 0, '2026-08-06 11:33:53'),
(735, 179, 18, NULL, NULL, 4, 175.00, 0, '2026-08-06 11:33:53'),
(736, 179, 1, NULL, NULL, 5, 95.00, 1, '2026-08-06 11:33:53'),
(737, 180, 16, 219, NULL, 4, 670.00, 1, '2026-08-06 12:29:49'),
(738, 180, 17, 219, NULL, 1, 670.00, 1, '2026-08-06 12:29:49'),
(739, 180, 18, 218, NULL, 0, 30.00, 0, '2026-08-06 12:29:49'),
(740, 180, 1, 218, NULL, 1, 30.00, 0, '2026-08-06 12:29:49'),
(741, 181, 16, 220, NULL, 2, 130.00, 1, '2026-08-06 12:50:44'),
(742, 181, 17, 221, NULL, 2, 280.00, 0, '2026-08-06 12:50:44'),
(743, 181, 18, 221, NULL, 2, 280.00, 0, '2026-08-06 12:50:44'),
(744, 181, 1, 220, NULL, 3, 130.00, 1, '2026-08-06 12:50:44'),
(745, 182, 16, NULL, NULL, 1, 60.00, 0, '2026-08-07 11:05:50'),
(746, 182, 17, NULL, NULL, 0, 0.00, 0, '2026-08-07 11:05:50'),
(747, 182, 18, NULL, NULL, 0, 0.00, 0, '2026-08-07 11:05:50'),
(748, 182, 21, NULL, NULL, 3, 120.00, 1, '2026-08-07 11:05:50'),
(749, 183, 16, NULL, NULL, 1, 35.00, 0, '2026-08-07 11:24:46'),
(750, 183, 17, NULL, NULL, 0, 0.00, 0, '2026-08-07 11:24:46'),
(751, 183, 18, NULL, NULL, 0, 0.00, 0, '2026-08-07 11:24:46'),
(752, 183, 1, NULL, NULL, 3, 365.00, 1, '2026-08-07 11:24:46'),
(753, 183, 21, NULL, NULL, 1, 10.00, 0, '2026-08-07 11:24:46'),
(754, 184, 16, 222, NULL, 4, 420.00, 1, '2026-08-07 11:53:29'),
(755, 184, 17, 222, NULL, 1, 420.00, 1, '2026-08-07 11:53:29'),
(756, 184, 18, 223, NULL, 1, 20.00, 0, '2026-08-07 11:53:29'),
(757, 184, 1, 223, NULL, 0, 20.00, 0, '2026-08-07 11:53:29'),
(758, 185, 16, 225, NULL, 3, 430.00, 1, '2026-08-07 12:22:16'),
(759, 185, 17, 224, NULL, 3, 370.00, 0, '2026-08-07 12:22:16'),
(760, 185, 18, 224, NULL, 1, 370.00, 0, '2026-08-07 12:22:16'),
(761, 185, 1, 225, NULL, 2, 430.00, 1, '2026-08-07 12:22:16');

-- --------------------------------------------------------

--
-- Table structure for table `game_rounds`
--

CREATE TABLE `game_rounds` (
  `id` int UNSIGNED NOT NULL,
  `game_id` int UNSIGNED NOT NULL,
  `round_number` int UNSIGNED NOT NULL,
  `winner_participant_id` int UNSIGNED NOT NULL,
  `winner_team_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `winning_card_id` int UNSIGNED DEFAULT NULL,
  `win_type_id` int UNSIGNED DEFAULT NULL,
  `calculated_score` decimal(10,2) DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `game_rounds`
--

INSERT INTO `game_rounds` (`id`, `game_id`, `round_number`, `winner_participant_id`, `winner_team_name`, `winning_card_id`, `win_type_id`, `calculated_score`, `created_at`) VALUES
(592, 147, 1, 616, NULL, 1, NULL, 5.00, '2026-07-22 13:47:14'),
(593, 147, 2, 616, NULL, 9, NULL, 10.00, '2026-07-22 13:50:57'),
(594, 147, 3, 615, NULL, 1, NULL, 5.00, '2026-07-22 13:56:12'),
(595, 147, 4, 616, NULL, 1, NULL, 5.00, '2026-07-22 14:03:00'),
(596, 147, 5, 617, NULL, 9, NULL, 10.00, '2026-07-22 14:05:16'),
(597, 147, 6, 617, NULL, 1, NULL, 5.00, '2026-07-22 14:12:42'),
(598, 147, 7, 615, NULL, 9, NULL, 10.00, '2026-07-22 14:18:40'),
(599, 147, 8, 614, NULL, 9, NULL, 10.00, '2026-07-22 14:26:21'),
(600, 147, 9, 614, NULL, 9, NULL, 10.00, '2026-07-22 14:35:16'),
(601, 147, 10, 615, NULL, 14, NULL, 15.00, '2026-07-22 14:39:53'),
(602, 147, 11, 615, NULL, 9, NULL, 10.00, '2026-07-22 14:43:06'),
(603, 147, 12, 617, NULL, 1, NULL, 5.00, '2026-07-22 14:45:35'),
(604, 147, 13, 617, NULL, 7, NULL, 15.00, '2026-07-22 14:50:38'),
(605, 147, 14, 616, NULL, 1, NULL, 5.00, '2026-07-22 14:54:08'),
(606, 147, 15, 614, NULL, 1, NULL, 5.00, '2026-07-22 14:59:37'),
(607, 147, 16, 615, NULL, 1, NULL, 5.00, '2026-07-22 15:04:27'),
(608, 148, 1, 618, NULL, 1, NULL, 5.00, '2026-07-22 18:50:13'),
(609, 148, 2, 619, NULL, 1, NULL, 5.00, '2026-07-22 18:54:27'),
(610, 148, 3, 618, NULL, 2, NULL, 5.00, '2026-07-22 18:57:13'),
(611, 148, 4, 620, NULL, 5, NULL, 10.00, '2026-07-22 19:04:02'),
(612, 148, 5, 619, NULL, 1, NULL, 5.00, '2026-07-22 19:06:33'),
(613, 148, 6, 618, NULL, 1, NULL, 5.00, '2026-07-22 19:10:14'),
(614, 148, 7, 619, NULL, 9, NULL, 10.00, '2026-07-22 19:13:01'),
(615, 148, 8, 619, NULL, 1, NULL, 5.00, '2026-07-22 19:14:52'),
(616, 148, 9, 620, NULL, 9, NULL, 10.00, '2026-07-22 19:17:20'),
(617, 148, 10, 619, NULL, 9, NULL, 10.00, '2026-07-22 19:21:29'),
(618, 149, 1, 622, NULL, 1, NULL, 5.00, '2026-07-23 12:40:10'),
(619, 149, 2, 624, NULL, 9, NULL, 10.00, '2026-07-23 12:42:51'),
(621, 149, 3, 622, NULL, 10, NULL, 20.00, '2026-07-23 12:48:47'),
(622, 149, 4, 624, NULL, 7, NULL, 15.00, '2026-07-23 12:52:43'),
(623, 149, 5, 623, NULL, 1, NULL, 5.00, '2026-07-23 12:55:41'),
(624, 149, 6, 622, NULL, 1, NULL, 5.00, '2026-07-23 13:04:01'),
(625, 149, 7, 622, NULL, 9, NULL, 10.00, '2026-07-23 13:06:36'),
(626, 149, 8, 624, NULL, 1, NULL, 5.00, '2026-07-23 13:09:39'),
(627, 149, 9, 622, NULL, 1, NULL, 5.00, '2026-07-23 13:15:26'),
(628, 150, 1, 625, 'تیم 1', 14, NULL, 30.00, '2026-07-23 13:21:54'),
(629, 150, 2, 625, 'تیم 1', 1, NULL, 10.00, '2026-07-23 13:27:00'),
(630, 150, 3, 625, 'تیم 1', 1, NULL, 10.00, '2026-07-23 13:30:54'),
(631, 150, 4, 628, 'تیم 2', 1, NULL, 10.00, '2026-07-23 13:33:28'),
(632, 150, 5, 628, 'تیم 2', 6, NULL, 30.00, '2026-07-23 13:38:01'),
(633, 150, 6, 628, 'تیم 2', 9, NULL, 20.00, '2026-07-23 13:40:28'),
(634, 150, 7, 628, 'تیم 2', 1, NULL, 10.00, '2026-07-23 13:43:28'),
(635, 150, 8, 625, 'تیم 1', 1, NULL, 10.00, '2026-07-23 13:45:56'),
(636, 150, 9, 625, 'تیم 1', 1, NULL, 10.00, '2026-07-23 13:50:16'),
(637, 151, 1, 632, 'تیم 2', 1, NULL, 10.00, '2026-07-23 13:53:29'),
(638, 151, 2, 632, 'تیم 2', 1, NULL, 10.00, '2026-07-23 13:58:43'),
(639, 151, 3, 629, 'تیم 1', 3, NULL, 10.00, '2026-07-23 14:01:46'),
(640, 151, 4, 630, 'تیم 1', 1, NULL, 10.00, '2026-07-23 14:05:58'),
(641, 151, 5, 632, 'تیم 2', 1, NULL, 10.00, '2026-07-23 14:13:32'),
(642, 146, 1, 611, NULL, 9, NULL, 10.00, '2026-07-23 14:18:15'),
(643, 146, 2, 611, NULL, 11, NULL, 25.00, '2026-07-23 14:19:54'),
(644, 146, 3, 613, NULL, 9, NULL, 10.00, '2026-07-23 14:21:03'),
(645, 146, 4, 613, NULL, 9, NULL, 10.00, '2026-07-23 14:25:38'),
(646, 146, 5, 612, NULL, 1, NULL, 5.00, '2026-07-23 14:27:30'),
(647, 146, 6, 612, NULL, 5, NULL, 10.00, '2026-07-23 14:29:45'),
(648, 146, 7, 613, NULL, 9, NULL, 10.00, '2026-07-23 14:31:42'),
(649, 146, 8, 611, NULL, 9, NULL, 10.00, '2026-07-23 14:34:40'),
(650, 146, 9, 612, NULL, 4, NULL, 10.00, '2026-07-23 14:36:32'),
(651, 146, 10, 612, NULL, 9, NULL, 10.00, '2026-07-23 14:38:46'),
(652, 146, 11, 613, NULL, 1, NULL, 5.00, '2026-07-23 14:40:25'),
(653, 146, 12, 611, NULL, 9, NULL, 10.00, '2026-07-23 14:43:47'),
(654, 146, 13, 613, NULL, 1, NULL, 5.00, '2026-07-23 14:47:35'),
(655, 146, 14, 612, NULL, 1, NULL, 5.00, '2026-07-23 14:50:57'),
(656, 146, 15, 613, NULL, 7, NULL, 15.00, '2026-07-23 14:52:58'),
(657, 146, 16, 612, NULL, 1, NULL, 5.00, '2026-07-23 14:56:38'),
(658, 146, 17, 612, NULL, 5, NULL, 10.00, '2026-07-23 14:58:30'),
(659, 146, 18, 613, NULL, 5, NULL, 10.00, '2026-07-23 15:00:19'),
(660, 146, 19, 612, NULL, 11, NULL, 25.00, '2026-07-23 15:05:22'),
(661, 146, 20, 612, NULL, 11, NULL, 25.00, '2026-07-23 15:07:18'),
(662, 146, 21, 611, NULL, 1, NULL, 5.00, '2026-07-23 15:13:56'),
(663, 146, 22, 612, NULL, 1, NULL, 5.00, '2026-07-23 15:17:01'),
(664, 152, 1, 636, NULL, 1, NULL, 5.00, '2026-07-24 13:04:26'),
(665, 152, 2, 634, NULL, 1, NULL, 5.00, '2026-07-24 13:07:07'),
(666, 152, 3, 634, NULL, 9, NULL, 10.00, '2026-07-24 13:11:14'),
(667, 152, 4, 635, NULL, 7, NULL, 15.00, '2026-07-24 13:13:52'),
(668, 152, 5, 635, NULL, 1, NULL, 5.00, '2026-07-24 13:16:21'),
(669, 152, 6, 635, NULL, 5, NULL, 10.00, '2026-07-24 13:20:28'),
(670, 153, 1, 637, NULL, 9, 1, 10.00, '2026-07-24 13:30:27'),
(671, 153, 2, 640, NULL, 14, 1, 15.00, '2026-07-24 13:38:22'),
(672, 153, 3, 640, NULL, 14, 1, 15.00, '2026-07-24 13:42:56'),
(673, 153, 4, 637, NULL, 1, 1, 5.00, '2026-07-24 13:45:27'),
(674, 153, 5, 638, NULL, 15, 4, 50.00, '2026-07-24 13:45:45'),
(675, 153, 6, 639, NULL, 1, 1, 5.00, '2026-07-24 13:49:31'),
(676, 153, 7, 637, NULL, 14, 1, 15.00, '2026-07-24 13:53:54'),
(677, 154, 1, 645, NULL, 9, NULL, 10.00, '2026-07-24 14:02:14'),
(678, 154, 2, 642, NULL, 1, NULL, 5.00, '2026-07-24 14:06:28'),
(679, 154, 3, 644, NULL, 9, NULL, 10.00, '2026-07-24 14:13:20'),
(680, 154, 4, 642, NULL, 9, NULL, 10.00, '2026-07-24 14:18:15'),
(681, 154, 5, 644, NULL, 1, NULL, 5.00, '2026-07-24 14:23:17'),
(682, 154, 6, 646, NULL, 4, NULL, 10.00, '2026-07-24 14:29:07'),
(683, 154, 7, 644, NULL, 1, NULL, 5.00, '2026-07-24 14:33:45'),
(684, 155, 1, 647, NULL, 1, NULL, 5.00, '2026-07-24 14:43:25'),
(685, 155, 2, 649, NULL, 1, NULL, 5.00, '2026-07-24 14:47:32'),
(686, 155, 3, 649, NULL, 1, NULL, 5.00, '2026-07-24 14:49:22'),
(687, 155, 4, 649, NULL, 1, NULL, 5.00, '2026-07-24 14:54:27'),
(688, 155, 5, 648, NULL, 1, NULL, 5.00, '2026-07-24 14:57:22'),
(689, 155, 6, 649, NULL, 1, NULL, 5.00, '2026-07-24 15:01:06'),
(690, 155, 7, 647, NULL, 2, NULL, 5.00, '2026-07-24 15:04:32'),
(691, 155, 8, 649, NULL, 9, NULL, 10.00, '2026-07-24 15:08:47'),
(692, 155, 9, 649, NULL, 1, NULL, 5.00, '2026-07-24 15:12:34'),
(693, 155, 10, 649, NULL, 9, NULL, 10.00, '2026-07-24 15:14:39'),
(694, 155, 11, 647, NULL, 5, NULL, 10.00, '2026-07-24 15:16:47'),
(695, 155, 12, 648, NULL, 1, NULL, 5.00, '2026-07-24 15:21:38'),
(696, 155, 13, 649, NULL, 7, NULL, 15.00, '2026-07-24 15:24:14'),
(697, 155, 14, 647, NULL, 9, NULL, 10.00, '2026-07-24 15:31:18'),
(698, 155, 15, 647, NULL, 9, NULL, 10.00, '2026-07-24 15:33:46'),
(699, 155, 16, 647, NULL, 13, NULL, 25.00, '2026-07-24 15:36:18'),
(700, 155, 17, 647, NULL, 12, NULL, 25.00, '2026-07-24 15:39:51'),
(701, 155, 18, 649, NULL, 13, NULL, 25.00, '2026-07-24 15:42:50'),
(702, 155, 19, 648, NULL, 9, NULL, 10.00, '2026-07-24 15:45:38'),
(703, 155, 20, 649, NULL, 1, NULL, 5.00, '2026-07-24 15:47:57'),
(704, 156, 1, 651, NULL, 9, NULL, 10.00, '2026-07-25 12:02:00'),
(705, 156, 2, 650, NULL, 1, NULL, 5.00, '2026-07-25 12:04:22'),
(706, 156, 3, 651, NULL, 9, NULL, 10.00, '2026-07-25 12:08:49'),
(707, 156, 4, 652, NULL, 15, NULL, 100.00, '2026-07-25 12:11:52'),
(708, 156, 5, 650, NULL, 14, NULL, 25.00, '2026-07-25 12:14:52'),
(709, 156, 6, 651, NULL, 7, NULL, 25.00, '2026-07-25 12:16:58'),
(710, 156, 7, 652, NULL, 12, NULL, 50.00, '2026-07-25 12:19:14'),
(711, 156, 8, 652, NULL, 7, NULL, 25.00, '2026-07-25 12:21:48'),
(712, 156, 9, 651, NULL, 9, NULL, 10.00, '2026-07-25 12:25:49'),
(713, 156, 10, 652, NULL, 13, NULL, 50.00, '2026-07-25 12:28:45'),
(714, 156, 11, 652, NULL, 9, NULL, 10.00, '2026-07-25 12:29:44'),
(715, 156, 12, 650, NULL, 13, NULL, 50.00, '2026-07-25 12:30:59'),
(716, 156, 13, 651, NULL, 11, NULL, 10.00, '2026-07-25 12:32:33'),
(717, 156, 14, 652, NULL, 1, NULL, 5.00, '2026-07-25 12:34:19'),
(718, 156, 15, 650, NULL, 12, NULL, 50.00, '2026-07-25 12:38:10'),
(719, 156, 16, 650, NULL, 9, NULL, 10.00, '2026-07-25 12:39:53'),
(720, 156, 17, 652, NULL, 1, NULL, 5.00, '2026-07-25 12:41:56'),
(721, 156, 18, 651, NULL, 15, NULL, 100.00, '2026-07-25 12:44:21'),
(722, 156, 19, 652, NULL, 11, NULL, 10.00, '2026-07-25 12:46:08'),
(723, 156, 20, 650, NULL, 11, NULL, 10.00, '2026-07-25 12:48:25'),
(724, 156, 21, 650, NULL, 1, NULL, 5.00, '2026-07-25 12:53:19'),
(725, 156, 22, 651, NULL, 1, NULL, 5.00, '2026-07-25 12:55:01'),
(726, 156, 23, 652, NULL, 9, NULL, 10.00, '2026-07-25 12:56:39'),
(727, 156, 24, 652, NULL, 1, NULL, 5.00, '2026-07-25 12:59:13'),
(728, 157, 1, 655, NULL, 15, NULL, 110.00, '2026-07-25 13:02:47'),
(729, 157, 2, 654, NULL, 1, NULL, 5.00, '2026-07-25 13:05:40'),
(730, 157, 3, 653, NULL, 9, NULL, 10.00, '2026-07-25 13:07:34'),
(731, 157, 4, 654, NULL, 12, NULL, 50.00, '2026-07-25 13:09:37'),
(732, 157, 5, 653, NULL, 6, NULL, 15.00, '2026-07-25 13:14:53'),
(733, 157, 6, 655, NULL, 7, NULL, 35.00, '2026-07-25 13:17:15'),
(734, 157, 7, 653, NULL, 9, NULL, 10.00, '2026-07-25 13:18:46'),
(735, 158, 1, 657, NULL, 12, NULL, 50.00, '2026-07-25 13:25:55'),
(736, 158, 2, 659, NULL, 5, NULL, 20.00, '2026-07-25 13:31:59'),
(737, 158, 3, 659, NULL, 3, NULL, 15.00, '2026-07-25 13:36:26'),
(738, 158, 4, 658, NULL, 9, NULL, 10.00, '2026-07-25 13:40:47'),
(739, 158, 5, 658, NULL, 7, NULL, 25.00, '2026-07-25 13:43:39'),
(740, 158, 6, 656, NULL, 9, NULL, 20.00, '2026-07-25 13:46:00'),
(741, 158, 7, 659, NULL, 1, NULL, 15.00, '2026-07-25 13:50:37'),
(742, 159, 1, 663, 'تیم 2', 15, NULL, 210.00, '2026-07-25 13:57:49'),
(743, 159, 2, 660, 'تیم 1', 9, NULL, 30.00, '2026-07-25 14:01:10'),
(744, 159, 3, 663, 'تیم 2', 9, NULL, 30.00, '2026-07-25 14:03:49'),
(745, 159, 4, 661, 'تیم 1', 1, NULL, 10.00, '2026-07-25 14:08:25'),
(746, 159, 5, 663, 'تیم 2', 7, NULL, 60.00, '2026-07-25 14:13:14'),
(747, 159, 6, 662, 'تیم 2', 13, NULL, 110.00, '2026-07-25 14:16:50'),
(748, 159, 7, 663, 'تیم 2', 9, NULL, 30.00, '2026-07-25 14:18:11'),
(749, 160, 1, 664, 'تیم 1', 1, NULL, 20.00, '2026-07-25 14:21:58'),
(750, 160, 2, 667, 'تیم 2', 1, NULL, 20.00, '2026-07-25 14:25:53'),
(751, 160, 3, 665, 'تیم 1', 6, NULL, 40.00, '2026-07-25 14:28:35'),
(752, 160, 4, 667, 'تیم 2', 1, NULL, 20.00, '2026-07-25 14:35:50'),
(753, 160, 5, 665, 'تیم 1', 1, NULL, 20.00, '2026-07-25 14:40:13'),
(754, 160, 6, 667, 'تیم 2', 1, NULL, 20.00, '2026-07-25 14:43:32'),
(755, 160, 7, 665, 'تیم 1', 12, NULL, 110.00, '2026-07-25 14:46:08'),
(757, 160, 8, 664, 'تیم 1', 13, NULL, 110.00, '2026-07-25 14:49:08'),
(758, 162, 1, 673, NULL, 1, NULL, 15.00, '2026-07-25 14:54:27'),
(759, 162, 2, 674, NULL, 1, NULL, 15.00, '2026-07-25 14:56:20'),
(760, 162, 3, 672, NULL, 9, NULL, 20.00, '2026-07-25 14:57:18'),
(761, 162, 4, 674, NULL, 12, NULL, 60.00, '2026-07-25 15:00:25'),
(762, 162, 5, 673, NULL, 1, NULL, 15.00, '2026-07-25 15:04:19'),
(763, 162, 6, 673, NULL, 4, NULL, 20.00, '2026-07-25 15:06:35'),
(764, 162, 7, 674, NULL, 12, NULL, 60.00, '2026-07-25 15:07:48'),
(765, 162, 8, 673, NULL, 9, NULL, 20.00, '2026-07-25 15:09:07'),
(766, 162, 9, 674, NULL, 13, NULL, 60.00, '2026-07-25 15:12:55'),
(767, 162, 10, 673, NULL, 9, NULL, 20.00, '2026-07-25 15:15:03'),
(768, 162, 11, 674, NULL, 15, NULL, 110.00, '2026-07-25 15:17:03'),
(769, 161, 1, 669, 'تیم 1', 6, NULL, 40.00, '2026-07-25 15:25:12'),
(770, 161, 2, 668, 'تیم 1', 7, NULL, 60.00, '2026-07-25 15:28:06'),
(771, 161, 3, 669, 'تیم 1', 1, NULL, 20.00, '2026-07-25 15:31:56'),
(772, 163, 1, 678, NULL, 5, NULL, 10.00, '2026-07-28 14:41:50'),
(773, 163, 2, 675, NULL, 1, NULL, 15.00, '2026-07-28 14:46:39'),
(774, 163, 3, 678, NULL, 9, NULL, 10.00, '2026-07-28 14:51:36'),
(775, 163, 4, 677, NULL, 4, NULL, 10.00, '2026-07-28 14:59:26'),
(776, 163, 5, 676, NULL, 12, NULL, 60.00, '2026-07-28 15:02:13'),
(777, 163, 6, 677, NULL, 1, NULL, 5.00, '2026-07-28 15:06:59'),
(778, 163, 7, 678, NULL, 1, NULL, 5.00, '2026-07-28 15:09:47'),
(779, 165, 1, 688, NULL, 1, NULL, 5.00, '2026-07-31 11:47:23'),
(780, 165, 2, 688, NULL, 9, NULL, 10.00, '2026-07-31 11:49:42'),
(781, 165, 3, 686, NULL, 1, NULL, 15.00, '2026-07-31 11:58:08'),
(782, 165, 4, 688, NULL, 5, NULL, 10.00, '2026-07-31 12:02:57'),
(783, 166, 1, 690, NULL, 1, NULL, 15.00, '2026-08-01 14:47:16'),
(784, 166, 2, 690, NULL, 1, NULL, 15.00, '2026-08-01 14:48:55'),
(785, 166, 3, 691, NULL, 1, NULL, 15.00, '2026-08-01 14:50:14'),
(786, 166, 4, 691, NULL, 6, NULL, 25.00, '2026-08-01 14:53:21'),
(787, 166, 5, 692, NULL, 1, NULL, 5.00, '2026-08-01 14:57:46'),
(788, 166, 6, 692, NULL, 9, NULL, 10.00, '2026-08-01 15:06:54'),
(789, 166, 7, 690, NULL, 1, NULL, 15.00, '2026-08-01 15:11:27'),
(790, 167, 1, 695, 'تیم 1', 12, NULL, 100.00, '2026-08-01 15:16:51'),
(791, 167, 2, 694, 'تیم 2', 1, NULL, 20.00, '2026-08-01 15:18:34'),
(792, 167, 3, 694, 'تیم 2', 6, NULL, 40.00, '2026-08-01 15:22:03'),
(793, 167, 4, 693, 'تیم 2', 15, NULL, 210.00, '2026-08-01 15:27:55'),
(794, 168, 1, 697, NULL, 1, NULL, 15.00, '2026-08-03 19:17:13'),
(795, 168, 2, 699, NULL, 12, NULL, 60.00, '2026-08-03 19:22:35'),
(796, 168, 3, 698, NULL, 1, NULL, 15.00, '2026-08-03 19:25:41'),
(797, 168, 4, 698, NULL, 9, NULL, 20.00, '2026-08-03 19:27:42'),
(798, 168, 5, 698, NULL, 7, NULL, 35.00, '2026-08-03 19:31:05'),
(799, 168, 6, 697, NULL, 15, NULL, 110.00, '2026-08-03 19:33:54'),
(800, 168, 7, 697, NULL, 12, NULL, 60.00, '2026-08-03 19:36:58'),
(801, 168, 8, 698, NULL, 13, NULL, 60.00, '2026-08-03 19:39:14'),
(802, 168, 9, 699, NULL, 9, NULL, 20.00, '2026-08-03 19:42:03'),
(803, 168, 10, 697, NULL, 5, NULL, 20.00, '2026-08-03 19:43:36'),
(804, 168, 11, 699, NULL, 7, NULL, 35.00, '2026-08-03 19:45:48'),
(805, 168, 12, 697, NULL, 5, NULL, 20.00, '2026-08-03 19:49:12'),
(806, 168, 13, 698, NULL, 6, NULL, 25.00, '2026-08-03 19:53:33'),
(807, 168, 14, 697, NULL, 4, NULL, 20.00, '2026-08-03 19:55:40'),
(808, 168, 15, 699, NULL, 13, NULL, 60.00, '2026-08-03 19:59:59'),
(809, 168, 16, 697, NULL, 4, NULL, 20.00, '2026-08-03 20:02:43'),
(810, 168, 17, 699, NULL, 9, NULL, 20.00, '2026-08-03 20:04:13'),
(811, 168, 18, 697, NULL, 11, NULL, 20.00, '2026-08-03 20:05:38'),
(812, 168, 19, 698, NULL, 1, NULL, 15.00, '2026-08-03 20:12:11'),
(813, 168, 20, 698, NULL, 1, NULL, 15.00, '2026-08-03 20:14:44'),
(814, 168, 21, 697, NULL, 1, NULL, 15.00, '2026-08-03 20:19:24'),
(815, 168, 22, 698, NULL, 9, NULL, 20.00, '2026-08-03 20:20:43'),
(816, 168, 23, 699, NULL, 7, NULL, 35.00, '2026-08-03 20:22:16'),
(817, 168, 24, 698, NULL, 4, NULL, 20.00, '2026-08-03 20:25:11'),
(818, 168, 25, 697, NULL, 1, NULL, 15.00, '2026-08-03 20:27:36'),
(819, 169, 1, 700, NULL, 1, NULL, 15.00, '2026-08-03 20:32:36'),
(820, 169, 2, 702, NULL, 1, NULL, 15.00, '2026-08-03 20:35:01'),
(821, 169, 3, 701, NULL, 9, NULL, 20.00, '2026-08-03 20:37:17'),
(822, 169, 4, 702, NULL, 7, NULL, 35.00, '2026-08-03 20:39:29'),
(823, 169, 5, 701, NULL, 1, NULL, 15.00, '2026-08-03 20:44:11'),
(824, 169, 6, 701, NULL, 1, NULL, 15.00, '2026-08-03 20:46:09'),
(825, 169, 7, 700, NULL, 1, NULL, 15.00, '2026-08-03 20:49:36'),
(826, 169, 8, 700, NULL, 2, NULL, 15.00, '2026-08-03 20:53:43'),
(827, 169, 9, 702, NULL, 6, NULL, 25.00, '2026-08-03 20:56:36'),
(828, 169, 10, 701, NULL, 1, NULL, 15.00, '2026-08-03 20:58:19'),
(829, 169, 11, 700, NULL, 9, NULL, 20.00, '2026-08-03 20:59:18'),
(830, 169, 12, 700, NULL, 9, NULL, 20.00, '2026-08-03 21:03:30'),
(831, 170, 1, 705, NULL, 1, NULL, 15.00, '2026-08-04 12:45:02'),
(832, 170, 2, 703, NULL, 15, NULL, 110.00, '2026-08-04 12:46:22'),
(833, 170, 3, 705, NULL, 11, NULL, 20.00, '2026-08-04 12:49:45'),
(834, 170, 4, 705, NULL, 9, NULL, 20.00, '2026-08-04 12:50:59'),
(835, 170, 5, 705, NULL, 9, NULL, 20.00, '2026-08-04 12:53:05'),
(836, 170, 6, 703, NULL, 9, NULL, 20.00, '2026-08-04 12:57:05'),
(837, 170, 7, 703, NULL, 7, NULL, 35.00, '2026-08-04 13:02:23'),
(838, 170, 8, 704, NULL, 1, NULL, 15.00, '2026-08-04 13:04:12'),
(839, 170, 9, 705, NULL, 1, NULL, 15.00, '2026-08-04 13:08:34'),
(840, 170, 10, 703, NULL, 3, NULL, 15.00, '2026-08-04 13:11:10'),
(841, 170, 11, 703, NULL, 1, NULL, 15.00, '2026-08-04 13:14:34'),
(842, 170, 12, 703, NULL, 1, NULL, 15.00, '2026-08-04 13:16:07'),
(843, 170, 13, 705, NULL, 1, NULL, 15.00, '2026-08-04 13:19:19'),
(844, 170, 14, 704, NULL, 9, NULL, 20.00, '2026-08-04 13:22:50'),
(845, 170, 15, 705, NULL, 1, NULL, 15.00, '2026-08-04 13:26:17'),
(846, 171, 1, 707, NULL, 9, NULL, 20.00, '2026-08-04 13:28:30'),
(847, 171, 2, 707, NULL, 1, NULL, 15.00, '2026-08-04 13:31:36'),
(848, 171, 3, 707, NULL, 1, NULL, 15.00, '2026-08-04 13:36:56'),
(849, 171, 4, 707, NULL, 9, NULL, 20.00, '2026-08-04 13:38:19'),
(850, 171, 5, 709, NULL, 12, NULL, 60.00, '2026-08-04 13:41:48'),
(851, 171, 6, 707, NULL, 9, NULL, 20.00, '2026-08-04 13:43:53'),
(852, 172, 1, 712, 'تیم 2', 1, NULL, 20.00, '2026-08-04 13:48:47'),
(853, 172, 2, 713, 'تیم 1', 1, NULL, 20.00, '2026-08-04 13:55:56'),
(854, 172, 3, 712, 'تیم 2', 1, NULL, 20.00, '2026-08-04 14:00:29'),
(855, 172, 4, 712, 'تیم 2', 9, NULL, 30.00, '2026-08-04 14:02:46'),
(856, 172, 5, 710, 'تیم 1', 1, NULL, 20.00, '2026-08-04 14:08:57'),
(857, 172, 6, 712, 'تیم 2', 12, NULL, 110.00, '2026-08-04 14:16:42'),
(858, 172, 7, 710, 'تیم 1', 12, NULL, 110.00, '2026-08-04 14:21:15'),
(859, 172, 8, 712, 'تیم 2', 15, NULL, 210.00, '2026-08-04 14:24:01'),
(860, 172, 9, 713, 'تیم 1', 1, NULL, 20.00, '2026-08-04 14:29:24'),
(861, 172, 10, 712, 'تیم 2', 14, NULL, 60.00, '2026-08-04 14:32:36'),
(862, 172, 11, 710, 'تیم 1', 9, NULL, 30.00, '2026-08-04 14:34:45'),
(863, 172, 12, 711, 'تیم 2', 9, NULL, 30.00, '2026-08-04 14:36:19'),
(864, 172, 13, 713, 'تیم 1', 1, NULL, 20.00, '2026-08-04 14:40:05'),
(865, 172, 14, 710, 'تیم 1', 1, NULL, 20.00, '2026-08-04 14:43:17'),
(866, 172, 15, 712, 'تیم 2', 7, NULL, 60.00, '2026-08-04 14:50:34'),
(867, 172, 16, 712, 'تیم 2', 15, NULL, 210.00, '2026-08-04 14:55:03'),
(868, 172, 17, 712, 'تیم 2', 7, NULL, 60.00, '2026-08-04 14:58:36'),
(869, 170, 16, 705, NULL, 7, NULL, 35.00, '2026-08-04 15:01:23'),
(870, 170, 17, 703, NULL, 13, NULL, 60.00, '2026-08-04 15:04:45'),
(871, 170, 18, 705, NULL, 9, NULL, 20.00, '2026-08-04 15:06:50'),
(872, 170, 19, 704, NULL, 1, NULL, 105.00, '2026-08-04 15:08:51'),
(873, 170, 20, 705, NULL, 11, NULL, 20.00, '2026-08-04 15:12:54'),
(874, 173, 1, 714, NULL, 11, NULL, 110.00, '2026-08-05 11:35:17'),
(875, 173, 2, 714, NULL, 15, NULL, 200.00, '2026-08-05 11:37:56'),
(876, 173, 3, 715, NULL, 6, NULL, 25.00, '2026-08-05 11:39:30'),
(877, 173, 4, 716, NULL, 11, NULL, 20.00, '2026-08-05 11:41:07'),
(878, 173, 5, 714, NULL, 1, NULL, 105.00, '2026-08-05 11:43:02'),
(879, 173, 6, 716, NULL, 14, NULL, 35.00, '2026-08-05 11:47:33'),
(880, 173, 7, 716, NULL, 13, NULL, 60.00, '2026-08-05 11:49:49'),
(881, 173, 8, 715, NULL, 1, NULL, 15.00, '2026-08-05 11:51:51'),
(882, 173, 9, 716, NULL, 9, NULL, 20.00, '2026-08-05 11:53:33'),
(883, 173, 10, 715, NULL, 1, NULL, 15.00, '2026-08-05 11:56:36'),
(884, 173, 11, 715, NULL, 1, NULL, 15.00, '2026-08-05 12:03:00'),
(885, 173, 12, 714, NULL, 12, NULL, 150.00, '2026-08-05 12:07:38'),
(886, 173, 13, 715, NULL, 9, NULL, 20.00, '2026-08-05 12:10:58'),
(892, 175, 1, 720, NULL, 9, NULL, 110.00, '2026-08-05 14:20:32'),
(893, 175, 2, 721, NULL, 15, NULL, 110.00, '2026-08-05 14:21:56'),
(894, 175, 3, 719, NULL, 9, NULL, 20.00, '2026-08-05 14:23:38'),
(895, 175, 4, 720, NULL, 14, NULL, 125.00, '2026-08-05 14:25:46'),
(896, 175, 5, 719, NULL, 1, NULL, 15.00, '2026-08-05 14:27:06'),
(897, 175, 6, 720, NULL, 15, NULL, 200.00, '2026-08-05 14:30:02'),
(898, 175, 7, 720, NULL, 9, NULL, 110.00, '2026-08-05 14:31:22'),
(899, 175, 8, 721, NULL, 11, NULL, 20.00, '2026-08-05 14:34:03'),
(900, 175, 9, 721, NULL, 11, NULL, 20.00, '2026-08-05 14:35:28'),
(901, 175, 10, 721, NULL, 15, NULL, 110.00, '2026-08-05 14:37:32'),
(902, 175, 11, 719, NULL, 1, NULL, 15.00, '2026-08-05 14:39:50'),
(903, 175, 12, 721, NULL, 9, NULL, 20.00, '2026-08-05 14:41:26'),
(904, 175, 13, 721, NULL, 6, NULL, 25.00, '2026-08-05 14:42:54'),
(905, 175, 14, 721, NULL, 1, NULL, 15.00, '2026-08-05 14:45:18'),
(906, 175, 15, 721, NULL, 1, NULL, 15.00, '2026-08-05 14:47:53'),
(907, 175, 16, 720, NULL, 1, NULL, 105.00, '2026-08-05 14:49:52'),
(908, 175, 17, 719, NULL, 5, NULL, 20.00, '2026-08-05 14:51:35'),
(909, 175, 18, 719, NULL, 1, NULL, 15.00, '2026-08-05 14:53:21'),
(910, 175, 19, 720, NULL, 1, NULL, 105.00, '2026-08-05 14:55:14'),
(911, 175, 20, 719, NULL, 1, NULL, 15.00, '2026-08-05 14:57:36'),
(912, 175, 21, 719, NULL, 1, NULL, 15.00, '2026-08-05 14:59:41'),
(913, 175, 22, 721, NULL, 1, NULL, 15.00, '2026-08-05 15:02:03'),
(914, 175, 23, 721, NULL, 1, NULL, 15.00, '2026-08-05 15:03:37'),
(915, 176, 1, 722, NULL, 11, NULL, 20.00, '2026-08-05 15:05:16'),
(916, 176, 2, 722, NULL, 9, NULL, 20.00, '2026-08-05 15:07:10'),
(917, 176, 3, 723, NULL, 14, NULL, 125.00, '2026-08-05 15:09:08'),
(918, 176, 4, 722, NULL, 1, NULL, 15.00, '2026-08-05 15:12:22'),
(919, 176, 5, 722, NULL, 13, NULL, 60.00, '2026-08-05 15:14:52'),
(920, 176, 6, 724, NULL, 15, NULL, 110.00, '2026-08-05 15:16:17'),
(921, 176, 7, 723, NULL, 1, NULL, 105.00, '2026-08-05 15:17:14'),
(922, 176, 8, 722, NULL, 1, NULL, 15.00, '2026-08-05 15:21:16'),
(923, 177, 1, 725, 'تیم 2', 4, NULL, 30.00, '2026-08-05 15:32:38'),
(924, 177, 2, 725, 'تیم 2', 4, NULL, 30.00, '2026-08-05 15:39:40'),
(925, 177, 3, 726, 'تیم 2', 12, NULL, 200.00, '2026-08-05 15:43:52'),
(926, 177, 4, 727, 'تیم 1', 9, NULL, 30.00, '2026-08-05 15:47:33'),
(927, 177, 5, 728, 'تیم 1', 1, NULL, 20.00, '2026-08-05 15:52:52'),
(928, 177, 6, 725, 'تیم 2', 7, NULL, 60.00, '2026-08-05 15:55:34'),
(929, 177, 7, 728, 'تیم 1', 15, NULL, 210.00, '2026-08-05 16:01:44'),
(930, 177, 8, 728, 'تیم 1', 5, NULL, 30.00, '2026-08-05 16:06:44'),
(931, 177, 9, 728, 'تیم 1', 9, NULL, 30.00, '2026-08-05 16:09:41'),
(932, 178, 1, 732, NULL, 9, NULL, 20.00, '2026-08-06 10:43:25'),
(933, 178, 2, 732, NULL, 12, NULL, 60.00, '2026-08-06 10:47:15'),
(935, 178, 3, 731, NULL, 9, NULL, 20.00, '2026-08-06 10:50:27'),
(936, 178, 4, 731, NULL, 1, NULL, 15.00, '2026-08-06 10:51:48'),
(937, 178, 5, 732, NULL, 8, NULL, 20.00, '2026-08-06 10:55:42'),
(938, 178, 6, 730, NULL, 1, NULL, 105.00, '2026-08-06 11:01:56'),
(939, 178, 7, 731, NULL, 8, NULL, 20.00, '2026-08-06 11:11:47'),
(940, 178, 8, 729, NULL, 1, NULL, 15.00, '2026-08-06 11:15:18'),
(941, 178, 9, 731, NULL, 1, NULL, 15.00, '2026-08-06 11:19:27'),
(942, 178, 10, 730, NULL, 1, NULL, 105.00, '2026-08-06 11:22:19'),
(943, 178, 11, 731, NULL, 1, NULL, 15.00, '2026-08-06 11:31:29'),
(944, 179, 1, 735, NULL, 9, NULL, 20.00, '2026-08-06 11:36:47'),
(945, 179, 2, 736, NULL, 1, NULL, 15.00, '2026-08-06 11:38:19'),
(946, 179, 3, 736, NULL, 9, NULL, 20.00, '2026-08-06 11:40:41'),
(947, 179, 4, 734, NULL, 1, NULL, 105.00, '2026-08-06 11:44:10'),
(948, 179, 5, 735, NULL, 12, NULL, 60.00, '2026-08-06 11:48:58'),
(949, 179, 6, 735, NULL, 7, NULL, 35.00, '2026-08-06 11:52:29'),
(950, 179, 7, 733, NULL, 9, NULL, 20.00, '2026-08-06 11:58:55'),
(951, 179, 8, 735, NULL, 12, NULL, 60.00, '2026-08-06 12:01:56'),
(952, 179, 9, 733, NULL, 9, NULL, 20.00, '2026-08-06 12:05:09'),
(953, 179, 10, 733, NULL, 9, NULL, 20.00, '2026-08-06 12:10:16'),
(954, 179, 11, 736, NULL, 9, NULL, 20.00, '2026-08-06 12:11:41'),
(955, 179, 12, 733, NULL, 14, NULL, 35.00, '2026-08-06 12:15:15'),
(956, 179, 13, 736, NULL, 9, NULL, 20.00, '2026-08-06 12:18:01'),
(957, 179, 14, 734, NULL, 1, NULL, 105.00, '2026-08-06 12:24:15'),
(958, 179, 15, 736, NULL, 9, NULL, 20.00, '2026-08-06 12:27:59'),
(959, 180, 1, 740, 'تیم 1', 5, NULL, 30.00, '2026-08-06 12:32:34'),
(960, 180, 2, 738, 'تیم 2', 1, NULL, 110.00, '2026-08-06 12:37:35'),
(961, 180, 3, 737, 'تیم 2', 5, NULL, 30.00, '2026-08-06 12:41:36'),
(962, 180, 4, 737, 'تیم 2', 15, NULL, 210.00, '2026-08-06 12:44:13'),
(963, 180, 5, 737, 'تیم 2', 12, NULL, 110.00, '2026-08-06 12:47:37'),
(964, 180, 6, 737, 'تیم 2', 15, NULL, 210.00, '2026-08-06 12:49:47'),
(965, 181, 1, 744, 'تیم 1', 6, NULL, 40.00, '2026-08-06 12:52:50'),
(966, 181, 2, 744, 'تیم 1', 1, NULL, 20.00, '2026-08-06 12:54:45'),
(967, 181, 3, 742, 'تیم 2', 1, NULL, 110.00, '2026-08-06 12:57:40'),
(968, 181, 4, 742, 'تیم 2', 1, NULL, 110.00, '2026-08-06 13:03:14'),
(969, 181, 5, 741, 'تیم 1', 9, NULL, 30.00, '2026-08-06 13:06:47'),
(970, 181, 6, 743, 'تیم 2', 1, NULL, 20.00, '2026-08-06 13:10:24'),
(971, 181, 7, 741, 'تیم 1', 3, NULL, 20.00, '2026-08-06 13:14:03'),
(972, 181, 8, 743, 'تیم 2', 6, NULL, 40.00, '2026-08-06 13:16:09'),
(973, 181, 9, 744, 'تیم 1', 1, NULL, 20.00, '2026-08-06 13:19:59'),
(974, 182, 1, 748, NULL, 1, NULL, 5.00, '2026-08-07 11:09:40'),
(976, 182, 2, 748, NULL, 6, NULL, 15.00, '2026-08-07 11:11:15'),
(977, 182, 3, 745, NULL, 13, NULL, 60.00, '2026-08-07 11:13:43'),
(978, 182, 4, 748, NULL, 15, NULL, 100.00, '2026-08-07 11:22:38'),
(979, 183, 1, 749, NULL, 7, NULL, 35.00, '2026-08-07 11:33:21'),
(980, 183, 2, 752, NULL, 9, NULL, 110.00, '2026-08-07 11:36:00'),
(981, 183, 3, 752, NULL, 1, NULL, 105.00, '2026-08-07 11:41:17'),
(982, 183, 4, 753, NULL, 9, NULL, 10.00, '2026-08-07 11:46:05'),
(983, 183, 5, 752, NULL, 13, NULL, 150.00, '2026-08-07 11:51:28'),
(984, 184, 1, 756, 'تیم 2', 1, NULL, 20.00, '2026-08-07 11:58:12'),
(985, 184, 2, 754, 'تیم 1', 1, NULL, 20.00, '2026-08-07 12:00:36'),
(986, 184, 3, 754, 'تیم 1', 15, NULL, 210.00, '2026-08-07 12:05:40'),
(987, 184, 4, 754, 'تیم 1', 1, NULL, 20.00, '2026-08-07 12:11:30'),
(988, 184, 5, 755, 'تیم 1', 7, NULL, 150.00, '2026-08-07 12:16:29'),
(989, 184, 6, 754, 'تیم 1', 1, NULL, 20.00, '2026-08-07 12:20:07'),
(990, 185, 1, 759, 'تیم 1', 9, NULL, 120.00, '2026-08-07 12:27:16'),
(991, 185, 2, 761, 'تیم 2', 5, NULL, 120.00, '2026-08-07 12:32:17'),
(992, 185, 3, 758, 'تیم 2', 1, NULL, 20.00, '2026-08-07 12:34:50'),
(993, 185, 4, 760, 'تیم 1', 1, NULL, 20.00, '2026-08-07 12:37:12'),
(994, 185, 5, 758, 'تیم 2', 14, NULL, 60.00, '2026-08-07 12:39:34'),
(995, 185, 6, 759, 'تیم 1', 9, NULL, 120.00, '2026-08-07 12:42:24'),
(996, 185, 7, 761, 'تیم 2', 9, NULL, 120.00, '2026-08-07 12:45:02'),
(997, 185, 8, 759, 'تیم 1', 1, NULL, 110.00, '2026-08-07 12:48:20'),
(998, 185, 9, 758, 'تیم 2', 12, NULL, 110.00, '2026-08-07 12:55:20');

-- --------------------------------------------------------

--
-- Table structure for table `leaderboard_cache`
--

CREATE TABLE `leaderboard_cache` (
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
  `last_updated` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `leaderboard_cache`
--

INSERT INTO `leaderboard_cache` (`user_id`, `total_games`, `total_wins`, `total_losses`, `total_points`, `win_rate`, `points_per_game`, `confidence_factor`, `final_rank_score`, `current_streak`, `best_streak`, `last_updated`) VALUES
(1, 32, 13, 20, 4745.00, 40.63, 148.28, 0.00, 0.00, 0, 0, '2026-08-07 12:55:27'),
(16, 29, 10, 20, 4055.00, 34.48, 139.83, 0.00, 0.00, 0, 0, '2026-08-07 12:55:27'),
(17, 34, 15, 20, 7170.00, 44.12, 210.88, 0.00, 0.00, 0, 0, '2026-08-07 12:55:27'),
(18, 31, 8, 24, 3630.00, 25.81, 117.10, 0.00, 0.00, 0, 0, '2026-08-07 12:55:27'),
(19, 1, 0, 2, 50.00, 0.00, 50.00, 0.00, 0.00, 0, 0, '2026-07-24 13:54:30'),
(20, 5, 2, 4, 95.00, 40.00, 19.00, 0.00, 0.00, 0, 0, '2026-07-31 12:03:10'),
(21, 7, 1, 7, 245.00, 14.29, 35.00, 0.00, 0.00, 0, 0, '2026-08-07 11:51:35'),
(24, 4, 1, 4, 155.00, 25.00, 38.75, 0.00, 0.00, 0, 0, '2026-08-01 15:28:08');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int UNSIGNED NOT NULL,
  `user_id` int UNSIGNED NOT NULL,
  `type` enum('achievement','title','level_up','challenge','streak','system') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `icon` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 0xF09F9494,
  `link` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'لینک مرتبط',
  `is_read` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `type`, `title`, `message`, `icon`, `link`, `is_read`, `created_at`) VALUES
(2109, 16, 'title', '🏆 عنوان جدید!', 'تبریک! شما عنوان «تازه‌کار» را کسب کردید.', '🌱', '/achievements', 1, '2026-07-22 15:31:06'),
(2110, 17, 'title', '🏆 عنوان جدید!', 'تبریک! شما عنوان «تازه‌کار» را کسب کردید.', '🌱', '/achievements', 1, '2026-07-22 15:31:06'),
(2111, 18, 'title', '🏆 عنوان جدید!', 'تبریک! شما عنوان «تازه‌کار» را کسب کردید.', '🌱', '/achievements', 0, '2026-07-22 15:31:06'),
(2112, 1, 'title', '🏆 عنوان جدید!', 'تبریک! شما عنوان «تازه‌کار» را کسب کردید.', '🌱', '/achievements', 1, '2026-07-22 15:31:06'),
(2113, 17, 'achievement', 'مدال جدید: اولین پیروزی', 'تبریک! شما مدال 🏆 اولین پیروزی را کسب کردید و 15 امتیاز تجربه دریافت کردید.', '🏆', '/achievements#achievements-section', 1, '2026-07-22 15:31:06'),
(2114, 18, 'achievement', 'مدال جدید: اولین پیروزی', 'تبریک! شما مدال 🏆 اولین پیروزی را کسب کردید و 15 امتیاز تجربه دریافت کردید.', '🏆', '/achievements#achievements-section', 1, '2026-07-22 19:22:32'),
(2115, 16, 'achievement', 'مدال جدید: اولین پیروزی', 'تبریک! شما مدال 🏆 اولین پیروزی را کسب کردید و 15 امتیاز تجربه دریافت کردید.', '🏆', '/achievements#achievements-section', 1, '2026-07-23 13:51:08'),
(2116, 20, 'title', '🏆 عنوان جدید!', 'تبریک! شما عنوان «تازه‌کار» را کسب کردید.', '🌱', '/achievements', 0, '2026-07-24 13:22:28'),
(2117, 21, 'title', '🏆 عنوان جدید!', 'تبریک! شما عنوان «تازه‌کار» را کسب کردید.', '🌱', '/achievements', 0, '2026-07-24 13:22:28'),
(2118, 20, 'achievement', 'مدال جدید: اولین پیروزی', 'تبریک! شما مدال 🏆 اولین پیروزی را کسب کردید و 15 امتیاز تجربه دریافت کردید.', '🏆', '/achievements#achievements-section', 0, '2026-07-24 13:22:28'),
(2119, 19, 'title', '🏆 عنوان جدید!', 'تبریک! شما عنوان «تازه‌کار» را کسب کردید.', '🌱', '/achievements', 0, '2026-07-24 13:54:30'),
(2120, 1, 'achievement', 'مدال جدید: اولین پیروزی', 'تبریک! شما مدال 🏆 اولین پیروزی را کسب کردید و 15 امتیاز تجربه دریافت کردید.', '🏆', '/achievements#achievements-section', 0, '2026-07-24 14:34:03'),
(2121, 1, 'title', '🏆 عنوان جدید!', 'تبریک! شما عنوان «بازیکن فعال» را کسب کردید. بونوس: +10 امتیاز در هر برد', '🎯', '/achievements', 0, '2026-07-25 12:59:32'),
(2122, 16, 'title', '🏆 عنوان جدید!', 'تبریک! شما عنوان «بازیکن فعال» را کسب کردید. بونوس: +10 امتیاز در هر برد', '🎯', '/achievements', 1, '2026-07-25 13:19:02'),
(2123, 18, 'title', '🏆 عنوان جدید!', 'تبریک! شما عنوان «بازیکن فعال» را کسب کردید. بونوس: +10 امتیاز در هر برد', '🎯', '/achievements', 0, '2026-07-25 13:50:48'),
(2124, 17, 'title', '🏆 عنوان جدید!', 'تبریک! شما عنوان «بازیکن فعال» را کسب کردید. بونوس: +10 امتیاز در هر برد', '🎯', '/achievements', 1, '2026-07-25 14:18:18'),
(2125, 24, 'title', '🏆 عنوان جدید!', 'تبریک! شما عنوان «کارآموز» را کسب کردید.', '👨‍🏫', '/achievements', 0, '2026-07-28 15:09:57'),
(2126, 24, 'achievement', 'مدال جدید: اولین پیروزی', 'تبریک! شما مدال 🏆 اولین پیروزی را کسب کردید و 15 امتیاز تجربه دریافت کردید.', '🏆', '/achievements#achievements-section', 0, '2026-07-31 12:03:11'),
(2127, 18, 'achievement', 'مدال جدید: نابغه', 'تبریک! شما مدال 🧠 نابغه را کسب کردید و 100 امتیاز تجربه دریافت کردید.', '🧠', '/achievements#achievements-section', 0, '2026-08-01 15:28:09'),
(2128, 21, 'achievement', 'مدال جدید: امتیازآور', 'تبریک! شما مدال 💯 امتیازآور را کسب کردید و 15 امتیاز تجربه دریافت کردید.', '💯', '/achievements#achievements-section', 0, '2026-08-01 15:28:09'),
(2129, 17, 'title', '🏆 عنوان جدید!', 'تبریک! شما عنوان «برنده» را کسب کردید. بونوس: +100 امتیاز در هر برد', '🏆', '/achievements', 1, '2026-08-04 14:58:44'),
(2130, 17, 'achievement', 'مدال جدید: برنده ده‌تایی', 'تبریک! شما مدال 🥇 برنده ده‌تایی را کسب کردید و 30 امتیاز تجربه دریافت کردید.', '🥇', '/achievements#achievements-section', 1, '2026-08-04 14:58:44'),
(2131, 21, 'achievement', 'مدال جدید: اولین پیروزی', 'تبریک! شما مدال 🏆 اولین پیروزی را کسب کردید و 15 امتیاز تجربه دریافت کردید.', '🏆', '/achievements#achievements-section', 0, '2026-08-07 11:22:44'),
(2132, 1, 'achievement', 'مدال جدید: تیم‌باز', 'تبریک! شما مدال 👥 تیم‌باز را کسب کردید و 20 امتیاز تجربه دریافت کردید.', '👥', '/achievements#achievements-section', 0, '2026-08-07 12:20:39'),
(2133, 16, 'achievement', 'مدال جدید: برنده ده‌تایی', 'تبریک! شما مدال 🥇 برنده ده‌تایی را کسب کردید و 30 امتیاز تجربه دریافت کردید.', '🥇', '/achievements#achievements-section', 0, '2026-08-07 12:55:27');

-- --------------------------------------------------------

--
-- Table structure for table `notification_templates`
--

CREATE TABLE `notification_templates` (
  `id` int UNSIGNED NOT NULL,
  `event_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `text_template` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `color_hex` varchar(7) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '#3B82F6',
  `sound_file` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `animation_type` enum('bounce','shake','slide','fade') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'fade',
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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

CREATE TABLE `otp_codes` (
  `id` int UNSIGNED NOT NULL,
  `phone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `purpose` enum('register','login','reset_password') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `expires_at` timestamp NOT NULL,
  `used` tinyint(1) DEFAULT '0',
  `attempts` tinyint UNSIGNED DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `otp_codes`
--

INSERT INTO `otp_codes` (`id`, `phone`, `code`, `purpose`, `ip_address`, `expires_at`, `used`, `attempts`, `created_at`) VALUES
(10, '09019177577', '417992', 'login', '2.184.121.80', '2026-07-22 10:52:49', 1, 1, '2026-07-22 10:47:49'),
(11, '09019177577', '631410', 'login', '113.203.11.53', '2026-07-22 11:16:12', 1, 1, '2026-07-22 11:11:12'),
(12, '09305586873', '967803', 'register', '5.120.213.100', '2026-07-22 13:36:05', 1, 1, '2026-07-22 13:31:05'),
(13, '09373056591', '200569', 'register', '113.203.87.72', '2026-07-22 13:40:42', 1, 1, '2026-07-22 13:35:42'),
(14, '09179292500', '189210', 'register', '5.119.202.144', '2026-07-22 13:45:13', 1, 1, '2026-07-22 13:40:13'),
(15, '09019177577', '982561', 'login', '2.184.121.80', '2026-07-22 14:17:52', 1, 1, '2026-07-22 14:12:52'),
(16, '09179292500', '796386', 'login', '89.45.156.251', '2026-07-22 19:45:17', 1, 1, '2026-07-22 19:40:17'),
(17, '09305586873', '112284', 'login', '2.184.121.80', '2026-07-23 15:20:31', 1, 1, '2026-07-23 15:15:31'),
(18, '09367186786', '376120', 'register', '89.45.156.251', '2026-07-24 11:05:16', 1, 1, '2026-07-24 11:00:16'),
(19, '09056893928', '677409', 'register', '5.119.156.131', '2026-07-24 11:49:09', 1, 1, '2026-07-24 11:44:09'),
(20, '09331039013', '904739', 'register', '5.120.149.235', '2026-07-24 11:54:31', 1, 1, '2026-07-24 11:49:31'),
(21, '09056893928', '691357', 'login', '5.119.156.131', '2026-07-24 12:07:58', 1, 0, '2026-07-24 12:02:58'),
(22, '09056893928', '662377', 'login', '5.119.156.131', '2026-07-24 12:09:09', 1, 1, '2026-07-24 12:04:09'),
(23, '09930934671', '769821', 'register', '89.45.156.251', '2026-07-24 12:27:49', 1, 1, '2026-07-24 12:22:49'),
(24, '09305586873', '361920', 'login', '89.45.156.251', '2026-07-24 12:31:20', 1, 1, '2026-07-24 12:26:20'),
(25, '09930934671', '586674', 'login', '89.45.156.251', '2026-07-24 12:34:29', 1, 1, '2026-07-24 12:29:29'),
(26, '09305586873', '833295', 'login', '89.45.156.251', '2026-07-24 12:38:43', 1, 1, '2026-07-24 12:33:43'),
(27, '09019177577', '107079', 'login', '217.219.119.181', '2026-07-24 13:43:12', 1, 1, '2026-07-24 13:38:12'),
(28, '09360801254', '534079', 'register', '5.119.231.184', '2026-07-26 10:38:14', 1, 1, '2026-07-26 10:33:14'),
(29, '09395188474', '338499', 'register', '5.119.163.220', '2026-07-28 14:39:41', 1, 1, '2026-07-28 14:34:41');

-- --------------------------------------------------------

--
-- Table structure for table `player_levels`
--

CREATE TABLE `player_levels` (
  `id` int UNSIGNED NOT NULL,
  `level` int UNSIGNED NOT NULL,
  `min_xp` int UNSIGNED NOT NULL COMMENT 'حداقل XP برای این سطح',
  `max_xp` int UNSIGNED NOT NULL COMMENT 'حداکثر XP برای این سطح',
  `title` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'عنوان سطح',
  `color` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '#6366f1' COMMENT 'رنگ سطح',
  `icon` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '⭐' COMMENT 'آیکون سطح'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `player_levels`
--

INSERT INTO `player_levels` (`id`, `level`, `min_xp`, `max_xp`, `title`, `color`, `icon`) VALUES
(1, 1, 0, 499, 'تازه‌کار', '#94a3b8', '🌱'),
(2, 2, 500, 4999, 'مبتدی', '#60a5fa', '🎮'),
(3, 3, 5000, 9999, 'بازیکن', '#3b82f6', '⭐'),
(4, 4, 10000, 99999, 'حرفه‌ای', '#8b5cf6', '🌟'),
(5, 5, 100000, 999999, 'ماهر', '#a855f7', '💫'),
(6, 6, 1000000, 1999999, 'استاد', '#d946ef', '✨'),
(7, 7, 2000000, 2999999, 'افسانه', '#ec4899', '🔥'),
(8, 8, 3000000, 3999999, 'اسطوره', '#f43f5e', '⚡'),
(9, 9, 4000000, 4999999, 'قهرمان', '#f59e0b', '🏆'),
(10, 10, 5000000, 6999999, 'پادشاه', '#eab308', '👑'),
(11, 11, 7000000, 8999999, 'امپراطور', '#dc2626', '💎'),
(12, 12, 9999999, 20000000, 'جاودانه', '#7c2d12', '🌠');

-- --------------------------------------------------------

--
-- Table structure for table `referee_actions_log`
--

CREATE TABLE `referee_actions_log` (
  `id` int UNSIGNED NOT NULL,
  `game_id` int UNSIGNED NOT NULL,
  `referee_id` int UNSIGNED NOT NULL,
  `action_type` enum('create','start','pause','resume','finish','cancel','score_edit','handover','round_add','round_edit') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `target_type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `target_id` int UNSIGNED DEFAULT NULL,
  `old_value` json DEFAULT NULL,
  `new_value` json DEFAULT NULL,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `referee_actions_log`
--

INSERT INTO `referee_actions_log` (`id`, `game_id`, `referee_id`, `action_type`, `target_type`, `target_id`, `old_value`, `new_value`, `ip_address`, `created_at`) VALUES
(713, 149, 1, '', 'game_round', 620, '{\"id\": 620, \"game_id\": 149, \"created_at\": \"2026-07-23 16:17:49\", \"win_type_id\": null, \"round_number\": 3, \"winning_card_id\": 1, \"calculated_score\": \"5.00\", \"winner_team_name\": null, \"winner_participant_id\": 622}', NULL, NULL, '2026-07-23 12:48:32'),
(714, 153, 1, '', 'user', 18, '{\"referee_id\": 1}', '{\"referee_id\": 18}', NULL, '2026-07-24 13:23:40'),
(715, 160, 1, '', 'game_round', 756, '{\"id\": 756, \"game_id\": 160, \"created_at\": \"2026-07-25 18:18:45\", \"win_type_id\": null, \"round_number\": 8, \"winning_card_id\": 1, \"calculated_score\": \"20.00\", \"winner_team_name\": \"تیم 1\", \"winner_participant_id\": 664}', NULL, NULL, '2026-07-25 14:49:02'),
(716, 178, 1, '', 'game_round', 934, '{\"id\": 934, \"game_id\": 178, \"created_at\": \"2026-08-06 14:20:06\", \"win_type_id\": null, \"round_number\": 3, \"winning_card_id\": 9, \"calculated_score\": \"20.00\", \"winner_team_name\": null, \"winner_participant_id\": 729}', NULL, NULL, '2026-08-06 10:50:21'),
(717, 182, 17, '', 'game_round', 975, '{\"id\": 975, \"game_id\": 182, \"created_at\": \"2026-08-07 14:40:55\", \"win_type_id\": null, \"round_number\": 2, \"winning_card_id\": null, \"calculated_score\": \"5.00\", \"winner_team_name\": null, \"winner_participant_id\": 748}', NULL, NULL, '2026-08-07 11:11:06');

-- --------------------------------------------------------

--
-- Table structure for table `sse_events`
--

CREATE TABLE `sse_events` (
  `id` bigint UNSIGNED NOT NULL,
  `channel` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'کانال رویداد (game_1, user_5, ...)',
  `event_type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'نوع رویداد',
  `data` json NOT NULL COMMENT 'داده‌های رویداد',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sse_events`
--

INSERT INTO `sse_events` (`id`, `channel`, `event_type`, `data`, `created_at`) VALUES
(2961, 'game_147', 'game_started', '{\"status\": \"active\", \"game_id\": 147, \"started_at\": \"2026-07-22 17:12:00\", \"first_player\": {\"id\": 616, \"name\": \"امپراطور\"}, \"source_user_id\": 1}', '2026-07-22 13:42:00'),
(2962, 'game_147', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 5, \"winner\": {\"id\": 18, \"name\": \"امپراطور\", \"participant_id\": 616}, \"game_id\": 147, \"round_id\": 592, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-22 17:17:14\", \"round_number\": 1, \"source_user_id\": 1}', '2026-07-22 13:47:14'),
(2963, 'game_147', 'round_recorded', '{\"card\": {\"id\": 9, \"name\": \"هدیه\", \"emoji\": \"🎁\", \"rarity\": \"rare\", \"multiplier\": 2}, \"score\": 10, \"winner\": {\"id\": 18, \"name\": \"امپراطور\", \"participant_id\": 616}, \"game_id\": 147, \"round_id\": 593, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-22 17:20:57\", \"round_number\": 2, \"source_user_id\": 1}', '2026-07-22 13:50:57'),
(2964, 'game_147', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 5, \"winner\": {\"id\": 17, \"name\": \"RANGER\", \"participant_id\": 615}, \"game_id\": 147, \"round_id\": 594, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-22 17:26:13\", \"round_number\": 3, \"source_user_id\": 1}', '2026-07-22 13:56:13'),
(2965, 'game_147', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 5, \"winner\": {\"id\": 18, \"name\": \"امپراطور\", \"participant_id\": 616}, \"game_id\": 147, \"round_id\": 595, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-22 17:33:00\", \"round_number\": 4, \"source_user_id\": 1}', '2026-07-22 14:03:00'),
(2966, 'game_147', 'round_recorded', '{\"card\": {\"id\": 9, \"name\": \"هدیه\", \"emoji\": \"🎁\", \"rarity\": \"rare\", \"multiplier\": 2}, \"score\": 10, \"winner\": {\"id\": 1, \"name\": \"سنتری\", \"participant_id\": 617}, \"game_id\": 147, \"round_id\": 596, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-22 17:35:16\", \"round_number\": 5, \"source_user_id\": 1}', '2026-07-22 14:05:16'),
(2967, 'game_147', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 5, \"winner\": {\"id\": 1, \"name\": \"سنتری\", \"participant_id\": 617}, \"game_id\": 147, \"round_id\": 597, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-22 17:42:42\", \"round_number\": 6, \"source_user_id\": 1}', '2026-07-22 14:12:42'),
(2968, 'game_147', 'round_recorded', '{\"card\": {\"id\": 9, \"name\": \"هدیه\", \"emoji\": \"🎁\", \"rarity\": \"rare\", \"multiplier\": 2}, \"score\": 10, \"winner\": {\"id\": 17, \"name\": \"RANGER\", \"participant_id\": 615}, \"game_id\": 147, \"round_id\": 598, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-22 17:48:40\", \"round_number\": 7, \"source_user_id\": 1}', '2026-07-22 14:18:40'),
(2969, 'game_147', 'game_target_changed', '{\"game_id\": 147, \"max_wins\": 3, \"changed_at\": \"2026-07-22 17:50:01\", \"min_target\": 3, \"new_target\": 5, \"old_target\": 10, \"source_user_id\": 1}', '2026-07-22 14:20:01'),
(2970, 'game_147', 'round_recorded', '{\"card\": {\"id\": 9, \"name\": \"هدیه\", \"emoji\": \"🎁\", \"rarity\": \"rare\", \"multiplier\": 2}, \"score\": 10, \"winner\": {\"id\": 16, \"name\": \"AVANGR\", \"participant_id\": 614}, \"game_id\": 147, \"round_id\": 599, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-22 17:56:21\", \"round_number\": 8, \"source_user_id\": 1}', '2026-07-22 14:26:21'),
(2971, 'game_147', 'round_recorded', '{\"card\": {\"id\": 9, \"name\": \"هدیه\", \"emoji\": \"🎁\", \"rarity\": \"rare\", \"multiplier\": 2}, \"score\": 10, \"winner\": {\"id\": 16, \"name\": \"AVANGR\", \"participant_id\": 614}, \"game_id\": 147, \"round_id\": 600, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-22 18:05:16\", \"round_number\": 9, \"source_user_id\": 1}', '2026-07-22 14:35:16'),
(2972, 'game_147', 'round_recorded', '{\"card\": {\"id\": 14, \"name\": \"جریمه دوتایی انتخابی\", \"emoji\": \"🎯\", \"rarity\": \"rare\", \"multiplier\": 3}, \"score\": 15, \"winner\": {\"id\": 17, \"name\": \"RANGER\", \"participant_id\": 615}, \"game_id\": 147, \"round_id\": 601, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-22 18:09:53\", \"round_number\": 10, \"source_user_id\": 1}', '2026-07-22 14:39:53'),
(2973, 'game_147', 'round_recorded', '{\"card\": {\"id\": 9, \"name\": \"هدیه\", \"emoji\": \"🎁\", \"rarity\": \"rare\", \"multiplier\": 2}, \"score\": 10, \"winner\": {\"id\": 17, \"name\": \"RANGER\", \"participant_id\": 615}, \"game_id\": 147, \"round_id\": 602, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-22 18:13:06\", \"round_number\": 11, \"source_user_id\": 1}', '2026-07-22 14:43:07'),
(2974, 'game_147', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 5, \"winner\": {\"id\": 1, \"name\": \"سنتری\", \"participant_id\": 617}, \"game_id\": 147, \"round_id\": 603, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-22 18:15:35\", \"round_number\": 12, \"source_user_id\": 1}', '2026-07-22 14:45:35'),
(2975, 'game_147', 'round_recorded', '{\"card\": {\"id\": 7, \"name\": \"سپر\", \"emoji\": \"🛡️\", \"rarity\": \"rare\", \"multiplier\": 3}, \"score\": 15, \"winner\": {\"id\": 1, \"name\": \"سنتری\", \"participant_id\": 617}, \"game_id\": 147, \"round_id\": 604, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-22 18:20:38\", \"round_number\": 13, \"source_user_id\": 1}', '2026-07-22 14:50:38'),
(2976, 'game_147', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 5, \"winner\": {\"id\": 18, \"name\": \"امپراطور\", \"participant_id\": 616}, \"game_id\": 147, \"round_id\": 605, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-22 18:24:08\", \"round_number\": 14, \"source_user_id\": 1}', '2026-07-22 14:54:08'),
(2977, 'game_147', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 5, \"winner\": {\"id\": 16, \"name\": \"AVANGR\", \"participant_id\": 614}, \"game_id\": 147, \"round_id\": 606, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-22 18:29:37\", \"round_number\": 15, \"source_user_id\": 1}', '2026-07-22 14:59:37'),
(2978, 'game_147', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 5, \"winner\": {\"id\": 17, \"name\": \"RANGER\", \"participant_id\": 615}, \"game_id\": 147, \"round_id\": 607, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-22 18:34:27\", \"round_number\": 16, \"source_user_id\": 1}', '2026-07-22 15:04:27'),
(2979, 'game_147', 'game_finished', '{\"status\": \"finished\", \"winner\": {\"id\": 17, \"name\": \"RANGER\", \"participant_id\": 615}, \"game_id\": 147, \"finished_at\": \"2026-07-22 19:01:07\", \"total_rounds\": 16, \"source_user_id\": 1}', '2026-07-22 15:31:07'),
(2980, 'game_148', 'game_started', '{\"status\": \"active\", \"game_id\": 148, \"started_at\": \"2026-07-22 22:16:43\", \"first_player\": {\"id\": 618, \"name\": \"RANGER\"}, \"source_user_id\": 1}', '2026-07-22 18:46:43'),
(2981, 'game_148', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 5, \"winner\": {\"id\": 17, \"name\": \"RANGER\", \"participant_id\": 618}, \"game_id\": 148, \"round_id\": 608, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-22 22:20:13\", \"round_number\": 1, \"source_user_id\": 1}', '2026-07-22 18:50:13'),
(2982, 'game_148', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 5, \"winner\": {\"id\": 18, \"name\": \"امپراطور\", \"participant_id\": 619}, \"game_id\": 148, \"round_id\": 609, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-22 22:24:27\", \"round_number\": 2, \"source_user_id\": 1}', '2026-07-22 18:54:27'),
(2983, 'game_148', 'round_recorded', '{\"card\": {\"id\": 2, \"name\": \"پرش یکی\", \"emoji\": \"⏭️\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 5, \"winner\": {\"id\": 17, \"name\": \"RANGER\", \"participant_id\": 618}, \"game_id\": 148, \"round_id\": 610, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-22 22:27:13\", \"round_number\": 3, \"source_user_id\": 1}', '2026-07-22 18:57:13'),
(2984, 'game_148', 'round_recorded', '{\"card\": {\"id\": 5, \"name\": \"تغییر رنگ\", \"emoji\": \"🌈\", \"rarity\": \"common\", \"multiplier\": 2}, \"score\": 10, \"winner\": {\"id\": 1, \"name\": \"سنتری\", \"participant_id\": 620}, \"game_id\": 148, \"round_id\": 611, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-22 22:34:03\", \"round_number\": 4, \"source_user_id\": 1}', '2026-07-22 19:04:03'),
(2985, 'game_148', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 5, \"winner\": {\"id\": 18, \"name\": \"امپراطور\", \"participant_id\": 619}, \"game_id\": 148, \"round_id\": 612, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-22 22:36:33\", \"round_number\": 5, \"source_user_id\": 1}', '2026-07-22 19:06:33'),
(2986, 'game_148', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 5, \"winner\": {\"id\": 17, \"name\": \"RANGER\", \"participant_id\": 618}, \"game_id\": 148, \"round_id\": 613, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-22 22:40:14\", \"round_number\": 6, \"source_user_id\": 1}', '2026-07-22 19:10:14'),
(2987, 'game_148', 'round_recorded', '{\"card\": {\"id\": 9, \"name\": \"هدیه\", \"emoji\": \"🎁\", \"rarity\": \"rare\", \"multiplier\": 2}, \"score\": 10, \"winner\": {\"id\": 18, \"name\": \"امپراطور\", \"participant_id\": 619}, \"game_id\": 148, \"round_id\": 614, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-22 22:43:02\", \"round_number\": 7, \"source_user_id\": 1}', '2026-07-22 19:13:02'),
(2988, 'game_148', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 5, \"winner\": {\"id\": 18, \"name\": \"امپراطور\", \"participant_id\": 619}, \"game_id\": 148, \"round_id\": 615, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-22 22:44:52\", \"round_number\": 8, \"source_user_id\": 1}', '2026-07-22 19:14:52'),
(2989, 'game_148', 'round_recorded', '{\"card\": {\"id\": 9, \"name\": \"هدیه\", \"emoji\": \"🎁\", \"rarity\": \"rare\", \"multiplier\": 2}, \"score\": 10, \"winner\": {\"id\": 1, \"name\": \"سنتری\", \"participant_id\": 620}, \"game_id\": 148, \"round_id\": 616, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-22 22:47:20\", \"round_number\": 9, \"source_user_id\": 1}', '2026-07-22 19:17:20'),
(2990, 'game_148', 'round_recorded', '{\"card\": {\"id\": 9, \"name\": \"هدیه\", \"emoji\": \"🎁\", \"rarity\": \"rare\", \"multiplier\": 2}, \"score\": 10, \"winner\": {\"id\": 18, \"name\": \"امپراطور\", \"participant_id\": 619}, \"game_id\": 148, \"round_id\": 617, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-22 22:51:29\", \"round_number\": 10, \"source_user_id\": 1}', '2026-07-22 19:21:29'),
(2991, 'game_148', 'game_finished', '{\"status\": \"finished\", \"winner\": {\"id\": 18, \"name\": \"امپراطور\", \"participant_id\": 619}, \"game_id\": 148, \"finished_at\": \"2026-07-22 22:52:32\", \"total_rounds\": 10, \"source_user_id\": 1}', '2026-07-22 19:22:32'),
(2992, 'game_149', 'game_started', '{\"status\": \"active\", \"game_id\": 149, \"started_at\": \"2026-07-23 16:05:15\", \"first_player\": {\"id\": 622, \"name\": \"RANGER\"}, \"source_user_id\": 1}', '2026-07-23 12:35:15'),
(2993, 'game_149', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 5, \"winner\": {\"id\": 17, \"name\": \"RANGER\", \"participant_id\": 622}, \"game_id\": 149, \"round_id\": 618, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-23 16:10:10\", \"round_number\": 1, \"source_user_id\": 1}', '2026-07-23 12:40:10'),
(2994, 'game_149', 'round_recorded', '{\"card\": {\"id\": 9, \"name\": \"هدیه\", \"emoji\": \"🎁\", \"rarity\": \"rare\", \"multiplier\": 2}, \"score\": 10, \"winner\": {\"id\": 1, \"name\": \"سنتری\", \"participant_id\": 624}, \"game_id\": 149, \"round_id\": 619, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-23 16:12:51\", \"round_number\": 2, \"source_user_id\": 1}', '2026-07-23 12:42:51'),
(2995, 'game_149', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 5, \"winner\": {\"id\": 17, \"name\": \"RANGER\", \"participant_id\": 622}, \"game_id\": 149, \"round_id\": 620, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-23 16:17:49\", \"round_number\": 3, \"source_user_id\": 1}', '2026-07-23 12:47:49'),
(2996, 'game_149', 'round_undone', '{\"game_id\": 149, \"undone_at\": \"2026-07-23 16:18:32\", \"undone_round\": 3, \"source_user_id\": 1}', '2026-07-23 12:48:32'),
(2997, 'game_149', 'round_recorded', '{\"card\": {\"id\": 10, \"name\": \"تعویض\", \"emoji\": \"🔀\", \"rarity\": \"rare\", \"multiplier\": 4}, \"score\": 20, \"winner\": {\"id\": 17, \"name\": \"RANGER\", \"participant_id\": 622}, \"game_id\": 149, \"round_id\": 621, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-23 16:18:47\", \"round_number\": 3, \"source_user_id\": 1}', '2026-07-23 12:48:47'),
(2998, 'game_149', 'round_recorded', '{\"card\": {\"id\": 7, \"name\": \"سپر\", \"emoji\": \"🛡️\", \"rarity\": \"rare\", \"multiplier\": 3}, \"score\": 15, \"winner\": {\"id\": 1, \"name\": \"سنتری\", \"participant_id\": 624}, \"game_id\": 149, \"round_id\": 622, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-23 16:22:43\", \"round_number\": 4, \"source_user_id\": 1}', '2026-07-23 12:52:43'),
(2999, 'game_149', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 5, \"winner\": {\"id\": 18, \"name\": \"امپراطور\", \"participant_id\": 623}, \"game_id\": 149, \"round_id\": 623, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-23 16:25:41\", \"round_number\": 5, \"source_user_id\": 1}', '2026-07-23 12:55:42'),
(3000, 'game_149', 'game_target_changed', '{\"game_id\": 149, \"max_wins\": 2, \"changed_at\": \"2026-07-23 16:26:16\", \"min_target\": 3, \"new_target\": 5, \"old_target\": 10, \"source_user_id\": 1}', '2026-07-23 12:56:16'),
(3001, 'game_149', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 5, \"winner\": {\"id\": 17, \"name\": \"RANGER\", \"participant_id\": 622}, \"game_id\": 149, \"round_id\": 624, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-23 16:34:01\", \"round_number\": 6, \"source_user_id\": 1}', '2026-07-23 13:04:01'),
(3002, 'game_149', 'round_recorded', '{\"card\": {\"id\": 9, \"name\": \"هدیه\", \"emoji\": \"🎁\", \"rarity\": \"rare\", \"multiplier\": 2}, \"score\": 10, \"winner\": {\"id\": 17, \"name\": \"RANGER\", \"participant_id\": 622}, \"game_id\": 149, \"round_id\": 625, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-23 16:36:37\", \"round_number\": 7, \"source_user_id\": 1}', '2026-07-23 13:06:37'),
(3003, 'game_149', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 5, \"winner\": {\"id\": 1, \"name\": \"سنتری\", \"participant_id\": 624}, \"game_id\": 149, \"round_id\": 626, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-23 16:39:39\", \"round_number\": 8, \"source_user_id\": 1}', '2026-07-23 13:09:40'),
(3004, 'game_149', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 5, \"winner\": {\"id\": 17, \"name\": \"RANGER\", \"participant_id\": 622}, \"game_id\": 149, \"round_id\": 627, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-23 16:45:26\", \"round_number\": 9, \"source_user_id\": 1}', '2026-07-23 13:15:26'),
(3005, 'game_149', 'game_finished', '{\"status\": \"finished\", \"winner\": {\"id\": 17, \"name\": \"RANGER\", \"participant_id\": 622}, \"game_id\": 149, \"finished_at\": \"2026-07-23 16:45:32\", \"total_rounds\": 9, \"source_user_id\": 1}', '2026-07-23 13:15:32'),
(3006, 'game_150', 'game_started', '{\"status\": \"active\", \"game_id\": 150, \"started_at\": \"2026-07-23 16:47:31\", \"first_player\": {\"id\": 628, \"name\": \"سنتری\"}, \"source_user_id\": 1}', '2026-07-23 13:17:31'),
(3007, 'game_150', 'round_recorded', '{\"card\": {\"id\": 14, \"name\": \"جریمه دوتایی انتخابی\", \"emoji\": \"🎯\", \"rarity\": \"rare\", \"multiplier\": 3}, \"score\": 30, \"winner\": {\"id\": 16, \"name\": \"KiNG\", \"participant_id\": 625}, \"game_id\": 150, \"round_id\": 628, \"win_type\": null, \"team_name\": \"تیم 1\", \"recorded_at\": \"2026-07-23 16:51:55\", \"round_number\": 1, \"source_user_id\": 1}', '2026-07-23 13:21:55'),
(3008, 'game_150', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 10, \"winner\": {\"id\": 16, \"name\": \"KiNG\", \"participant_id\": 625}, \"game_id\": 150, \"round_id\": 629, \"win_type\": null, \"team_name\": \"تیم 1\", \"recorded_at\": \"2026-07-23 16:57:00\", \"round_number\": 2, \"source_user_id\": 1}', '2026-07-23 13:27:00'),
(3009, 'game_150', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 10, \"winner\": {\"id\": 16, \"name\": \"KiNG\", \"participant_id\": 625}, \"game_id\": 150, \"round_id\": 630, \"win_type\": null, \"team_name\": \"تیم 1\", \"recorded_at\": \"2026-07-23 17:00:54\", \"round_number\": 3, \"source_user_id\": 1}', '2026-07-23 13:30:54'),
(3010, 'game_150', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 10, \"winner\": {\"id\": 1, \"name\": \"سنتری\", \"participant_id\": 628}, \"game_id\": 150, \"round_id\": 631, \"win_type\": null, \"team_name\": \"تیم 2\", \"recorded_at\": \"2026-07-23 17:03:28\", \"round_number\": 4, \"source_user_id\": 1}', '2026-07-23 13:33:28'),
(3011, 'game_150', 'round_recorded', '{\"card\": {\"id\": 6, \"name\": \"جریمه ۴ تایی\", \"emoji\": \"+4\", \"rarity\": \"rare\", \"multiplier\": 3}, \"score\": 30, \"winner\": {\"id\": 1, \"name\": \"سنتری\", \"participant_id\": 628}, \"game_id\": 150, \"round_id\": 632, \"win_type\": null, \"team_name\": \"تیم 2\", \"recorded_at\": \"2026-07-23 17:08:02\", \"round_number\": 5, \"source_user_id\": 1}', '2026-07-23 13:38:02'),
(3012, 'game_150', 'round_recorded', '{\"card\": {\"id\": 9, \"name\": \"هدیه\", \"emoji\": \"🎁\", \"rarity\": \"rare\", \"multiplier\": 2}, \"score\": 20, \"winner\": {\"id\": 1, \"name\": \"سنتری\", \"participant_id\": 628}, \"game_id\": 150, \"round_id\": 633, \"win_type\": null, \"team_name\": \"تیم 2\", \"recorded_at\": \"2026-07-23 17:10:28\", \"round_number\": 6, \"source_user_id\": 1}', '2026-07-23 13:40:28'),
(3013, 'game_150', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 10, \"winner\": {\"id\": 1, \"name\": \"سنتری\", \"participant_id\": 628}, \"game_id\": 150, \"round_id\": 634, \"win_type\": null, \"team_name\": \"تیم 2\", \"recorded_at\": \"2026-07-23 17:13:28\", \"round_number\": 7, \"source_user_id\": 1}', '2026-07-23 13:43:28'),
(3014, 'game_150', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 10, \"winner\": {\"id\": 16, \"name\": \"KiNG\", \"participant_id\": 625}, \"game_id\": 150, \"round_id\": 635, \"win_type\": null, \"team_name\": \"تیم 1\", \"recorded_at\": \"2026-07-23 17:15:56\", \"round_number\": 8, \"source_user_id\": 1}', '2026-07-23 13:45:56'),
(3015, 'game_150', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 10, \"winner\": {\"id\": 16, \"name\": \"KiNG\", \"participant_id\": 625}, \"game_id\": 150, \"round_id\": 636, \"win_type\": null, \"team_name\": \"تیم 1\", \"recorded_at\": \"2026-07-23 17:20:16\", \"round_number\": 9, \"source_user_id\": 1}', '2026-07-23 13:50:16'),
(3016, 'game_150', 'game_finished', '{\"status\": \"finished\", \"winner\": {\"id\": 16, \"name\": \"تیم تیم 1\", \"participant_id\": 625}, \"game_id\": 150, \"finished_at\": \"2026-07-23 17:21:08\", \"total_rounds\": 9, \"source_user_id\": 1}', '2026-07-23 13:51:08'),
(3017, 'game_151', 'game_started', '{\"status\": \"active\", \"game_id\": 151, \"started_at\": \"2026-07-23 17:21:18\", \"first_player\": {\"id\": 630, \"name\": \"RANGER\"}, \"source_user_id\": 1}', '2026-07-23 13:51:18'),
(3018, 'game_151', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 10, \"winner\": {\"id\": 1, \"name\": \"سنتری\", \"participant_id\": 632}, \"game_id\": 151, \"round_id\": 637, \"win_type\": null, \"team_name\": \"تیم 2\", \"recorded_at\": \"2026-07-23 17:23:29\", \"round_number\": 1, \"source_user_id\": 1}', '2026-07-23 13:53:29'),
(3019, 'game_151', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 10, \"winner\": {\"id\": 1, \"name\": \"سنتری\", \"participant_id\": 632}, \"game_id\": 151, \"round_id\": 638, \"win_type\": null, \"team_name\": \"تیم 2\", \"recorded_at\": \"2026-07-23 17:28:43\", \"round_number\": 2, \"source_user_id\": 1}', '2026-07-23 13:58:43'),
(3020, 'game_151', 'round_recorded', '{\"card\": {\"id\": 3, \"name\": \"دوربرگردان\", \"emoji\": \"🔄\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 10, \"winner\": {\"id\": 16, \"name\": \"KiNG\", \"participant_id\": 629}, \"game_id\": 151, \"round_id\": 639, \"win_type\": null, \"team_name\": \"تیم 1\", \"recorded_at\": \"2026-07-23 17:31:46\", \"round_number\": 3, \"source_user_id\": 1}', '2026-07-23 14:01:46'),
(3021, 'game_151', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 10, \"winner\": {\"id\": 17, \"name\": \"RANGER\", \"participant_id\": 630}, \"game_id\": 151, \"round_id\": 640, \"win_type\": null, \"team_name\": \"تیم 1\", \"recorded_at\": \"2026-07-23 17:35:58\", \"round_number\": 4, \"source_user_id\": 1}', '2026-07-23 14:05:58'),
(3022, 'game_151', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 10, \"winner\": {\"id\": 1, \"name\": \"سنتری\", \"participant_id\": 632}, \"game_id\": 151, \"round_id\": 641, \"win_type\": null, \"team_name\": \"تیم 2\", \"recorded_at\": \"2026-07-23 17:43:32\", \"round_number\": 5, \"source_user_id\": 1}', '2026-07-23 14:13:32'),
(3023, 'game_151', 'game_finished', '{\"status\": \"finished\", \"winner\": {\"id\": 18, \"name\": \"تیم تیم 2\", \"participant_id\": 631}, \"game_id\": 151, \"finished_at\": \"2026-07-23 17:43:45\", \"total_rounds\": 5, \"source_user_id\": 1}', '2026-07-23 14:13:45'),
(3024, 'game_146', 'game_started', '{\"status\": \"active\", \"game_id\": 146, \"started_at\": \"2026-07-23 17:45:06\", \"first_player\": {\"id\": 611, \"name\": \"KiNG\"}, \"source_user_id\": 1}', '2026-07-23 14:15:07'),
(3025, 'game_146', 'game_target_changed', '{\"game_id\": 146, \"max_wins\": 0, \"changed_at\": \"2026-07-23 17:45:16\", \"min_target\": 3, \"new_target\": 10, \"old_target\": 5, \"source_user_id\": 1}', '2026-07-23 14:15:16'),
(3026, 'game_146', 'round_recorded', '{\"card\": {\"id\": 9, \"name\": \"هدیه\", \"emoji\": \"🎁\", \"rarity\": \"rare\", \"multiplier\": 2}, \"score\": 10, \"winner\": {\"id\": 16, \"name\": \"KiNG\", \"participant_id\": 611}, \"game_id\": 146, \"round_id\": 642, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-23 17:48:15\", \"round_number\": 1, \"source_user_id\": 1}', '2026-07-23 14:18:15'),
(3027, 'game_146', 'round_recorded', '{\"card\": {\"id\": 11, \"name\": \"پرش دوتایی\", \"emoji\": \"⏩\", \"rarity\": \"rare\", \"multiplier\": 5}, \"score\": 25, \"winner\": {\"id\": 16, \"name\": \"KiNG\", \"participant_id\": 611}, \"game_id\": 146, \"round_id\": 643, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-23 17:49:54\", \"round_number\": 2, \"source_user_id\": 1}', '2026-07-23 14:19:54'),
(3028, 'game_146', 'round_recorded', '{\"card\": {\"id\": 9, \"name\": \"هدیه\", \"emoji\": \"🎁\", \"rarity\": \"rare\", \"multiplier\": 2}, \"score\": 10, \"winner\": {\"id\": 1, \"name\": \"سنتری\", \"participant_id\": 613}, \"game_id\": 146, \"round_id\": 644, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-23 17:51:04\", \"round_number\": 3, \"source_user_id\": 1}', '2026-07-23 14:21:04'),
(3029, 'game_146', 'round_recorded', '{\"card\": {\"id\": 9, \"name\": \"هدیه\", \"emoji\": \"🎁\", \"rarity\": \"rare\", \"multiplier\": 2}, \"score\": 10, \"winner\": {\"id\": 1, \"name\": \"سنتری\", \"participant_id\": 613}, \"game_id\": 146, \"round_id\": 645, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-23 17:55:38\", \"round_number\": 4, \"source_user_id\": 1}', '2026-07-23 14:25:38'),
(3030, 'game_146', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 5, \"winner\": {\"id\": 17, \"name\": \"RANGER\", \"participant_id\": 612}, \"game_id\": 146, \"round_id\": 646, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-23 17:57:30\", \"round_number\": 5, \"source_user_id\": 1}', '2026-07-23 14:27:30'),
(3031, 'game_146', 'round_recorded', '{\"card\": {\"id\": 5, \"name\": \"تغییر رنگ\", \"emoji\": \"🌈\", \"rarity\": \"common\", \"multiplier\": 2}, \"score\": 10, \"winner\": {\"id\": 17, \"name\": \"RANGER\", \"participant_id\": 612}, \"game_id\": 146, \"round_id\": 647, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-23 17:59:45\", \"round_number\": 6, \"source_user_id\": 1}', '2026-07-23 14:29:45'),
(3032, 'game_146', 'round_recorded', '{\"card\": {\"id\": 9, \"name\": \"هدیه\", \"emoji\": \"🎁\", \"rarity\": \"rare\", \"multiplier\": 2}, \"score\": 10, \"winner\": {\"id\": 1, \"name\": \"سنتری\", \"participant_id\": 613}, \"game_id\": 146, \"round_id\": 648, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-23 18:01:42\", \"round_number\": 7, \"source_user_id\": 1}', '2026-07-23 14:31:42'),
(3033, 'game_146', 'round_recorded', '{\"card\": {\"id\": 9, \"name\": \"هدیه\", \"emoji\": \"🎁\", \"rarity\": \"rare\", \"multiplier\": 2}, \"score\": 10, \"winner\": {\"id\": 16, \"name\": \"KiNG\", \"participant_id\": 611}, \"game_id\": 146, \"round_id\": 649, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-23 18:04:40\", \"round_number\": 8, \"source_user_id\": 1}', '2026-07-23 14:34:40'),
(3034, 'game_146', 'round_recorded', '{\"card\": {\"id\": 4, \"name\": \"جریمه دوتایی\", \"emoji\": \"+2\", \"rarity\": \"common\", \"multiplier\": 2}, \"score\": 10, \"winner\": {\"id\": 17, \"name\": \"RANGER\", \"participant_id\": 612}, \"game_id\": 146, \"round_id\": 650, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-23 18:06:32\", \"round_number\": 9, \"source_user_id\": 1}', '2026-07-23 14:36:32'),
(3035, 'game_146', 'round_recorded', '{\"card\": {\"id\": 9, \"name\": \"هدیه\", \"emoji\": \"🎁\", \"rarity\": \"rare\", \"multiplier\": 2}, \"score\": 10, \"winner\": {\"id\": 17, \"name\": \"RANGER\", \"participant_id\": 612}, \"game_id\": 146, \"round_id\": 651, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-23 18:08:46\", \"round_number\": 10, \"source_user_id\": 1}', '2026-07-23 14:38:47'),
(3036, 'game_146', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 5, \"winner\": {\"id\": 1, \"name\": \"سنتری\", \"participant_id\": 613}, \"game_id\": 146, \"round_id\": 652, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-23 18:10:25\", \"round_number\": 11, \"source_user_id\": 1}', '2026-07-23 14:40:25'),
(3037, 'game_146', 'round_recorded', '{\"card\": {\"id\": 9, \"name\": \"هدیه\", \"emoji\": \"🎁\", \"rarity\": \"rare\", \"multiplier\": 2}, \"score\": 10, \"winner\": {\"id\": 16, \"name\": \"KiNG\", \"participant_id\": 611}, \"game_id\": 146, \"round_id\": 653, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-23 18:13:47\", \"round_number\": 12, \"source_user_id\": 1}', '2026-07-23 14:43:47'),
(3038, 'game_146', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 5, \"winner\": {\"id\": 1, \"name\": \"سنتری\", \"participant_id\": 613}, \"game_id\": 146, \"round_id\": 654, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-23 18:17:35\", \"round_number\": 13, \"source_user_id\": 1}', '2026-07-23 14:47:36'),
(3039, 'game_146', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 5, \"winner\": {\"id\": 17, \"name\": \"RANGER\", \"participant_id\": 612}, \"game_id\": 146, \"round_id\": 655, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-23 18:20:57\", \"round_number\": 14, \"source_user_id\": 1}', '2026-07-23 14:50:57'),
(3040, 'game_146', 'round_recorded', '{\"card\": {\"id\": 7, \"name\": \"سپر\", \"emoji\": \"🛡️\", \"rarity\": \"rare\", \"multiplier\": 3}, \"score\": 15, \"winner\": {\"id\": 1, \"name\": \"سنتری\", \"participant_id\": 613}, \"game_id\": 146, \"round_id\": 656, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-23 18:22:58\", \"round_number\": 15, \"source_user_id\": 1}', '2026-07-23 14:52:58'),
(3041, 'game_146', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 5, \"winner\": {\"id\": 17, \"name\": \"RANGER\", \"participant_id\": 612}, \"game_id\": 146, \"round_id\": 657, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-23 18:26:38\", \"round_number\": 16, \"source_user_id\": 1}', '2026-07-23 14:56:38'),
(3042, 'game_146', 'round_recorded', '{\"card\": {\"id\": 5, \"name\": \"تغییر رنگ\", \"emoji\": \"🌈\", \"rarity\": \"common\", \"multiplier\": 2}, \"score\": 10, \"winner\": {\"id\": 17, \"name\": \"RANGER\", \"participant_id\": 612}, \"game_id\": 146, \"round_id\": 658, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-23 18:28:30\", \"round_number\": 17, \"source_user_id\": 1}', '2026-07-23 14:58:30'),
(3043, 'game_146', 'round_recorded', '{\"card\": {\"id\": 5, \"name\": \"تغییر رنگ\", \"emoji\": \"🌈\", \"rarity\": \"common\", \"multiplier\": 2}, \"score\": 10, \"winner\": {\"id\": 1, \"name\": \"سنتری\", \"participant_id\": 613}, \"game_id\": 146, \"round_id\": 659, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-23 18:30:19\", \"round_number\": 18, \"source_user_id\": 1}', '2026-07-23 15:00:19'),
(3044, 'game_146', 'round_recorded', '{\"card\": {\"id\": 11, \"name\": \"پرش دوتایی\", \"emoji\": \"⏩\", \"rarity\": \"rare\", \"multiplier\": 5}, \"score\": 25, \"winner\": {\"id\": 17, \"name\": \"RANGER\", \"participant_id\": 612}, \"game_id\": 146, \"round_id\": 660, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-23 18:35:23\", \"round_number\": 19, \"source_user_id\": 1}', '2026-07-23 15:05:23'),
(3045, 'game_146', 'round_recorded', '{\"card\": {\"id\": 11, \"name\": \"پرش دوتایی\", \"emoji\": \"⏩\", \"rarity\": \"rare\", \"multiplier\": 5}, \"score\": 25, \"winner\": {\"id\": 17, \"name\": \"RANGER\", \"participant_id\": 612}, \"game_id\": 146, \"round_id\": 661, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-23 18:37:18\", \"round_number\": 20, \"source_user_id\": 1}', '2026-07-23 15:07:18'),
(3046, 'game_146', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 5, \"winner\": {\"id\": 16, \"name\": \"KiNG\", \"participant_id\": 611}, \"game_id\": 146, \"round_id\": 662, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-23 18:43:56\", \"round_number\": 21, \"source_user_id\": 1}', '2026-07-23 15:13:56'),
(3047, 'game_146', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 5, \"winner\": {\"id\": 17, \"name\": \"RANGER\", \"participant_id\": 612}, \"game_id\": 146, \"round_id\": 663, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-23 18:47:01\", \"round_number\": 22, \"source_user_id\": 1}', '2026-07-23 15:17:01'),
(3048, 'game_146', 'game_finished', '{\"status\": \"finished\", \"winner\": {\"id\": 17, \"name\": \"RANGER\", \"participant_id\": 612}, \"game_id\": 146, \"finished_at\": \"2026-07-23 18:47:08\", \"total_rounds\": 22, \"source_user_id\": 1}', '2026-07-23 15:17:08'),
(3049, 'game_153', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 5, \"winner\": {\"id\": 18, \"name\": \"امپراطور\", \"participant_id\": 639}, \"game_id\": 153, \"round_id\": 675, \"win_type\": {\"id\": 1, \"icon\": \"✅\", \"name\": \"برد معمولی\", \"multiplier\": 1}, \"team_name\": null, \"recorded_at\": \"2026-07-24 17:19:31\", \"round_number\": 6, \"source_user_id\": 18}', '2026-07-24 13:49:31'),
(3050, 'game_153', 'game_status_changed', '{\"status\": \"paused\", \"game_id\": 153, \"source_user_id\": 18}', '2026-07-24 13:51:34'),
(3051, 'game_153', 'game_status_changed', '{\"status\": \"active\", \"game_id\": 153, \"source_user_id\": 18}', '2026-07-24 13:51:48'),
(3052, 'game_153', 'game_target_changed', '{\"game_id\": 153, \"max_wins\": 2, \"changed_at\": \"2026-07-24 17:22:51\", \"min_target\": 3, \"new_target\": 3, \"old_target\": 5, \"source_user_id\": 18}', '2026-07-24 13:52:51'),
(3053, 'game_153', 'round_recorded', '{\"card\": {\"id\": 14, \"name\": \"جریمه دوتایی انتخابی\", \"emoji\": \"🎯\", \"rarity\": \"rare\", \"multiplier\": 3}, \"score\": 15, \"winner\": {\"id\": 16, \"name\": \"KiNG\", \"participant_id\": 637}, \"game_id\": 153, \"round_id\": 676, \"win_type\": {\"id\": 1, \"icon\": \"✅\", \"name\": \"برد معمولی\", \"multiplier\": 1}, \"team_name\": null, \"recorded_at\": \"2026-07-24 17:23:54\", \"round_number\": 7, \"source_user_id\": 18}', '2026-07-24 13:53:54'),
(3054, 'game_153', 'game_finished', '{\"status\": \"finished\", \"winner\": {\"id\": 16, \"name\": \"KiNG\", \"participant_id\": 637}, \"game_id\": 153, \"finished_at\": \"2026-07-24 17:24:30\", \"total_rounds\": 7, \"source_user_id\": 18}', '2026-07-24 13:54:30'),
(3055, 'game_154', 'game_started', '{\"status\": \"active\", \"game_id\": 154, \"started_at\": \"2026-07-24 17:26:04\", \"first_player\": {\"id\": 643, \"name\": \"امپراطور\"}, \"source_user_id\": 1}', '2026-07-24 13:56:04'),
(3056, 'game_154', 'round_recorded', '{\"card\": {\"id\": 9, \"name\": \"هدیه\", \"emoji\": \"🎁\", \"rarity\": \"rare\", \"multiplier\": 2}, \"score\": 10, \"winner\": {\"id\": 20, \"name\": \"فرانکلین\", \"participant_id\": 645}, \"game_id\": 154, \"round_id\": 677, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-24 17:32:14\", \"round_number\": 1, \"source_user_id\": 1}', '2026-07-24 14:02:14'),
(3057, 'game_154', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 5, \"winner\": {\"id\": 16, \"name\": \"KiNG\", \"participant_id\": 642}, \"game_id\": 154, \"round_id\": 678, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-24 17:36:28\", \"round_number\": 2, \"source_user_id\": 1}', '2026-07-24 14:06:28'),
(3058, 'game_154', 'game_status_changed', '{\"status\": \"paused\", \"game_id\": 154, \"source_user_id\": 1}', '2026-07-24 14:09:57'),
(3059, 'game_154', 'game_status_changed', '{\"status\": \"active\", \"game_id\": 154, \"source_user_id\": 1}', '2026-07-24 14:10:07'),
(3060, 'game_154', 'round_recorded', '{\"card\": {\"id\": 9, \"name\": \"هدیه\", \"emoji\": \"🎁\", \"rarity\": \"rare\", \"multiplier\": 2}, \"score\": 10, \"winner\": {\"id\": 1, \"name\": \"سنتری\", \"participant_id\": 644}, \"game_id\": 154, \"round_id\": 679, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-24 17:43:20\", \"round_number\": 3, \"source_user_id\": 1}', '2026-07-24 14:13:20'),
(3061, 'game_154', 'round_recorded', '{\"card\": {\"id\": 9, \"name\": \"هدیه\", \"emoji\": \"🎁\", \"rarity\": \"rare\", \"multiplier\": 2}, \"score\": 10, \"winner\": {\"id\": 16, \"name\": \"KiNG\", \"participant_id\": 642}, \"game_id\": 154, \"round_id\": 680, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-24 17:48:15\", \"round_number\": 4, \"source_user_id\": 1}', '2026-07-24 14:18:15'),
(3062, 'game_154', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 5, \"winner\": {\"id\": 1, \"name\": \"سنتری\", \"participant_id\": 644}, \"game_id\": 154, \"round_id\": 681, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-24 17:53:17\", \"round_number\": 5, \"source_user_id\": 1}', '2026-07-24 14:23:17'),
(3063, 'game_154', 'round_recorded', '{\"card\": {\"id\": 4, \"name\": \"جریمه دوتایی\", \"emoji\": \"+2\", \"rarity\": \"common\", \"multiplier\": 2}, \"score\": 10, \"winner\": {\"id\": 21, \"name\": \"کماندو\", \"participant_id\": 646}, \"game_id\": 154, \"round_id\": 682, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-24 17:59:07\", \"round_number\": 6, \"source_user_id\": 1}', '2026-07-24 14:29:07'),
(3064, 'game_154', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 5, \"winner\": {\"id\": 1, \"name\": \"سنتری\", \"participant_id\": 644}, \"game_id\": 154, \"round_id\": 683, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-24 18:03:45\", \"round_number\": 7, \"source_user_id\": 1}', '2026-07-24 14:33:45'),
(3065, 'game_154', 'game_finished', '{\"status\": \"finished\", \"winner\": {\"id\": 1, \"name\": \"سنتری\", \"participant_id\": 644}, \"game_id\": 154, \"finished_at\": \"2026-07-24 18:04:03\", \"total_rounds\": 7, \"source_user_id\": 1}', '2026-07-24 14:34:03'),
(3066, 'game_155', 'game_started', '{\"status\": \"active\", \"game_id\": 155, \"started_at\": \"2026-07-24 18:05:53\", \"first_player\": {\"id\": 647, \"name\": \"KiNG\"}, \"source_user_id\": 1}', '2026-07-24 14:35:53'),
(3067, 'game_155', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 5, \"winner\": {\"id\": 16, \"name\": \"KiNG\", \"participant_id\": 647}, \"game_id\": 155, \"round_id\": 684, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-24 18:13:25\", \"round_number\": 1, \"source_user_id\": 1}', '2026-07-24 14:43:25'),
(3068, 'game_155', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 5, \"winner\": {\"id\": 1, \"name\": \"سنتری\", \"participant_id\": 649}, \"game_id\": 155, \"round_id\": 685, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-24 18:17:32\", \"round_number\": 2, \"source_user_id\": 1}', '2026-07-24 14:47:32'),
(3069, 'game_155', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 5, \"winner\": {\"id\": 1, \"name\": \"سنتری\", \"participant_id\": 649}, \"game_id\": 155, \"round_id\": 686, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-24 18:19:22\", \"round_number\": 3, \"source_user_id\": 1}', '2026-07-24 14:49:22'),
(3070, 'game_155', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 5, \"winner\": {\"id\": 1, \"name\": \"سنتری\", \"participant_id\": 649}, \"game_id\": 155, \"round_id\": 687, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-24 18:24:28\", \"round_number\": 4, \"source_user_id\": 1}', '2026-07-24 14:54:28'),
(3071, 'game_155', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 5, \"winner\": {\"id\": 18, \"name\": \"امپراطور\", \"participant_id\": 648}, \"game_id\": 155, \"round_id\": 688, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-24 18:27:22\", \"round_number\": 5, \"source_user_id\": 1}', '2026-07-24 14:57:22'),
(3072, 'game_155', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 5, \"winner\": {\"id\": 1, \"name\": \"سنتری\", \"participant_id\": 649}, \"game_id\": 155, \"round_id\": 689, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-24 18:31:06\", \"round_number\": 6, \"source_user_id\": 1}', '2026-07-24 15:01:06'),
(3073, 'game_155', 'game_target_changed', '{\"game_id\": 155, \"max_wins\": 4, \"changed_at\": \"2026-07-24 18:31:41\", \"min_target\": 4, \"new_target\": 10, \"old_target\": 5, \"source_user_id\": 1}', '2026-07-24 15:01:41'),
(3074, 'game_155', 'round_recorded', '{\"card\": {\"id\": 2, \"name\": \"پرش یکی\", \"emoji\": \"⏭️\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 5, \"winner\": {\"id\": 16, \"name\": \"KiNG\", \"participant_id\": 647}, \"game_id\": 155, \"round_id\": 690, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-24 18:34:32\", \"round_number\": 7, \"source_user_id\": 1}', '2026-07-24 15:04:32'),
(3075, 'game_155', 'round_recorded', '{\"card\": {\"id\": 9, \"name\": \"هدیه\", \"emoji\": \"🎁\", \"rarity\": \"rare\", \"multiplier\": 2}, \"score\": 10, \"winner\": {\"id\": 1, \"name\": \"سنتری\", \"participant_id\": 649}, \"game_id\": 155, \"round_id\": 691, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-24 18:38:47\", \"round_number\": 8, \"source_user_id\": 1}', '2026-07-24 15:08:47'),
(3076, 'game_155', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 5, \"winner\": {\"id\": 1, \"name\": \"سنتری\", \"participant_id\": 649}, \"game_id\": 155, \"round_id\": 692, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-24 18:42:34\", \"round_number\": 9, \"source_user_id\": 1}', '2026-07-24 15:12:34'),
(3077, 'game_155', 'round_recorded', '{\"card\": {\"id\": 9, \"name\": \"هدیه\", \"emoji\": \"🎁\", \"rarity\": \"rare\", \"multiplier\": 2}, \"score\": 10, \"winner\": {\"id\": 1, \"name\": \"سنتری\", \"participant_id\": 649}, \"game_id\": 155, \"round_id\": 693, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-24 18:44:39\", \"round_number\": 10, \"source_user_id\": 1}', '2026-07-24 15:14:39'),
(3078, 'game_155', 'round_recorded', '{\"card\": {\"id\": 5, \"name\": \"تغییر رنگ\", \"emoji\": \"🌈\", \"rarity\": \"common\", \"multiplier\": 2}, \"score\": 10, \"winner\": {\"id\": 16, \"name\": \"KiNG\", \"participant_id\": 647}, \"game_id\": 155, \"round_id\": 694, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-24 18:46:47\", \"round_number\": 11, \"source_user_id\": 1}', '2026-07-24 15:16:47'),
(3079, 'game_155', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 5, \"winner\": {\"id\": 18, \"name\": \"امپراطور\", \"participant_id\": 648}, \"game_id\": 155, \"round_id\": 695, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-24 18:51:38\", \"round_number\": 12, \"source_user_id\": 1}', '2026-07-24 15:21:38'),
(3080, 'game_155', 'round_recorded', '{\"card\": {\"id\": 7, \"name\": \"سپر\", \"emoji\": \"🛡️\", \"rarity\": \"rare\", \"multiplier\": 3}, \"score\": 15, \"winner\": {\"id\": 1, \"name\": \"سنتری\", \"participant_id\": 649}, \"game_id\": 155, \"round_id\": 696, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-24 18:54:14\", \"round_number\": 13, \"source_user_id\": 1}', '2026-07-24 15:24:14'),
(3081, 'game_155', 'round_recorded', '{\"card\": {\"id\": 9, \"name\": \"هدیه\", \"emoji\": \"🎁\", \"rarity\": \"rare\", \"multiplier\": 2}, \"score\": 10, \"winner\": {\"id\": 16, \"name\": \"KiNG\", \"participant_id\": 647}, \"game_id\": 155, \"round_id\": 697, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-24 19:01:18\", \"round_number\": 14, \"source_user_id\": 1}', '2026-07-24 15:31:18'),
(3082, 'game_155', 'round_recorded', '{\"card\": {\"id\": 9, \"name\": \"هدیه\", \"emoji\": \"🎁\", \"rarity\": \"rare\", \"multiplier\": 2}, \"score\": 10, \"winner\": {\"id\": 16, \"name\": \"KiNG\", \"participant_id\": 647}, \"game_id\": 155, \"round_id\": 698, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-24 19:03:46\", \"round_number\": 15, \"source_user_id\": 1}', '2026-07-24 15:33:46'),
(3083, 'game_155', 'round_recorded', '{\"card\": {\"id\": 13, \"name\": \"قفل کردن\", \"emoji\": \"🔒\", \"rarity\": \"rare\", \"multiplier\": 5}, \"score\": 25, \"winner\": {\"id\": 16, \"name\": \"KiNG\", \"participant_id\": 647}, \"game_id\": 155, \"round_id\": 699, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-24 19:06:18\", \"round_number\": 16, \"source_user_id\": 1}', '2026-07-24 15:36:18'),
(3084, 'game_155', 'round_recorded', '{\"card\": {\"id\": 12, \"name\": \"شافل\", \"emoji\": \"🌀\", \"rarity\": \"rare\", \"multiplier\": 5}, \"score\": 25, \"winner\": {\"id\": 16, \"name\": \"KiNG\", \"participant_id\": 647}, \"game_id\": 155, \"round_id\": 700, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-24 19:09:51\", \"round_number\": 17, \"source_user_id\": 1}', '2026-07-24 15:39:51'),
(3085, 'game_155', 'round_recorded', '{\"card\": {\"id\": 13, \"name\": \"قفل کردن\", \"emoji\": \"🔒\", \"rarity\": \"rare\", \"multiplier\": 5}, \"score\": 25, \"winner\": {\"id\": 1, \"name\": \"سنتری\", \"participant_id\": 649}, \"game_id\": 155, \"round_id\": 701, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-24 19:12:50\", \"round_number\": 18, \"source_user_id\": 1}', '2026-07-24 15:42:50'),
(3086, 'game_155', 'round_recorded', '{\"card\": {\"id\": 9, \"name\": \"هدیه\", \"emoji\": \"🎁\", \"rarity\": \"rare\", \"multiplier\": 2}, \"score\": 10, \"winner\": {\"id\": 18, \"name\": \"امپراطور\", \"participant_id\": 648}, \"game_id\": 155, \"round_id\": 702, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-24 19:15:38\", \"round_number\": 19, \"source_user_id\": 1}', '2026-07-24 15:45:38'),
(3087, 'game_155', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 5, \"winner\": {\"id\": 1, \"name\": \"سنتری\", \"participant_id\": 649}, \"game_id\": 155, \"round_id\": 703, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-24 19:17:57\", \"round_number\": 20, \"source_user_id\": 1}', '2026-07-24 15:47:57'),
(3088, 'game_155', 'game_finished', '{\"status\": \"finished\", \"winner\": {\"id\": 1, \"name\": \"سنتری\", \"participant_id\": 649}, \"game_id\": 155, \"finished_at\": \"2026-07-24 19:18:04\", \"total_rounds\": 20, \"source_user_id\": 1}', '2026-07-24 15:48:04'),
(3089, 'game_156', 'game_started', '{\"status\": \"active\", \"game_id\": 156, \"started_at\": \"2026-07-25 15:29:31\", \"first_player\": {\"id\": 651, \"name\": \"RANGER\"}, \"source_user_id\": 1}', '2026-07-25 11:59:31'),
(3090, 'game_156', 'round_recorded', '{\"card\": {\"id\": 9, \"name\": \"هدیه\", \"emoji\": \"🎁\", \"rarity\": \"common\", \"multiplier\": 2}, \"score\": 10, \"winner\": {\"id\": 17, \"name\": \"RANGER\", \"participant_id\": 651}, \"game_id\": 156, \"round_id\": 704, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-25 15:32:00\", \"round_number\": 1, \"source_user_id\": 1}', '2026-07-25 12:02:00'),
(3091, 'game_156', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 5, \"winner\": {\"id\": 16, \"name\": \"KiNG\", \"participant_id\": 650}, \"game_id\": 156, \"round_id\": 705, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-25 15:34:22\", \"round_number\": 2, \"source_user_id\": 1}', '2026-07-25 12:04:22');
INSERT INTO `sse_events` (`id`, `channel`, `event_type`, `data`, `created_at`) VALUES
(3092, 'game_156', 'round_recorded', '{\"card\": {\"id\": 9, \"name\": \"هدیه\", \"emoji\": \"🎁\", \"rarity\": \"common\", \"multiplier\": 2}, \"score\": 10, \"winner\": {\"id\": 17, \"name\": \"RANGER\", \"participant_id\": 651}, \"game_id\": 156, \"round_id\": 706, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-25 15:38:49\", \"round_number\": 3, \"source_user_id\": 1}', '2026-07-25 12:08:49'),
(3093, 'game_156', 'round_recorded', '{\"card\": {\"id\": 15, \"name\": \"کینگ\", \"emoji\": \"👑\", \"rarity\": \"legendary\", \"multiplier\": 20}, \"score\": 100, \"winner\": {\"id\": 1, \"name\": \"سنتری\", \"participant_id\": 652}, \"game_id\": 156, \"round_id\": 707, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-25 15:41:52\", \"round_number\": 4, \"source_user_id\": 1}', '2026-07-25 12:11:52'),
(3094, 'game_156', 'round_recorded', '{\"card\": {\"id\": 14, \"name\": \"جریمه دوتایی انتخابی\", \"emoji\": \"🎯\", \"rarity\": \"rare\", \"multiplier\": 5}, \"score\": 25, \"winner\": {\"id\": 16, \"name\": \"KiNG\", \"participant_id\": 650}, \"game_id\": 156, \"round_id\": 708, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-25 15:44:52\", \"round_number\": 5, \"source_user_id\": 1}', '2026-07-25 12:14:52'),
(3095, 'game_156', 'round_recorded', '{\"card\": {\"id\": 7, \"name\": \"سپر\", \"emoji\": \"🛡️\", \"rarity\": \"rare\", \"multiplier\": 5}, \"score\": 25, \"winner\": {\"id\": 17, \"name\": \"RANGER\", \"participant_id\": 651}, \"game_id\": 156, \"round_id\": 709, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-25 15:46:58\", \"round_number\": 6, \"source_user_id\": 1}', '2026-07-25 12:16:58'),
(3096, 'game_156', 'round_recorded', '{\"card\": {\"id\": 12, \"name\": \"شافل\", \"emoji\": \"🌀\", \"rarity\": \"rare\", \"multiplier\": 10}, \"score\": 50, \"winner\": {\"id\": 1, \"name\": \"سنتری\", \"participant_id\": 652}, \"game_id\": 156, \"round_id\": 710, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-25 15:49:14\", \"round_number\": 7, \"source_user_id\": 1}', '2026-07-25 12:19:14'),
(3097, 'game_156', 'round_recorded', '{\"card\": {\"id\": 7, \"name\": \"سپر\", \"emoji\": \"🛡️\", \"rarity\": \"rare\", \"multiplier\": 5}, \"score\": 25, \"winner\": {\"id\": 1, \"name\": \"سنتری\", \"participant_id\": 652}, \"game_id\": 156, \"round_id\": 711, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-25 15:51:48\", \"round_number\": 8, \"source_user_id\": 1}', '2026-07-25 12:21:48'),
(3098, 'game_156', 'round_recorded', '{\"card\": {\"id\": 9, \"name\": \"هدیه\", \"emoji\": \"🎁\", \"rarity\": \"common\", \"multiplier\": 2}, \"score\": 10, \"winner\": {\"id\": 17, \"name\": \"RANGER\", \"participant_id\": 651}, \"game_id\": 156, \"round_id\": 712, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-25 15:55:49\", \"round_number\": 9, \"source_user_id\": 1}', '2026-07-25 12:25:49'),
(3099, 'game_156', 'round_recorded', '{\"card\": {\"id\": 13, \"name\": \"قفل کردن\", \"emoji\": \"🔒\", \"rarity\": \"rare\", \"multiplier\": 10}, \"score\": 50, \"winner\": {\"id\": 1, \"name\": \"سنتری\", \"participant_id\": 652}, \"game_id\": 156, \"round_id\": 713, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-25 15:58:45\", \"round_number\": 10, \"source_user_id\": 1}', '2026-07-25 12:28:45'),
(3100, 'game_156', 'round_recorded', '{\"card\": {\"id\": 9, \"name\": \"هدیه\", \"emoji\": \"🎁\", \"rarity\": \"common\", \"multiplier\": 2}, \"score\": 10, \"winner\": {\"id\": 1, \"name\": \"سنتری\", \"participant_id\": 652}, \"game_id\": 156, \"round_id\": 714, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-25 15:59:44\", \"round_number\": 11, \"source_user_id\": 1}', '2026-07-25 12:29:44'),
(3101, 'game_156', 'round_recorded', '{\"card\": {\"id\": 13, \"name\": \"قفل کردن\", \"emoji\": \"🔒\", \"rarity\": \"rare\", \"multiplier\": 10}, \"score\": 50, \"winner\": {\"id\": 16, \"name\": \"KiNG\", \"participant_id\": 650}, \"game_id\": 156, \"round_id\": 715, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-25 16:00:59\", \"round_number\": 12, \"source_user_id\": 1}', '2026-07-25 12:30:59'),
(3102, 'game_156', 'round_recorded', '{\"card\": {\"id\": 11, \"name\": \"پرش دوتایی\", \"emoji\": \"⏩\", \"rarity\": \"common\", \"multiplier\": 2}, \"score\": 10, \"winner\": {\"id\": 17, \"name\": \"RANGER\", \"participant_id\": 651}, \"game_id\": 156, \"round_id\": 716, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-25 16:02:33\", \"round_number\": 13, \"source_user_id\": 1}', '2026-07-25 12:32:33'),
(3103, 'game_156', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 5, \"winner\": {\"id\": 1, \"name\": \"سنتری\", \"participant_id\": 652}, \"game_id\": 156, \"round_id\": 717, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-25 16:04:19\", \"round_number\": 14, \"source_user_id\": 1}', '2026-07-25 12:34:19'),
(3104, 'game_156', 'round_recorded', '{\"card\": {\"id\": 12, \"name\": \"شافل\", \"emoji\": \"🌀\", \"rarity\": \"rare\", \"multiplier\": 10}, \"score\": 50, \"winner\": {\"id\": 16, \"name\": \"KiNG\", \"participant_id\": 650}, \"game_id\": 156, \"round_id\": 718, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-25 16:08:10\", \"round_number\": 15, \"source_user_id\": 1}', '2026-07-25 12:38:10'),
(3105, 'game_156', 'round_recorded', '{\"card\": {\"id\": 9, \"name\": \"هدیه\", \"emoji\": \"🎁\", \"rarity\": \"common\", \"multiplier\": 2}, \"score\": 10, \"winner\": {\"id\": 16, \"name\": \"KiNG\", \"participant_id\": 650}, \"game_id\": 156, \"round_id\": 719, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-25 16:09:53\", \"round_number\": 16, \"source_user_id\": 1}', '2026-07-25 12:39:53'),
(3106, 'game_156', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 5, \"winner\": {\"id\": 1, \"name\": \"سنتری\", \"participant_id\": 652}, \"game_id\": 156, \"round_id\": 720, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-25 16:11:56\", \"round_number\": 17, \"source_user_id\": 1}', '2026-07-25 12:41:56'),
(3107, 'game_156', 'round_recorded', '{\"card\": {\"id\": 15, \"name\": \"کینگ\", \"emoji\": \"👑\", \"rarity\": \"legendary\", \"multiplier\": 20}, \"score\": 100, \"winner\": {\"id\": 17, \"name\": \"RANGER\", \"participant_id\": 651}, \"game_id\": 156, \"round_id\": 721, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-25 16:14:21\", \"round_number\": 18, \"source_user_id\": 1}', '2026-07-25 12:44:21'),
(3108, 'game_156', 'round_recorded', '{\"card\": {\"id\": 11, \"name\": \"پرش دوتایی\", \"emoji\": \"⏩\", \"rarity\": \"common\", \"multiplier\": 2}, \"score\": 10, \"winner\": {\"id\": 1, \"name\": \"سنتری\", \"participant_id\": 652}, \"game_id\": 156, \"round_id\": 722, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-25 16:16:08\", \"round_number\": 19, \"source_user_id\": 1}', '2026-07-25 12:46:08'),
(3109, 'game_156', 'round_recorded', '{\"card\": {\"id\": 11, \"name\": \"پرش دوتایی\", \"emoji\": \"⏩\", \"rarity\": \"common\", \"multiplier\": 2}, \"score\": 10, \"winner\": {\"id\": 16, \"name\": \"KiNG\", \"participant_id\": 650}, \"game_id\": 156, \"round_id\": 723, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-25 16:18:25\", \"round_number\": 20, \"source_user_id\": 1}', '2026-07-25 12:48:25'),
(3110, 'game_156', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 5, \"winner\": {\"id\": 16, \"name\": \"KiNG\", \"participant_id\": 650}, \"game_id\": 156, \"round_id\": 724, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-25 16:23:19\", \"round_number\": 21, \"source_user_id\": 1}', '2026-07-25 12:53:19'),
(3111, 'game_156', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 5, \"winner\": {\"id\": 17, \"name\": \"RANGER\", \"participant_id\": 651}, \"game_id\": 156, \"round_id\": 725, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-25 16:25:01\", \"round_number\": 22, \"source_user_id\": 1}', '2026-07-25 12:55:01'),
(3112, 'game_156', 'round_recorded', '{\"card\": {\"id\": 9, \"name\": \"هدیه\", \"emoji\": \"🎁\", \"rarity\": \"common\", \"multiplier\": 2}, \"score\": 10, \"winner\": {\"id\": 1, \"name\": \"سنتری\", \"participant_id\": 652}, \"game_id\": 156, \"round_id\": 726, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-25 16:26:39\", \"round_number\": 23, \"source_user_id\": 1}', '2026-07-25 12:56:39'),
(3113, 'game_156', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 5, \"winner\": {\"id\": 1, \"name\": \"سنتری\", \"participant_id\": 652}, \"game_id\": 156, \"round_id\": 727, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-25 16:29:13\", \"round_number\": 24, \"source_user_id\": 1}', '2026-07-25 12:59:13'),
(3114, 'game_156', 'game_finished', '{\"status\": \"finished\", \"winner\": {\"id\": 1, \"name\": \"سنتری\", \"participant_id\": 652}, \"game_id\": 156, \"finished_at\": \"2026-07-25 16:29:32\", \"total_rounds\": 24, \"source_user_id\": 1}', '2026-07-25 12:59:32'),
(3115, 'game_157', 'game_started', '{\"status\": \"active\", \"game_id\": 157, \"started_at\": \"2026-07-25 16:30:35\", \"first_player\": {\"id\": 655, \"name\": \"سنتری\"}, \"source_user_id\": 1}', '2026-07-25 13:00:35'),
(3116, 'game_157', 'round_recorded', '{\"card\": {\"id\": 15, \"name\": \"کینگ\", \"emoji\": \"👑\", \"rarity\": \"legendary\", \"multiplier\": 20}, \"score\": 110, \"winner\": {\"id\": 1, \"name\": \"سنتری\", \"participant_id\": 655}, \"game_id\": 157, \"round_id\": 728, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-25 16:32:47\", \"round_number\": 1, \"source_user_id\": 1}', '2026-07-25 13:02:47'),
(3117, 'game_157', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 5, \"winner\": {\"id\": 17, \"name\": \"RANGER\", \"participant_id\": 654}, \"game_id\": 157, \"round_id\": 729, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-25 16:35:41\", \"round_number\": 2, \"source_user_id\": 1}', '2026-07-25 13:05:41'),
(3118, 'game_157', 'game_target_changed', '{\"game_id\": 157, \"max_wins\": 1, \"changed_at\": \"2026-07-25 16:35:51\", \"min_target\": 3, \"new_target\": 3, \"old_target\": 10, \"source_user_id\": 1}', '2026-07-25 13:05:51'),
(3119, 'game_157', 'round_recorded', '{\"card\": {\"id\": 9, \"name\": \"هدیه\", \"emoji\": \"🎁\", \"rarity\": \"common\", \"multiplier\": 2}, \"score\": 10, \"winner\": {\"id\": 16, \"name\": \"KiNG\", \"participant_id\": 653}, \"game_id\": 157, \"round_id\": 730, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-25 16:37:34\", \"round_number\": 3, \"source_user_id\": 1}', '2026-07-25 13:07:34'),
(3120, 'game_157', 'round_recorded', '{\"card\": {\"id\": 12, \"name\": \"شافل\", \"emoji\": \"🌀\", \"rarity\": \"rare\", \"multiplier\": 10}, \"score\": 50, \"winner\": {\"id\": 17, \"name\": \"RANGER\", \"participant_id\": 654}, \"game_id\": 157, \"round_id\": 731, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-25 16:39:37\", \"round_number\": 4, \"source_user_id\": 1}', '2026-07-25 13:09:37'),
(3121, 'game_157', 'round_recorded', '{\"card\": {\"id\": 6, \"name\": \"جریمه ۴ تایی\", \"emoji\": \"+4\", \"rarity\": \"common\", \"multiplier\": 3}, \"score\": 15, \"winner\": {\"id\": 16, \"name\": \"KiNG\", \"participant_id\": 653}, \"game_id\": 157, \"round_id\": 732, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-25 16:44:53\", \"round_number\": 5, \"source_user_id\": 1}', '2026-07-25 13:14:53'),
(3122, 'game_157', 'round_recorded', '{\"card\": {\"id\": 7, \"name\": \"سپر\", \"emoji\": \"🛡️\", \"rarity\": \"rare\", \"multiplier\": 5}, \"score\": 35, \"winner\": {\"id\": 1, \"name\": \"سنتری\", \"participant_id\": 655}, \"game_id\": 157, \"round_id\": 733, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-25 16:47:15\", \"round_number\": 6, \"source_user_id\": 1}', '2026-07-25 13:17:16'),
(3123, 'game_157', 'round_recorded', '{\"card\": {\"id\": 9, \"name\": \"هدیه\", \"emoji\": \"🎁\", \"rarity\": \"common\", \"multiplier\": 2}, \"score\": 10, \"winner\": {\"id\": 16, \"name\": \"KiNG\", \"participant_id\": 653}, \"game_id\": 157, \"round_id\": 734, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-25 16:48:46\", \"round_number\": 7, \"source_user_id\": 1}', '2026-07-25 13:18:46'),
(3124, 'game_157', 'game_finished', '{\"status\": \"finished\", \"winner\": {\"id\": 16, \"name\": \"KiNG\", \"participant_id\": 653}, \"game_id\": 157, \"finished_at\": \"2026-07-25 16:49:02\", \"total_rounds\": 7, \"source_user_id\": 1}', '2026-07-25 13:19:02'),
(3125, 'game_158', 'game_started', '{\"status\": \"active\", \"game_id\": 158, \"started_at\": \"2026-07-25 16:50:21\", \"first_player\": {\"id\": 657, \"name\": \"RANGER\"}, \"source_user_id\": 1}', '2026-07-25 13:20:22'),
(3126, 'game_158', 'round_recorded', '{\"card\": {\"id\": 12, \"name\": \"شافل\", \"emoji\": \"🌀\", \"rarity\": \"rare\", \"multiplier\": 10}, \"score\": 50, \"winner\": {\"id\": 17, \"name\": \"RANGER\", \"participant_id\": 657}, \"game_id\": 158, \"round_id\": 735, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-25 16:55:56\", \"round_number\": 1, \"source_user_id\": 1}', '2026-07-25 13:25:56'),
(3127, 'game_158', 'game_target_changed', '{\"game_id\": 158, \"max_wins\": 1, \"changed_at\": \"2026-07-25 16:57:00\", \"min_target\": 3, \"new_target\": 3, \"old_target\": 10, \"source_user_id\": 1}', '2026-07-25 13:27:00'),
(3128, 'game_158', 'round_recorded', '{\"card\": {\"id\": 5, \"name\": \"تغییر رنگ\", \"emoji\": \"🌈\", \"rarity\": \"common\", \"multiplier\": 2}, \"score\": 20, \"winner\": {\"id\": 1, \"name\": \"سنتری\", \"participant_id\": 659}, \"game_id\": 158, \"round_id\": 736, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-25 17:01:59\", \"round_number\": 2, \"source_user_id\": 1}', '2026-07-25 13:31:59'),
(3129, 'game_158', 'round_recorded', '{\"card\": {\"id\": 3, \"name\": \"دوربرگردان\", \"emoji\": \"🔄\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 15, \"winner\": {\"id\": 1, \"name\": \"سنتری\", \"participant_id\": 659}, \"game_id\": 158, \"round_id\": 737, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-25 17:06:26\", \"round_number\": 3, \"source_user_id\": 1}', '2026-07-25 13:36:26'),
(3130, 'game_158', 'round_recorded', '{\"card\": {\"id\": 9, \"name\": \"هدیه\", \"emoji\": \"🎁\", \"rarity\": \"common\", \"multiplier\": 2}, \"score\": 10, \"winner\": {\"id\": 18, \"name\": \"امپراطور\", \"participant_id\": 658}, \"game_id\": 158, \"round_id\": 738, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-25 17:10:47\", \"round_number\": 4, \"source_user_id\": 1}', '2026-07-25 13:40:48'),
(3131, 'game_158', 'round_recorded', '{\"card\": {\"id\": 7, \"name\": \"سپر\", \"emoji\": \"🛡️\", \"rarity\": \"rare\", \"multiplier\": 5}, \"score\": 25, \"winner\": {\"id\": 18, \"name\": \"امپراطور\", \"participant_id\": 658}, \"game_id\": 158, \"round_id\": 739, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-25 17:13:39\", \"round_number\": 5, \"source_user_id\": 1}', '2026-07-25 13:43:39'),
(3132, 'game_158', 'round_recorded', '{\"card\": {\"id\": 9, \"name\": \"هدیه\", \"emoji\": \"🎁\", \"rarity\": \"common\", \"multiplier\": 2}, \"score\": 20, \"winner\": {\"id\": 16, \"name\": \"KiNG\", \"participant_id\": 656}, \"game_id\": 158, \"round_id\": 740, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-25 17:16:00\", \"round_number\": 6, \"source_user_id\": 1}', '2026-07-25 13:46:00'),
(3133, 'game_158', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 15, \"winner\": {\"id\": 1, \"name\": \"سنتری\", \"participant_id\": 659}, \"game_id\": 158, \"round_id\": 741, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-25 17:20:37\", \"round_number\": 7, \"source_user_id\": 1}', '2026-07-25 13:50:37'),
(3134, 'game_158', 'game_finished', '{\"status\": \"finished\", \"winner\": {\"id\": 1, \"name\": \"سنتری\", \"participant_id\": 659}, \"game_id\": 158, \"finished_at\": \"2026-07-25 17:20:48\", \"total_rounds\": 7, \"source_user_id\": 1}', '2026-07-25 13:50:48'),
(3135, 'game_159', 'game_started', '{\"status\": \"active\", \"game_id\": 159, \"started_at\": \"2026-07-25 17:22:44\", \"first_player\": {\"id\": 663, \"name\": \"سنتری\"}, \"source_user_id\": 1}', '2026-07-25 13:52:44'),
(3136, 'game_159', 'round_recorded', '{\"card\": {\"id\": 15, \"name\": \"کینگ\", \"emoji\": \"👑\", \"rarity\": \"legendary\", \"multiplier\": 20}, \"score\": 210, \"winner\": {\"id\": 1, \"name\": \"سنتری\", \"participant_id\": 663}, \"game_id\": 159, \"round_id\": 742, \"win_type\": null, \"team_name\": \"تیم 2\", \"recorded_at\": \"2026-07-25 17:27:49\", \"round_number\": 1, \"source_user_id\": 1}', '2026-07-25 13:57:49'),
(3137, 'game_159', 'round_recorded', '{\"card\": {\"id\": 9, \"name\": \"هدیه\", \"emoji\": \"🎁\", \"rarity\": \"common\", \"multiplier\": 2}, \"score\": 30, \"winner\": {\"id\": 16, \"name\": \"KiNG\", \"participant_id\": 660}, \"game_id\": 159, \"round_id\": 743, \"win_type\": null, \"team_name\": \"تیم 1\", \"recorded_at\": \"2026-07-25 17:31:10\", \"round_number\": 2, \"source_user_id\": 1}', '2026-07-25 14:01:10'),
(3138, 'game_159', 'round_recorded', '{\"card\": {\"id\": 9, \"name\": \"هدیه\", \"emoji\": \"🎁\", \"rarity\": \"common\", \"multiplier\": 2}, \"score\": 30, \"winner\": {\"id\": 1, \"name\": \"سنتری\", \"participant_id\": 663}, \"game_id\": 159, \"round_id\": 744, \"win_type\": null, \"team_name\": \"تیم 2\", \"recorded_at\": \"2026-07-25 17:33:50\", \"round_number\": 3, \"source_user_id\": 1}', '2026-07-25 14:03:50'),
(3139, 'game_159', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 10, \"winner\": {\"id\": 17, \"name\": \"RANGER\", \"participant_id\": 661}, \"game_id\": 159, \"round_id\": 745, \"win_type\": null, \"team_name\": \"تیم 1\", \"recorded_at\": \"2026-07-25 17:38:25\", \"round_number\": 4, \"source_user_id\": 1}', '2026-07-25 14:08:25'),
(3140, 'game_159', 'round_recorded', '{\"card\": {\"id\": 7, \"name\": \"سپر\", \"emoji\": \"🛡️\", \"rarity\": \"rare\", \"multiplier\": 5}, \"score\": 60, \"winner\": {\"id\": 1, \"name\": \"سنتری\", \"participant_id\": 663}, \"game_id\": 159, \"round_id\": 746, \"win_type\": null, \"team_name\": \"تیم 2\", \"recorded_at\": \"2026-07-25 17:43:14\", \"round_number\": 5, \"source_user_id\": 1}', '2026-07-25 14:13:14'),
(3141, 'game_159', 'round_recorded', '{\"card\": {\"id\": 13, \"name\": \"قفل کردن\", \"emoji\": \"🔒\", \"rarity\": \"rare\", \"multiplier\": 10}, \"score\": 110, \"winner\": {\"id\": 18, \"name\": \"امپراطور\", \"participant_id\": 662}, \"game_id\": 159, \"round_id\": 747, \"win_type\": null, \"team_name\": \"تیم 2\", \"recorded_at\": \"2026-07-25 17:46:50\", \"round_number\": 6, \"source_user_id\": 1}', '2026-07-25 14:16:50'),
(3142, 'game_159', 'round_recorded', '{\"card\": {\"id\": 9, \"name\": \"هدیه\", \"emoji\": \"🎁\", \"rarity\": \"common\", \"multiplier\": 2}, \"score\": 30, \"winner\": {\"id\": 1, \"name\": \"سنتری\", \"participant_id\": 663}, \"game_id\": 159, \"round_id\": 748, \"win_type\": null, \"team_name\": \"تیم 2\", \"recorded_at\": \"2026-07-25 17:48:11\", \"round_number\": 7, \"source_user_id\": 1}', '2026-07-25 14:18:11'),
(3143, 'game_159', 'game_finished', '{\"status\": \"finished\", \"winner\": {\"id\": 18, \"name\": \"تیم تیم 2\", \"participant_id\": 662}, \"game_id\": 159, \"finished_at\": \"2026-07-25 17:48:18\", \"total_rounds\": 7, \"source_user_id\": 1}', '2026-07-25 14:18:18'),
(3144, 'game_160', 'game_started', '{\"status\": \"active\", \"game_id\": 160, \"started_at\": \"2026-07-25 17:49:25\", \"first_player\": {\"id\": 667, \"name\": \"سنتری\"}, \"source_user_id\": 1}', '2026-07-25 14:19:25'),
(3145, 'game_160', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 20, \"winner\": {\"id\": 16, \"name\": \"KiNG\", \"participant_id\": 664}, \"game_id\": 160, \"round_id\": 749, \"win_type\": null, \"team_name\": \"تیم 1\", \"recorded_at\": \"2026-07-25 17:51:58\", \"round_number\": 1, \"source_user_id\": 1}', '2026-07-25 14:21:58'),
(3146, 'game_160', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 20, \"winner\": {\"id\": 1, \"name\": \"سنتری\", \"participant_id\": 667}, \"game_id\": 160, \"round_id\": 750, \"win_type\": null, \"team_name\": \"تیم 2\", \"recorded_at\": \"2026-07-25 17:55:53\", \"round_number\": 2, \"source_user_id\": 1}', '2026-07-25 14:25:53'),
(3147, 'game_160', 'round_recorded', '{\"card\": {\"id\": 6, \"name\": \"جریمه ۴ تایی\", \"emoji\": \"+4\", \"rarity\": \"common\", \"multiplier\": 3}, \"score\": 40, \"winner\": {\"id\": 17, \"name\": \"RANGER\", \"participant_id\": 665}, \"game_id\": 160, \"round_id\": 751, \"win_type\": null, \"team_name\": \"تیم 1\", \"recorded_at\": \"2026-07-25 17:58:35\", \"round_number\": 3, \"source_user_id\": 1}', '2026-07-25 14:28:35'),
(3148, 'game_160', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 20, \"winner\": {\"id\": 1, \"name\": \"سنتری\", \"participant_id\": 667}, \"game_id\": 160, \"round_id\": 752, \"win_type\": null, \"team_name\": \"تیم 2\", \"recorded_at\": \"2026-07-25 18:05:50\", \"round_number\": 4, \"source_user_id\": 1}', '2026-07-25 14:35:50'),
(3149, 'game_160', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 20, \"winner\": {\"id\": 17, \"name\": \"RANGER\", \"participant_id\": 665}, \"game_id\": 160, \"round_id\": 753, \"win_type\": null, \"team_name\": \"تیم 1\", \"recorded_at\": \"2026-07-25 18:10:13\", \"round_number\": 5, \"source_user_id\": 1}', '2026-07-25 14:40:13'),
(3150, 'game_160', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 20, \"winner\": {\"id\": 1, \"name\": \"سنتری\", \"participant_id\": 667}, \"game_id\": 160, \"round_id\": 754, \"win_type\": null, \"team_name\": \"تیم 2\", \"recorded_at\": \"2026-07-25 18:13:33\", \"round_number\": 6, \"source_user_id\": 1}', '2026-07-25 14:43:33'),
(3151, 'game_160', 'round_recorded', '{\"card\": {\"id\": 12, \"name\": \"شافل\", \"emoji\": \"🌀\", \"rarity\": \"rare\", \"multiplier\": 10}, \"score\": 110, \"winner\": {\"id\": 17, \"name\": \"RANGER\", \"participant_id\": 665}, \"game_id\": 160, \"round_id\": 755, \"win_type\": null, \"team_name\": \"تیم 1\", \"recorded_at\": \"2026-07-25 18:16:08\", \"round_number\": 7, \"source_user_id\": 1}', '2026-07-25 14:46:08'),
(3152, 'game_160', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 20, \"winner\": {\"id\": 16, \"name\": \"KiNG\", \"participant_id\": 664}, \"game_id\": 160, \"round_id\": 756, \"win_type\": null, \"team_name\": \"تیم 1\", \"recorded_at\": \"2026-07-25 18:18:45\", \"round_number\": 8, \"source_user_id\": 1}', '2026-07-25 14:48:45'),
(3153, 'game_160', 'round_undone', '{\"game_id\": 160, \"undone_at\": \"2026-07-25 18:19:02\", \"undone_round\": 8, \"source_user_id\": 1}', '2026-07-25 14:49:02'),
(3154, 'game_160', 'round_recorded', '{\"card\": {\"id\": 13, \"name\": \"قفل کردن\", \"emoji\": \"🔒\", \"rarity\": \"rare\", \"multiplier\": 10}, \"score\": 110, \"winner\": {\"id\": 16, \"name\": \"KiNG\", \"participant_id\": 664}, \"game_id\": 160, \"round_id\": 757, \"win_type\": null, \"team_name\": \"تیم 1\", \"recorded_at\": \"2026-07-25 18:19:08\", \"round_number\": 8, \"source_user_id\": 1}', '2026-07-25 14:49:08'),
(3155, 'game_160', 'game_finished', '{\"status\": \"finished\", \"winner\": {\"id\": 16, \"name\": \"تیم تیم 1\", \"participant_id\": 664}, \"game_id\": 160, \"finished_at\": \"2026-07-25 18:19:18\", \"total_rounds\": 8, \"source_user_id\": 1}', '2026-07-25 14:49:18'),
(3156, 'game_161', 'game_started', '{\"status\": \"active\", \"game_id\": 161, \"started_at\": \"2026-07-25 18:20:12\", \"first_player\": {\"id\": 668, \"name\": \"KiNG\"}, \"source_user_id\": 1}', '2026-07-25 14:50:12'),
(3157, 'game_161', 'game_status_changed', '{\"status\": \"paused\", \"game_id\": 161, \"source_user_id\": 1}', '2026-07-25 14:50:38'),
(3158, 'game_162', 'game_started', '{\"status\": \"active\", \"game_id\": 162, \"started_at\": \"2026-07-25 18:21:37\", \"first_player\": {\"id\": 673, \"name\": \"RANGER\"}, \"source_user_id\": 1}', '2026-07-25 14:51:37'),
(3159, 'game_162', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 15, \"winner\": {\"id\": 17, \"name\": \"RANGER\", \"participant_id\": 673}, \"game_id\": 162, \"round_id\": 758, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-25 18:24:27\", \"round_number\": 1, \"source_user_id\": 1}', '2026-07-25 14:54:27'),
(3160, 'game_162', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 15, \"winner\": {\"id\": 1, \"name\": \"سنتری\", \"participant_id\": 674}, \"game_id\": 162, \"round_id\": 759, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-25 18:26:20\", \"round_number\": 2, \"source_user_id\": 1}', '2026-07-25 14:56:20'),
(3161, 'game_162', 'round_recorded', '{\"card\": {\"id\": 9, \"name\": \"هدیه\", \"emoji\": \"🎁\", \"rarity\": \"common\", \"multiplier\": 2}, \"score\": 20, \"winner\": {\"id\": 16, \"name\": \"KiNG\", \"participant_id\": 672}, \"game_id\": 162, \"round_id\": 760, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-25 18:27:19\", \"round_number\": 3, \"source_user_id\": 1}', '2026-07-25 14:57:19'),
(3162, 'game_162', 'round_recorded', '{\"card\": {\"id\": 12, \"name\": \"شافل\", \"emoji\": \"🌀\", \"rarity\": \"rare\", \"multiplier\": 10}, \"score\": 60, \"winner\": {\"id\": 1, \"name\": \"سنتری\", \"participant_id\": 674}, \"game_id\": 162, \"round_id\": 761, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-25 18:30:25\", \"round_number\": 4, \"source_user_id\": 1}', '2026-07-25 15:00:25'),
(3163, 'game_162', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 15, \"winner\": {\"id\": 17, \"name\": \"RANGER\", \"participant_id\": 673}, \"game_id\": 162, \"round_id\": 762, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-25 18:34:19\", \"round_number\": 5, \"source_user_id\": 1}', '2026-07-25 15:04:19'),
(3164, 'game_162', 'round_recorded', '{\"card\": {\"id\": 4, \"name\": \"جریمه دوتایی\", \"emoji\": \"+2\", \"rarity\": \"common\", \"multiplier\": 2}, \"score\": 20, \"winner\": {\"id\": 17, \"name\": \"RANGER\", \"participant_id\": 673}, \"game_id\": 162, \"round_id\": 763, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-25 18:36:35\", \"round_number\": 6, \"source_user_id\": 1}', '2026-07-25 15:06:35'),
(3165, 'game_162', 'round_recorded', '{\"card\": {\"id\": 12, \"name\": \"شافل\", \"emoji\": \"🌀\", \"rarity\": \"rare\", \"multiplier\": 10}, \"score\": 60, \"winner\": {\"id\": 1, \"name\": \"سنتری\", \"participant_id\": 674}, \"game_id\": 162, \"round_id\": 764, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-25 18:37:48\", \"round_number\": 7, \"source_user_id\": 1}', '2026-07-25 15:07:49'),
(3166, 'game_162', 'round_recorded', '{\"card\": {\"id\": 9, \"name\": \"هدیه\", \"emoji\": \"🎁\", \"rarity\": \"common\", \"multiplier\": 2}, \"score\": 20, \"winner\": {\"id\": 17, \"name\": \"RANGER\", \"participant_id\": 673}, \"game_id\": 162, \"round_id\": 765, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-25 18:39:07\", \"round_number\": 8, \"source_user_id\": 1}', '2026-07-25 15:09:08'),
(3167, 'game_162', 'round_recorded', '{\"card\": {\"id\": 13, \"name\": \"قفل کردن\", \"emoji\": \"🔒\", \"rarity\": \"rare\", \"multiplier\": 10}, \"score\": 60, \"winner\": {\"id\": 1, \"name\": \"سنتری\", \"participant_id\": 674}, \"game_id\": 162, \"round_id\": 766, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-25 18:42:55\", \"round_number\": 9, \"source_user_id\": 1}', '2026-07-25 15:12:55'),
(3168, 'game_162', 'round_recorded', '{\"card\": {\"id\": 9, \"name\": \"هدیه\", \"emoji\": \"🎁\", \"rarity\": \"common\", \"multiplier\": 2}, \"score\": 20, \"winner\": {\"id\": 17, \"name\": \"RANGER\", \"participant_id\": 673}, \"game_id\": 162, \"round_id\": 767, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-25 18:45:03\", \"round_number\": 10, \"source_user_id\": 1}', '2026-07-25 15:15:03'),
(3169, 'game_162', 'round_recorded', '{\"card\": {\"id\": 15, \"name\": \"کینگ\", \"emoji\": \"👑\", \"rarity\": \"legendary\", \"multiplier\": 20}, \"score\": 110, \"winner\": {\"id\": 1, \"name\": \"سنتری\", \"participant_id\": 674}, \"game_id\": 162, \"round_id\": 768, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-25 18:47:03\", \"round_number\": 11, \"source_user_id\": 1}', '2026-07-25 15:17:03'),
(3170, 'game_162', 'game_target_changed', '{\"game_id\": 162, \"max_wins\": 5, \"changed_at\": \"2026-07-25 18:47:26\", \"min_target\": 5, \"new_target\": 5, \"old_target\": 10, \"source_user_id\": 1}', '2026-07-25 15:17:26'),
(3171, 'game_162', 'game_finished', '{\"status\": \"finished\", \"winner\": {\"id\": 17, \"name\": \"RANGER\", \"participant_id\": 673}, \"game_id\": 162, \"finished_at\": \"2026-07-25 18:47:33\", \"total_rounds\": 11, \"source_user_id\": 1}', '2026-07-25 15:17:33'),
(3172, 'game_161', 'game_status_changed', '{\"status\": \"active\", \"game_id\": 161, \"source_user_id\": 1}', '2026-07-25 15:18:40'),
(3173, 'game_161', 'round_recorded', '{\"card\": {\"id\": 6, \"name\": \"جریمه ۴ تایی\", \"emoji\": \"+4\", \"rarity\": \"common\", \"multiplier\": 3}, \"score\": 40, \"winner\": {\"id\": 17, \"name\": \"RANGER\", \"participant_id\": 669}, \"game_id\": 161, \"round_id\": 769, \"win_type\": null, \"team_name\": \"تیم 1\", \"recorded_at\": \"2026-07-25 18:55:12\", \"round_number\": 1, \"source_user_id\": 1}', '2026-07-25 15:25:12'),
(3174, 'game_161', 'round_recorded', '{\"card\": {\"id\": 7, \"name\": \"سپر\", \"emoji\": \"🛡️\", \"rarity\": \"rare\", \"multiplier\": 5}, \"score\": 60, \"winner\": {\"id\": 16, \"name\": \"KiNG\", \"participant_id\": 668}, \"game_id\": 161, \"round_id\": 770, \"win_type\": null, \"team_name\": \"تیم 1\", \"recorded_at\": \"2026-07-25 18:58:06\", \"round_number\": 2, \"source_user_id\": 1}', '2026-07-25 15:28:06'),
(3175, 'game_161', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 20, \"winner\": {\"id\": 17, \"name\": \"RANGER\", \"participant_id\": 669}, \"game_id\": 161, \"round_id\": 771, \"win_type\": null, \"team_name\": \"تیم 1\", \"recorded_at\": \"2026-07-25 19:01:56\", \"round_number\": 3, \"source_user_id\": 1}', '2026-07-25 15:31:56'),
(3176, 'game_161', 'game_finished', '{\"status\": \"finished\", \"winner\": {\"id\": 16, \"name\": \"تیم تیم 1\", \"participant_id\": 668}, \"game_id\": 161, \"finished_at\": \"2026-07-25 19:02:18\", \"total_rounds\": 3, \"source_user_id\": 1}', '2026-07-25 15:32:18'),
(3177, 'game_163', 'game_started', '{\"status\": \"active\", \"game_id\": 163, \"started_at\": \"2026-07-28 18:06:33\", \"first_player\": {\"id\": 677, \"name\": \"سرسخت\"}, \"source_user_id\": 18}', '2026-07-28 14:36:33'),
(3178, 'game_163', 'round_recorded', '{\"card\": {\"id\": 5, \"name\": \"تغییر رنگ\", \"emoji\": \"🌈\", \"rarity\": \"common\", \"multiplier\": 2}, \"score\": 10, \"winner\": {\"id\": 20, \"name\": \"فرانکلین\", \"participant_id\": 678}, \"game_id\": 163, \"round_id\": 772, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-28 18:11:50\", \"round_number\": 1, \"source_user_id\": 18}', '2026-07-28 14:41:50'),
(3179, 'game_163', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 15, \"winner\": {\"id\": 17, \"name\": \"RANGER\", \"participant_id\": 675}, \"game_id\": 163, \"round_id\": 773, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-28 18:16:39\", \"round_number\": 2, \"source_user_id\": 18}', '2026-07-28 14:46:39'),
(3180, 'game_163', 'round_recorded', '{\"card\": {\"id\": 9, \"name\": \"هدیه\", \"emoji\": \"🎁\", \"rarity\": \"common\", \"multiplier\": 2}, \"score\": 10, \"winner\": {\"id\": 20, \"name\": \"فرانکلین\", \"participant_id\": 678}, \"game_id\": 163, \"round_id\": 774, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-28 18:21:36\", \"round_number\": 3, \"source_user_id\": 18}', '2026-07-28 14:51:36'),
(3181, 'game_163', 'round_recorded', '{\"card\": {\"id\": 4, \"name\": \"جریمه دوتایی\", \"emoji\": \"+2\", \"rarity\": \"common\", \"multiplier\": 2}, \"score\": 10, \"winner\": {\"id\": 24, \"name\": \"سرسخت\", \"participant_id\": 677}, \"game_id\": 163, \"round_id\": 775, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-28 18:29:27\", \"round_number\": 4, \"source_user_id\": 18}', '2026-07-28 14:59:27'),
(3182, 'game_163', 'round_recorded', '{\"card\": {\"id\": 12, \"name\": \"شافل\", \"emoji\": \"🌀\", \"rarity\": \"rare\", \"multiplier\": 10}, \"score\": 60, \"winner\": {\"id\": 18, \"name\": \"امپراطور\", \"participant_id\": 676}, \"game_id\": 163, \"round_id\": 776, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-28 18:32:13\", \"round_number\": 5, \"source_user_id\": 18}', '2026-07-28 15:02:13'),
(3183, 'game_163', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 5, \"winner\": {\"id\": 24, \"name\": \"سرسخت\", \"participant_id\": 677}, \"game_id\": 163, \"round_id\": 777, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-28 18:36:59\", \"round_number\": 6, \"source_user_id\": 18}', '2026-07-28 15:06:59'),
(3184, 'game_163', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 5, \"winner\": {\"id\": 20, \"name\": \"فرانکلین\", \"participant_id\": 678}, \"game_id\": 163, \"round_id\": 778, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-28 18:39:47\", \"round_number\": 7, \"source_user_id\": 18}', '2026-07-28 15:09:47'),
(3185, 'game_163', 'game_finished', '{\"status\": \"finished\", \"winner\": {\"id\": 20, \"name\": \"فرانکلین\", \"participant_id\": 678}, \"game_id\": 163, \"finished_at\": \"2026-07-28 18:39:57\", \"total_rounds\": 7, \"source_user_id\": 18}', '2026-07-28 15:09:57'),
(3186, 'game_164', 'game_started', '{\"status\": \"active\", \"game_id\": 164, \"started_at\": \"2026-07-31 15:10:47\", \"first_player\": {\"id\": 681, \"name\": \"امپراطور\"}, \"source_user_id\": 18}', '2026-07-31 11:40:47'),
(3187, 'game_164', 'game_status_changed', '{\"status\": \"cancelled\", \"game_id\": 164, \"source_user_id\": 18}', '2026-07-31 11:45:00'),
(3188, 'game_165', 'game_started', '{\"status\": \"active\", \"game_id\": 165, \"started_at\": \"2026-07-31 15:15:54\", \"first_player\": {\"id\": 689, \"name\": \"فرانکلین\"}, \"source_user_id\": 18}', '2026-07-31 11:45:54'),
(3189, 'game_165', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 5, \"winner\": {\"id\": 24, \"name\": \"سرسخت\", \"participant_id\": 688}, \"game_id\": 165, \"round_id\": 779, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-31 15:17:23\", \"round_number\": 1, \"source_user_id\": 18}', '2026-07-31 11:47:23'),
(3190, 'game_165', 'round_recorded', '{\"card\": {\"id\": 9, \"name\": \"هدیه\", \"emoji\": \"🎁\", \"rarity\": \"common\", \"multiplier\": 2}, \"score\": 10, \"winner\": {\"id\": 24, \"name\": \"سرسخت\", \"participant_id\": 688}, \"game_id\": 165, \"round_id\": 780, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-31 15:19:42\", \"round_number\": 2, \"source_user_id\": 18}', '2026-07-31 11:49:42'),
(3191, 'game_165', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 15, \"winner\": {\"id\": 17, \"name\": \"RANGER\", \"participant_id\": 686}, \"game_id\": 165, \"round_id\": 781, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-31 15:28:08\", \"round_number\": 3, \"source_user_id\": 18}', '2026-07-31 11:58:08'),
(3192, 'game_165', 'round_recorded', '{\"card\": {\"id\": 5, \"name\": \"تغییر رنگ\", \"emoji\": \"🌈\", \"rarity\": \"common\", \"multiplier\": 2}, \"score\": 10, \"winner\": {\"id\": 24, \"name\": \"سرسخت\", \"participant_id\": 688}, \"game_id\": 165, \"round_id\": 782, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-07-31 15:32:57\", \"round_number\": 4, \"source_user_id\": 18}', '2026-07-31 12:02:57'),
(3193, 'game_165', 'game_finished', '{\"status\": \"finished\", \"winner\": {\"id\": 24, \"name\": \"سرسخت\", \"participant_id\": 688}, \"game_id\": 165, \"finished_at\": \"2026-07-31 15:33:11\", \"total_rounds\": 4, \"source_user_id\": 18}', '2026-07-31 12:03:11'),
(3194, 'game_166', 'game_started', '{\"status\": \"active\", \"game_id\": 166, \"started_at\": \"2026-08-01 18:16:04\", \"first_player\": {\"id\": 692, \"name\": \"سرسخت\"}, \"source_user_id\": 18}', '2026-08-01 14:46:04'),
(3195, 'game_166', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 15, \"winner\": {\"id\": 17, \"name\": \"RANGER\", \"participant_id\": 690}, \"game_id\": 166, \"round_id\": 783, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-01 18:17:17\", \"round_number\": 1, \"source_user_id\": 18}', '2026-08-01 14:47:17'),
(3196, 'game_166', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 15, \"winner\": {\"id\": 17, \"name\": \"RANGER\", \"participant_id\": 690}, \"game_id\": 166, \"round_id\": 784, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-01 18:18:55\", \"round_number\": 2, \"source_user_id\": 18}', '2026-08-01 14:48:55'),
(3197, 'game_166', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 15, \"winner\": {\"id\": 18, \"name\": \"امپراطور\", \"participant_id\": 691}, \"game_id\": 166, \"round_id\": 785, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-01 18:20:14\", \"round_number\": 3, \"source_user_id\": 18}', '2026-08-01 14:50:14'),
(3198, 'game_166', 'round_recorded', '{\"card\": {\"id\": 6, \"name\": \"جریمه ۴ تایی\", \"emoji\": \"+4\", \"rarity\": \"common\", \"multiplier\": 3}, \"score\": 25, \"winner\": {\"id\": 18, \"name\": \"امپراطور\", \"participant_id\": 691}, \"game_id\": 166, \"round_id\": 786, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-01 18:23:21\", \"round_number\": 4, \"source_user_id\": 18}', '2026-08-01 14:53:21'),
(3199, 'game_166', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 5, \"winner\": {\"id\": 24, \"name\": \"سرسخت\", \"participant_id\": 692}, \"game_id\": 166, \"round_id\": 787, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-01 18:27:46\", \"round_number\": 5, \"source_user_id\": 18}', '2026-08-01 14:57:46'),
(3200, 'game_166', 'round_recorded', '{\"card\": {\"id\": 9, \"name\": \"هدیه\", \"emoji\": \"🎁\", \"rarity\": \"common\", \"multiplier\": 2}, \"score\": 10, \"winner\": {\"id\": 24, \"name\": \"سرسخت\", \"participant_id\": 692}, \"game_id\": 166, \"round_id\": 788, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-01 18:36:54\", \"round_number\": 6, \"source_user_id\": 18}', '2026-08-01 15:06:54'),
(3201, 'game_166', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 15, \"winner\": {\"id\": 17, \"name\": \"RANGER\", \"participant_id\": 690}, \"game_id\": 166, \"round_id\": 789, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-01 18:41:28\", \"round_number\": 7, \"source_user_id\": 18}', '2026-08-01 15:11:28'),
(3202, 'game_166', 'game_finished', '{\"status\": \"finished\", \"winner\": {\"id\": 17, \"name\": \"RANGER\", \"participant_id\": 690}, \"game_id\": 166, \"finished_at\": \"2026-08-01 18:41:45\", \"total_rounds\": 7, \"source_user_id\": 18}', '2026-08-01 15:11:45'),
(3203, 'game_167', 'game_started', '{\"status\": \"active\", \"game_id\": 167, \"started_at\": \"2026-08-01 18:43:53\", \"first_player\": {\"id\": 694, \"name\": \"امپراطور\"}, \"source_user_id\": 18}', '2026-08-01 15:13:53'),
(3204, 'game_167', 'round_recorded', '{\"card\": {\"id\": 12, \"name\": \"شافل\", \"emoji\": \"🌀\", \"rarity\": \"rare\", \"multiplier\": 10}, \"score\": 100, \"winner\": {\"id\": 24, \"name\": \"سرسخت\", \"participant_id\": 695}, \"game_id\": 167, \"round_id\": 790, \"win_type\": null, \"team_name\": \"تیم 1\", \"recorded_at\": \"2026-08-01 18:46:51\", \"round_number\": 1, \"source_user_id\": 18}', '2026-08-01 15:16:51'),
(3205, 'game_167', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 20, \"winner\": {\"id\": 18, \"name\": \"امپراطور\", \"participant_id\": 694}, \"game_id\": 167, \"round_id\": 791, \"win_type\": null, \"team_name\": \"تیم 2\", \"recorded_at\": \"2026-08-01 18:48:34\", \"round_number\": 2, \"source_user_id\": 18}', '2026-08-01 15:18:34'),
(3206, 'game_167', 'round_recorded', '{\"card\": {\"id\": 6, \"name\": \"جریمه ۴ تایی\", \"emoji\": \"+4\", \"rarity\": \"common\", \"multiplier\": 3}, \"score\": 40, \"winner\": {\"id\": 18, \"name\": \"امپراطور\", \"participant_id\": 694}, \"game_id\": 167, \"round_id\": 792, \"win_type\": null, \"team_name\": \"تیم 2\", \"recorded_at\": \"2026-08-01 18:52:03\", \"round_number\": 3, \"source_user_id\": 18}', '2026-08-01 15:22:03'),
(3207, 'game_167', 'round_recorded', '{\"card\": {\"id\": 15, \"name\": \"کینگ\", \"emoji\": \"👑\", \"rarity\": \"legendary\", \"multiplier\": 20}, \"score\": 210, \"winner\": {\"id\": 17, \"name\": \"RANGER\", \"participant_id\": 693}, \"game_id\": 167, \"round_id\": 793, \"win_type\": null, \"team_name\": \"تیم 2\", \"recorded_at\": \"2026-08-01 18:57:55\", \"round_number\": 4, \"source_user_id\": 18}', '2026-08-01 15:27:56'),
(3208, 'game_167', 'game_finished', '{\"status\": \"finished\", \"winner\": {\"id\": 17, \"name\": \"تیم تیم 2\", \"participant_id\": 693}, \"game_id\": 167, \"finished_at\": \"2026-08-01 18:58:09\", \"total_rounds\": 4, \"source_user_id\": 18}', '2026-08-01 15:28:09'),
(3209, 'game_168', 'game_started', '{\"status\": \"active\", \"game_id\": 168, \"started_at\": \"2026-08-03 22:45:19\", \"first_player\": {\"id\": 697, \"name\": \"RANGER\"}, \"source_user_id\": 1}', '2026-08-03 19:15:19'),
(3210, 'game_168', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 15, \"winner\": {\"id\": 17, \"name\": \"RANGER\", \"participant_id\": 697}, \"game_id\": 168, \"round_id\": 794, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-03 22:47:13\", \"round_number\": 1, \"source_user_id\": 1}', '2026-08-03 19:17:13'),
(3211, 'game_168', 'round_recorded', '{\"card\": {\"id\": 12, \"name\": \"شافل\", \"emoji\": \"🌀\", \"rarity\": \"rare\", \"multiplier\": 10}, \"score\": 60, \"winner\": {\"id\": 1, \"name\": \"سنتری\", \"participant_id\": 699}, \"game_id\": 168, \"round_id\": 795, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-03 22:52:35\", \"round_number\": 2, \"source_user_id\": 1}', '2026-08-03 19:22:36'),
(3212, 'game_168', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 15, \"winner\": {\"id\": 18, \"name\": \"امپراطور\", \"participant_id\": 698}, \"game_id\": 168, \"round_id\": 796, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-03 22:55:41\", \"round_number\": 3, \"source_user_id\": 1}', '2026-08-03 19:25:41'),
(3213, 'game_168', 'round_recorded', '{\"card\": {\"id\": 9, \"name\": \"هدیه\", \"emoji\": \"🎁\", \"rarity\": \"common\", \"multiplier\": 2}, \"score\": 20, \"winner\": {\"id\": 18, \"name\": \"امپراطور\", \"participant_id\": 698}, \"game_id\": 168, \"round_id\": 797, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-03 22:57:42\", \"round_number\": 4, \"source_user_id\": 1}', '2026-08-03 19:27:42'),
(3214, 'game_168', 'round_recorded', '{\"card\": {\"id\": 7, \"name\": \"سپر\", \"emoji\": \"🛡️\", \"rarity\": \"rare\", \"multiplier\": 5}, \"score\": 35, \"winner\": {\"id\": 18, \"name\": \"امپراطور\", \"participant_id\": 698}, \"game_id\": 168, \"round_id\": 798, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-03 23:01:05\", \"round_number\": 5, \"source_user_id\": 1}', '2026-08-03 19:31:05'),
(3215, 'game_168', 'round_recorded', '{\"card\": {\"id\": 15, \"name\": \"کینگ\", \"emoji\": \"👑\", \"rarity\": \"legendary\", \"multiplier\": 20}, \"score\": 110, \"winner\": {\"id\": 17, \"name\": \"RANGER\", \"participant_id\": 697}, \"game_id\": 168, \"round_id\": 799, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-03 23:03:54\", \"round_number\": 6, \"source_user_id\": 1}', '2026-08-03 19:33:54'),
(3216, 'game_168', 'round_recorded', '{\"card\": {\"id\": 12, \"name\": \"شافل\", \"emoji\": \"🌀\", \"rarity\": \"rare\", \"multiplier\": 10}, \"score\": 60, \"winner\": {\"id\": 17, \"name\": \"RANGER\", \"participant_id\": 697}, \"game_id\": 168, \"round_id\": 800, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-03 23:06:59\", \"round_number\": 7, \"source_user_id\": 1}', '2026-08-03 19:36:59'),
(3217, 'game_168', 'round_recorded', '{\"card\": {\"id\": 13, \"name\": \"قفل کردن\", \"emoji\": \"🔒\", \"rarity\": \"rare\", \"multiplier\": 10}, \"score\": 60, \"winner\": {\"id\": 18, \"name\": \"امپراطور\", \"participant_id\": 698}, \"game_id\": 168, \"round_id\": 801, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-03 23:09:14\", \"round_number\": 8, \"source_user_id\": 1}', '2026-08-03 19:39:14'),
(3218, 'game_168', 'game_target_changed', '{\"game_id\": 168, \"max_wins\": 4, \"changed_at\": \"2026-08-03 23:09:33\", \"min_target\": 4, \"new_target\": 5, \"old_target\": 5, \"source_user_id\": 1}', '2026-08-03 19:39:33'),
(3219, 'game_168', 'game_target_changed', '{\"game_id\": 168, \"max_wins\": 4, \"changed_at\": \"2026-08-03 23:09:44\", \"min_target\": 4, \"new_target\": 10, \"old_target\": 5, \"source_user_id\": 1}', '2026-08-03 19:39:44'),
(3220, 'game_168', 'round_recorded', '{\"card\": {\"id\": 9, \"name\": \"هدیه\", \"emoji\": \"🎁\", \"rarity\": \"common\", \"multiplier\": 2}, \"score\": 20, \"winner\": {\"id\": 1, \"name\": \"سنتری\", \"participant_id\": 699}, \"game_id\": 168, \"round_id\": 802, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-03 23:12:03\", \"round_number\": 9, \"source_user_id\": 1}', '2026-08-03 19:42:03'),
(3221, 'game_168', 'round_recorded', '{\"card\": {\"id\": 5, \"name\": \"تغییر رنگ\", \"emoji\": \"🌈\", \"rarity\": \"common\", \"multiplier\": 2}, \"score\": 20, \"winner\": {\"id\": 17, \"name\": \"RANGER\", \"participant_id\": 697}, \"game_id\": 168, \"round_id\": 803, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-03 23:13:36\", \"round_number\": 10, \"source_user_id\": 1}', '2026-08-03 19:43:36'),
(3222, 'game_168', 'round_recorded', '{\"card\": {\"id\": 7, \"name\": \"سپر\", \"emoji\": \"🛡️\", \"rarity\": \"rare\", \"multiplier\": 5}, \"score\": 35, \"winner\": {\"id\": 1, \"name\": \"سنتری\", \"participant_id\": 699}, \"game_id\": 168, \"round_id\": 804, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-03 23:15:48\", \"round_number\": 11, \"source_user_id\": 1}', '2026-08-03 19:45:48'),
(3223, 'game_168', 'round_recorded', '{\"card\": {\"id\": 5, \"name\": \"تغییر رنگ\", \"emoji\": \"🌈\", \"rarity\": \"common\", \"multiplier\": 2}, \"score\": 20, \"winner\": {\"id\": 17, \"name\": \"RANGER\", \"participant_id\": 697}, \"game_id\": 168, \"round_id\": 805, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-03 23:19:12\", \"round_number\": 12, \"source_user_id\": 1}', '2026-08-03 19:49:12');
INSERT INTO `sse_events` (`id`, `channel`, `event_type`, `data`, `created_at`) VALUES
(3224, 'game_168', 'round_recorded', '{\"card\": {\"id\": 6, \"name\": \"جریمه ۴ تایی\", \"emoji\": \"+4\", \"rarity\": \"common\", \"multiplier\": 3}, \"score\": 25, \"winner\": {\"id\": 18, \"name\": \"امپراطور\", \"participant_id\": 698}, \"game_id\": 168, \"round_id\": 806, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-03 23:23:33\", \"round_number\": 13, \"source_user_id\": 1}', '2026-08-03 19:53:34'),
(3225, 'game_168', 'round_recorded', '{\"card\": {\"id\": 4, \"name\": \"جریمه دوتایی\", \"emoji\": \"+2\", \"rarity\": \"common\", \"multiplier\": 2}, \"score\": 20, \"winner\": {\"id\": 17, \"name\": \"RANGER\", \"participant_id\": 697}, \"game_id\": 168, \"round_id\": 807, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-03 23:25:40\", \"round_number\": 14, \"source_user_id\": 1}', '2026-08-03 19:55:40'),
(3226, 'game_168', 'round_recorded', '{\"card\": {\"id\": 13, \"name\": \"قفل کردن\", \"emoji\": \"🔒\", \"rarity\": \"rare\", \"multiplier\": 10}, \"score\": 60, \"winner\": {\"id\": 1, \"name\": \"سنتری\", \"participant_id\": 699}, \"game_id\": 168, \"round_id\": 808, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-03 23:29:59\", \"round_number\": 15, \"source_user_id\": 1}', '2026-08-03 19:59:59'),
(3227, 'game_168', 'round_recorded', '{\"card\": {\"id\": 4, \"name\": \"جریمه دوتایی\", \"emoji\": \"+2\", \"rarity\": \"common\", \"multiplier\": 2}, \"score\": 20, \"winner\": {\"id\": 17, \"name\": \"RANGER\", \"participant_id\": 697}, \"game_id\": 168, \"round_id\": 809, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-03 23:32:43\", \"round_number\": 16, \"source_user_id\": 1}', '2026-08-03 20:02:43'),
(3228, 'game_168', 'round_recorded', '{\"card\": {\"id\": 9, \"name\": \"هدیه\", \"emoji\": \"🎁\", \"rarity\": \"common\", \"multiplier\": 2}, \"score\": 20, \"winner\": {\"id\": 1, \"name\": \"سنتری\", \"participant_id\": 699}, \"game_id\": 168, \"round_id\": 810, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-03 23:34:13\", \"round_number\": 17, \"source_user_id\": 1}', '2026-08-03 20:04:13'),
(3229, 'game_168', 'round_recorded', '{\"card\": {\"id\": 11, \"name\": \"پرش دوتایی\", \"emoji\": \"⏩\", \"rarity\": \"common\", \"multiplier\": 2}, \"score\": 20, \"winner\": {\"id\": 17, \"name\": \"RANGER\", \"participant_id\": 697}, \"game_id\": 168, \"round_id\": 811, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-03 23:35:38\", \"round_number\": 18, \"source_user_id\": 1}', '2026-08-03 20:05:38'),
(3230, 'game_168', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 15, \"winner\": {\"id\": 18, \"name\": \"امپراطور\", \"participant_id\": 698}, \"game_id\": 168, \"round_id\": 812, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-03 23:42:11\", \"round_number\": 19, \"source_user_id\": 1}', '2026-08-03 20:12:11'),
(3231, 'game_168', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 15, \"winner\": {\"id\": 18, \"name\": \"امپراطور\", \"participant_id\": 698}, \"game_id\": 168, \"round_id\": 813, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-03 23:44:44\", \"round_number\": 20, \"source_user_id\": 1}', '2026-08-03 20:14:44'),
(3232, 'game_168', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 15, \"winner\": {\"id\": 17, \"name\": \"RANGER\", \"participant_id\": 697}, \"game_id\": 168, \"round_id\": 814, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-03 23:49:24\", \"round_number\": 21, \"source_user_id\": 1}', '2026-08-03 20:19:24'),
(3233, 'game_168', 'round_recorded', '{\"card\": {\"id\": 9, \"name\": \"هدیه\", \"emoji\": \"🎁\", \"rarity\": \"common\", \"multiplier\": 2}, \"score\": 20, \"winner\": {\"id\": 18, \"name\": \"امپراطور\", \"participant_id\": 698}, \"game_id\": 168, \"round_id\": 815, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-03 23:50:43\", \"round_number\": 22, \"source_user_id\": 1}', '2026-08-03 20:20:43'),
(3234, 'game_168', 'round_recorded', '{\"card\": {\"id\": 7, \"name\": \"سپر\", \"emoji\": \"🛡️\", \"rarity\": \"rare\", \"multiplier\": 5}, \"score\": 35, \"winner\": {\"id\": 1, \"name\": \"سنتری\", \"participant_id\": 699}, \"game_id\": 168, \"round_id\": 816, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-03 23:52:16\", \"round_number\": 23, \"source_user_id\": 1}', '2026-08-03 20:22:16'),
(3235, 'game_168', 'round_recorded', '{\"card\": {\"id\": 4, \"name\": \"جریمه دوتایی\", \"emoji\": \"+2\", \"rarity\": \"common\", \"multiplier\": 2}, \"score\": 20, \"winner\": {\"id\": 18, \"name\": \"امپراطور\", \"participant_id\": 698}, \"game_id\": 168, \"round_id\": 817, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-03 23:55:11\", \"round_number\": 24, \"source_user_id\": 1}', '2026-08-03 20:25:11'),
(3236, 'game_168', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 15, \"winner\": {\"id\": 17, \"name\": \"RANGER\", \"participant_id\": 697}, \"game_id\": 168, \"round_id\": 818, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-03 23:57:36\", \"round_number\": 25, \"source_user_id\": 1}', '2026-08-03 20:27:36'),
(3237, 'game_168', 'game_finished', '{\"status\": \"finished\", \"winner\": {\"id\": 17, \"name\": \"RANGER\", \"participant_id\": 697}, \"game_id\": 168, \"finished_at\": \"2026-08-03 23:57:47\", \"total_rounds\": 25, \"source_user_id\": 1}', '2026-08-03 20:27:47'),
(3238, 'game_169', 'game_started', '{\"status\": \"active\", \"game_id\": 169, \"started_at\": \"2026-08-03 23:59:02\", \"first_player\": {\"id\": 700, \"name\": \"RANGER\"}, \"source_user_id\": 1}', '2026-08-03 20:29:02'),
(3239, 'game_169', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 15, \"winner\": {\"id\": 17, \"name\": \"RANGER\", \"participant_id\": 700}, \"game_id\": 169, \"round_id\": 819, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-04 00:02:36\", \"round_number\": 1, \"source_user_id\": 1}', '2026-08-03 20:32:36'),
(3240, 'game_169', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 15, \"winner\": {\"id\": 1, \"name\": \"سنتری\", \"participant_id\": 702}, \"game_id\": 169, \"round_id\": 820, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-04 00:05:01\", \"round_number\": 2, \"source_user_id\": 1}', '2026-08-03 20:35:01'),
(3241, 'game_169', 'round_recorded', '{\"card\": {\"id\": 9, \"name\": \"هدیه\", \"emoji\": \"🎁\", \"rarity\": \"common\", \"multiplier\": 2}, \"score\": 20, \"winner\": {\"id\": 18, \"name\": \"امپراطور\", \"participant_id\": 701}, \"game_id\": 169, \"round_id\": 821, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-04 00:07:17\", \"round_number\": 3, \"source_user_id\": 1}', '2026-08-03 20:37:17'),
(3242, 'game_169', 'round_recorded', '{\"card\": {\"id\": 7, \"name\": \"سپر\", \"emoji\": \"🛡️\", \"rarity\": \"rare\", \"multiplier\": 5}, \"score\": 35, \"winner\": {\"id\": 1, \"name\": \"سنتری\", \"participant_id\": 702}, \"game_id\": 169, \"round_id\": 822, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-04 00:09:29\", \"round_number\": 4, \"source_user_id\": 1}', '2026-08-03 20:39:29'),
(3243, 'game_169', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 15, \"winner\": {\"id\": 18, \"name\": \"امپراطور\", \"participant_id\": 701}, \"game_id\": 169, \"round_id\": 823, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-04 00:14:11\", \"round_number\": 5, \"source_user_id\": 1}', '2026-08-03 20:44:12'),
(3244, 'game_169', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 15, \"winner\": {\"id\": 18, \"name\": \"امپراطور\", \"participant_id\": 701}, \"game_id\": 169, \"round_id\": 824, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-04 00:16:09\", \"round_number\": 6, \"source_user_id\": 1}', '2026-08-03 20:46:09'),
(3245, 'game_169', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 15, \"winner\": {\"id\": 17, \"name\": \"RANGER\", \"participant_id\": 700}, \"game_id\": 169, \"round_id\": 825, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-04 00:19:36\", \"round_number\": 7, \"source_user_id\": 1}', '2026-08-03 20:49:36'),
(3246, 'game_169', 'round_recorded', '{\"card\": {\"id\": 2, \"name\": \"پرش یکی\", \"emoji\": \"⏭️\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 15, \"winner\": {\"id\": 17, \"name\": \"RANGER\", \"participant_id\": 700}, \"game_id\": 169, \"round_id\": 826, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-04 00:23:43\", \"round_number\": 8, \"source_user_id\": 1}', '2026-08-03 20:53:43'),
(3247, 'game_169', 'round_recorded', '{\"card\": {\"id\": 6, \"name\": \"جریمه ۴ تایی\", \"emoji\": \"+4\", \"rarity\": \"common\", \"multiplier\": 3}, \"score\": 25, \"winner\": {\"id\": 1, \"name\": \"سنتری\", \"participant_id\": 702}, \"game_id\": 169, \"round_id\": 827, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-04 00:26:36\", \"round_number\": 9, \"source_user_id\": 1}', '2026-08-03 20:56:36'),
(3248, 'game_169', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 15, \"winner\": {\"id\": 18, \"name\": \"امپراطور\", \"participant_id\": 701}, \"game_id\": 169, \"round_id\": 828, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-04 00:28:19\", \"round_number\": 10, \"source_user_id\": 1}', '2026-08-03 20:58:19'),
(3249, 'game_169', 'round_recorded', '{\"card\": {\"id\": 9, \"name\": \"هدیه\", \"emoji\": \"🎁\", \"rarity\": \"common\", \"multiplier\": 2}, \"score\": 20, \"winner\": {\"id\": 17, \"name\": \"RANGER\", \"participant_id\": 700}, \"game_id\": 169, \"round_id\": 829, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-04 00:29:18\", \"round_number\": 11, \"source_user_id\": 1}', '2026-08-03 20:59:18'),
(3250, 'game_169', 'round_recorded', '{\"card\": {\"id\": 9, \"name\": \"هدیه\", \"emoji\": \"🎁\", \"rarity\": \"common\", \"multiplier\": 2}, \"score\": 20, \"winner\": {\"id\": 17, \"name\": \"RANGER\", \"participant_id\": 700}, \"game_id\": 169, \"round_id\": 830, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-04 00:33:30\", \"round_number\": 12, \"source_user_id\": 1}', '2026-08-03 21:03:30'),
(3251, 'game_169', 'game_finished', '{\"status\": \"finished\", \"winner\": {\"id\": 17, \"name\": \"RANGER\", \"participant_id\": 700}, \"game_id\": 169, \"finished_at\": \"2026-08-04 00:33:45\", \"total_rounds\": 12, \"source_user_id\": 1}', '2026-08-03 21:03:45'),
(3252, 'game_170', 'game_started', '{\"status\": \"active\", \"game_id\": 170, \"started_at\": \"2026-08-04 16:11:59\", \"first_player\": {\"id\": 704, \"name\": \"RANGER\"}, \"source_user_id\": 1}', '2026-08-04 12:41:59'),
(3253, 'game_170', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 15, \"winner\": {\"id\": 1, \"name\": \"سنتری\", \"participant_id\": 705}, \"game_id\": 170, \"round_id\": 831, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-04 16:15:02\", \"round_number\": 1, \"source_user_id\": 1}', '2026-08-04 12:45:02'),
(3254, 'game_170', 'round_recorded', '{\"card\": {\"id\": 15, \"name\": \"کینگ\", \"emoji\": \"👑\", \"rarity\": \"legendary\", \"multiplier\": 20}, \"score\": 110, \"winner\": {\"id\": 16, \"name\": \"KiNG\", \"participant_id\": 703}, \"game_id\": 170, \"round_id\": 832, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-04 16:16:22\", \"round_number\": 2, \"source_user_id\": 1}', '2026-08-04 12:46:22'),
(3255, 'game_170', 'round_recorded', '{\"card\": {\"id\": 11, \"name\": \"پرش دوتایی\", \"emoji\": \"⏩\", \"rarity\": \"common\", \"multiplier\": 2}, \"score\": 20, \"winner\": {\"id\": 1, \"name\": \"سنتری\", \"participant_id\": 705}, \"game_id\": 170, \"round_id\": 833, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-04 16:19:45\", \"round_number\": 3, \"source_user_id\": 1}', '2026-08-04 12:49:46'),
(3256, 'game_170', 'round_recorded', '{\"card\": {\"id\": 9, \"name\": \"هدیه\", \"emoji\": \"🎁\", \"rarity\": \"common\", \"multiplier\": 2}, \"score\": 20, \"winner\": {\"id\": 1, \"name\": \"سنتری\", \"participant_id\": 705}, \"game_id\": 170, \"round_id\": 834, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-04 16:20:59\", \"round_number\": 4, \"source_user_id\": 1}', '2026-08-04 12:50:59'),
(3257, 'game_170', 'round_recorded', '{\"card\": {\"id\": 9, \"name\": \"هدیه\", \"emoji\": \"🎁\", \"rarity\": \"common\", \"multiplier\": 2}, \"score\": 20, \"winner\": {\"id\": 1, \"name\": \"سنتری\", \"participant_id\": 705}, \"game_id\": 170, \"round_id\": 835, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-04 16:23:05\", \"round_number\": 5, \"source_user_id\": 1}', '2026-08-04 12:53:05'),
(3258, 'game_170', 'round_recorded', '{\"card\": {\"id\": 9, \"name\": \"هدیه\", \"emoji\": \"🎁\", \"rarity\": \"common\", \"multiplier\": 2}, \"score\": 20, \"winner\": {\"id\": 16, \"name\": \"KiNG\", \"participant_id\": 703}, \"game_id\": 170, \"round_id\": 836, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-04 16:27:05\", \"round_number\": 6, \"source_user_id\": 1}', '2026-08-04 12:57:05'),
(3259, 'game_170', 'round_recorded', '{\"card\": {\"id\": 7, \"name\": \"سپر\", \"emoji\": \"🛡️\", \"rarity\": \"rare\", \"multiplier\": 5}, \"score\": 35, \"winner\": {\"id\": 16, \"name\": \"KiNG\", \"participant_id\": 703}, \"game_id\": 170, \"round_id\": 837, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-04 16:32:23\", \"round_number\": 7, \"source_user_id\": 1}', '2026-08-04 13:02:23'),
(3260, 'game_170', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 15, \"winner\": {\"id\": 17, \"name\": \"RANGER\", \"participant_id\": 704}, \"game_id\": 170, \"round_id\": 838, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-04 16:34:12\", \"round_number\": 8, \"source_user_id\": 1}', '2026-08-04 13:04:12'),
(3261, 'game_170', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 15, \"winner\": {\"id\": 1, \"name\": \"سنتری\", \"participant_id\": 705}, \"game_id\": 170, \"round_id\": 839, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-04 16:38:34\", \"round_number\": 9, \"source_user_id\": 1}', '2026-08-04 13:08:34'),
(3262, 'game_170', 'round_recorded', '{\"card\": {\"id\": 3, \"name\": \"دوربرگردان\", \"emoji\": \"🔄\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 15, \"winner\": {\"id\": 16, \"name\": \"KiNG\", \"participant_id\": 703}, \"game_id\": 170, \"round_id\": 840, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-04 16:41:10\", \"round_number\": 10, \"source_user_id\": 1}', '2026-08-04 13:11:10'),
(3263, 'game_170', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 15, \"winner\": {\"id\": 16, \"name\": \"KiNG\", \"participant_id\": 703}, \"game_id\": 170, \"round_id\": 841, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-04 16:44:34\", \"round_number\": 11, \"source_user_id\": 1}', '2026-08-04 13:14:34'),
(3264, 'game_170', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 15, \"winner\": {\"id\": 16, \"name\": \"KiNG\", \"participant_id\": 703}, \"game_id\": 170, \"round_id\": 842, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-04 16:46:07\", \"round_number\": 12, \"source_user_id\": 1}', '2026-08-04 13:16:07'),
(3265, 'game_170', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 15, \"winner\": {\"id\": 1, \"name\": \"سنتری\", \"participant_id\": 705}, \"game_id\": 170, \"round_id\": 843, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-04 16:49:19\", \"round_number\": 13, \"source_user_id\": 1}', '2026-08-04 13:19:19'),
(3266, 'game_170', 'round_recorded', '{\"card\": {\"id\": 9, \"name\": \"هدیه\", \"emoji\": \"🎁\", \"rarity\": \"common\", \"multiplier\": 2}, \"score\": 20, \"winner\": {\"id\": 17, \"name\": \"RANGER\", \"participant_id\": 704}, \"game_id\": 170, \"round_id\": 844, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-04 16:52:50\", \"round_number\": 14, \"source_user_id\": 1}', '2026-08-04 13:22:50'),
(3267, 'game_170', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 15, \"winner\": {\"id\": 1, \"name\": \"سنتری\", \"participant_id\": 705}, \"game_id\": 170, \"round_id\": 845, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-04 16:56:18\", \"round_number\": 15, \"source_user_id\": 1}', '2026-08-04 13:26:18'),
(3268, 'game_170', 'game_status_changed', '{\"status\": \"paused\", \"game_id\": 170, \"source_user_id\": 1}', '2026-08-04 13:26:24'),
(3269, 'game_171', 'game_started', '{\"status\": \"active\", \"game_id\": 171, \"started_at\": \"2026-08-04 16:57:21\", \"first_player\": {\"id\": 708, \"name\": \"امپراطور\"}, \"source_user_id\": 1}', '2026-08-04 13:27:21'),
(3270, 'game_171', 'round_recorded', '{\"card\": {\"id\": 9, \"name\": \"هدیه\", \"emoji\": \"🎁\", \"rarity\": \"common\", \"multiplier\": 2}, \"score\": 20, \"winner\": {\"id\": 17, \"name\": \"RANGER\", \"participant_id\": 707}, \"game_id\": 171, \"round_id\": 846, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-04 16:58:31\", \"round_number\": 1, \"source_user_id\": 1}', '2026-08-04 13:28:31'),
(3271, 'game_171', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 15, \"winner\": {\"id\": 17, \"name\": \"RANGER\", \"participant_id\": 707}, \"game_id\": 171, \"round_id\": 847, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-04 17:01:37\", \"round_number\": 2, \"source_user_id\": 1}', '2026-08-04 13:31:37'),
(3272, 'game_171', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 15, \"winner\": {\"id\": 17, \"name\": \"RANGER\", \"participant_id\": 707}, \"game_id\": 171, \"round_id\": 848, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-04 17:06:56\", \"round_number\": 3, \"source_user_id\": 1}', '2026-08-04 13:36:56'),
(3273, 'game_171', 'round_recorded', '{\"card\": {\"id\": 9, \"name\": \"هدیه\", \"emoji\": \"🎁\", \"rarity\": \"common\", \"multiplier\": 2}, \"score\": 20, \"winner\": {\"id\": 17, \"name\": \"RANGER\", \"participant_id\": 707}, \"game_id\": 171, \"round_id\": 849, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-04 17:08:19\", \"round_number\": 4, \"source_user_id\": 1}', '2026-08-04 13:38:19'),
(3274, 'game_171', 'round_recorded', '{\"card\": {\"id\": 12, \"name\": \"شافل\", \"emoji\": \"🌀\", \"rarity\": \"rare\", \"multiplier\": 10}, \"score\": 60, \"winner\": {\"id\": 1, \"name\": \"سنتری\", \"participant_id\": 709}, \"game_id\": 171, \"round_id\": 850, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-04 17:11:48\", \"round_number\": 5, \"source_user_id\": 1}', '2026-08-04 13:41:48'),
(3275, 'game_171', 'round_recorded', '{\"card\": {\"id\": 9, \"name\": \"هدیه\", \"emoji\": \"🎁\", \"rarity\": \"common\", \"multiplier\": 2}, \"score\": 20, \"winner\": {\"id\": 17, \"name\": \"RANGER\", \"participant_id\": 707}, \"game_id\": 171, \"round_id\": 851, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-04 17:13:54\", \"round_number\": 6, \"source_user_id\": 1}', '2026-08-04 13:43:54'),
(3276, 'game_171', 'game_finished', '{\"status\": \"finished\", \"winner\": {\"id\": 17, \"name\": \"RANGER\", \"participant_id\": 707}, \"game_id\": 171, \"finished_at\": \"2026-08-04 17:14:01\", \"total_rounds\": 6, \"source_user_id\": 1}', '2026-08-04 13:44:01'),
(3277, 'game_172', 'game_started', '{\"status\": \"active\", \"game_id\": 172, \"started_at\": \"2026-08-04 17:15:30\", \"first_player\": {\"id\": 713, \"name\": \"سنتری\"}, \"source_user_id\": 1}', '2026-08-04 13:45:30'),
(3278, 'game_172', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 20, \"winner\": {\"id\": 18, \"name\": \"امپراطور\", \"participant_id\": 712}, \"game_id\": 172, \"round_id\": 852, \"win_type\": null, \"team_name\": \"تیم 2\", \"recorded_at\": \"2026-08-04 17:18:47\", \"round_number\": 1, \"source_user_id\": 1}', '2026-08-04 13:48:47'),
(3279, 'game_172', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 20, \"winner\": {\"id\": 1, \"name\": \"سنتری\", \"participant_id\": 713}, \"game_id\": 172, \"round_id\": 853, \"win_type\": null, \"team_name\": \"تیم 1\", \"recorded_at\": \"2026-08-04 17:25:56\", \"round_number\": 2, \"source_user_id\": 1}', '2026-08-04 13:55:56'),
(3280, 'game_172', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 20, \"winner\": {\"id\": 18, \"name\": \"امپراطور\", \"participant_id\": 712}, \"game_id\": 172, \"round_id\": 854, \"win_type\": null, \"team_name\": \"تیم 2\", \"recorded_at\": \"2026-08-04 17:30:29\", \"round_number\": 3, \"source_user_id\": 1}', '2026-08-04 14:00:29'),
(3281, 'game_172', 'round_recorded', '{\"card\": {\"id\": 9, \"name\": \"هدیه\", \"emoji\": \"🎁\", \"rarity\": \"common\", \"multiplier\": 2}, \"score\": 30, \"winner\": {\"id\": 18, \"name\": \"امپراطور\", \"participant_id\": 712}, \"game_id\": 172, \"round_id\": 855, \"win_type\": null, \"team_name\": \"تیم 2\", \"recorded_at\": \"2026-08-04 17:32:46\", \"round_number\": 4, \"source_user_id\": 1}', '2026-08-04 14:02:46'),
(3282, 'game_172', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 20, \"winner\": {\"id\": 16, \"name\": \"KiNG\", \"participant_id\": 710}, \"game_id\": 172, \"round_id\": 856, \"win_type\": null, \"team_name\": \"تیم 1\", \"recorded_at\": \"2026-08-04 17:38:57\", \"round_number\": 5, \"source_user_id\": 1}', '2026-08-04 14:08:57'),
(3283, 'game_172', 'round_recorded', '{\"card\": {\"id\": 12, \"name\": \"شافل\", \"emoji\": \"🌀\", \"rarity\": \"rare\", \"multiplier\": 10}, \"score\": 110, \"winner\": {\"id\": 18, \"name\": \"امپراطور\", \"participant_id\": 712}, \"game_id\": 172, \"round_id\": 857, \"win_type\": null, \"team_name\": \"تیم 2\", \"recorded_at\": \"2026-08-04 17:46:42\", \"round_number\": 6, \"source_user_id\": 1}', '2026-08-04 14:16:42'),
(3284, 'game_172', 'round_recorded', '{\"card\": {\"id\": 12, \"name\": \"شافل\", \"emoji\": \"🌀\", \"rarity\": \"rare\", \"multiplier\": 10}, \"score\": 110, \"winner\": {\"id\": 16, \"name\": \"KiNG\", \"participant_id\": 710}, \"game_id\": 172, \"round_id\": 858, \"win_type\": null, \"team_name\": \"تیم 1\", \"recorded_at\": \"2026-08-04 17:51:15\", \"round_number\": 7, \"source_user_id\": 1}', '2026-08-04 14:21:15'),
(3285, 'game_172', 'round_recorded', '{\"card\": {\"id\": 15, \"name\": \"کینگ\", \"emoji\": \"👑\", \"rarity\": \"legendary\", \"multiplier\": 20}, \"score\": 210, \"winner\": {\"id\": 18, \"name\": \"امپراطور\", \"participant_id\": 712}, \"game_id\": 172, \"round_id\": 859, \"win_type\": null, \"team_name\": \"تیم 2\", \"recorded_at\": \"2026-08-04 17:54:01\", \"round_number\": 8, \"source_user_id\": 1}', '2026-08-04 14:24:02'),
(3286, 'game_172', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 20, \"winner\": {\"id\": 1, \"name\": \"سنتری\", \"participant_id\": 713}, \"game_id\": 172, \"round_id\": 860, \"win_type\": null, \"team_name\": \"تیم 1\", \"recorded_at\": \"2026-08-04 17:59:24\", \"round_number\": 9, \"source_user_id\": 1}', '2026-08-04 14:29:24'),
(3287, 'game_172', 'round_recorded', '{\"card\": {\"id\": 14, \"name\": \"جریمه دوتایی انتخابی\", \"emoji\": \"🎯\", \"rarity\": \"rare\", \"multiplier\": 5}, \"score\": 60, \"winner\": {\"id\": 18, \"name\": \"امپراطور\", \"participant_id\": 712}, \"game_id\": 172, \"round_id\": 861, \"win_type\": null, \"team_name\": \"تیم 2\", \"recorded_at\": \"2026-08-04 18:02:36\", \"round_number\": 10, \"source_user_id\": 1}', '2026-08-04 14:32:36'),
(3288, 'game_172', 'round_recorded', '{\"card\": {\"id\": 9, \"name\": \"هدیه\", \"emoji\": \"🎁\", \"rarity\": \"common\", \"multiplier\": 2}, \"score\": 30, \"winner\": {\"id\": 16, \"name\": \"KiNG\", \"participant_id\": 710}, \"game_id\": 172, \"round_id\": 862, \"win_type\": null, \"team_name\": \"تیم 1\", \"recorded_at\": \"2026-08-04 18:04:45\", \"round_number\": 11, \"source_user_id\": 1}', '2026-08-04 14:34:45'),
(3289, 'game_172', 'round_recorded', '{\"card\": {\"id\": 9, \"name\": \"هدیه\", \"emoji\": \"🎁\", \"rarity\": \"common\", \"multiplier\": 2}, \"score\": 30, \"winner\": {\"id\": 17, \"name\": \"RANGER\", \"participant_id\": 711}, \"game_id\": 172, \"round_id\": 863, \"win_type\": null, \"team_name\": \"تیم 2\", \"recorded_at\": \"2026-08-04 18:06:19\", \"round_number\": 12, \"source_user_id\": 1}', '2026-08-04 14:36:19'),
(3290, 'game_172', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 20, \"winner\": {\"id\": 1, \"name\": \"سنتری\", \"participant_id\": 713}, \"game_id\": 172, \"round_id\": 864, \"win_type\": null, \"team_name\": \"تیم 1\", \"recorded_at\": \"2026-08-04 18:10:05\", \"round_number\": 13, \"source_user_id\": 1}', '2026-08-04 14:40:05'),
(3291, 'game_172', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 20, \"winner\": {\"id\": 16, \"name\": \"KiNG\", \"participant_id\": 710}, \"game_id\": 172, \"round_id\": 865, \"win_type\": null, \"team_name\": \"تیم 1\", \"recorded_at\": \"2026-08-04 18:13:17\", \"round_number\": 14, \"source_user_id\": 1}', '2026-08-04 14:43:17'),
(3292, 'game_172', 'round_recorded', '{\"card\": {\"id\": 7, \"name\": \"سپر\", \"emoji\": \"🛡️\", \"rarity\": \"rare\", \"multiplier\": 5}, \"score\": 60, \"winner\": {\"id\": 18, \"name\": \"امپراطور\", \"participant_id\": 712}, \"game_id\": 172, \"round_id\": 866, \"win_type\": null, \"team_name\": \"تیم 2\", \"recorded_at\": \"2026-08-04 18:20:34\", \"round_number\": 15, \"source_user_id\": 1}', '2026-08-04 14:50:34'),
(3293, 'game_172', 'round_recorded', '{\"card\": {\"id\": 15, \"name\": \"کینگ\", \"emoji\": \"👑\", \"rarity\": \"legendary\", \"multiplier\": 20}, \"score\": 210, \"winner\": {\"id\": 18, \"name\": \"امپراطور\", \"participant_id\": 712}, \"game_id\": 172, \"round_id\": 867, \"win_type\": null, \"team_name\": \"تیم 2\", \"recorded_at\": \"2026-08-04 18:25:04\", \"round_number\": 16, \"source_user_id\": 1}', '2026-08-04 14:55:04'),
(3294, 'game_172', 'round_recorded', '{\"card\": {\"id\": 7, \"name\": \"سپر\", \"emoji\": \"🛡️\", \"rarity\": \"rare\", \"multiplier\": 5}, \"score\": 60, \"winner\": {\"id\": 18, \"name\": \"امپراطور\", \"participant_id\": 712}, \"game_id\": 172, \"round_id\": 868, \"win_type\": null, \"team_name\": \"تیم 2\", \"recorded_at\": \"2026-08-04 18:28:36\", \"round_number\": 17, \"source_user_id\": 1}', '2026-08-04 14:58:36'),
(3295, 'game_172', 'game_finished', '{\"status\": \"finished\", \"winner\": {\"id\": 17, \"name\": \"تیم تیم 2\", \"participant_id\": 711}, \"game_id\": 172, \"finished_at\": \"2026-08-04 18:28:44\", \"total_rounds\": 17, \"source_user_id\": 1}', '2026-08-04 14:58:44'),
(3296, 'game_170', 'game_status_changed', '{\"status\": \"active\", \"game_id\": 170, \"source_user_id\": 1}', '2026-08-04 14:59:44'),
(3297, 'game_170', 'round_recorded', '{\"card\": {\"id\": 7, \"name\": \"سپر\", \"emoji\": \"🛡️\", \"rarity\": \"rare\", \"multiplier\": 5}, \"score\": 35, \"winner\": {\"id\": 1, \"name\": \"سنتری\", \"participant_id\": 705}, \"game_id\": 170, \"round_id\": 869, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-04 18:31:23\", \"round_number\": 16, \"source_user_id\": 1}', '2026-08-04 15:01:23'),
(3298, 'game_170', 'round_recorded', '{\"card\": {\"id\": 13, \"name\": \"قفل کردن\", \"emoji\": \"🔒\", \"rarity\": \"rare\", \"multiplier\": 10}, \"score\": 60, \"winner\": {\"id\": 16, \"name\": \"KiNG\", \"participant_id\": 703}, \"game_id\": 170, \"round_id\": 870, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-04 18:34:45\", \"round_number\": 17, \"source_user_id\": 1}', '2026-08-04 15:04:45'),
(3299, 'game_170', 'round_recorded', '{\"card\": {\"id\": 9, \"name\": \"هدیه\", \"emoji\": \"🎁\", \"rarity\": \"common\", \"multiplier\": 2}, \"score\": 20, \"winner\": {\"id\": 1, \"name\": \"سنتری\", \"participant_id\": 705}, \"game_id\": 170, \"round_id\": 871, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-04 18:36:50\", \"round_number\": 18, \"source_user_id\": 1}', '2026-08-04 15:06:50'),
(3300, 'game_170', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 105, \"winner\": {\"id\": 17, \"name\": \"RANGER\", \"participant_id\": 704}, \"game_id\": 170, \"round_id\": 872, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-04 18:38:51\", \"round_number\": 19, \"source_user_id\": 1}', '2026-08-04 15:08:51'),
(3301, 'game_170', 'round_recorded', '{\"card\": {\"id\": 11, \"name\": \"پرش دوتایی\", \"emoji\": \"⏩\", \"rarity\": \"common\", \"multiplier\": 2}, \"score\": 20, \"winner\": {\"id\": 1, \"name\": \"سنتری\", \"participant_id\": 705}, \"game_id\": 170, \"round_id\": 873, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-04 18:42:55\", \"round_number\": 20, \"source_user_id\": 1}', '2026-08-04 15:12:55'),
(3302, 'game_170', 'game_finished', '{\"status\": \"finished\", \"winner\": {\"id\": 1, \"name\": \"سنتری\", \"participant_id\": 705}, \"game_id\": 170, \"finished_at\": \"2026-08-04 18:43:03\", \"total_rounds\": 20, \"source_user_id\": 1}', '2026-08-04 15:13:04'),
(3303, 'game_173', 'game_started', '{\"status\": \"active\", \"game_id\": 173, \"started_at\": \"2026-08-05 14:57:06\", \"first_player\": {\"id\": 716, \"name\": \"سنتری\"}, \"source_user_id\": 1}', '2026-08-05 11:27:06'),
(3304, 'game_173', 'game_target_changed', '{\"game_id\": 173, \"max_wins\": 0, \"changed_at\": \"2026-08-05 15:03:14\", \"min_target\": 3, \"new_target\": 5, \"old_target\": 10, \"source_user_id\": 1}', '2026-08-05 11:33:14'),
(3305, 'game_173', 'round_recorded', '{\"card\": {\"id\": 11, \"name\": \"پرش دوتایی\", \"emoji\": \"⏩\", \"rarity\": \"common\", \"multiplier\": 2}, \"score\": 110, \"winner\": {\"id\": 17, \"name\": \"RANGER\", \"participant_id\": 714}, \"game_id\": 173, \"round_id\": 874, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-05 15:05:17\", \"round_number\": 1, \"source_user_id\": 1}', '2026-08-05 11:35:17'),
(3306, 'game_173', 'round_recorded', '{\"card\": {\"id\": 15, \"name\": \"کینگ\", \"emoji\": \"👑\", \"rarity\": \"legendary\", \"multiplier\": 20}, \"score\": 200, \"winner\": {\"id\": 17, \"name\": \"RANGER\", \"participant_id\": 714}, \"game_id\": 173, \"round_id\": 875, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-05 15:07:56\", \"round_number\": 2, \"source_user_id\": 1}', '2026-08-05 11:37:56'),
(3307, 'game_173', 'round_recorded', '{\"card\": {\"id\": 6, \"name\": \"جریمه ۴ تایی\", \"emoji\": \"+4\", \"rarity\": \"common\", \"multiplier\": 3}, \"score\": 25, \"winner\": {\"id\": 18, \"name\": \"امپراطور\", \"participant_id\": 715}, \"game_id\": 173, \"round_id\": 876, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-05 15:09:30\", \"round_number\": 3, \"source_user_id\": 1}', '2026-08-05 11:39:30'),
(3308, 'game_173', 'round_recorded', '{\"card\": {\"id\": 11, \"name\": \"پرش دوتایی\", \"emoji\": \"⏩\", \"rarity\": \"common\", \"multiplier\": 2}, \"score\": 20, \"winner\": {\"id\": 1, \"name\": \"سنتری\", \"participant_id\": 716}, \"game_id\": 173, \"round_id\": 877, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-05 15:11:07\", \"round_number\": 4, \"source_user_id\": 1}', '2026-08-05 11:41:07'),
(3309, 'game_173', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 105, \"winner\": {\"id\": 17, \"name\": \"RANGER\", \"participant_id\": 714}, \"game_id\": 173, \"round_id\": 878, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-05 15:13:02\", \"round_number\": 5, \"source_user_id\": 1}', '2026-08-05 11:43:02'),
(3310, 'game_173', 'round_recorded', '{\"card\": {\"id\": 14, \"name\": \"جریمه دوتایی انتخابی\", \"emoji\": \"🎯\", \"rarity\": \"rare\", \"multiplier\": 5}, \"score\": 35, \"winner\": {\"id\": 1, \"name\": \"سنتری\", \"participant_id\": 716}, \"game_id\": 173, \"round_id\": 879, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-05 15:17:33\", \"round_number\": 6, \"source_user_id\": 1}', '2026-08-05 11:47:34'),
(3311, 'game_173', 'round_recorded', '{\"card\": {\"id\": 13, \"name\": \"قفل کردن\", \"emoji\": \"🔒\", \"rarity\": \"rare\", \"multiplier\": 10}, \"score\": 60, \"winner\": {\"id\": 1, \"name\": \"سنتری\", \"participant_id\": 716}, \"game_id\": 173, \"round_id\": 880, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-05 15:19:49\", \"round_number\": 7, \"source_user_id\": 1}', '2026-08-05 11:49:49'),
(3312, 'game_173', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 15, \"winner\": {\"id\": 18, \"name\": \"امپراطور\", \"participant_id\": 715}, \"game_id\": 173, \"round_id\": 881, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-05 15:21:51\", \"round_number\": 8, \"source_user_id\": 1}', '2026-08-05 11:51:51'),
(3313, 'game_173', 'round_recorded', '{\"card\": {\"id\": 9, \"name\": \"هدیه\", \"emoji\": \"🎁\", \"rarity\": \"common\", \"multiplier\": 2}, \"score\": 20, \"winner\": {\"id\": 1, \"name\": \"سنتری\", \"participant_id\": 716}, \"game_id\": 173, \"round_id\": 882, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-05 15:23:33\", \"round_number\": 9, \"source_user_id\": 1}', '2026-08-05 11:53:33'),
(3314, 'game_173', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 15, \"winner\": {\"id\": 18, \"name\": \"امپراطور\", \"participant_id\": 715}, \"game_id\": 173, \"round_id\": 883, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-05 15:26:36\", \"round_number\": 10, \"source_user_id\": 1}', '2026-08-05 11:56:37'),
(3315, 'game_173', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 15, \"winner\": {\"id\": 18, \"name\": \"امپراطور\", \"participant_id\": 715}, \"game_id\": 173, \"round_id\": 884, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-05 15:33:00\", \"round_number\": 11, \"source_user_id\": 1}', '2026-08-05 12:03:00'),
(3316, 'game_173', 'round_recorded', '{\"card\": {\"id\": 12, \"name\": \"شافل\", \"emoji\": \"🌀\", \"rarity\": \"rare\", \"multiplier\": 10}, \"score\": 150, \"winner\": {\"id\": 17, \"name\": \"RANGER\", \"participant_id\": 714}, \"game_id\": 173, \"round_id\": 885, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-05 15:37:38\", \"round_number\": 12, \"source_user_id\": 1}', '2026-08-05 12:07:38'),
(3317, 'game_173', 'round_recorded', '{\"card\": {\"id\": 9, \"name\": \"هدیه\", \"emoji\": \"🎁\", \"rarity\": \"common\", \"multiplier\": 2}, \"score\": 20, \"winner\": {\"id\": 18, \"name\": \"امپراطور\", \"participant_id\": 715}, \"game_id\": 173, \"round_id\": 886, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-05 15:40:58\", \"round_number\": 13, \"source_user_id\": 1}', '2026-08-05 12:10:58'),
(3318, 'game_173', 'game_finished', '{\"status\": \"finished\", \"winner\": {\"id\": 18, \"name\": \"امپراطور\", \"participant_id\": 715}, \"game_id\": 173, \"finished_at\": \"2026-08-05 15:43:00\", \"total_rounds\": 13, \"source_user_id\": 1}', '2026-08-05 12:13:00'),
(3319, 'game_174', 'game_started', '{\"status\": \"active\", \"game_id\": 174, \"started_at\": \"2026-08-05 17:34:56\", \"first_player\": {\"id\": 718, \"name\": \"RANGER\"}, \"source_user_id\": 16}', '2026-08-05 14:04:56'),
(3320, 'game_174', 'round_recorded', '{\"card\": {\"id\": 11, \"name\": \"پرش دوتایی\", \"emoji\": \"⏩\", \"rarity\": \"common\", \"multiplier\": 2}, \"score\": 110, \"winner\": {\"id\": 17, \"name\": \"RANGER\", \"participant_id\": 718}, \"game_id\": 174, \"round_id\": 887, \"win_type\": {\"id\": 1, \"icon\": \"✅\", \"name\": \"برد معمولی\", \"multiplier\": 1}, \"team_name\": null, \"recorded_at\": \"2026-08-05 17:36:42\", \"round_number\": 1, \"source_user_id\": 16}', '2026-08-05 14:06:42'),
(3321, 'game_174', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 15, \"winner\": {\"id\": 16, \"name\": \"KiNG\", \"participant_id\": 717}, \"game_id\": 174, \"round_id\": 888, \"win_type\": {\"id\": 2, \"icon\": \"🔄\", \"name\": \"کامبک\", \"multiplier\": 1}, \"team_name\": null, \"recorded_at\": \"2026-08-05 17:38:18\", \"round_number\": 2, \"source_user_id\": 16}', '2026-08-05 14:08:18'),
(3322, 'game_174', 'round_recorded', '{\"card\": {\"id\": 7, \"name\": \"سپر\", \"emoji\": \"🛡️\", \"rarity\": \"rare\", \"multiplier\": 5}, \"score\": 35, \"winner\": {\"id\": 16, \"name\": \"KiNG\", \"participant_id\": 717}, \"game_id\": 174, \"round_id\": 889, \"win_type\": {\"id\": 1, \"icon\": \"✅\", \"name\": \"برد معمولی\", \"multiplier\": 1}, \"team_name\": null, \"recorded_at\": \"2026-08-05 17:39:12\", \"round_number\": 3, \"source_user_id\": 16}', '2026-08-05 14:09:13'),
(3323, 'game_174', 'round_recorded', '{\"card\": {\"id\": 9, \"name\": \"هدیه\", \"emoji\": \"🎁\", \"rarity\": \"common\", \"multiplier\": 2}, \"score\": 110, \"winner\": {\"id\": 17, \"name\": \"RANGER\", \"participant_id\": 718}, \"game_id\": 174, \"round_id\": 890, \"win_type\": {\"id\": 1, \"icon\": \"✅\", \"name\": \"برد معمولی\", \"multiplier\": 1}, \"team_name\": null, \"recorded_at\": \"2026-08-05 17:41:53\", \"round_number\": 4, \"source_user_id\": 16}', '2026-08-05 14:11:53'),
(3324, 'game_174', 'round_recorded', '{\"card\": {\"id\": 15, \"name\": \"کینگ\", \"emoji\": \"👑\", \"rarity\": \"legendary\", \"multiplier\": 20}, \"score\": 200, \"winner\": {\"id\": 17, \"name\": \"RANGER\", \"participant_id\": 718}, \"game_id\": 174, \"round_id\": 891, \"win_type\": {\"id\": 1, \"icon\": \"✅\", \"name\": \"برد معمولی\", \"multiplier\": 1}, \"team_name\": null, \"recorded_at\": \"2026-08-05 17:43:44\", \"round_number\": 5, \"source_user_id\": 16}', '2026-08-05 14:13:44'),
(3325, 'game_174', 'game_status_changed', '{\"status\": \"cancelled\", \"game_id\": 174, \"source_user_id\": 16}', '2026-08-05 14:14:02'),
(3326, 'game_175', 'game_started', '{\"status\": \"active\", \"game_id\": 175, \"started_at\": \"2026-08-05 17:47:31\", \"first_player\": {\"id\": 719, \"name\": \"KiNG\"}, \"source_user_id\": 1}', '2026-08-05 14:17:31'),
(3327, 'game_175', 'round_recorded', '{\"card\": {\"id\": 9, \"name\": \"هدیه\", \"emoji\": \"🎁\", \"rarity\": \"common\", \"multiplier\": 2}, \"score\": 110, \"winner\": {\"id\": 17, \"name\": \"RANGER\", \"participant_id\": 720}, \"game_id\": 175, \"round_id\": 892, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-05 17:50:32\", \"round_number\": 1, \"source_user_id\": 1}', '2026-08-05 14:20:32'),
(3328, 'game_175', 'round_recorded', '{\"card\": {\"id\": 15, \"name\": \"کینگ\", \"emoji\": \"👑\", \"rarity\": \"legendary\", \"multiplier\": 20}, \"score\": 110, \"winner\": {\"id\": 1, \"name\": \"سنتری\", \"participant_id\": 721}, \"game_id\": 175, \"round_id\": 893, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-05 17:51:56\", \"round_number\": 2, \"source_user_id\": 1}', '2026-08-05 14:21:56'),
(3329, 'game_175', 'round_recorded', '{\"card\": {\"id\": 9, \"name\": \"هدیه\", \"emoji\": \"🎁\", \"rarity\": \"common\", \"multiplier\": 2}, \"score\": 20, \"winner\": {\"id\": 16, \"name\": \"KiNG\", \"participant_id\": 719}, \"game_id\": 175, \"round_id\": 894, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-05 17:53:38\", \"round_number\": 3, \"source_user_id\": 1}', '2026-08-05 14:23:38'),
(3330, 'game_175', 'round_recorded', '{\"card\": {\"id\": 14, \"name\": \"جریمه دوتایی انتخابی\", \"emoji\": \"🎯\", \"rarity\": \"rare\", \"multiplier\": 5}, \"score\": 125, \"winner\": {\"id\": 17, \"name\": \"RANGER\", \"participant_id\": 720}, \"game_id\": 175, \"round_id\": 895, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-05 17:55:47\", \"round_number\": 4, \"source_user_id\": 1}', '2026-08-05 14:25:47'),
(3331, 'game_175', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 15, \"winner\": {\"id\": 16, \"name\": \"KiNG\", \"participant_id\": 719}, \"game_id\": 175, \"round_id\": 896, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-05 17:57:06\", \"round_number\": 5, \"source_user_id\": 1}', '2026-08-05 14:27:06'),
(3332, 'game_175', 'round_recorded', '{\"card\": {\"id\": 15, \"name\": \"کینگ\", \"emoji\": \"👑\", \"rarity\": \"legendary\", \"multiplier\": 20}, \"score\": 200, \"winner\": {\"id\": 17, \"name\": \"RANGER\", \"participant_id\": 720}, \"game_id\": 175, \"round_id\": 897, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-05 18:00:02\", \"round_number\": 6, \"source_user_id\": 1}', '2026-08-05 14:30:02'),
(3333, 'game_175', 'round_recorded', '{\"card\": {\"id\": 9, \"name\": \"هدیه\", \"emoji\": \"🎁\", \"rarity\": \"common\", \"multiplier\": 2}, \"score\": 110, \"winner\": {\"id\": 17, \"name\": \"RANGER\", \"participant_id\": 720}, \"game_id\": 175, \"round_id\": 898, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-05 18:01:22\", \"round_number\": 7, \"source_user_id\": 1}', '2026-08-05 14:31:22'),
(3334, 'game_175', 'round_recorded', '{\"card\": {\"id\": 11, \"name\": \"پرش دوتایی\", \"emoji\": \"⏩\", \"rarity\": \"common\", \"multiplier\": 2}, \"score\": 20, \"winner\": {\"id\": 1, \"name\": \"سنتری\", \"participant_id\": 721}, \"game_id\": 175, \"round_id\": 899, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-05 18:04:03\", \"round_number\": 8, \"source_user_id\": 1}', '2026-08-05 14:34:03'),
(3335, 'game_175', 'round_recorded', '{\"card\": {\"id\": 11, \"name\": \"پرش دوتایی\", \"emoji\": \"⏩\", \"rarity\": \"common\", \"multiplier\": 2}, \"score\": 20, \"winner\": {\"id\": 1, \"name\": \"سنتری\", \"participant_id\": 721}, \"game_id\": 175, \"round_id\": 900, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-05 18:05:28\", \"round_number\": 9, \"source_user_id\": 1}', '2026-08-05 14:35:28'),
(3336, 'game_175', 'round_recorded', '{\"card\": {\"id\": 15, \"name\": \"کینگ\", \"emoji\": \"👑\", \"rarity\": \"legendary\", \"multiplier\": 20}, \"score\": 110, \"winner\": {\"id\": 1, \"name\": \"سنتری\", \"participant_id\": 721}, \"game_id\": 175, \"round_id\": 901, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-05 18:07:32\", \"round_number\": 10, \"source_user_id\": 1}', '2026-08-05 14:37:32'),
(3337, 'game_175', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 15, \"winner\": {\"id\": 16, \"name\": \"KiNG\", \"participant_id\": 719}, \"game_id\": 175, \"round_id\": 902, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-05 18:09:50\", \"round_number\": 11, \"source_user_id\": 1}', '2026-08-05 14:39:50'),
(3338, 'game_175', 'round_recorded', '{\"card\": {\"id\": 9, \"name\": \"هدیه\", \"emoji\": \"🎁\", \"rarity\": \"common\", \"multiplier\": 2}, \"score\": 20, \"winner\": {\"id\": 1, \"name\": \"سنتری\", \"participant_id\": 721}, \"game_id\": 175, \"round_id\": 903, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-05 18:11:26\", \"round_number\": 12, \"source_user_id\": 1}', '2026-08-05 14:41:26'),
(3339, 'game_175', 'round_recorded', '{\"card\": {\"id\": 6, \"name\": \"جریمه ۴ تایی\", \"emoji\": \"+4\", \"rarity\": \"common\", \"multiplier\": 3}, \"score\": 25, \"winner\": {\"id\": 1, \"name\": \"سنتری\", \"participant_id\": 721}, \"game_id\": 175, \"round_id\": 904, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-05 18:12:54\", \"round_number\": 13, \"source_user_id\": 1}', '2026-08-05 14:42:54'),
(3340, 'game_175', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 15, \"winner\": {\"id\": 1, \"name\": \"سنتری\", \"participant_id\": 721}, \"game_id\": 175, \"round_id\": 905, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-05 18:15:18\", \"round_number\": 14, \"source_user_id\": 1}', '2026-08-05 14:45:18'),
(3341, 'game_175', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 15, \"winner\": {\"id\": 1, \"name\": \"سنتری\", \"participant_id\": 721}, \"game_id\": 175, \"round_id\": 906, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-05 18:17:53\", \"round_number\": 15, \"source_user_id\": 1}', '2026-08-05 14:47:53'),
(3342, 'game_175', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 105, \"winner\": {\"id\": 17, \"name\": \"RANGER\", \"participant_id\": 720}, \"game_id\": 175, \"round_id\": 907, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-05 18:19:52\", \"round_number\": 16, \"source_user_id\": 1}', '2026-08-05 14:49:52'),
(3343, 'game_175', 'round_recorded', '{\"card\": {\"id\": 5, \"name\": \"تغییر رنگ\", \"emoji\": \"🌈\", \"rarity\": \"common\", \"multiplier\": 2}, \"score\": 20, \"winner\": {\"id\": 16, \"name\": \"KiNG\", \"participant_id\": 719}, \"game_id\": 175, \"round_id\": 908, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-05 18:21:35\", \"round_number\": 17, \"source_user_id\": 1}', '2026-08-05 14:51:35'),
(3344, 'game_175', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 15, \"winner\": {\"id\": 16, \"name\": \"KiNG\", \"participant_id\": 719}, \"game_id\": 175, \"round_id\": 909, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-05 18:23:21\", \"round_number\": 18, \"source_user_id\": 1}', '2026-08-05 14:53:21'),
(3345, 'game_175', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 105, \"winner\": {\"id\": 17, \"name\": \"RANGER\", \"participant_id\": 720}, \"game_id\": 175, \"round_id\": 910, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-05 18:25:14\", \"round_number\": 19, \"source_user_id\": 1}', '2026-08-05 14:55:14'),
(3346, 'game_175', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 15, \"winner\": {\"id\": 16, \"name\": \"KiNG\", \"participant_id\": 719}, \"game_id\": 175, \"round_id\": 911, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-05 18:27:36\", \"round_number\": 20, \"source_user_id\": 1}', '2026-08-05 14:57:36'),
(3347, 'game_175', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 15, \"winner\": {\"id\": 16, \"name\": \"KiNG\", \"participant_id\": 719}, \"game_id\": 175, \"round_id\": 912, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-05 18:29:41\", \"round_number\": 21, \"source_user_id\": 1}', '2026-08-05 14:59:41'),
(3348, 'game_175', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 15, \"winner\": {\"id\": 1, \"name\": \"سنتری\", \"participant_id\": 721}, \"game_id\": 175, \"round_id\": 913, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-05 18:32:03\", \"round_number\": 22, \"source_user_id\": 1}', '2026-08-05 15:02:03');
INSERT INTO `sse_events` (`id`, `channel`, `event_type`, `data`, `created_at`) VALUES
(3349, 'game_175', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 15, \"winner\": {\"id\": 1, \"name\": \"سنتری\", \"participant_id\": 721}, \"game_id\": 175, \"round_id\": 914, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-05 18:33:37\", \"round_number\": 23, \"source_user_id\": 1}', '2026-08-05 15:03:37'),
(3350, 'game_175', 'game_finished', '{\"status\": \"finished\", \"winner\": {\"id\": 1, \"name\": \"سنتری\", \"participant_id\": 721}, \"game_id\": 175, \"finished_at\": \"2026-08-05 18:33:44\", \"total_rounds\": 23, \"source_user_id\": 1}', '2026-08-05 15:03:44'),
(3351, 'game_176', 'game_started', '{\"status\": \"active\", \"game_id\": 176, \"started_at\": \"2026-08-05 18:34:19\", \"first_player\": {\"id\": 724, \"name\": \"سنتری\"}, \"source_user_id\": 1}', '2026-08-05 15:04:19'),
(3352, 'game_176', 'round_recorded', '{\"card\": {\"id\": 11, \"name\": \"پرش دوتایی\", \"emoji\": \"⏩\", \"rarity\": \"common\", \"multiplier\": 2}, \"score\": 20, \"winner\": {\"id\": 16, \"name\": \"KiNG\", \"participant_id\": 722}, \"game_id\": 176, \"round_id\": 915, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-05 18:35:16\", \"round_number\": 1, \"source_user_id\": 1}', '2026-08-05 15:05:16'),
(3353, 'game_176', 'round_recorded', '{\"card\": {\"id\": 9, \"name\": \"هدیه\", \"emoji\": \"🎁\", \"rarity\": \"common\", \"multiplier\": 2}, \"score\": 20, \"winner\": {\"id\": 16, \"name\": \"KiNG\", \"participant_id\": 722}, \"game_id\": 176, \"round_id\": 916, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-05 18:37:10\", \"round_number\": 2, \"source_user_id\": 1}', '2026-08-05 15:07:10'),
(3354, 'game_176', 'round_recorded', '{\"card\": {\"id\": 14, \"name\": \"جریمه دوتایی انتخابی\", \"emoji\": \"🎯\", \"rarity\": \"rare\", \"multiplier\": 5}, \"score\": 125, \"winner\": {\"id\": 17, \"name\": \"RANGER\", \"participant_id\": 723}, \"game_id\": 176, \"round_id\": 917, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-05 18:39:08\", \"round_number\": 3, \"source_user_id\": 1}', '2026-08-05 15:09:08'),
(3355, 'game_176', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 15, \"winner\": {\"id\": 16, \"name\": \"KiNG\", \"participant_id\": 722}, \"game_id\": 176, \"round_id\": 918, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-05 18:42:22\", \"round_number\": 4, \"source_user_id\": 1}', '2026-08-05 15:12:22'),
(3356, 'game_176', 'round_recorded', '{\"card\": {\"id\": 13, \"name\": \"قفل کردن\", \"emoji\": \"🔒\", \"rarity\": \"rare\", \"multiplier\": 10}, \"score\": 60, \"winner\": {\"id\": 16, \"name\": \"KiNG\", \"participant_id\": 722}, \"game_id\": 176, \"round_id\": 919, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-05 18:44:52\", \"round_number\": 5, \"source_user_id\": 1}', '2026-08-05 15:14:52'),
(3357, 'game_176', 'round_recorded', '{\"card\": {\"id\": 15, \"name\": \"کینگ\", \"emoji\": \"👑\", \"rarity\": \"legendary\", \"multiplier\": 20}, \"score\": 110, \"winner\": {\"id\": 1, \"name\": \"سنتری\", \"participant_id\": 724}, \"game_id\": 176, \"round_id\": 920, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-05 18:46:17\", \"round_number\": 6, \"source_user_id\": 1}', '2026-08-05 15:16:17'),
(3358, 'game_176', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 105, \"winner\": {\"id\": 17, \"name\": \"RANGER\", \"participant_id\": 723}, \"game_id\": 176, \"round_id\": 921, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-05 18:47:14\", \"round_number\": 7, \"source_user_id\": 1}', '2026-08-05 15:17:14'),
(3359, 'game_176', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 15, \"winner\": {\"id\": 16, \"name\": \"KiNG\", \"participant_id\": 722}, \"game_id\": 176, \"round_id\": 922, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-05 18:51:16\", \"round_number\": 8, \"source_user_id\": 1}', '2026-08-05 15:21:16'),
(3360, 'game_176', 'game_finished', '{\"status\": \"finished\", \"winner\": {\"id\": 16, \"name\": \"KiNG\", \"participant_id\": 722}, \"game_id\": 176, \"finished_at\": \"2026-08-05 18:51:23\", \"total_rounds\": 8, \"source_user_id\": 1}', '2026-08-05 15:21:23'),
(3361, 'game_177', 'game_started', '{\"status\": \"active\", \"game_id\": 177, \"started_at\": \"2026-08-05 18:52:31\", \"first_player\": {\"id\": 725, \"name\": \"KiNG\"}, \"source_user_id\": 1}', '2026-08-05 15:22:31'),
(3362, 'game_177', 'round_recorded', '{\"card\": {\"id\": 4, \"name\": \"جریمه دوتایی\", \"emoji\": \"+2\", \"rarity\": \"common\", \"multiplier\": 2}, \"score\": 30, \"winner\": {\"id\": 16, \"name\": \"KiNG\", \"participant_id\": 725}, \"game_id\": 177, \"round_id\": 923, \"win_type\": null, \"team_name\": \"تیم 2\", \"recorded_at\": \"2026-08-05 19:02:38\", \"round_number\": 1, \"source_user_id\": 1}', '2026-08-05 15:32:38'),
(3363, 'game_177', 'round_recorded', '{\"card\": {\"id\": 4, \"name\": \"جریمه دوتایی\", \"emoji\": \"+2\", \"rarity\": \"common\", \"multiplier\": 2}, \"score\": 30, \"winner\": {\"id\": 16, \"name\": \"KiNG\", \"participant_id\": 725}, \"game_id\": 177, \"round_id\": 924, \"win_type\": null, \"team_name\": \"تیم 2\", \"recorded_at\": \"2026-08-05 19:09:40\", \"round_number\": 2, \"source_user_id\": 1}', '2026-08-05 15:39:40'),
(3364, 'game_177', 'round_recorded', '{\"card\": {\"id\": 12, \"name\": \"شافل\", \"emoji\": \"🌀\", \"rarity\": \"rare\", \"multiplier\": 10}, \"score\": 200, \"winner\": {\"id\": 17, \"name\": \"RANGER\", \"participant_id\": 726}, \"game_id\": 177, \"round_id\": 925, \"win_type\": null, \"team_name\": \"تیم 2\", \"recorded_at\": \"2026-08-05 19:13:52\", \"round_number\": 3, \"source_user_id\": 1}', '2026-08-05 15:43:52'),
(3365, 'game_177', 'round_recorded', '{\"card\": {\"id\": 9, \"name\": \"هدیه\", \"emoji\": \"🎁\", \"rarity\": \"common\", \"multiplier\": 2}, \"score\": 30, \"winner\": {\"id\": 18, \"name\": \"امپراطور\", \"participant_id\": 727}, \"game_id\": 177, \"round_id\": 926, \"win_type\": null, \"team_name\": \"تیم 1\", \"recorded_at\": \"2026-08-05 19:17:33\", \"round_number\": 4, \"source_user_id\": 1}', '2026-08-05 15:47:33'),
(3366, 'game_177', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 20, \"winner\": {\"id\": 1, \"name\": \"سنتری\", \"participant_id\": 728}, \"game_id\": 177, \"round_id\": 927, \"win_type\": null, \"team_name\": \"تیم 1\", \"recorded_at\": \"2026-08-05 19:22:52\", \"round_number\": 5, \"source_user_id\": 1}', '2026-08-05 15:52:52'),
(3367, 'game_177', 'round_recorded', '{\"card\": {\"id\": 7, \"name\": \"سپر\", \"emoji\": \"🛡️\", \"rarity\": \"rare\", \"multiplier\": 5}, \"score\": 60, \"winner\": {\"id\": 16, \"name\": \"KiNG\", \"participant_id\": 725}, \"game_id\": 177, \"round_id\": 928, \"win_type\": null, \"team_name\": \"تیم 2\", \"recorded_at\": \"2026-08-05 19:25:34\", \"round_number\": 6, \"source_user_id\": 1}', '2026-08-05 15:55:34'),
(3368, 'game_177', 'round_recorded', '{\"card\": {\"id\": 15, \"name\": \"کینگ\", \"emoji\": \"👑\", \"rarity\": \"legendary\", \"multiplier\": 20}, \"score\": 210, \"winner\": {\"id\": 1, \"name\": \"سنتری\", \"participant_id\": 728}, \"game_id\": 177, \"round_id\": 929, \"win_type\": null, \"team_name\": \"تیم 1\", \"recorded_at\": \"2026-08-05 19:31:44\", \"round_number\": 7, \"source_user_id\": 1}', '2026-08-05 16:01:44'),
(3369, 'game_177', 'round_recorded', '{\"card\": {\"id\": 5, \"name\": \"تغییر رنگ\", \"emoji\": \"🌈\", \"rarity\": \"common\", \"multiplier\": 2}, \"score\": 30, \"winner\": {\"id\": 1, \"name\": \"سنتری\", \"participant_id\": 728}, \"game_id\": 177, \"round_id\": 930, \"win_type\": null, \"team_name\": \"تیم 1\", \"recorded_at\": \"2026-08-05 19:36:44\", \"round_number\": 8, \"source_user_id\": 1}', '2026-08-05 16:06:44'),
(3370, 'game_177', 'round_recorded', '{\"card\": {\"id\": 9, \"name\": \"هدیه\", \"emoji\": \"🎁\", \"rarity\": \"common\", \"multiplier\": 2}, \"score\": 30, \"winner\": {\"id\": 1, \"name\": \"سنتری\", \"participant_id\": 728}, \"game_id\": 177, \"round_id\": 931, \"win_type\": null, \"team_name\": \"تیم 1\", \"recorded_at\": \"2026-08-05 19:39:41\", \"round_number\": 9, \"source_user_id\": 1}', '2026-08-05 16:09:41'),
(3371, 'game_177', 'game_finished', '{\"status\": \"finished\", \"winner\": {\"id\": 18, \"name\": \"تیم تیم 1\", \"participant_id\": 727}, \"game_id\": 177, \"finished_at\": \"2026-08-05 19:39:53\", \"total_rounds\": 9, \"source_user_id\": 1}', '2026-08-05 16:09:53'),
(3372, 'game_178', 'game_started', '{\"status\": \"active\", \"game_id\": 178, \"started_at\": \"2026-08-06 14:10:01\", \"first_player\": {\"id\": 731, \"name\": \"امپراطور\"}, \"source_user_id\": 1}', '2026-08-06 10:40:01'),
(3373, 'game_178', 'round_recorded', '{\"card\": {\"id\": 9, \"name\": \"هدیه\", \"emoji\": \"🎁\", \"rarity\": \"common\", \"multiplier\": 2}, \"score\": 20, \"winner\": {\"id\": 1, \"name\": \"سنتری\", \"participant_id\": 732}, \"game_id\": 178, \"round_id\": 932, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-06 14:13:25\", \"round_number\": 1, \"source_user_id\": 1}', '2026-08-06 10:43:25'),
(3374, 'game_178', 'round_recorded', '{\"card\": {\"id\": 12, \"name\": \"شافل\", \"emoji\": \"🌀\", \"rarity\": \"rare\", \"multiplier\": 10}, \"score\": 60, \"winner\": {\"id\": 1, \"name\": \"سنتری\", \"participant_id\": 732}, \"game_id\": 178, \"round_id\": 933, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-06 14:17:15\", \"round_number\": 2, \"source_user_id\": 1}', '2026-08-06 10:47:15'),
(3375, 'game_178', 'round_recorded', '{\"card\": {\"id\": 9, \"name\": \"هدیه\", \"emoji\": \"🎁\", \"rarity\": \"common\", \"multiplier\": 2}, \"score\": 20, \"winner\": {\"id\": 16, \"name\": \"KiNG\", \"participant_id\": 729}, \"game_id\": 178, \"round_id\": 934, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-06 14:20:06\", \"round_number\": 3, \"source_user_id\": 1}', '2026-08-06 10:50:06'),
(3376, 'game_178', 'round_undone', '{\"game_id\": 178, \"undone_at\": \"2026-08-06 14:20:21\", \"undone_round\": 3, \"source_user_id\": 1}', '2026-08-06 10:50:21'),
(3377, 'game_178', 'round_recorded', '{\"card\": {\"id\": 9, \"name\": \"هدیه\", \"emoji\": \"🎁\", \"rarity\": \"common\", \"multiplier\": 2}, \"score\": 20, \"winner\": {\"id\": 18, \"name\": \"امپراطور\", \"participant_id\": 731}, \"game_id\": 178, \"round_id\": 935, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-06 14:20:27\", \"round_number\": 3, \"source_user_id\": 1}', '2026-08-06 10:50:27'),
(3378, 'game_178', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 15, \"winner\": {\"id\": 18, \"name\": \"امپراطور\", \"participant_id\": 731}, \"game_id\": 178, \"round_id\": 936, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-06 14:21:48\", \"round_number\": 4, \"source_user_id\": 1}', '2026-08-06 10:51:48'),
(3379, 'game_178', 'round_recorded', '{\"card\": {\"id\": 8, \"name\": \"دید زدن\", \"emoji\": \"👁️\", \"rarity\": \"common\", \"multiplier\": 2}, \"score\": 20, \"winner\": {\"id\": 1, \"name\": \"سنتری\", \"participant_id\": 732}, \"game_id\": 178, \"round_id\": 937, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-06 14:25:42\", \"round_number\": 5, \"source_user_id\": 1}', '2026-08-06 10:55:42'),
(3380, 'game_178', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 105, \"winner\": {\"id\": 17, \"name\": \"RANGER\", \"participant_id\": 730}, \"game_id\": 178, \"round_id\": 938, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-06 14:31:56\", \"round_number\": 6, \"source_user_id\": 1}', '2026-08-06 11:01:56'),
(3381, 'game_178', 'round_recorded', '{\"card\": {\"id\": 8, \"name\": \"دید زدن\", \"emoji\": \"👁️\", \"rarity\": \"common\", \"multiplier\": 2}, \"score\": 20, \"winner\": {\"id\": 18, \"name\": \"امپراطور\", \"participant_id\": 731}, \"game_id\": 178, \"round_id\": 939, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-06 14:41:47\", \"round_number\": 7, \"source_user_id\": 1}', '2026-08-06 11:11:47'),
(3382, 'game_178', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 15, \"winner\": {\"id\": 16, \"name\": \"KiNG\", \"participant_id\": 729}, \"game_id\": 178, \"round_id\": 940, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-06 14:45:18\", \"round_number\": 8, \"source_user_id\": 1}', '2026-08-06 11:15:18'),
(3383, 'game_178', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 15, \"winner\": {\"id\": 18, \"name\": \"امپراطور\", \"participant_id\": 731}, \"game_id\": 178, \"round_id\": 941, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-06 14:49:27\", \"round_number\": 9, \"source_user_id\": 1}', '2026-08-06 11:19:27'),
(3384, 'game_178', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 105, \"winner\": {\"id\": 17, \"name\": \"RANGER\", \"participant_id\": 730}, \"game_id\": 178, \"round_id\": 942, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-06 14:52:19\", \"round_number\": 10, \"source_user_id\": 1}', '2026-08-06 11:22:19'),
(3385, 'game_178', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 15, \"winner\": {\"id\": 18, \"name\": \"امپراطور\", \"participant_id\": 731}, \"game_id\": 178, \"round_id\": 943, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-06 15:01:29\", \"round_number\": 11, \"source_user_id\": 1}', '2026-08-06 11:31:29'),
(3386, 'game_178', 'game_finished', '{\"status\": \"finished\", \"winner\": {\"id\": 18, \"name\": \"امپراطور\", \"participant_id\": 731}, \"game_id\": 178, \"finished_at\": \"2026-08-06 15:01:39\", \"total_rounds\": 11, \"source_user_id\": 1}', '2026-08-06 11:31:39'),
(3387, 'game_179', 'game_started', '{\"status\": \"active\", \"game_id\": 179, \"started_at\": \"2026-08-06 15:03:57\", \"first_player\": {\"id\": 734, \"name\": \"RANGER\"}, \"source_user_id\": 1}', '2026-08-06 11:33:57'),
(3388, 'game_179', 'round_recorded', '{\"card\": {\"id\": 9, \"name\": \"هدیه\", \"emoji\": \"🎁\", \"rarity\": \"common\", \"multiplier\": 2}, \"score\": 20, \"winner\": {\"id\": 18, \"name\": \"امپراطور\", \"participant_id\": 735}, \"game_id\": 179, \"round_id\": 944, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-06 15:06:47\", \"round_number\": 1, \"source_user_id\": 1}', '2026-08-06 11:36:47'),
(3389, 'game_179', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 15, \"winner\": {\"id\": 1, \"name\": \"سنتری\", \"participant_id\": 736}, \"game_id\": 179, \"round_id\": 945, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-06 15:08:19\", \"round_number\": 2, \"source_user_id\": 1}', '2026-08-06 11:38:19'),
(3390, 'game_179', 'round_recorded', '{\"card\": {\"id\": 9, \"name\": \"هدیه\", \"emoji\": \"🎁\", \"rarity\": \"common\", \"multiplier\": 2}, \"score\": 20, \"winner\": {\"id\": 1, \"name\": \"سنتری\", \"participant_id\": 736}, \"game_id\": 179, \"round_id\": 946, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-06 15:10:41\", \"round_number\": 3, \"source_user_id\": 1}', '2026-08-06 11:40:41'),
(3391, 'game_179', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 105, \"winner\": {\"id\": 17, \"name\": \"RANGER\", \"participant_id\": 734}, \"game_id\": 179, \"round_id\": 947, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-06 15:14:10\", \"round_number\": 4, \"source_user_id\": 1}', '2026-08-06 11:44:11'),
(3392, 'game_179', 'round_recorded', '{\"card\": {\"id\": 12, \"name\": \"شافل\", \"emoji\": \"🌀\", \"rarity\": \"rare\", \"multiplier\": 10}, \"score\": 60, \"winner\": {\"id\": 18, \"name\": \"امپراطور\", \"participant_id\": 735}, \"game_id\": 179, \"round_id\": 948, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-06 15:18:58\", \"round_number\": 5, \"source_user_id\": 1}', '2026-08-06 11:48:58'),
(3393, 'game_179', 'round_recorded', '{\"card\": {\"id\": 7, \"name\": \"سپر\", \"emoji\": \"🛡️\", \"rarity\": \"rare\", \"multiplier\": 5}, \"score\": 35, \"winner\": {\"id\": 18, \"name\": \"امپراطور\", \"participant_id\": 735}, \"game_id\": 179, \"round_id\": 949, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-06 15:22:29\", \"round_number\": 6, \"source_user_id\": 1}', '2026-08-06 11:52:29'),
(3394, 'game_179', 'round_recorded', '{\"card\": {\"id\": 9, \"name\": \"هدیه\", \"emoji\": \"🎁\", \"rarity\": \"common\", \"multiplier\": 2}, \"score\": 20, \"winner\": {\"id\": 16, \"name\": \"KiNG\", \"participant_id\": 733}, \"game_id\": 179, \"round_id\": 950, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-06 15:28:55\", \"round_number\": 7, \"source_user_id\": 1}', '2026-08-06 11:58:55'),
(3395, 'game_179', 'round_recorded', '{\"card\": {\"id\": 12, \"name\": \"شافل\", \"emoji\": \"🌀\", \"rarity\": \"rare\", \"multiplier\": 10}, \"score\": 60, \"winner\": {\"id\": 18, \"name\": \"امپراطور\", \"participant_id\": 735}, \"game_id\": 179, \"round_id\": 951, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-06 15:31:56\", \"round_number\": 8, \"source_user_id\": 1}', '2026-08-06 12:01:56'),
(3396, 'game_179', 'round_recorded', '{\"card\": {\"id\": 9, \"name\": \"هدیه\", \"emoji\": \"🎁\", \"rarity\": \"common\", \"multiplier\": 2}, \"score\": 20, \"winner\": {\"id\": 16, \"name\": \"KiNG\", \"participant_id\": 733}, \"game_id\": 179, \"round_id\": 952, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-06 15:35:09\", \"round_number\": 9, \"source_user_id\": 1}', '2026-08-06 12:05:09'),
(3397, 'game_179', 'round_recorded', '{\"card\": {\"id\": 9, \"name\": \"هدیه\", \"emoji\": \"🎁\", \"rarity\": \"common\", \"multiplier\": 2}, \"score\": 20, \"winner\": {\"id\": 16, \"name\": \"KiNG\", \"participant_id\": 733}, \"game_id\": 179, \"round_id\": 953, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-06 15:40:16\", \"round_number\": 10, \"source_user_id\": 1}', '2026-08-06 12:10:16'),
(3398, 'game_179', 'round_recorded', '{\"card\": {\"id\": 9, \"name\": \"هدیه\", \"emoji\": \"🎁\", \"rarity\": \"common\", \"multiplier\": 2}, \"score\": 20, \"winner\": {\"id\": 1, \"name\": \"سنتری\", \"participant_id\": 736}, \"game_id\": 179, \"round_id\": 954, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-06 15:41:41\", \"round_number\": 11, \"source_user_id\": 1}', '2026-08-06 12:11:41'),
(3399, 'game_179', 'round_recorded', '{\"card\": {\"id\": 14, \"name\": \"جریمه دوتایی انتخابی\", \"emoji\": \"🎯\", \"rarity\": \"rare\", \"multiplier\": 5}, \"score\": 35, \"winner\": {\"id\": 16, \"name\": \"KiNG\", \"participant_id\": 733}, \"game_id\": 179, \"round_id\": 955, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-06 15:45:15\", \"round_number\": 12, \"source_user_id\": 1}', '2026-08-06 12:15:15'),
(3400, 'game_179', 'round_recorded', '{\"card\": {\"id\": 9, \"name\": \"هدیه\", \"emoji\": \"🎁\", \"rarity\": \"common\", \"multiplier\": 2}, \"score\": 20, \"winner\": {\"id\": 1, \"name\": \"سنتری\", \"participant_id\": 736}, \"game_id\": 179, \"round_id\": 956, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-06 15:48:01\", \"round_number\": 13, \"source_user_id\": 1}', '2026-08-06 12:18:01'),
(3401, 'game_179', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 105, \"winner\": {\"id\": 17, \"name\": \"RANGER\", \"participant_id\": 734}, \"game_id\": 179, \"round_id\": 957, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-06 15:54:15\", \"round_number\": 14, \"source_user_id\": 1}', '2026-08-06 12:24:16'),
(3402, 'game_179', 'round_recorded', '{\"card\": {\"id\": 9, \"name\": \"هدیه\", \"emoji\": \"🎁\", \"rarity\": \"common\", \"multiplier\": 2}, \"score\": 20, \"winner\": {\"id\": 1, \"name\": \"سنتری\", \"participant_id\": 736}, \"game_id\": 179, \"round_id\": 958, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-06 15:57:59\", \"round_number\": 15, \"source_user_id\": 1}', '2026-08-06 12:27:59'),
(3403, 'game_179', 'game_finished', '{\"status\": \"finished\", \"winner\": {\"id\": 1, \"name\": \"سنتری\", \"participant_id\": 736}, \"game_id\": 179, \"finished_at\": \"2026-08-06 15:58:11\", \"total_rounds\": 15, \"source_user_id\": 1}', '2026-08-06 12:28:11'),
(3404, 'game_180', 'game_started', '{\"status\": \"active\", \"game_id\": 180, \"started_at\": \"2026-08-06 15:59:55\", \"first_player\": {\"id\": 739, \"name\": \"امپراطور\"}, \"source_user_id\": 1}', '2026-08-06 12:29:55'),
(3405, 'game_180', 'round_recorded', '{\"card\": {\"id\": 5, \"name\": \"تغییر رنگ\", \"emoji\": \"🌈\", \"rarity\": \"common\", \"multiplier\": 2}, \"score\": 30, \"winner\": {\"id\": 1, \"name\": \"سنتری\", \"participant_id\": 740}, \"game_id\": 180, \"round_id\": 959, \"win_type\": null, \"team_name\": \"تیم 1\", \"recorded_at\": \"2026-08-06 16:02:34\", \"round_number\": 1, \"source_user_id\": 1}', '2026-08-06 12:32:34'),
(3406, 'game_180', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 110, \"winner\": {\"id\": 17, \"name\": \"RANGER\", \"participant_id\": 738}, \"game_id\": 180, \"round_id\": 960, \"win_type\": null, \"team_name\": \"تیم 2\", \"recorded_at\": \"2026-08-06 16:07:35\", \"round_number\": 2, \"source_user_id\": 1}', '2026-08-06 12:37:35'),
(3407, 'game_180', 'round_recorded', '{\"card\": {\"id\": 5, \"name\": \"تغییر رنگ\", \"emoji\": \"🌈\", \"rarity\": \"common\", \"multiplier\": 2}, \"score\": 30, \"winner\": {\"id\": 16, \"name\": \"KiNG\", \"participant_id\": 737}, \"game_id\": 180, \"round_id\": 961, \"win_type\": null, \"team_name\": \"تیم 2\", \"recorded_at\": \"2026-08-06 16:11:36\", \"round_number\": 3, \"source_user_id\": 1}', '2026-08-06 12:41:36'),
(3408, 'game_180', 'round_recorded', '{\"card\": {\"id\": 15, \"name\": \"کینگ\", \"emoji\": \"👑\", \"rarity\": \"legendary\", \"multiplier\": 20}, \"score\": 210, \"winner\": {\"id\": 16, \"name\": \"KiNG\", \"participant_id\": 737}, \"game_id\": 180, \"round_id\": 962, \"win_type\": null, \"team_name\": \"تیم 2\", \"recorded_at\": \"2026-08-06 16:14:13\", \"round_number\": 4, \"source_user_id\": 1}', '2026-08-06 12:44:13'),
(3409, 'game_180', 'round_recorded', '{\"card\": {\"id\": 12, \"name\": \"شافل\", \"emoji\": \"🌀\", \"rarity\": \"rare\", \"multiplier\": 10}, \"score\": 110, \"winner\": {\"id\": 16, \"name\": \"KiNG\", \"participant_id\": 737}, \"game_id\": 180, \"round_id\": 963, \"win_type\": null, \"team_name\": \"تیم 2\", \"recorded_at\": \"2026-08-06 16:17:37\", \"round_number\": 5, \"source_user_id\": 1}', '2026-08-06 12:47:37'),
(3410, 'game_180', 'round_recorded', '{\"card\": {\"id\": 15, \"name\": \"کینگ\", \"emoji\": \"👑\", \"rarity\": \"legendary\", \"multiplier\": 20}, \"score\": 210, \"winner\": {\"id\": 16, \"name\": \"KiNG\", \"participant_id\": 737}, \"game_id\": 180, \"round_id\": 964, \"win_type\": null, \"team_name\": \"تیم 2\", \"recorded_at\": \"2026-08-06 16:19:47\", \"round_number\": 6, \"source_user_id\": 1}', '2026-08-06 12:49:47'),
(3411, 'game_180', 'game_finished', '{\"status\": \"finished\", \"winner\": {\"id\": 16, \"name\": \"تیم تیم 2\", \"participant_id\": 737}, \"game_id\": 180, \"finished_at\": \"2026-08-06 16:19:56\", \"total_rounds\": 6, \"source_user_id\": 1}', '2026-08-06 12:49:56'),
(3412, 'game_181', 'game_started', '{\"status\": \"active\", \"game_id\": 181, \"started_at\": \"2026-08-06 16:20:50\", \"first_player\": {\"id\": 742, \"name\": \"RANGER\"}, \"source_user_id\": 1}', '2026-08-06 12:50:50'),
(3413, 'game_181', 'round_recorded', '{\"card\": {\"id\": 6, \"name\": \"جریمه ۴ تایی\", \"emoji\": \"+4\", \"rarity\": \"common\", \"multiplier\": 3}, \"score\": 40, \"winner\": {\"id\": 1, \"name\": \"سنتری\", \"participant_id\": 744}, \"game_id\": 181, \"round_id\": 965, \"win_type\": null, \"team_name\": \"تیم 1\", \"recorded_at\": \"2026-08-06 16:22:50\", \"round_number\": 1, \"source_user_id\": 1}', '2026-08-06 12:52:50'),
(3414, 'game_181', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 20, \"winner\": {\"id\": 1, \"name\": \"سنتری\", \"participant_id\": 744}, \"game_id\": 181, \"round_id\": 966, \"win_type\": null, \"team_name\": \"تیم 1\", \"recorded_at\": \"2026-08-06 16:24:45\", \"round_number\": 2, \"source_user_id\": 1}', '2026-08-06 12:54:45'),
(3415, 'game_181', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 110, \"winner\": {\"id\": 17, \"name\": \"RANGER\", \"participant_id\": 742}, \"game_id\": 181, \"round_id\": 967, \"win_type\": null, \"team_name\": \"تیم 2\", \"recorded_at\": \"2026-08-06 16:27:40\", \"round_number\": 3, \"source_user_id\": 1}', '2026-08-06 12:57:40'),
(3416, 'game_181', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 110, \"winner\": {\"id\": 17, \"name\": \"RANGER\", \"participant_id\": 742}, \"game_id\": 181, \"round_id\": 968, \"win_type\": null, \"team_name\": \"تیم 2\", \"recorded_at\": \"2026-08-06 16:33:14\", \"round_number\": 4, \"source_user_id\": 1}', '2026-08-06 13:03:15'),
(3417, 'game_181', 'round_recorded', '{\"card\": {\"id\": 9, \"name\": \"هدیه\", \"emoji\": \"🎁\", \"rarity\": \"common\", \"multiplier\": 2}, \"score\": 30, \"winner\": {\"id\": 16, \"name\": \"KiNG\", \"participant_id\": 741}, \"game_id\": 181, \"round_id\": 969, \"win_type\": null, \"team_name\": \"تیم 1\", \"recorded_at\": \"2026-08-06 16:36:47\", \"round_number\": 5, \"source_user_id\": 1}', '2026-08-06 13:06:47'),
(3418, 'game_181', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 20, \"winner\": {\"id\": 18, \"name\": \"امپراطور\", \"participant_id\": 743}, \"game_id\": 181, \"round_id\": 970, \"win_type\": null, \"team_name\": \"تیم 2\", \"recorded_at\": \"2026-08-06 16:40:24\", \"round_number\": 6, \"source_user_id\": 1}', '2026-08-06 13:10:24'),
(3419, 'game_181', 'round_recorded', '{\"card\": {\"id\": 3, \"name\": \"دوربرگردان\", \"emoji\": \"🔄\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 20, \"winner\": {\"id\": 16, \"name\": \"KiNG\", \"participant_id\": 741}, \"game_id\": 181, \"round_id\": 971, \"win_type\": null, \"team_name\": \"تیم 1\", \"recorded_at\": \"2026-08-06 16:44:03\", \"round_number\": 7, \"source_user_id\": 1}', '2026-08-06 13:14:03'),
(3420, 'game_181', 'round_recorded', '{\"card\": {\"id\": 6, \"name\": \"جریمه ۴ تایی\", \"emoji\": \"+4\", \"rarity\": \"common\", \"multiplier\": 3}, \"score\": 40, \"winner\": {\"id\": 18, \"name\": \"امپراطور\", \"participant_id\": 743}, \"game_id\": 181, \"round_id\": 972, \"win_type\": null, \"team_name\": \"تیم 2\", \"recorded_at\": \"2026-08-06 16:46:09\", \"round_number\": 8, \"source_user_id\": 1}', '2026-08-06 13:16:09'),
(3421, 'game_181', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 20, \"winner\": {\"id\": 1, \"name\": \"سنتری\", \"participant_id\": 744}, \"game_id\": 181, \"round_id\": 973, \"win_type\": null, \"team_name\": \"تیم 1\", \"recorded_at\": \"2026-08-06 16:49:59\", \"round_number\": 9, \"source_user_id\": 1}', '2026-08-06 13:19:59'),
(3422, 'game_181', 'game_finished', '{\"status\": \"finished\", \"winner\": {\"id\": 16, \"name\": \"تیم تیم 1\", \"participant_id\": 741}, \"game_id\": 181, \"finished_at\": \"2026-08-06 16:50:08\", \"total_rounds\": 9, \"source_user_id\": 1}', '2026-08-06 13:20:08'),
(3423, 'game_182', 'game_started', '{\"status\": \"active\", \"game_id\": 182, \"started_at\": \"2026-08-07 14:35:57\", \"first_player\": {\"id\": 746, \"name\": \"RANGER\"}, \"source_user_id\": 17}', '2026-08-07 11:05:57'),
(3424, 'game_182', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 5, \"winner\": {\"id\": 21, \"name\": \"کماندو\", \"participant_id\": 748}, \"game_id\": 182, \"round_id\": 974, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-07 14:39:40\", \"round_number\": 1, \"source_user_id\": 17}', '2026-08-07 11:09:41'),
(3425, 'game_182', 'round_recorded', '{\"card\": null, \"score\": 5, \"winner\": {\"id\": 21, \"name\": \"کماندو\", \"participant_id\": 748}, \"game_id\": 182, \"round_id\": 975, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-07 14:40:55\", \"round_number\": 2, \"source_user_id\": 17}', '2026-08-07 11:10:55'),
(3426, 'game_182', 'round_undone', '{\"game_id\": 182, \"undone_at\": \"2026-08-07 14:41:06\", \"undone_round\": 2, \"source_user_id\": 17}', '2026-08-07 11:11:06'),
(3427, 'game_182', 'round_recorded', '{\"card\": {\"id\": 6, \"name\": \"جریمه ۴ تایی\", \"emoji\": \"+4\", \"rarity\": \"common\", \"multiplier\": 3}, \"score\": 15, \"winner\": {\"id\": 21, \"name\": \"کماندو\", \"participant_id\": 748}, \"game_id\": 182, \"round_id\": 976, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-07 14:41:15\", \"round_number\": 2, \"source_user_id\": 17}', '2026-08-07 11:11:15'),
(3428, 'game_182', 'round_recorded', '{\"card\": {\"id\": 13, \"name\": \"قفل کردن\", \"emoji\": \"🔒\", \"rarity\": \"rare\", \"multiplier\": 10}, \"score\": 60, \"winner\": {\"id\": 16, \"name\": \"KiNG\", \"participant_id\": 745}, \"game_id\": 182, \"round_id\": 977, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-07 14:43:43\", \"round_number\": 3, \"source_user_id\": 17}', '2026-08-07 11:13:43'),
(3429, 'game_182', 'game_status_changed', '{\"status\": \"paused\", \"game_id\": 182, \"source_user_id\": 17}', '2026-08-07 11:20:48'),
(3430, 'game_182', 'game_status_changed', '{\"status\": \"active\", \"game_id\": 182, \"source_user_id\": 17}', '2026-08-07 11:21:09'),
(3431, 'game_182', 'game_target_changed', '{\"game_id\": 182, \"max_wins\": 2, \"changed_at\": \"2026-08-07 14:52:26\", \"min_target\": 3, \"new_target\": 3, \"old_target\": 5, \"source_user_id\": 17}', '2026-08-07 11:22:26'),
(3432, 'game_182', 'round_recorded', '{\"card\": {\"id\": 15, \"name\": \"کینگ\", \"emoji\": \"👑\", \"rarity\": \"legendary\", \"multiplier\": 20}, \"score\": 100, \"winner\": {\"id\": 21, \"name\": \"کماندو\", \"participant_id\": 748}, \"game_id\": 182, \"round_id\": 978, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-07 14:52:39\", \"round_number\": 4, \"source_user_id\": 17}', '2026-08-07 11:22:39'),
(3433, 'game_182', 'game_finished', '{\"status\": \"finished\", \"winner\": {\"id\": 21, \"name\": \"کماندو\", \"members\": null, \"team_id\": null, \"participant_id\": 748}, \"game_id\": 182, \"finished_at\": \"2026-08-07 14:52:44\", \"is_team_mode\": false, \"total_rounds\": 4, \"source_user_id\": 17}', '2026-08-07 11:22:44'),
(3434, 'game_183', 'game_started', '{\"status\": \"active\", \"game_id\": 183, \"started_at\": \"2026-08-07 14:55:10\", \"first_player\": {\"id\": 753, \"name\": \"کماندو\"}, \"source_user_id\": 1}', '2026-08-07 11:25:10'),
(3435, 'game_183', 'game_target_changed', '{\"game_id\": 183, \"max_wins\": 0, \"changed_at\": \"2026-08-07 14:59:42\", \"min_target\": 3, \"new_target\": 3, \"old_target\": 5, \"source_user_id\": 1}', '2026-08-07 11:29:42'),
(3436, 'game_183', 'round_recorded', '{\"card\": {\"id\": 7, \"name\": \"سپر\", \"emoji\": \"🛡️\", \"rarity\": \"rare\", \"multiplier\": 5}, \"score\": 35, \"winner\": {\"id\": 16, \"name\": \"KiNG\", \"participant_id\": 749}, \"game_id\": 183, \"round_id\": 979, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-07 15:03:21\", \"round_number\": 1, \"source_user_id\": 1}', '2026-08-07 11:33:21'),
(3437, 'game_183', 'round_recorded', '{\"card\": {\"id\": 9, \"name\": \"هدیه\", \"emoji\": \"🎁\", \"rarity\": \"common\", \"multiplier\": 2}, \"score\": 110, \"winner\": {\"id\": 1, \"name\": \"سنتری\", \"participant_id\": 752}, \"game_id\": 183, \"round_id\": 980, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-07 15:06:00\", \"round_number\": 2, \"source_user_id\": 1}', '2026-08-07 11:36:00'),
(3438, 'game_183', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 105, \"winner\": {\"id\": 1, \"name\": \"سنتری\", \"participant_id\": 752}, \"game_id\": 183, \"round_id\": 981, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-07 15:11:17\", \"round_number\": 3, \"source_user_id\": 1}', '2026-08-07 11:41:17'),
(3439, 'game_183', 'round_recorded', '{\"card\": {\"id\": 9, \"name\": \"هدیه\", \"emoji\": \"🎁\", \"rarity\": \"common\", \"multiplier\": 2}, \"score\": 10, \"winner\": {\"id\": 21, \"name\": \"کماندو\", \"participant_id\": 753}, \"game_id\": 183, \"round_id\": 982, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-07 15:16:05\", \"round_number\": 4, \"source_user_id\": 1}', '2026-08-07 11:46:05'),
(3440, 'game_183', 'round_recorded', '{\"card\": {\"id\": 13, \"name\": \"قفل کردن\", \"emoji\": \"🔒\", \"rarity\": \"rare\", \"multiplier\": 10}, \"score\": 150, \"winner\": {\"id\": 1, \"name\": \"سنتری\", \"participant_id\": 752}, \"game_id\": 183, \"round_id\": 983, \"win_type\": null, \"team_name\": null, \"recorded_at\": \"2026-08-07 15:21:28\", \"round_number\": 5, \"source_user_id\": 1}', '2026-08-07 11:51:28'),
(3441, 'game_183', 'game_finished', '{\"status\": \"finished\", \"winner\": {\"id\": 1, \"name\": \"سنتری\", \"members\": null, \"team_id\": null, \"participant_id\": 752}, \"game_id\": 183, \"finished_at\": \"2026-08-07 15:21:35\", \"is_team_mode\": false, \"total_rounds\": 5, \"source_user_id\": 1}', '2026-08-07 11:51:35'),
(3442, 'game_184', 'game_started', '{\"status\": \"active\", \"game_id\": 184, \"started_at\": \"2026-08-07 15:23:46\", \"first_player\": {\"id\": 755, \"name\": \"RANGER\"}, \"source_user_id\": 1}', '2026-08-07 11:53:46'),
(3443, 'game_184', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 20, \"winner\": {\"id\": 18, \"name\": \"امپراطور\", \"participant_id\": 756}, \"game_id\": 184, \"round_id\": 984, \"win_type\": null, \"team_name\": \"تیم 2\", \"recorded_at\": \"2026-08-07 15:28:12\", \"round_number\": 1, \"source_user_id\": 1}', '2026-08-07 11:58:12'),
(3444, 'game_184', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 20, \"winner\": {\"id\": 16, \"name\": \"KiNG\", \"participant_id\": 754}, \"game_id\": 184, \"round_id\": 985, \"win_type\": null, \"team_name\": \"تیم 1\", \"recorded_at\": \"2026-08-07 15:30:36\", \"round_number\": 2, \"source_user_id\": 1}', '2026-08-07 12:00:36'),
(3445, 'game_184', 'round_recorded', '{\"card\": {\"id\": 15, \"name\": \"کینگ\", \"emoji\": \"👑\", \"rarity\": \"legendary\", \"multiplier\": 20}, \"score\": 210, \"winner\": {\"id\": 16, \"name\": \"KiNG\", \"participant_id\": 754}, \"game_id\": 184, \"round_id\": 986, \"win_type\": null, \"team_name\": \"تیم 1\", \"recorded_at\": \"2026-08-07 15:35:40\", \"round_number\": 3, \"source_user_id\": 1}', '2026-08-07 12:05:40'),
(3446, 'game_184', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 20, \"winner\": {\"id\": 16, \"name\": \"KiNG\", \"participant_id\": 754}, \"game_id\": 184, \"round_id\": 987, \"win_type\": null, \"team_name\": \"تیم 1\", \"recorded_at\": \"2026-08-07 15:41:30\", \"round_number\": 4, \"source_user_id\": 1}', '2026-08-07 12:11:30'),
(3447, 'game_184', 'round_recorded', '{\"card\": {\"id\": 7, \"name\": \"سپر\", \"emoji\": \"🛡️\", \"rarity\": \"rare\", \"multiplier\": 5}, \"score\": 150, \"winner\": {\"id\": 17, \"name\": \"RANGER\", \"participant_id\": 755}, \"game_id\": 184, \"round_id\": 988, \"win_type\": null, \"team_name\": \"تیم 1\", \"recorded_at\": \"2026-08-07 15:46:29\", \"round_number\": 5, \"source_user_id\": 1}', '2026-08-07 12:16:29'),
(3448, 'game_184', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 20, \"winner\": {\"id\": 16, \"name\": \"KiNG\", \"participant_id\": 754}, \"game_id\": 184, \"round_id\": 989, \"win_type\": null, \"team_name\": \"تیم 1\", \"recorded_at\": \"2026-08-07 15:50:07\", \"round_number\": 6, \"source_user_id\": 1}', '2026-08-07 12:20:07'),
(3449, 'game_184', 'game_finished', '{\"status\": \"finished\", \"winner\": {\"id\": null, \"name\": \"تیم تیم 1\", \"members\": [{\"id\": 16, \"name\": \"KiNG\", \"user_id\": 16, \"participant_id\": 754}, {\"id\": 17, \"name\": \"RANGER\", \"user_id\": 17, \"participant_id\": 755}], \"team_id\": 222, \"participant_id\": null}, \"game_id\": 184, \"finished_at\": \"2026-08-07 15:50:39\", \"is_team_mode\": true, \"total_rounds\": 6, \"source_user_id\": 1}', '2026-08-07 12:20:39'),
(3450, 'game_185', 'game_started', '{\"status\": \"active\", \"game_id\": 185, \"started_at\": \"2026-08-07 15:52:23\", \"first_player\": {\"id\": 758, \"name\": \"KiNG\"}, \"source_user_id\": 1}', '2026-08-07 12:22:23'),
(3451, 'game_185', 'round_recorded', '{\"card\": {\"id\": 9, \"name\": \"هدیه\", \"emoji\": \"🎁\", \"rarity\": \"common\", \"multiplier\": 2}, \"score\": 120, \"winner\": {\"id\": 17, \"name\": \"RANGER\", \"participant_id\": 759}, \"game_id\": 185, \"round_id\": 990, \"win_type\": null, \"team_name\": \"تیم 1\", \"recorded_at\": \"2026-08-07 15:57:16\", \"round_number\": 1, \"source_user_id\": 1}', '2026-08-07 12:27:16'),
(3452, 'game_185', 'round_recorded', '{\"card\": {\"id\": 5, \"name\": \"تغییر رنگ\", \"emoji\": \"🌈\", \"rarity\": \"common\", \"multiplier\": 2}, \"score\": 120, \"winner\": {\"id\": 1, \"name\": \"سنتری\", \"participant_id\": 761}, \"game_id\": 185, \"round_id\": 991, \"win_type\": null, \"team_name\": \"تیم 2\", \"recorded_at\": \"2026-08-07 16:02:17\", \"round_number\": 2, \"source_user_id\": 1}', '2026-08-07 12:32:17'),
(3453, 'game_185', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 20, \"winner\": {\"id\": 16, \"name\": \"KiNG\", \"participant_id\": 758}, \"game_id\": 185, \"round_id\": 992, \"win_type\": null, \"team_name\": \"تیم 2\", \"recorded_at\": \"2026-08-07 16:04:50\", \"round_number\": 3, \"source_user_id\": 1}', '2026-08-07 12:34:50'),
(3454, 'game_185', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 20, \"winner\": {\"id\": 18, \"name\": \"امپراطور\", \"participant_id\": 760}, \"game_id\": 185, \"round_id\": 993, \"win_type\": null, \"team_name\": \"تیم 1\", \"recorded_at\": \"2026-08-07 16:07:12\", \"round_number\": 4, \"source_user_id\": 1}', '2026-08-07 12:37:12'),
(3455, 'game_185', 'round_recorded', '{\"card\": {\"id\": 14, \"name\": \"جریمه دوتایی انتخابی\", \"emoji\": \"🎯\", \"rarity\": \"rare\", \"multiplier\": 5}, \"score\": 60, \"winner\": {\"id\": 16, \"name\": \"KiNG\", \"participant_id\": 758}, \"game_id\": 185, \"round_id\": 994, \"win_type\": null, \"team_name\": \"تیم 2\", \"recorded_at\": \"2026-08-07 16:09:34\", \"round_number\": 5, \"source_user_id\": 1}', '2026-08-07 12:39:34'),
(3456, 'game_185', 'round_recorded', '{\"card\": {\"id\": 9, \"name\": \"هدیه\", \"emoji\": \"🎁\", \"rarity\": \"common\", \"multiplier\": 2}, \"score\": 120, \"winner\": {\"id\": 17, \"name\": \"RANGER\", \"participant_id\": 759}, \"game_id\": 185, \"round_id\": 995, \"win_type\": null, \"team_name\": \"تیم 1\", \"recorded_at\": \"2026-08-07 16:12:24\", \"round_number\": 6, \"source_user_id\": 1}', '2026-08-07 12:42:24'),
(3457, 'game_185', 'round_recorded', '{\"card\": {\"id\": 9, \"name\": \"هدیه\", \"emoji\": \"🎁\", \"rarity\": \"common\", \"multiplier\": 2}, \"score\": 120, \"winner\": {\"id\": 1, \"name\": \"سنتری\", \"participant_id\": 761}, \"game_id\": 185, \"round_id\": 996, \"win_type\": null, \"team_name\": \"تیم 2\", \"recorded_at\": \"2026-08-07 16:15:02\", \"round_number\": 7, \"source_user_id\": 1}', '2026-08-07 12:45:02'),
(3458, 'game_185', 'round_recorded', '{\"card\": {\"id\": 1, \"name\": \"کارت عددی\", \"emoji\": \"🔢\", \"rarity\": \"common\", \"multiplier\": 1}, \"score\": 110, \"winner\": {\"id\": 17, \"name\": \"RANGER\", \"participant_id\": 759}, \"game_id\": 185, \"round_id\": 997, \"win_type\": null, \"team_name\": \"تیم 1\", \"recorded_at\": \"2026-08-07 16:18:20\", \"round_number\": 8, \"source_user_id\": 1}', '2026-08-07 12:48:20'),
(3459, 'game_185', 'round_recorded', '{\"card\": {\"id\": 12, \"name\": \"شافل\", \"emoji\": \"🌀\", \"rarity\": \"rare\", \"multiplier\": 10}, \"score\": 110, \"winner\": {\"id\": 16, \"name\": \"KiNG\", \"participant_id\": 758}, \"game_id\": 185, \"round_id\": 998, \"win_type\": null, \"team_name\": \"تیم 2\", \"recorded_at\": \"2026-08-07 16:25:20\", \"round_number\": 9, \"source_user_id\": 1}', '2026-08-07 12:55:20'),
(3460, 'game_185', 'game_finished', '{\"status\": \"finished\", \"winner\": {\"id\": null, \"name\": \"تیم تیم 2\", \"members\": [{\"id\": 16, \"name\": \"KiNG\", \"user_id\": 16, \"participant_id\": 758}, {\"id\": 1, \"name\": \"سنتری\", \"user_id\": 1, \"participant_id\": 761}], \"team_id\": 225, \"participant_id\": null}, \"game_id\": 185, \"finished_at\": \"2026-08-07 16:25:27\", \"is_team_mode\": true, \"total_rounds\": 9, \"source_user_id\": 1}', '2026-08-07 12:55:27');

-- --------------------------------------------------------

--
-- Table structure for table `suspicious_games`
--

CREATE TABLE `suspicious_games` (
  `id` int UNSIGNED NOT NULL,
  `game_id` int UNSIGNED NOT NULL,
  `user_id` int UNSIGNED DEFAULT NULL,
  `cheat_types` json NOT NULL COMMENT 'انواع تقلب شناسایی شده',
  `risk_level` enum('low','medium','high','critical') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `details` json DEFAULT NULL COMMENT 'جزئیات بیشتر',
  `is_reviewed` tinyint(1) DEFAULT '0',
  `reviewed_by` int UNSIGNED DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `suspicious_games`
--

INSERT INTO `suspicious_games` (`id`, `game_id`, `user_id`, `cheat_types`, `risk_level`, `details`, `is_reviewed`, `reviewed_by`, `reviewed_at`, `created_at`) VALUES
(54, 151, 1, '[\"low_target_wins\"]', 'low', '{\"low_target\": {\"threshold\": 5, \"target_wins\": 3, \"is_suspicious\": true}}', 0, NULL, NULL, '2026-07-23 14:13:45'),
(55, 152, 1, '[\"low_target_wins\"]', 'low', '{\"low_target\": {\"threshold\": 5, \"target_wins\": 3, \"is_suspicious\": true}}', 0, NULL, NULL, '2026-07-24 13:22:28'),
(56, 153, 18, '[\"low_target_wins\"]', 'low', '{\"low_target\": {\"threshold\": 5, \"target_wins\": 3, \"is_suspicious\": true}}', 0, NULL, NULL, '2026-07-24 13:54:30'),
(57, 154, 1, '[\"low_target_wins\"]', 'low', '{\"low_target\": {\"threshold\": 5, \"target_wins\": 3, \"is_suspicious\": true}}', 0, NULL, NULL, '2026-07-24 14:34:03'),
(58, 157, 1, '[\"low_target_wins\"]', 'low', '{\"low_target\": {\"threshold\": 5, \"target_wins\": 3, \"is_suspicious\": true}}', 0, NULL, NULL, '2026-07-25 13:19:02'),
(59, 158, 1, '[\"low_target_wins\"]', 'low', '{\"low_target\": {\"threshold\": 5, \"target_wins\": 3, \"is_suspicious\": true}}', 0, NULL, NULL, '2026-07-25 13:50:48'),
(60, 161, 1, '[\"low_target_wins\"]', 'low', '{\"low_target\": {\"threshold\": 5, \"target_wins\": 3, \"is_suspicious\": true}}', 0, NULL, NULL, '2026-07-25 15:32:18'),
(61, 163, 18, '[\"low_target_wins\"]', 'low', '{\"low_target\": {\"threshold\": 5, \"target_wins\": 3, \"is_suspicious\": true}}', 0, NULL, NULL, '2026-07-28 15:09:57'),
(62, 165, 18, '[\"low_target_wins\"]', 'low', '{\"low_target\": {\"threshold\": 5, \"target_wins\": 3, \"is_suspicious\": true}}', 0, NULL, NULL, '2026-07-31 12:03:11'),
(63, 166, 18, '[\"low_target_wins\"]', 'low', '{\"low_target\": {\"threshold\": 5, \"target_wins\": 3, \"is_suspicious\": true}}', 0, NULL, NULL, '2026-08-01 15:11:45'),
(64, 167, 18, '[\"low_target_wins\"]', 'low', '{\"low_target\": {\"threshold\": 5, \"target_wins\": 3, \"is_suspicious\": true}}', 0, NULL, NULL, '2026-08-01 15:28:09');

-- --------------------------------------------------------

--
-- Table structure for table `system_settings`
--

CREATE TABLE `system_settings` (
  `id` int UNSIGNED NOT NULL,
  `setting_key` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `setting_value` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `setting_type` enum('string','integer','boolean','json') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'string',
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `category` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'general',
  `updated_by` int UNSIGNED DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `system_settings`
--

INSERT INTO `system_settings` (`id`, `setting_key`, `setting_value`, `setting_type`, `description`, `category`, `updated_by`, `updated_at`) VALUES
(1, 'site_name', 'UNO Tracker', 'string', 'نام سایت', 'general', 1, '2026-07-07 17:36:29'),
(2, 'site_description', 'سیستم ثبت و آنالیز بازی‌های UNO', 'string', 'توضیحات سایت', 'general', NULL, '2026-07-17 05:04:39'),
(3, 'maintenance_mode', '0', 'integer', 'حالت تعمیر و نگهداری', 'general', NULL, '2026-07-14 16:21:44'),
(4, 'registration_enabled', '1', 'integer', 'فعال بودن ثبت‌نام', 'general', NULL, '2026-07-17 05:04:19'),
(5, 'max_guest_players', '0', 'integer', 'حداکثر تعداد بازیکن مهمان در هر بازی', 'game', 1, '2026-08-05 16:30:41'),
(6, 'default_target_wins', '10', 'integer', 'هدف برد پیش‌فرض', 'game', 1, '2026-08-05 16:30:41'),
(7, 'min_players_solo', '3', 'integer', 'حداقل بازیکن برای بازی انفرادی', 'game', 1, '2026-08-05 16:30:41'),
(8, 'min_players_team', '4', 'integer', 'حداقل بازیکن برای بازی تیمی', 'game', 1, '2026-08-05 16:30:41'),
(9, 'players_per_team', '2', 'integer', 'تعداد بازیکن در هر تیم', 'game', 1, '2026-08-05 16:30:41'),
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
(64, 'max_players_per_game', '10', 'integer', 'حداکثر بازیکن در یک بازی', 'game', 1, '2026-08-05 16:30:41'),
(65, 'xp_achievement_unlock', '50', 'integer', 'XP برای کسب نشان', 'gamification', NULL, '2026-07-08 12:28:17'),
(66, 'xp_challenge_complete', '30', 'integer', 'XP برای تکمیل ماموریت', 'gamification', NULL, '2026-07-08 12:28:17'),
(67, 'avatar_max_width', '500', 'integer', 'حداکثر عرض آواتار (پیکسل)', 'upload', NULL, '2026-07-08 12:28:17'),
(68, 'avatar_max_height', '500', 'integer', 'حداکثر ارتفاع آواتار (پیکسل)', 'upload', NULL, '2026-07-08 12:28:17'),
(69, 'default_theme', 'light', 'string', 'تم پیش‌فرض (light/dark)', 'display', NULL, '2026-07-08 12:28:17'),
(70, 'default_language', 'fa', 'string', 'زبان پیش‌فرض', 'display', NULL, '2026-07-08 12:28:17'),
(71, 'items_per_page', '20', 'integer', 'تعداد آیتم در هر صفحه', 'display', NULL, '2026-07-08 12:28:17'),
(72, 'enable_animations', '1', 'boolean', 'فعال بودن انیمیشن‌ها', 'display', NULL, '2026-07-08 12:28:17'),
(73, 'enable_notifications', '1', 'integer', 'فعال بودن اعلان‌های سیستم', 'notification', 1, '2026-08-04 13:37:11'),
(74, 'notification_sound_enabled', '1', 'integer', 'فعال بودن صدای اعلان', 'notification', 1, '2026-08-04 13:37:11'),
(75, 'default_notification_duration', '3000', 'integer', 'مدت نمایش اعلان پیش‌فرض (میلی‌ثانیه)', 'notification', 1, '2026-08-04 13:37:11'),
(76, 'notification_position', 'top-end', 'string', 'موقعیت اعلان‌ها', 'notification', 1, '2026-08-04 13:37:11'),
(77, 'scoring_base_score', '5', 'integer', 'امتیاز پایه هر برد', 'scoring', 1, '2026-08-04 18:37:18'),
(78, 'scoring_xp_multiplier', '2.0', '', 'ضریب تبدیل امتیاز به XP', 'scoring', 1, '2026-08-04 18:37:18'),
(79, 'scoring_win_bonus', '15', 'integer', 'XP اضافی برای برد', 'scoring', 1, '2026-08-04 18:37:18'),
(80, 'scoring_game_bonus', '5', 'integer', 'XP اضافی برای شرکت در بازی', 'scoring', 1, '2026-08-04 18:37:18'),
(81, 'scoring_team_multiplier', '2', 'integer', 'ضریب امتیاز بازی تیمی', 'scoring', 1, '2026-08-04 18:37:18'),
(82, 'scoring_winner_bonus', '50', 'integer', 'XP اضافی برنده بازی', 'scoring', 1, '2026-08-04 18:37:18'),
(83, 'scoring_min_target_wins', '3', 'integer', 'حداقل هدف برد مجاز', 'scoring', 1, '2026-08-04 18:37:18'),
(84, 'anticheat_enabled', '1', 'integer', 'فعال بودن سیستم ضدتقلب', 'anticheat', 1, '2026-08-05 16:32:23'),
(85, 'anticheat_min_round_duration', '60', 'integer', 'حداقل زمان هر دور (ثانیه)', 'anticheat', 1, '2026-08-05 16:32:23'),
(86, 'anticheat_min_players', '3', 'integer', 'حداقل تعداد بازیکنان', 'anticheat', 1, '2026-08-05 16:32:23'),
(87, 'anticheat_max_guests', '0', 'integer', 'حداکثر تعداد بازیکنان مهمان', 'anticheat', 1, '2026-08-05 16:32:23'),
(88, 'anticheat_max_guest_ratio', '1.0', '', 'حداکثر نسبت مهمان به عضو', 'anticheat', 1, '2026-08-05 16:32:23'),
(89, 'anticheat_min_members', '3', 'integer', 'حداقل تعداد بازیکنان عضو', 'anticheat', 1, '2026-08-05 16:32:23'),
(90, 'anticheat_max_win_percentage', '100', 'integer', 'حداکثر درصد برد یک بازیکن', 'anticheat', 1, '2026-08-05 16:32:23'),
(91, 'anticheat_max_games_per_hour', '2', 'integer', 'حداکثر تعداد بازی در ساعت', 'anticheat', 1, '2026-08-05 16:32:23'),
(92, 'anticheat_min_target_wins_threshold', '3', 'integer', 'آستانه هدف برد کم', 'anticheat', 1, '2026-08-05 16:32:23'),
(93, 'anticheat_max_low_target_games', '2', 'integer', 'حداکثر بازی‌های با هدف کم در روز', 'anticheat', 1, '2026-08-05 16:32:23'),
(94, 'anticheat_new_account_hours', '24', 'integer', 'ساعت‌های محدودیت حساب تازه', 'anticheat', 1, '2026-08-05 16:32:23'),
(95, 'anticheat_max_accounts_per_ip', '2', 'integer', 'حداکثر اکانت برای هر IP', 'anticheat', 1, '2026-08-05 16:32:23'),
(96, 'anticheat_max_games_created_per_day', '3', 'integer', 'حداکثر بازی‌های ایجاد شده در روز', 'anticheat', 1, '2026-08-05 16:32:23'),
(97, 'anticheat_max_solo_games_per_day', '3', 'integer', 'حداکثر بازی‌های انفرادی در روز', 'anticheat', 1, '2026-08-05 16:32:23'),
(98, 'anticheat_max_friendly_games_per_day', '3', 'integer', 'حداکثر بازی‌های دوستانه در روز', 'anticheat', 1, '2026-08-05 16:32:23'),
(99, 'anticheat_collusion_min_games', '3', 'integer', 'حداقل تعداد بازی در ۱ ساعت برای تشخیص تبانی', 'anticheat', 1, '2026-08-05 16:32:23'),
(100, 'anticheat_collusion_max_opponents', '2', 'integer', 'حداکثر تعداد حریف یکتا (کمتر = مشکوک‌تر)', 'anticheat', 1, '2026-08-05 16:32:23'),
(101, 'first_player_selection', 'random', 'string', 'نحوه انتخاب بازیکن شروع‌کننده بازی (random=تصادفی، by_score=بر اساس امتیاز، by_xp=بر اساس XP)', 'game', 1, '2026-08-05 16:30:41'),
(119, 'auth_method', 'sms', 'string', 'روش احراز هویت (password=رمز عبور، sms=پیامک)', 'security', NULL, '2026-07-21 14:54:17'),
(120, 'sms_enabled', '1', 'integer', 'فعال‌سازی سیستم پیامک', 'security', NULL, '2026-07-21 14:56:36'),
(121, 'sms_otp_length', '6', 'integer', 'طول کد تایید پیامکی', 'security', NULL, '2026-07-21 11:38:47'),
(122, 'sms_otp_expiry', '5', 'integer', 'زمان انقضای کد (دقیقه)', 'security', NULL, '2026-07-21 11:38:47'),
(123, 'sms_daily_limit', '10', 'integer', 'حداکثر پیامک روزانه برای هر شماره', 'security', NULL, '2026-07-21 11:38:47'),
(124, 'sms_otp_attempt_limit', '5', 'integer', 'حداکثر تلاش برای وارد کردن کد', 'security', NULL, '2026-07-21 11:38:47'),
(125, 'sse_sound_settings', '{\"game_started\":{\"enabled\":true,\"sound\":\"game-start.mp3\"},\"round_recorded\":{\"enabled\":true,\"sound\":\"round-recorded.mp3\"},\"round_winner\":{\"enabled\":true,\"sound\":\"round-win.mp3\"},\"round_loser\":{\"enabled\":true,\"sound\":\"round-lose-2.mp3\"},\"round_undone\":{\"enabled\":true,\"sound\":\"default.mp3\"},\"game_finished\":{\"enabled\":true,\"sound\":\"default.mp3\"},\"game_winner\":{\"enabled\":true,\"sound\":\"game-win.mp3\"},\"game_loser\":{\"enabled\":true,\"sound\":\"round-lose-3.mp3\"},\"score_updated\":{\"enabled\":false,\"sound\":\"default.mp3\"},\"notification\":{\"enabled\":true,\"sound\":\"default.mp3\"},\"system_message\":{\"enabled\":true,\"sound\":\"default.mp3\"},\"game_status_changed\":{\"paused\":{\"enabled\":true,\"sound\":\"game-pause.mp3\"},\"resumed\":{\"enabled\":true,\"sound\":\"game-resume.mp3\"}}}', 'json', 'تنظیمات صدای رویدادهای SSE (هر رویداد دارای enabled و sound)', 'notification', 1, '2026-08-04 13:37:11'),
(196, 'sse_fallback_refresh_seconds', '10', 'integer', 'زمان انتظار (ثانیه) قبل از refresh خودکار در صورت عدم دریافت رویداد SSE. مقدار 0 = غیرفعال', 'notification', NULL, '2026-08-07 16:22:46');

-- --------------------------------------------------------

--
-- Table structure for table `teammate_history`
--

CREATE TABLE `teammate_history` (
  `id` int UNSIGNED NOT NULL,
  `user_id_1` int UNSIGNED NOT NULL,
  `user_id_2` int UNSIGNED NOT NULL,
  `games_together` int UNSIGNED DEFAULT '1',
  `wins_together` int UNSIGNED DEFAULT '0',
  `last_played_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `teams`
--

CREATE TABLE `teams` (
  `id` int UNSIGNED NOT NULL,
  `game_id` int UNSIGNED NOT NULL,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `color_hex` varchar(7) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '#3B82F6',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `teams`
--

INSERT INTO `teams` (`id`, `game_id`, `name`, `color_hex`, `created_at`) VALUES
(202, 150, 'تیم 1', '#3B82F6', '2026-07-23 13:17:08'),
(203, 150, 'تیم 2', '#EF4444', '2026-07-23 13:17:08'),
(204, 151, 'تیم 1', '#3B82F6', '2026-07-23 13:51:13'),
(205, 151, 'تیم 2', '#EF4444', '2026-07-23 13:51:13'),
(206, 159, 'تیم 1', '#3B82F6', '2026-07-25 13:52:39'),
(207, 159, 'تیم 2', '#EF4444', '2026-07-25 13:52:39'),
(208, 160, 'تیم 1', '#3B82F6', '2026-07-25 14:19:20'),
(209, 160, 'تیم 2', '#EF4444', '2026-07-25 14:19:20'),
(210, 161, 'تیم 1', '#3B82F6', '2026-07-25 14:50:08'),
(211, 161, 'تیم 2', '#EF4444', '2026-07-25 14:50:08'),
(212, 167, 'تیم 1', '#3B82F6', '2026-08-01 15:13:43'),
(213, 167, 'تیم 2', '#EF4444', '2026-08-01 15:13:43'),
(214, 172, 'تیم 1', '#3B82F6', '2026-08-04 13:45:24'),
(215, 172, 'تیم 2', '#EF4444', '2026-08-04 13:45:24'),
(216, 177, 'تیم 1', '#3B82F6', '2026-08-05 15:22:22'),
(217, 177, 'تیم 2', '#EF4444', '2026-08-05 15:22:22'),
(218, 180, 'تیم 1', '#3B82F6', '2026-08-06 12:29:49'),
(219, 180, 'تیم 2', '#EF4444', '2026-08-06 12:29:49'),
(220, 181, 'تیم 1', '#3B82F6', '2026-08-06 12:50:44'),
(221, 181, 'تیم 2', '#EF4444', '2026-08-06 12:50:44'),
(222, 184, 'تیم 1', '#3B82F6', '2026-08-07 11:53:29'),
(223, 184, 'تیم 2', '#EF4444', '2026-08-07 11:53:29'),
(224, 185, 'تیم 1', '#3B82F6', '2026-08-07 12:22:16'),
(225, 185, 'تیم 2', '#EF4444', '2026-08-07 12:22:16');

-- --------------------------------------------------------

--
-- Table structure for table `titles`
--

CREATE TABLE `titles` (
  `id` int UNSIGNED NOT NULL,
  `code` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `icon` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 0xF09F8E96EFB88F,
  `condition_type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `condition_value` int UNSIGNED NOT NULL,
  `priority` int DEFAULT '0' COMMENT 'اولویت نمایش (بالا = مهم‌تر)',
  `bonus_points` int UNSIGNED DEFAULT '0' COMMENT 'امتیاز بونوس برای هر برد',
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `titles`
--

INSERT INTO `titles` (`id`, `code`, `name`, `description`, `icon`, `condition_type`, `condition_value`, `priority`, `bonus_points`, `is_active`, `created_at`) VALUES
(1, 'newbie', 'کارآموز', 'اولین بازی خود را انجام داده', '👨‍🏫', 'total_games', 1, 0, 0, 1, '2026-07-04 11:02:34'),
(2, 'active', 'بازیکن فعال', '۱۰ بازی انجام داده', '🎯', 'total_games', 10, 0, 10, 1, '2026-07-04 11:02:34'),
(3, 'winner', 'برنده', '۱۰ برد کسب کرده', '🏆', 'total_wins', 10, 0, 100, 1, '2026-07-04 11:02:34'),
(4, 'champion', 'قهرمان', '100 برد کسب کرده', '👑', 'total_wins', 100, 0, 500, 1, '2026-07-04 11:02:34'),
(5, 'legend', 'افسانه', '10 برد متوالی داشته', '👽', 'best_streak', 10, 0, 10000, 1, '2026-07-04 11:02:34'),
(6, 'unstoppable', 'سرسخت', '۵ برد متوالی داشته', '💀', 'best_streak', 5, 0, 1000, 1, '2026-07-04 11:02:34'),
(7, 'team_leader', 'رهبر تیم', '۱۰ برد تیمی داشته', '🤝', 'team_wins', 10, 0, 200, 1, '2026-07-04 11:02:34');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int UNSIGNED NOT NULL,
  `real_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nickname` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `password_hash` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `avatar_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tagline` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role` enum('user','admin','super_admin') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'user',
  `status` enum('active','banned','pending') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'active',
  `can_create_game` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'مجوز ساخت بازی',
  `can_join_game` tinyint(1) DEFAULT '1' COMMENT 'مجوز شرکت در بازی',
  `is_online` tinyint(1) DEFAULT '0',
  `last_seen_at` timestamp NULL DEFAULT NULL,
  `last_ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `registration_ip` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `playstyle_id` int UNSIGNED DEFAULT NULL,
  `current_title_id` int UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `real_name`, `nickname`, `phone`, `password_hash`, `avatar_path`, `tagline`, `role`, `status`, `can_create_game`, `can_join_game`, `is_online`, `last_seen_at`, `last_ip_address`, `registration_ip`, `playstyle_id`, `current_title_id`, `created_at`, `updated_at`) VALUES
(1, 'حسن', 'سنتری', '09019177577', '$2y$10$H1pqEElOELI3/zvaKLNrb.4qPI0YCMvi19fC9Ta542hh.s.lGjQvu', 'user_1_1784717496.jpg', 'باختی گریه کن من تماشا می‌کنم👑', 'super_admin', 'active', 1, 1, 1, '2026-07-24 13:38:26', '217.219.119.181', NULL, NULL, 3, '2026-06-30 14:56:11', '2026-08-07 11:17:40'),
(16, 'عباس', 'KiNG', '09305586873', '$2y$10$e/lamHckHp4KY58JWpNf7..x4KhRlm7VfItCfiPDBlVwi/wxJCn6.', 'user_16_1785122659.jpg', 'ابوکینگ قدرت مطلق', 'user', 'active', 1, 1, 1, '2026-07-24 12:33:48', '89.45.156.251', '5.120.213.100', NULL, 3, '2026-07-22 13:31:15', '2026-08-07 12:56:40'),
(17, 'مهدی', 'RANGER', '09373056591', '$2y$10$BtrGbsAoSLDtjERVTgpLI.ngSMmGDNjSsw0g4hdaT.JI9ECsYChre', 'user_17_1784920054.jpg', 'جوجه را آخر پاییز می شمارند 😎', 'user', 'active', 1, 1, 1, '2026-07-22 13:36:07', '113.203.87.72', '113.203.87.72', NULL, 3, '2026-07-22 13:36:07', '2026-08-04 18:48:22'),
(18, 'امپراتور', 'امپراطور', '09179292500', '$2y$10$qcnnE5AI37ajK5.hAPIcBu2taTIt/lhgT1nB4nzTae93lJ64ySX3O', 'user_18_1784912162.png', 'امپراطور تعیین میکند', 'user', 'active', 1, 1, 1, '2026-07-22 19:40:36', '89.45.156.251', '5.119.202.144', NULL, 2, '2026-07-22 13:40:39', '2026-07-26 19:22:27'),
(19, 'علی', 'Spider man', '09367186786', '$2y$10$bCoPD51pUMFFHCl2UB9Kluf5McOMrqXbAGVee32v9mJqVBS6F73nG', 'user_19_1784891054.jpg', 'شجاعت قدرت افتخار', 'user', 'active', 0, 1, 1, '2026-07-24 11:00:24', '89.45.156.251', '89.45.156.251', NULL, 1, '2026-07-24 11:00:24', '2026-07-24 13:54:30'),
(20, 'ابوالفضل', 'فرانکلین', '09056893928', '$2y$10$SIexH7VJ8RRcRyLEuL8kguv9cxtR77YO850aXemqeAJcF/izq7c3e', 'user_20_1784893659.jpg', 'همه میتوانن بزرگ شوند 🤠', 'user', 'active', 0, 1, 1, '2026-07-24 12:04:21', '5.119.156.131', '5.119.156.131', NULL, 1, '2026-07-24 11:44:37', '2026-07-24 13:22:28'),
(21, 'کاظم', 'کماندو', '09331039013', '$2y$10$fYwSD99mIYolmkujQJdUPOo/rJXd0D2bCMVsOultvrEfKbLsDT6c2', 'user_21_1784893813.jpg', 'همه می توانند این ماسک را بزنند ', 'user', 'active', 0, 1, 1, '2026-07-24 11:49:53', '5.120.149.235', '5.120.149.235', NULL, 1, '2026-07-24 11:49:53', '2026-07-24 13:22:28'),
(22, 'نهال', 'Unicorn', '09930934671', '$2y$10$QbEk8jNMnHWzoJAQ8qmvpuiYzFUekPwtUgpHhKGdpeyaEckd/GG5q', 'user_22_1784896369.jpg', '👑من پادشاهم 🦄', 'user', 'active', 0, 1, 0, '2026-07-24 12:33:39', '89.45.156.251', '89.45.156.251', NULL, NULL, '2026-07-24 12:22:54', '2026-07-24 12:33:39'),
(23, 'محمد', 'mohammadshirdel68', '09360801254', '$2y$10$/OjDTiMKUfBX.1wxwSdbteVpfolnmoRUq/XMkKkZUNKH3lgHiMft6', NULL, '', 'user', 'banned', 0, 0, 1, '2026-07-26 10:33:36', '5.119.231.184', '5.119.231.184', NULL, NULL, '2026-07-26 10:33:36', '2026-08-01 18:21:48'),
(24, 'محمد', 'سرسخت', '09395188474', '$2y$10$cpM1F4Kw87NKGHgW2KHxf.iBPZ2qcDHsQkXjKq/DI59UN/JUJmPFS', NULL, 'فکر نکن همین طوری ب کسی بها بدم.من سرسخت تلاش گر هستم', 'user', 'active', 0, 1, 1, '2026-07-28 14:35:13', '5.119.163.220', '5.119.163.220', NULL, 1, '2026-07-28 14:35:13', '2026-07-28 15:09:57');

-- --------------------------------------------------------

--
-- Table structure for table `user_achievements`
--

CREATE TABLE `user_achievements` (
  `id` int UNSIGNED NOT NULL,
  `user_id` int UNSIGNED NOT NULL,
  `achievement_id` int UNSIGNED NOT NULL,
  `progress` int UNSIGNED DEFAULT '0' COMMENT 'پیشرفت فعلی',
  `is_completed` tinyint(1) DEFAULT '0' COMMENT 'آیا تکمیل شده؟',
  `unlocked_at` timestamp NULL DEFAULT NULL COMMENT 'زمان کسب',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_achievements`
--

INSERT INTO `user_achievements` (`id`, `user_id`, `achievement_id`, `progress`, `is_completed`, `unlocked_at`, `created_at`, `updated_at`) VALUES
(438, 18, 1, 31, 1, '2026-08-07 12:56:40', '2026-07-22 13:47:14', '2026-08-07 12:56:40'),
(439, 18, 2, 31, 1, '2026-08-07 12:56:40', '2026-07-22 13:47:14', '2026-08-07 12:56:40'),
(440, 18, 3, 31, 0, NULL, '2026-07-22 13:47:14', '2026-08-07 12:55:27'),
(441, 18, 4, 31, 0, NULL, '2026-07-22 13:47:14', '2026-08-07 12:55:27'),
(442, 18, 5, 8, 1, '2026-08-07 12:56:40', '2026-07-22 13:47:14', '2026-08-07 12:56:40'),
(443, 18, 6, 8, 0, NULL, '2026-07-22 13:47:14', '2026-08-07 12:55:27'),
(444, 18, 7, 8, 0, NULL, '2026-07-22 13:47:14', '2026-08-07 12:55:27'),
(445, 18, 8, 8, 0, NULL, '2026-07-22 13:47:14', '2026-08-07 12:55:27'),
(446, 18, 9, 0, 0, NULL, '2026-07-22 13:47:14', '2026-07-22 13:47:14'),
(447, 18, 10, 0, 0, NULL, '2026-07-22 13:47:14', '2026-07-22 13:47:14'),
(448, 18, 11, 0, 0, NULL, '2026-07-22 13:47:14', '2026-07-22 13:47:14'),
(449, 18, 12, 12, 1, '2026-08-07 12:56:40', '2026-07-22 13:47:14', '2026-08-07 12:56:40'),
(450, 18, 13, 5, 0, NULL, '2026-07-22 13:47:14', '2026-08-07 12:55:27'),
(451, 18, 14, 3630, 1, '2026-08-07 12:56:40', '2026-07-22 13:47:14', '2026-08-07 12:56:40'),
(452, 18, 15, 3630, 1, '2026-08-07 12:56:40', '2026-07-22 13:47:14', '2026-08-07 12:56:40'),
(453, 18, 16, 3630, 1, '2026-08-07 12:56:40', '2026-07-22 13:47:14', '2026-08-07 12:56:40'),
(454, 17, 1, 34, 1, '2026-08-07 12:56:40', '2026-07-22 13:56:12', '2026-08-07 12:56:40'),
(455, 17, 2, 34, 1, '2026-08-07 12:56:40', '2026-07-22 13:56:12', '2026-08-07 12:56:40'),
(456, 17, 3, 34, 0, NULL, '2026-07-22 13:56:12', '2026-08-07 12:55:27'),
(457, 17, 4, 34, 0, NULL, '2026-07-22 13:56:12', '2026-08-07 12:55:27'),
(458, 17, 5, 15, 1, '2026-08-07 12:56:40', '2026-07-22 13:56:12', '2026-08-07 12:56:40'),
(459, 17, 6, 15, 1, '2026-08-07 12:56:40', '2026-07-22 13:56:12', '2026-08-07 12:56:40'),
(460, 17, 7, 15, 0, NULL, '2026-07-22 13:56:12', '2026-08-07 12:55:27'),
(461, 17, 8, 15, 0, NULL, '2026-07-22 13:56:12', '2026-08-07 12:55:27'),
(462, 17, 9, 0, 0, NULL, '2026-07-22 13:56:12', '2026-07-22 13:56:12'),
(463, 17, 10, 0, 0, NULL, '2026-07-22 13:56:12', '2026-07-22 13:56:12'),
(464, 17, 11, 0, 0, NULL, '2026-07-22 13:56:12', '2026-07-22 13:56:12'),
(465, 17, 12, 12, 1, '2026-08-07 12:56:40', '2026-07-22 13:56:12', '2026-08-07 12:56:40'),
(466, 17, 13, 7, 0, NULL, '2026-07-22 13:56:12', '2026-08-07 12:55:27'),
(467, 17, 14, 7170, 1, '2026-08-07 12:56:40', '2026-07-22 13:56:12', '2026-08-07 12:56:40'),
(468, 17, 15, 7170, 1, '2026-08-07 12:56:40', '2026-07-22 13:56:12', '2026-08-07 12:56:40'),
(469, 17, 16, 7170, 1, '2026-08-07 12:56:40', '2026-07-22 13:56:12', '2026-08-07 12:56:40'),
(470, 1, 1, 32, 1, '2026-08-07 12:56:40', '2026-07-22 14:05:16', '2026-08-07 12:56:40'),
(471, 1, 2, 32, 1, '2026-08-07 12:56:40', '2026-07-22 14:05:16', '2026-08-07 12:56:40'),
(472, 1, 3, 32, 0, NULL, '2026-07-22 14:05:16', '2026-08-07 12:55:27'),
(473, 1, 4, 32, 0, NULL, '2026-07-22 14:05:16', '2026-08-07 12:55:27'),
(474, 1, 5, 13, 1, '2026-08-07 12:56:40', '2026-07-22 14:05:16', '2026-08-07 12:56:40'),
(475, 1, 6, 13, 1, '2026-08-07 12:56:40', '2026-07-22 14:05:16', '2026-08-07 12:56:40'),
(476, 1, 7, 13, 0, NULL, '2026-07-22 14:05:16', '2026-08-07 12:55:27'),
(477, 1, 8, 13, 0, NULL, '2026-07-22 14:05:16', '2026-08-07 12:55:27'),
(478, 1, 9, 0, 0, NULL, '2026-07-22 14:05:16', '2026-07-22 14:05:16'),
(479, 1, 10, 0, 0, NULL, '2026-07-22 14:05:16', '2026-07-22 14:05:16'),
(480, 1, 11, 0, 0, NULL, '2026-07-22 14:05:16', '2026-07-22 14:05:16'),
(481, 1, 12, 11, 1, '2026-08-07 12:56:40', '2026-07-22 14:05:16', '2026-08-07 12:56:40'),
(482, 1, 13, 5, 0, NULL, '2026-07-22 14:05:16', '2026-08-07 12:55:27'),
(483, 1, 14, 4745, 1, '2026-08-07 12:56:40', '2026-07-22 14:05:16', '2026-08-07 12:56:40'),
(484, 1, 15, 4745, 1, '2026-08-07 12:56:40', '2026-07-22 14:05:16', '2026-08-07 12:56:40'),
(485, 1, 16, 4745, 1, '2026-08-07 12:56:40', '2026-07-22 14:05:16', '2026-08-07 12:56:40'),
(486, 16, 1, 29, 1, '2026-08-07 12:56:40', '2026-07-22 14:26:21', '2026-08-07 12:56:40'),
(487, 16, 2, 29, 1, '2026-08-07 12:56:40', '2026-07-22 14:26:21', '2026-08-07 12:56:40'),
(488, 16, 3, 29, 0, NULL, '2026-07-22 14:26:21', '2026-08-07 12:34:50'),
(489, 16, 4, 29, 0, NULL, '2026-07-22 14:26:21', '2026-08-07 12:34:50'),
(490, 16, 5, 10, 1, '2026-08-07 12:56:40', '2026-07-22 14:26:21', '2026-08-07 12:56:40'),
(491, 16, 6, 10, 1, '2026-08-07 12:56:40', '2026-07-22 14:26:21', '2026-08-07 12:56:40'),
(492, 16, 7, 10, 0, NULL, '2026-07-22 14:26:21', '2026-08-07 12:55:27'),
(493, 16, 8, 10, 0, NULL, '2026-07-22 14:26:21', '2026-08-07 12:55:27'),
(494, 16, 9, 0, 0, NULL, '2026-07-22 14:26:21', '2026-07-22 14:26:21'),
(495, 16, 10, 0, 0, NULL, '2026-07-22 14:26:21', '2026-07-22 14:26:21'),
(496, 16, 11, 0, 0, NULL, '2026-07-22 14:26:21', '2026-07-22 14:26:21'),
(497, 16, 12, 11, 1, '2026-08-07 12:56:40', '2026-07-22 14:26:21', '2026-08-07 12:56:40'),
(498, 16, 13, 7, 0, NULL, '2026-07-22 14:26:21', '2026-08-07 12:55:27'),
(499, 16, 14, 4055, 1, '2026-08-07 12:56:40', '2026-07-22 14:26:21', '2026-08-07 12:56:40'),
(500, 16, 15, 4055, 1, '2026-08-07 12:56:40', '2026-07-22 14:26:21', '2026-08-07 12:56:40'),
(501, 16, 16, 4055, 1, '2026-08-07 12:56:40', '2026-07-22 14:26:21', '2026-08-07 12:56:40'),
(502, 21, 1, 7, 1, '2026-08-07 12:56:40', '2026-07-24 13:04:26', '2026-08-07 12:56:40'),
(503, 21, 2, 7, 0, NULL, '2026-07-24 13:04:26', '2026-08-07 11:51:35'),
(504, 21, 3, 7, 0, NULL, '2026-07-24 13:04:26', '2026-08-07 11:51:35'),
(505, 21, 4, 7, 0, NULL, '2026-07-24 13:04:26', '2026-08-07 11:51:35'),
(506, 21, 5, 1, 1, '2026-08-07 12:56:40', '2026-07-24 13:04:26', '2026-08-07 12:56:40'),
(507, 21, 6, 1, 0, NULL, '2026-07-24 13:04:26', '2026-08-07 11:22:44'),
(508, 21, 7, 1, 0, NULL, '2026-07-24 13:04:26', '2026-08-07 11:22:44'),
(509, 21, 8, 1, 0, NULL, '2026-07-24 13:04:26', '2026-08-07 11:22:44'),
(510, 21, 9, 0, 0, NULL, '2026-07-24 13:04:26', '2026-07-24 13:04:26'),
(511, 21, 10, 0, 0, NULL, '2026-07-24 13:04:26', '2026-07-24 13:04:26'),
(512, 21, 11, 0, 0, NULL, '2026-07-24 13:04:26', '2026-07-24 13:04:26'),
(513, 21, 12, 1, 0, NULL, '2026-07-24 13:04:26', '2026-08-01 15:28:09'),
(514, 21, 13, 0, 0, NULL, '2026-07-24 13:04:26', '2026-07-24 13:04:26'),
(515, 21, 14, 245, 1, '2026-08-07 12:56:40', '2026-07-24 13:04:26', '2026-08-07 12:56:40'),
(516, 21, 15, 245, 0, NULL, '2026-07-24 13:04:26', '2026-08-07 11:46:05'),
(517, 21, 16, 245, 0, NULL, '2026-07-24 13:04:26', '2026-08-07 11:46:05'),
(518, 20, 1, 5, 1, '2026-08-07 12:56:40', '2026-07-24 13:13:52', '2026-08-07 12:56:40'),
(519, 20, 2, 5, 0, NULL, '2026-07-24 13:13:52', '2026-08-07 11:17:40'),
(520, 20, 3, 5, 0, NULL, '2026-07-24 13:13:52', '2026-08-07 11:17:40'),
(521, 20, 4, 5, 0, NULL, '2026-07-24 13:13:52', '2026-08-07 11:17:40'),
(522, 20, 5, 2, 1, '2026-08-07 12:56:40', '2026-07-24 13:13:52', '2026-08-07 12:56:40'),
(523, 20, 6, 2, 0, NULL, '2026-07-24 13:13:52', '2026-07-28 15:09:57'),
(524, 20, 7, 2, 0, NULL, '2026-07-24 13:13:52', '2026-07-28 15:09:57'),
(525, 20, 8, 2, 0, NULL, '2026-07-24 13:13:52', '2026-07-28 15:09:57'),
(526, 20, 9, 0, 0, NULL, '2026-07-24 13:13:52', '2026-07-24 13:13:52'),
(527, 20, 10, 0, 0, NULL, '2026-07-24 13:13:52', '2026-07-24 13:13:52'),
(528, 20, 11, 0, 0, NULL, '2026-07-24 13:13:52', '2026-07-24 13:13:52'),
(529, 20, 12, 0, 0, NULL, '2026-07-24 13:13:52', '2026-07-24 13:13:52'),
(530, 20, 13, 0, 0, NULL, '2026-07-24 13:13:52', '2026-07-24 13:13:52'),
(531, 20, 14, 95, 0, NULL, '2026-07-24 13:13:52', '2026-07-28 15:09:47'),
(532, 20, 15, 95, 0, NULL, '2026-07-24 13:13:52', '2026-07-28 15:09:47'),
(533, 20, 16, 95, 0, NULL, '2026-07-24 13:13:52', '2026-07-28 15:09:47'),
(534, 19, 1, 1, 1, '2026-08-07 12:56:40', '2026-07-24 13:45:45', '2026-08-07 12:56:40'),
(535, 19, 2, 1, 0, NULL, '2026-07-24 13:45:45', '2026-07-24 13:45:45'),
(536, 19, 3, 1, 0, NULL, '2026-07-24 13:45:45', '2026-07-24 13:45:45'),
(537, 19, 4, 1, 0, NULL, '2026-07-24 13:45:45', '2026-07-24 13:45:45'),
(538, 19, 5, 0, 0, NULL, '2026-07-24 13:45:45', '2026-07-24 13:45:45'),
(539, 19, 6, 0, 0, NULL, '2026-07-24 13:45:45', '2026-07-24 13:45:45'),
(540, 19, 7, 0, 0, NULL, '2026-07-24 13:45:45', '2026-07-24 13:45:45'),
(541, 19, 8, 0, 0, NULL, '2026-07-24 13:45:45', '2026-07-24 13:45:45'),
(542, 19, 9, 0, 0, NULL, '2026-07-24 13:45:45', '2026-07-24 13:45:45'),
(543, 19, 10, 0, 0, NULL, '2026-07-24 13:45:45', '2026-07-24 13:45:45'),
(544, 19, 11, 0, 0, NULL, '2026-07-24 13:45:45', '2026-07-24 13:45:45'),
(545, 19, 12, 0, 0, NULL, '2026-07-24 13:45:45', '2026-07-24 13:45:45'),
(546, 19, 13, 0, 0, NULL, '2026-07-24 13:45:45', '2026-07-24 13:45:45'),
(547, 19, 14, 50, 0, NULL, '2026-07-24 13:45:45', '2026-07-24 13:45:45'),
(548, 19, 15, 50, 0, NULL, '2026-07-24 13:45:45', '2026-07-24 13:45:45'),
(549, 19, 16, 50, 0, NULL, '2026-07-24 13:45:45', '2026-07-24 13:45:45'),
(550, 24, 1, 4, 1, '2026-08-07 12:56:40', '2026-07-28 14:59:26', '2026-08-07 12:56:40'),
(551, 24, 2, 4, 0, NULL, '2026-07-28 14:59:26', '2026-08-07 11:17:40'),
(552, 24, 3, 4, 0, NULL, '2026-07-28 14:59:26', '2026-08-07 11:17:40'),
(553, 24, 4, 4, 0, NULL, '2026-07-28 14:59:26', '2026-08-07 11:17:40'),
(554, 24, 5, 1, 1, '2026-08-07 12:56:40', '2026-07-28 14:59:26', '2026-08-07 12:56:40'),
(555, 24, 6, 1, 0, NULL, '2026-07-28 14:59:26', '2026-07-31 12:03:11'),
(556, 24, 7, 1, 0, NULL, '2026-07-28 14:59:26', '2026-07-31 12:03:11'),
(557, 24, 8, 1, 0, NULL, '2026-07-28 14:59:26', '2026-07-31 12:03:11'),
(558, 24, 9, 0, 0, NULL, '2026-07-28 14:59:26', '2026-07-28 14:59:26'),
(559, 24, 10, 0, 0, NULL, '2026-07-28 14:59:26', '2026-07-28 14:59:26'),
(560, 24, 11, 0, 0, NULL, '2026-07-28 14:59:26', '2026-07-28 14:59:26'),
(561, 24, 12, 1, 0, NULL, '2026-07-28 14:59:26', '2026-08-01 15:16:51'),
(562, 24, 13, 0, 0, NULL, '2026-07-28 14:59:26', '2026-07-28 14:59:26'),
(563, 24, 14, 155, 1, '2026-08-07 12:56:40', '2026-07-28 14:59:26', '2026-08-07 12:56:40'),
(564, 24, 15, 155, 0, NULL, '2026-07-28 14:59:26', '2026-08-01 15:16:51'),
(565, 24, 16, 155, 0, NULL, '2026-07-28 14:59:26', '2026-08-01 15:16:51');

-- --------------------------------------------------------

--
-- Table structure for table `user_ip_tracking`
--

CREATE TABLE `user_ip_tracking` (
  `id` int UNSIGNED NOT NULL,
  `user_id` int UNSIGNED NOT NULL,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `first_seen_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `last_seen_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `login_count` int UNSIGNED DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_ip_tracking`
--

INSERT INTO `user_ip_tracking` (`id`, `user_id`, `ip_address`, `user_agent`, `first_seen_at`, `last_seen_at`, `login_count`) VALUES
(5, 1, '2.184.121.80', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-22 10:48:07', '2026-07-22 14:13:53', 2),
(6, 1, '113.203.11.53', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Mobile Safari/537.36', '2026-07-22 11:11:28', '2026-07-22 11:11:28', 1),
(7, 16, '5.120.213.100', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Mobile Safari/537.36', '2026-07-22 13:31:15', '2026-07-22 13:31:15', 1),
(8, 17, '113.203.87.72', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Mobile Safari/537.36', '2026-07-22 13:36:07', '2026-07-22 13:36:07', 1),
(9, 18, '5.119.202.144', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Mobile Safari/537.36', '2026-07-22 13:40:39', '2026-07-22 13:40:39', 1),
(10, 18, '89.45.156.251', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-22 19:40:36', '2026-07-22 19:40:36', 1),
(11, 16, '2.184.121.80', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Mobile Safari/537.36', '2026-07-23 15:15:49', '2026-07-23 15:15:49', 1),
(12, 19, '89.45.156.251', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Mobile Safari/537.36', '2026-07-24 11:00:24', '2026-07-24 11:00:24', 1),
(13, 20, '5.119.156.131', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Mobile Safari/537.36', '2026-07-24 11:44:37', '2026-07-24 12:04:21', 2),
(14, 21, '5.120.149.235', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Mobile Safari/537.36', '2026-07-24 11:49:53', '2026-07-24 11:49:53', 1),
(15, 22, '89.45.156.251', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Mobile Safari/537.36', '2026-07-24 12:22:54', '2026-07-24 12:29:35', 2),
(16, 16, '89.45.156.251', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Mobile Safari/537.36', '2026-07-24 12:26:27', '2026-07-24 12:33:48', 2),
(17, 1, '217.219.119.181', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:153.0) Gecko/20100101 Firefox/153.0', '2026-07-24 13:38:26', '2026-07-24 13:38:26', 1),
(18, 23, '5.119.231.184', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-26 10:33:36', '2026-07-26 10:33:36', 1),
(19, 24, '5.119.163.220', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/27.0 Chrome/125.0.0.0 Mobile Safari/537.36', '2026-07-28 14:35:13', '2026-07-28 14:35:13', 1);

-- --------------------------------------------------------

--
-- Table structure for table `user_notification_settings`
--

CREATE TABLE `user_notification_settings` (
  `user_id` int UNSIGNED NOT NULL,
  `enable_game_updates` tinyint(1) DEFAULT '1',
  `enable_achievements` tinyint(1) DEFAULT '1',
  `enable_challenges` tinyint(1) DEFAULT '1',
  `enable_system` tinyint(1) DEFAULT '1',
  `enable_push` tinyint(1) DEFAULT '0',
  `quiet_hours_start` time DEFAULT NULL,
  `quiet_hours_end` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_sessions`
--

CREATE TABLE `user_sessions` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` int UNSIGNED NOT NULL,
  `session_id` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `page` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'صفحه فعلی',
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `last_activity` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_sessions`
--

INSERT INTO `user_sessions` (`id`, `user_id`, `session_id`, `page`, `ip_address`, `user_agent`, `last_activity`) VALUES
(65, 18, 'tt3jnij5q4n117ugji4cmk4efp', '/dashboard', '5.233.104.217', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 14:53:08'),
(66, 1, 'sgffk1p3r8mmklpbqr73iia1c4', '/achievements', '5.119.36.55', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-08-07 07:02:17'),
(67, 20, 's7tkjj98unsmb6qupkdcanpe0l', '/game/163', '2.184.110.19', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Mobile Safari/537.36', '2026-07-28 15:09:16'),
(68, 18, '286ko3ng5ivar8rccmj8ftku5h', '/game/183', '91.92.192.168', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Mobile Safari/537.36', '2026-08-07 11:45:28'),
(69, 1, 'isrp38muqhh6b2geifundsrvnv', '/game/153', '217.219.119.181', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:153.0) Gecko/20100101 Firefox/153.0', '2026-07-24 19:36:12'),
(70, 21, 'fak7a69r11gi544pfa390pcf5l', '/game/154', '5.120.220.233', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Mobile Safari/537.36', '2026-07-24 14:33:35'),
(71, 1, 'k5qgl4d6o59dkeukrmphdjd2db', '/users/16', '91.92.192.168', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Mobile Safari/537.36', '2026-08-07 13:20:35'),
(72, 16, 'eak15c2642lc3mce1m256h7o23', '/dashboard', '5.120.46.227', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Mobile Safari/537.36', '2026-08-08 09:07:26'),
(73, 1, 'fi5bc1o1cfb3qi5j9gaer19s6j', 'https://hamidionline.ir/tv/185', '91.92.192.168', 'Mozilla/5.0 (Linux; NetCast; U) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.3945.79 Safari/537.36 SmartTV/10.0 Colt/2.0', '2026-08-07 12:57:52'),
(74, 17, 'f2grd8b2k0827k5cag5h9r5k8n', '/dashboard', '91.92.192.168', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Mobile Safari/537.36', '2026-08-07 13:01:56'),
(75, 23, 'p8k517i478051auv0p02pc3036', '/dashboard', '5.119.231.184', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-26 10:34:39'),
(76, 24, '38rbh8v9vuekfjv17amio0pb8b', '/dashboard', '5.119.163.220', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/27.0 Chrome/125.0.0.0 Mobile Safari/537.36', '2026-07-28 14:41:36');

-- --------------------------------------------------------

--
-- Table structure for table `user_streaks`
--

CREATE TABLE `user_streaks` (
  `user_id` int UNSIGNED NOT NULL,
  `current_streak` int UNSIGNED DEFAULT '0' COMMENT 'استریک فعلی',
  `best_streak` int UNSIGNED DEFAULT '0' COMMENT 'بهترین استریک',
  `last_win_at` timestamp NULL DEFAULT NULL COMMENT 'زمان آخرین برد',
  `streak_broken_at` timestamp NULL DEFAULT NULL COMMENT 'زمان شکستن استریک',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_streaks`
--

INSERT INTO `user_streaks` (`user_id`, `current_streak`, `best_streak`, `last_win_at`, `streak_broken_at`, `updated_at`) VALUES
(1, 0, 0, NULL, NULL, '2026-07-22 15:31:07'),
(16, 0, 0, NULL, NULL, '2026-07-22 15:31:06'),
(17, 0, 0, NULL, NULL, '2026-07-22 15:31:06'),
(18, 0, 0, NULL, NULL, '2026-07-22 15:31:06'),
(19, 0, 0, NULL, NULL, '2026-07-24 13:54:30'),
(20, 0, 0, NULL, NULL, '2026-07-24 13:22:28'),
(21, 0, 0, NULL, NULL, '2026-07-24 13:22:28'),
(24, 0, 0, NULL, NULL, '2026-07-28 15:09:57');

-- --------------------------------------------------------

--
-- Table structure for table `user_titles`
--

CREATE TABLE `user_titles` (
  `id` int UNSIGNED NOT NULL,
  `user_id` int UNSIGNED NOT NULL,
  `title_id` int UNSIGNED NOT NULL,
  `is_active` tinyint(1) DEFAULT '0' COMMENT 'لقب فعال فعلی',
  `unlocked_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_titles`
--

INSERT INTO `user_titles` (`id`, `user_id`, `title_id`, `is_active`, `unlocked_at`) VALUES
(144, 16, 1, 0, '2026-08-07 11:17:40'),
(145, 17, 1, 0, '2026-08-07 11:17:40'),
(146, 18, 1, 0, '2026-07-22 15:31:06'),
(147, 1, 1, 0, '2026-07-22 15:31:06'),
(148, 20, 1, 1, '2026-07-24 13:22:28'),
(149, 21, 1, 1, '2026-07-24 13:22:28'),
(150, 19, 1, 1, '2026-07-24 13:54:30'),
(151, 1, 2, 0, '2026-07-25 12:59:32'),
(152, 16, 2, 0, '2026-08-05 14:14:48'),
(153, 18, 2, 1, '2026-07-25 13:50:48'),
(154, 17, 2, 0, '2026-08-07 11:17:40'),
(155, 24, 1, 1, '2026-07-28 15:09:57'),
(156, 17, 3, 1, '2026-08-05 14:14:48'),
(161, 1, 3, 1, '2026-08-07 11:17:40'),
(178, 16, 3, 1, '2026-08-07 12:56:40');

-- --------------------------------------------------------

--
-- Table structure for table `user_xp`
--

CREATE TABLE `user_xp` (
  `user_id` int UNSIGNED NOT NULL,
  `total_xp` int UNSIGNED DEFAULT '0' COMMENT 'کل XP',
  `current_level` int UNSIGNED DEFAULT '1' COMMENT 'سطح فعلی',
  `xp_to_next_level` int UNSIGNED DEFAULT '100' COMMENT 'XP مورد نیاز برای سطح بعدی',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_xp`
--

INSERT INTO `user_xp` (`user_id`, `total_xp`, `current_level`, `xp_to_next_level`, `updated_at`) VALUES
(1, 9845, 3, 10000, '2026-08-07 12:56:40'),
(16, 8405, 3, 10000, '2026-08-07 12:56:40'),
(17, 14735, 4, 100000, '2026-08-07 12:56:40'),
(18, 7535, 3, 10000, '2026-08-07 12:56:40'),
(19, 105, 1, 500, '2026-08-07 11:17:40'),
(20, 245, 1, 500, '2026-08-07 11:17:40'),
(21, 540, 2, 5000, '2026-08-07 12:56:40'),
(24, 345, 1, 500, '2026-08-07 11:17:40');

-- --------------------------------------------------------

--
-- Table structure for table `win_types`
--

CREATE TABLE `win_types` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `icon` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `score_multiplier` decimal(4,2) DEFAULT '1.00',
  `is_active` tinyint(1) DEFAULT '1',
  `sort_order` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
-- Indexes for dumped tables
--

--
-- Indexes for table `achievements`
--
ALTER TABLE `achievements`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`),
  ADD KEY `idx_code` (`code`),
  ADD KEY `idx_category` (`category`),
  ADD KEY `idx_rarity` (`rarity`);

--
-- Indexes for table `admin_logs`
--
ALTER TABLE `admin_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_admin` (`admin_id`),
  ADD KEY `idx_action` (`action_type`),
  ADD KEY `idx_target` (`target_type`,`target_id`),
  ADD KEY `idx_created` (`created_at`);

--
-- Indexes for table `cards`
--
ALTER TABLE `cards`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_cards_rarity` (`rarity`),
  ADD KEY `idx_cards_active` (`is_active`);

--
-- Indexes for table `card_mastery`
--
ALTER TABLE `card_mastery`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_card` (`user_id`,`card_id`),
  ADD KEY `card_id` (`card_id`);

--
-- Indexes for table `games`
--
ALTER TABLE `games`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_games_status` (`status`),
  ADD KEY `idx_games_referee` (`referee_id`),
  ADD KEY `idx_winner_team` (`winner_team_id`);

--
-- Indexes for table `game_participants`
--
ALTER TABLE `game_participants`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_participants_game` (`game_id`),
  ADD KEY `idx_participants_user` (`user_id`),
  ADD KEY `idx_participants_team` (`team_id`);

--
-- Indexes for table `game_rounds`
--
ALTER TABLE `game_rounds`
  ADD PRIMARY KEY (`id`),
  ADD KEY `winning_card_id` (`winning_card_id`),
  ADD KEY `win_type_id` (`win_type_id`),
  ADD KEY `idx_rounds_game` (`game_id`),
  ADD KEY `idx_rounds_winner` (`winner_participant_id`);

--
-- Indexes for table `leaderboard_cache`
--
ALTER TABLE `leaderboard_cache`
  ADD PRIMARY KEY (`user_id`),
  ADD KEY `idx_leaderboard_rank` (`final_rank_score` DESC),
  ADD KEY `idx_leaderboard_wins` (`total_wins` DESC);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_unread` (`user_id`,`is_read`),
  ADD KEY `idx_created` (`created_at`),
  ADD KEY `idx_type` (`type`);

--
-- Indexes for table `notification_templates`
--
ALTER TABLE `notification_templates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `event_name` (`event_name`);

--
-- Indexes for table `otp_codes`
--
ALTER TABLE `otp_codes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_phone_purpose` (`phone`,`purpose`),
  ADD KEY `idx_expires` (`expires_at`),
  ADD KEY `idx_code` (`code`);

--
-- Indexes for table `player_levels`
--
ALTER TABLE `player_levels`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_level` (`level`),
  ADD KEY `idx_xp_range` (`min_xp`,`max_xp`);

--
-- Indexes for table `referee_actions_log`
--
ALTER TABLE `referee_actions_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_reflog_game` (`game_id`),
  ADD KEY `idx_reflog_referee` (`referee_id`);

--
-- Indexes for table `sse_events`
--
ALTER TABLE `sse_events`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_channel_time` (`channel`,`created_at`),
  ADD KEY `idx_event_type` (`event_type`),
  ADD KEY `idx_created` (`created_at`);

--
-- Indexes for table `suspicious_games`
--
ALTER TABLE `suspicious_games`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_game_id` (`game_id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_risk_level` (`risk_level`),
  ADD KEY `idx_is_reviewed` (`is_reviewed`),
  ADD KEY `idx_created` (`created_at`);

--
-- Indexes for table `system_settings`
--
ALTER TABLE `system_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`),
  ADD KEY `updated_by` (`updated_by`),
  ADD KEY `idx_key` (`setting_key`),
  ADD KEY `idx_category` (`category`);

--
-- Indexes for table `teammate_history`
--
ALTER TABLE `teammate_history`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_teammates` (`user_id_1`,`user_id_2`),
  ADD KEY `user_id_2` (`user_id_2`);

--
-- Indexes for table `teams`
--
ALTER TABLE `teams`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_teams_game` (`game_id`);

--
-- Indexes for table `titles`
--
ALTER TABLE `titles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`),
  ADD KEY `idx_code` (`code`),
  ADD KEY `idx_priority` (`priority`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nickname` (`nickname`),
  ADD UNIQUE KEY `phone` (`phone`),
  ADD KEY `idx_users_nickname` (`nickname`),
  ADD KEY `idx_users_status` (`status`),
  ADD KEY `idx_users_online` (`is_online`);

--
-- Indexes for table `user_achievements`
--
ALTER TABLE `user_achievements`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_achievement` (`user_id`,`achievement_id`),
  ADD KEY `achievement_id` (`achievement_id`),
  ADD KEY `idx_user_completed` (`user_id`,`is_completed`),
  ADD KEY `idx_unlocked` (`unlocked_at`);

--
-- Indexes for table `user_ip_tracking`
--
ALTER TABLE `user_ip_tracking`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_ip` (`user_id`,`ip_address`),
  ADD KEY `idx_ip_address` (`ip_address`),
  ADD KEY `idx_user_id` (`user_id`);

--
-- Indexes for table `user_notification_settings`
--
ALTER TABLE `user_notification_settings`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `user_sessions`
--
ALTER TABLE `user_sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_session` (`session_id`),
  ADD KEY `idx_activity` (`last_activity`);

--
-- Indexes for table `user_streaks`
--
ALTER TABLE `user_streaks`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `user_titles`
--
ALTER TABLE `user_titles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_title` (`user_id`,`title_id`),
  ADD KEY `title_id` (`title_id`),
  ADD KEY `idx_user_active` (`user_id`,`is_active`);

--
-- Indexes for table `user_xp`
--
ALTER TABLE `user_xp`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `win_types`
--
ALTER TABLE `win_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `achievements`
--
ALTER TABLE `achievements`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `admin_logs`
--
ALTER TABLE `admin_logs`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=895;

--
-- AUTO_INCREMENT for table `cards`
--
ALTER TABLE `cards`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `card_mastery`
--
ALTER TABLE `card_mastery`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=126;

--
-- AUTO_INCREMENT for table `games`
--
ALTER TABLE `games`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=186;

--
-- AUTO_INCREMENT for table `game_participants`
--
ALTER TABLE `game_participants`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=762;

--
-- AUTO_INCREMENT for table `game_rounds`
--
ALTER TABLE `game_rounds`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=999;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2134;

--
-- AUTO_INCREMENT for table `notification_templates`
--
ALTER TABLE `notification_templates`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `otp_codes`
--
ALTER TABLE `otp_codes`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `player_levels`
--
ALTER TABLE `player_levels`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `referee_actions_log`
--
ALTER TABLE `referee_actions_log`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=718;

--
-- AUTO_INCREMENT for table `sse_events`
--
ALTER TABLE `sse_events`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3461;

--
-- AUTO_INCREMENT for table `suspicious_games`
--
ALTER TABLE `suspicious_games`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT for table `system_settings`
--
ALTER TABLE `system_settings`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=197;

--
-- AUTO_INCREMENT for table `teammate_history`
--
ALTER TABLE `teammate_history`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `teams`
--
ALTER TABLE `teams`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=226;

--
-- AUTO_INCREMENT for table `titles`
--
ALTER TABLE `titles`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `user_achievements`
--
ALTER TABLE `user_achievements`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=655;

--
-- AUTO_INCREMENT for table `user_ip_tracking`
--
ALTER TABLE `user_ip_tracking`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `user_sessions`
--
ALTER TABLE `user_sessions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=77;

--
-- AUTO_INCREMENT for table `user_titles`
--
ALTER TABLE `user_titles`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=188;

--
-- AUTO_INCREMENT for table `win_types`
--
ALTER TABLE `win_types`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

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
