-- phpMyAdmin SQL Dump
-- version 5.1.0
-- https://www.phpmyadmin.net/
--
-- ホスト: localhost:8889
-- 生成日時: 2022 年 6 月 02 日 02:44
-- サーバのバージョン： 5.7.34
-- PHP のバージョン: 7.4.21

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- データベース: `kadai_db`
--

-- --------------------------------------------------------

--
-- テーブルの構造 `items`
--

CREATE TABLE `items` (
  `id` text COLLATE utf32_unicode_ci NOT NULL,
  `name` text COLLATE utf32_unicode_ci NOT NULL,
  `description` varchar(128) COLLATE utf32_unicode_ci DEFAULT NULL,
  `image_path` text COLLATE utf32_unicode_ci,
  `date_created` datetime NOT NULL,
  `last_updated` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf32 COLLATE=utf32_unicode_ci;

--
-- テーブルのデータのダンプ `items`
--

INSERT INTO `items` (`id`, `name`, `description`, `image_path`, `date_created`, `last_updated`) VALUES
('AA000174', 'TEST1', 'DESC1', './images/AA000174_about_01.jpg', '2022-06-02 11:43:41', '2022-06-02 11:43:41'),
('AA000175', 'TEST2', 'DESC2', './images/AA000175_about_03.jpg', '2022-06-02 11:43:55', '2022-06-02 11:43:55'),
('AA000176', 'TEST3', 'DESC3', './images/AA000176_about_02.jpg', '2022-06-02 11:44:11', '2022-06-02 11:44:11');

--
-- ダンプしたテーブルのインデックス
--

--
-- テーブルのインデックス `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`id`(10));
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
