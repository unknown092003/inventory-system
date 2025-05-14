-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 14, 2025 at 10:22 AM
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
-- Database: `inventory-system`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_log`
--

CREATE TABLE `activity_log` (
  `id` int(11) NOT NULL,
  `action_type` varchar(20) NOT NULL,
  `table_name` varchar(255) NOT NULL,
  `record_id` int(11) NOT NULL,
  `old_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`old_data`)),
  `new_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`new_data`)),
  `user` varchar(255) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `description` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activity_log`
--

INSERT INTO `activity_log` (`id`, `action_type`, `table_name`, `record_id`, `old_data`, `new_data`, `user`, `timestamp`, `description`) VALUES
(18, 'create', 'inventory', 32, NULL, NULL, 'one', '2025-04-29 02:16:35', 'Created new inventory item: one'),
(19, 'edit', 'inventory', 32, NULL, NULL, '2025-04-29', '2025-04-29 02:17:39', ' one'),
(20, 'edit', 'inventory', 32, NULL, NULL, '2025-04-29', '2025-04-29 02:27:24', ' one'),
(21, 'create', 'inventory', 33, NULL, NULL, 'three', '2025-04-29 02:43:43', '3'),
(22, 'edit', 'inventory', 33, NULL, NULL, '2025-04-29', '2025-04-29 02:44:38', ' 3'),
(23, 'edit', 'inventory', 32, NULL, NULL, 'onebyone', '2025-04-29 02:50:27', ' one'),
(24, 'create', 'inventory', 34, NULL, NULL, 'THREE', '2025-04-29 02:52:02', '3'),
(25, 'edit', 'inventory', 34, NULL, NULL, 'NONE', '2025-04-29 02:52:31', ' 3'),
(26, 'edit', 'inventory', 32, NULL, NULL, 'onebyone11', '2025-04-29 03:10:02', ' one'),
(27, 'edit', 'inventory', 32, NULL, NULL, 'onebyone11', '2025-04-29 03:15:36', ' one'),
(28, 'edit', 'inventory', 32, NULL, NULL, 'onebyone111', '2025-04-29 03:15:48', ' one'),
(29, 'edit', 'inventory', 32, NULL, NULL, 'onebyone111', '2025-04-29 03:20:30', ' one'),
(30, 'edit', 'inventory', 32, NULL, NULL, 'onebyone111', '2025-04-29 05:14:43', ' one'),
(31, 'edit', 'inventory', 34, NULL, NULL, 'NONE', '2025-04-29 05:20:22', ' 3'),
(32, 'edit', 'inventory', 34, NULL, NULL, 'NONE', '2025-04-29 05:22:00', ' 3'),
(33, 'create', 'inventory', 35, NULL, NULL, 'STRIKING', '2025-04-29 05:22:56', '5'),
(34, 'edit', 'inventory', 35, NULL, NULL, 'STRIKING', '2025-04-29 05:23:58', ' 5'),
(35, 'edit', 'inventory', 35, NULL, NULL, 'STRIKING', '2025-04-29 05:40:31', ' 5'),
(36, 'edit', 'inventory', 32, NULL, NULL, 'onebyone111', '2025-04-29 05:40:52', ' one'),
(37, 'edit', 'inventory', 32, NULL, NULL, 'onebyone111', '2025-04-29 06:15:50', ' one'),
(38, 'edit', 'inventory', 32, NULL, NULL, 'onebyone111', '2025-04-29 06:17:33', ' one'),
(39, 'edit', 'inventory', 32, NULL, NULL, 'onebyone111', '2025-04-29 06:23:56', ' one'),
(40, 'edit', 'inventory', 32, NULL, NULL, 'onebyone111', '2025-04-29 06:24:44', ' one'),
(41, 'edit', 'inventory', 32, NULL, NULL, 'onebyone111', '2025-04-29 06:25:03', ' one'),
(42, 'edit', 'inventory', 32, NULL, NULL, 'onebyone111', '2025-04-29 06:25:54', ' one'),
(43, 'edit', 'inventory', 32, NULL, NULL, 'onebyone1111', '2025-04-29 06:26:44', ' one1'),
(44, 'edit', 'inventory', 32, NULL, NULL, 'onebyone111', '2025-04-29 06:34:13', ' one'),
(45, 'edit', 'inventory', 32, NULL, NULL, 'onebyone111', '2025-04-29 06:47:29', ' one'),
(46, 'edit', 'inventory', 33, NULL, NULL, '2025-04-29', '2025-04-29 06:48:02', ' 3'),
(47, 'edit', 'inventory', 32, NULL, NULL, 'onebyone111', '2025-04-29 06:51:13', ' one'),
(48, 'edit', 'inventory', 35, NULL, NULL, 'STRIKING', '2025-04-29 06:51:50', ' 5'),
(49, 'edit', 'inventory', 35, NULL, NULL, 'STRIKING', '2025-04-29 06:53:45', ' 5'),
(50, 'edit', 'inventory', 35, NULL, NULL, 'STRIKING', '2025-04-29 06:53:48', ' 5'),
(51, 'edit', 'inventory', 35, NULL, NULL, 'STRIKING', '2025-04-29 06:53:52', ' 5'),
(52, 'create', 'inventory', 36, NULL, NULL, 'THE TREE root', '2025-04-29 06:59:36', 'threePM'),
(53, 'edit', 'inventory', 32, NULL, NULL, 'onebyone111', '2025-04-29 07:00:00', ' one'),
(54, 'edit', 'inventory', 32, NULL, NULL, 'onebyone111', '2025-04-29 07:00:03', ' one'),
(55, 'edit', 'inventory', 32, NULL, NULL, 'onebyone111', '2025-04-29 07:00:05', ' one'),
(56, 'edit', 'inventory', 35, NULL, NULL, 'STRIKING', '2025-04-29 07:00:29', ' 5'),
(57, 'edit', 'inventory', 36, NULL, NULL, 'THE TREE root', '2025-04-29 07:00:47', ' threePM'),
(58, 'edit', 'inventory', 33, NULL, NULL, '2025-04-29', '2025-04-29 07:01:37', ' 3'),
(59, 'edit', 'inventory', 33, NULL, NULL, 'THE NUMBER 3', '2025-04-29 07:02:27', ' 3'),
(60, 'create', 'inventory', 37, NULL, NULL, 'none', '2025-04-30 03:28:23', 'trest'),
(61, 'create', 'inventory', 38, NULL, NULL, 'none', '2025-04-30 05:10:13', 'radio modem'),
(62, 'create', 'inventory', 39, NULL, NULL, 'none', '2025-04-30 05:37:48', 'desktop cpmputer, bysou cPU CLone SN: 223-06-09, AOC LED Monitor SN: HPYF61A000062'),
(63, 'edit', 'inventory', 36, NULL, NULL, 'THE TREE root', '2025-04-30 05:38:44', ' threePM'),
(64, 'edit', 'inventory', 39, NULL, NULL, 'none', '2025-04-30 05:40:29', ' desktop cpmputer, LED Monitor'),
(65, 'edit', 'inventory', 39, NULL, NULL, 'none', '2025-04-30 05:52:49', ' desktop cpmputer, LED Monitor'),
(66, 'edit', 'inventory', 32, NULL, NULL, 'onebyone111', '2025-04-30 09:00:12', ' one'),
(67, 'edit', 'inventory', 32, NULL, NULL, 'THEBIGONE', '2025-04-30 09:00:59', ' one'),
(68, 'edit', 'inventory', 37, NULL, NULL, 'NOLAN', '2025-04-30 11:48:44', ' jfkalsfjdkltrest'),
(69, 'edit', 'inventory', 39, NULL, NULL, 'none', '2025-05-01 13:33:58', 'desktop cpmputer, LED Monitor'),
(70, 'edit', 'inventory', 39, NULL, NULL, 'none', '2025-05-01 13:34:07', 'desktop cpmputer, LED Monitor'),
(71, 'edit', 'inventory', 32, NULL, NULL, 'THEBIGONE', '2025-05-01 13:34:48', 'one'),
(72, 'create', 'inventory', 40, NULL, NULL, 'onebyone111', '2025-05-01 13:36:10', 'fjaksldfjklas;djfioesjfsioa'),
(73, 'edit', 'inventory', 39, NULL, NULL, 'none', '2025-05-01 13:45:02', 'desktop cpmputer, LED Monitor'),
(74, 'edit', 'inventory', 32, NULL, NULL, 'THEBIGONE', '2025-05-02 02:18:39', 'end of the earth 2012'),
(75, 'edit', 'inventory', 40, NULL, NULL, 'onebyone111', '2025-05-02 02:29:07', 'lolabay'),
(76, 'create', 'inventory', 41, NULL, NULL, 'SIR', '2025-05-02 09:05:52', 'DFASDFA'),
(77, 'edit', 'inventory', 34, NULL, NULL, 'NONE', '2025-05-02 09:08:39', '3'),
(78, 'edit', 'inventory', 32, NULL, NULL, 'THEBIGONE', '2025-05-05 01:29:47', 'tesla'),
(79, 'edit', 'inventory', 33, NULL, NULL, 'THE NUMBER 3', '2025-05-05 01:29:58', 'tesla'),
(80, 'edit', 'inventory', 34, NULL, NULL, 'NONE', '2025-05-05 01:30:05', 'tesla'),
(81, 'edit', 'inventory', 32, NULL, NULL, 'THEBIGONE', '2025-05-05 01:30:50', 'tesla'),
(82, 'edit', 'inventory', 32, NULL, NULL, 'THEBIGONE', '2025-05-05 01:56:31', 'tala'),
(83, 'edit', 'inventory', 32, NULL, NULL, 'THEBIGONE', '2025-05-05 02:06:21', 'tala'),
(84, 'edit', 'inventory', 32, NULL, NULL, 'THEBIGONE', '2025-05-05 02:21:51', 'tala'),
(85, 'edit', 'inventory', 32, NULL, NULL, 'THEBIGONE', '2025-05-05 02:22:38', 'tala'),
(86, 'edit', 'inventory', 32, NULL, NULL, 'THEBIGONE', '2025-05-05 02:23:18', 'tala'),
(87, 'edit', 'inventory', 32, NULL, NULL, 'THEBIGONE', '2025-05-05 05:10:59', 'tala'),
(88, 'edit', 'inventory', 32, NULL, NULL, 'THEBIGONE', '2025-05-05 05:53:19', 'tala'),
(89, 'edit', 'inventory', 32, NULL, NULL, 'THEBIGONE', '2025-05-05 05:54:43', 'talaf'),
(90, 'edit', 'inventory', 32, NULL, NULL, 'THEBIGONE', '2025-05-05 05:57:50', 'talaf'),
(91, 'edit', 'inventory', 32, NULL, NULL, 'THEBIGONE', '2025-05-05 05:58:12', 'talaf'),
(92, 'edit', 'inventory', 32, NULL, NULL, 'THEBIGONE', '2025-05-05 05:59:06', 'talaf'),
(93, 'edit', 'inventory', 32, NULL, NULL, 'THEBIGONE', '2025-05-05 05:59:25', 'talaf'),
(94, 'edit', 'inventory', 32, NULL, NULL, 'THEBIGONE', '2025-05-05 06:01:23', 'talaf'),
(95, 'edit', 'inventory', 32, NULL, NULL, 'THEBIGONE', '2025-05-05 06:01:48', 'talaf'),
(96, 'edit', 'inventory', 32, NULL, NULL, 'THEBIGONE', '2025-05-05 06:11:35', 'talaf'),
(97, 'edit', 'inventory', 32, NULL, NULL, 'THEBIGONE', '2025-05-05 06:42:50', 'afasdfasdf'),
(98, 'edit', 'inventory', 32, NULL, NULL, 'THEBIGONE', '2025-05-05 06:43:55', 'fhf'),
(99, 'edit', 'inventory', 32, NULL, NULL, 'THEBIGONE', '2025-05-05 06:45:04', 'sdfgsdfg'),
(100, 'edit', 'inventory', 32, NULL, NULL, 'THEBIGONE', '2025-05-05 07:02:34', 'sdfgsdfg'),
(101, 'edit', 'inventory', 32, NULL, NULL, 'THEBIGONE', '2025-05-05 07:02:38', 'sdfgsdfg'),
(102, 'edit', 'inventory', 32, NULL, NULL, 'THEBIGONE', '2025-05-05 07:07:23', 'sdfgsdfg'),
(103, 'edit', 'inventory', 32, NULL, NULL, 'THEBIGONE', '2025-05-05 07:07:34', 'hyfghgfhfghfgh'),
(104, 'edit', 'inventory', 39, NULL, NULL, 'none', '2025-05-05 07:07:54', 'desktop cpmputer, LED Monitor'),
(105, 'edit', 'inventory', 39, NULL, NULL, 'none', '2025-05-05 07:08:33', 'desktop cpmputer, LED Monitor'),
(106, 'edit', 'inventory', 39, NULL, NULL, 'none', '2025-05-05 07:08:43', 'desktop cpmputer, LED Monitor'),
(107, 'edit', 'inventory', 39, NULL, NULL, 'none', '2025-05-05 07:10:32', 'desktop cpmputer, LED Monitor'),
(108, 'edit', 'inventory', 39, NULL, NULL, 'none', '2025-05-05 07:10:45', 'desktop cpmputer, LED Monitor'),
(109, 'edit', 'inventory', 32, NULL, NULL, 'THEBIGONE', '2025-05-05 07:11:24', 'Hesoyam'),
(110, 'edit', 'inventory', 41, NULL, NULL, 'SIR', '2025-05-05 07:32:13', 'hesoyam'),
(111, 'edit', 'inventory', 41, NULL, NULL, 'SIR', '2025-05-05 07:33:36', 'hesoyam'),
(112, 'edit', 'inventory', 36, NULL, NULL, 'THE TREE root', '2025-05-05 07:35:07', 'threePM'),
(113, 'edit', 'inventory', 37, NULL, NULL, 'NOLAN', '2025-05-05 08:01:00', 'jfkalsfjdkltrest'),
(114, 'edit', 'inventory', 37, NULL, NULL, 'NOLAN', '2025-05-05 08:01:16', 'Edited hesoyam'),
(115, 'edit', 'inventory', 34, NULL, NULL, 'hkjhkjhkj', '2025-05-05 08:02:04', 'tesla'),
(116, 'edit', 'inventory', 34, NULL, NULL, 'hkjhkjhkj', '2025-05-05 08:02:19', 'tesla'),
(117, 'edit', 'inventory', 32, NULL, NULL, 'THEBIGONE', '2025-05-05 08:02:44', 'Hesoyam'),
(118, 'edit', 'inventory', 36, NULL, NULL, 'THE TREE root', '2025-05-05 08:11:42', 'threePM'),
(119, 'edit', 'inventory', 36, NULL, NULL, 'THE TREE root', '2025-05-05 08:36:54', 'threePM'),
(120, 'edit', 'inventory', 35, NULL, NULL, 'STRIKING', '2025-05-05 08:37:34', 'small five'),
(121, 'edit', 'inventory', 35, NULL, NULL, 'STRIKING', '2025-05-05 08:37:41', 'small five'),
(122, 'edit', 'inventory', 35, NULL, NULL, 'STRIKING', '2025-05-05 08:37:49', 'small five'),
(123, 'edit', 'inventory', 35, NULL, NULL, 'STRIKING', '2025-05-05 08:38:15', 'small five'),
(124, 'edit', 'inventory', 35, NULL, NULL, 'STRIKING', '2025-05-05 08:40:22', 'small five geting biger '),
(125, 'edit', 'inventory', 38, NULL, NULL, 'none', '2025-05-05 08:40:56', 'radio modem 200'),
(126, 'edit', 'inventory', 38, NULL, NULL, 'none', '2025-05-05 08:41:23', 'radio modem 200'),
(127, 'edit', 'inventory', 38, NULL, NULL, 'none', '2025-05-05 08:41:51', 'radio modem 200'),
(128, 'edit', 'inventory', 38, NULL, NULL, 'none', '2025-05-05 08:42:50', 'radio modem 200'),
(129, 'edit', 'inventory', 38, NULL, NULL, 'none', '2025-05-05 08:47:44', 'radio modem 300'),
(130, 'edit', 'inventory', 38, NULL, NULL, 'none', '2025-05-05 08:47:57', 'radio modem 300'),
(131, 'edit', 'inventory', 41, NULL, NULL, 'SIR', '2025-05-05 08:49:19', 'hesoyam'),
(132, 'edit', 'inventory', 41, NULL, NULL, 'SIR', '2025-05-05 08:49:53', 'hesoyam'),
(133, 'edit', 'inventory', 41, NULL, NULL, 'SIR', '2025-05-05 08:50:14', 'hesoyam'),
(134, 'edit', 'inventory', 41, NULL, NULL, 'SIR', '2025-05-05 08:50:35', 'hesoyam23423'),
(135, 'edit', 'inventory', 41, NULL, NULL, 'SIR', '2025-05-05 08:50:48', 'hesoyam23423'),
(136, 'edit', 'inventory', 32, NULL, NULL, 'THEBIGONE', '2025-05-05 08:59:00', 'Hesoyam part 1'),
(137, 'edit', 'inventory', 32, NULL, NULL, 'THEBIGONE', '2025-05-05 08:59:08', 'Hesoyam part 1'),
(138, 'edit', 'inventory', 32, NULL, NULL, 'THEBIGONE', '2025-05-05 09:02:58', 'Hesoyam part 1'),
(139, 'edit', 'inventory', 32, NULL, NULL, 'THEBIGONE', '2025-05-06 01:27:51', 'hesoyam part 2'),
(140, 'edit', 'inventory', 32, NULL, NULL, 'THEBIGONE', '2025-05-06 01:33:03', 'hesoyam part 2'),
(141, 'edit', 'inventory', 37, NULL, NULL, 'NOLAN', '2025-05-06 01:41:19', 'Edited hesoyam'),
(142, 'edit', 'inventory', 37, NULL, NULL, 'NOLAN', '2025-05-06 01:41:55', 'Edited hesoyam'),
(143, 'edit', 'inventory', 32, NULL, NULL, 'THEBIGONE', '2025-05-06 01:51:37', 'hesoyam part 2'),
(144, 'edit', 'inventory', 32, NULL, NULL, 'THEBIGONE', '2025-05-06 01:51:44', 'hesoyam part 2'),
(145, 'edit', 'inventory', 32, NULL, NULL, 'THEBIGONE', '2025-05-06 01:55:00', 'hesoyam part 3'),
(146, 'edit', 'inventory', 32, NULL, NULL, 'THEBIGONE', '2025-05-06 01:55:49', 'hesoyam part 4'),
(147, 'edit', 'inventory', 32, NULL, NULL, 'THEBIGONE', '2025-05-06 01:56:01', 'hesoyam part 5'),
(148, 'edit', 'inventory', 32, NULL, NULL, 'THEBIGONE', '2025-05-06 01:56:03', 'hesoyam part 5'),
(149, 'edit', 'inventory', 32, NULL, NULL, 'THEBIGONE', '2025-05-06 01:56:04', 'hesoyam part 5'),
(150, 'edit', 'inventory', 32, NULL, NULL, 'THEBIGONE', '2025-05-06 01:56:25', 'hesoyam part 8'),
(151, 'edit', 'inventory', 32, NULL, NULL, 'THEBIGONE', '2025-05-06 01:58:25', 'hesoyam part 8'),
(152, 'edit', 'inventory', 33, NULL, NULL, 'THE NUMBER 3', '2025-05-06 02:01:31', 'tesla edited final'),
(153, 'edit', 'inventory', 33, NULL, NULL, 'THE NUMBER 3', '2025-05-06 02:01:45', 'tesla edited final'),
(154, 'edit', 'inventory', 33, NULL, NULL, 'THE NUMBER 3', '2025-05-06 02:02:02', 'tesla edited final'),
(155, 'edit', 'inventory', 33, NULL, NULL, 'THE NUMBER 3', '2025-05-06 02:02:07', 'tesla edited final'),
(156, 'edit', 'inventory', 33, NULL, NULL, 'THE NUMBER 3', '2025-05-06 02:02:16', 'tesla edited final'),
(157, 'edit', 'inventory', 33, NULL, NULL, 'THE NUMBER 3', '2025-05-06 02:02:28', 'tesla edited final destination'),
(158, 'edit', 'inventory', 33, NULL, NULL, 'THE NUMBER 3', '2025-05-06 02:02:36', 'tesla edited final destination'),
(159, 'edit', 'inventory', 33, NULL, NULL, 'THE NUMBER 3', '2025-05-06 02:02:45', 'tesla edited final destination'),
(160, 'edit', 'inventory', 33, NULL, NULL, 'THE NUMBER 3', '2025-05-06 02:03:32', 'tesla edited final destination'),
(161, 'create', 'inventory', 44, NULL, NULL, 'Errol John Pardillo', '2025-05-06 03:04:59', 'qr sticker '),
(162, 'edit', 'inventory', 40, NULL, NULL, 'onebyone111', '2025-05-06 03:05:41', 'lolabay'),
(163, 'edit', 'inventory', 44, NULL, NULL, 'Errol John Pardillo', '2025-05-06 03:06:12', 'qr sticker '),
(164, 'edit', 'inventory', 44, NULL, NULL, 'Errol John Pardillo', '2025-05-06 03:07:42', 'qr sticker '),
(165, 'edit', 'inventory', 44, NULL, NULL, 'Errol John Pardillo', '2025-05-06 03:09:19', 'QR Error'),
(166, 'edit', 'inventory', 44, NULL, NULL, 'Errol John Pardillo', '2025-05-06 03:17:26', 'QR Error'),
(167, 'edit', 'inventory', 44, NULL, NULL, 'Errol John Pardillo', '2025-05-06 03:17:40', 'QR Error'),
(168, 'edit', 'inventory', 44, NULL, NULL, 'Errol John Pardillo', '2025-05-06 03:18:05', 'QR Error'),
(169, 'edit', 'inventory', 44, NULL, NULL, 'Errol John Pardillo', '2025-05-06 03:18:29', 'QR Error'),
(170, 'edit', 'inventory', 32, NULL, NULL, 'THEBIGONE', '2025-05-06 05:37:54', 'hesoyam part 10'),
(171, 'edit', 'inventory', 32, NULL, NULL, 'THEBIGONE', '2025-05-06 05:38:12', 'hesoyam part 10'),
(172, 'edit', 'inventory', 32, NULL, NULL, 'THEBIGONE', '2025-05-06 05:38:34', 'hesoyam part 10'),
(173, 'edit', 'inventory', 32, NULL, NULL, 'THEBIGONE', '2025-05-06 05:38:45', 'hesoyam part 10'),
(174, 'edit', 'inventory', 32, NULL, NULL, 'THEBIGONE', '2025-05-06 05:39:17', 'hesoyam part 10'),
(175, 'edit', 'inventory', 32, NULL, NULL, 'THEBIGONE', '2025-05-06 05:39:47', 'hesoyam part 1019'),
(176, 'edit', 'inventory', 44, NULL, NULL, 'Errol John Pardillo', '2025-05-06 05:42:34', 'QR Error'),
(177, 'edit', 'inventory', 32, NULL, NULL, 'THEBIGONE', '2025-05-06 05:43:53', 'hesoyam part 1019'),
(178, 'edit', 'inventory', 32, NULL, NULL, 'THEBIGONE', '2025-05-06 05:44:05', 'hesoyam part 1019'),
(179, 'edit', 'inventory', 33, NULL, NULL, 'THE NUMBER 3', '2025-05-06 05:44:49', 'Number 3 Edited with the time 1:44pm'),
(180, 'edit', 'inventory', 32, NULL, NULL, 'THEBIGONE', '2025-05-06 07:09:11', 'the grand property desk'),
(181, 'edit', 'inventory', 36, NULL, NULL, 'THE TREE root', '2025-05-06 07:24:23', 'threePM'),
(182, 'edit', 'inventory', 36, NULL, NULL, 'THE TREE root', '2025-05-07 01:08:31', 'description no one '),
(183, 'edit', 'inventory', 33, NULL, NULL, 'THE NUMBER 3', '2025-05-07 01:12:00', 'Number 3 Edited with the time 1:44pm'),
(184, 'edit', 'inventory', 33, NULL, NULL, 'THE NUMBER 3', '2025-05-07 01:14:44', 'Number 3 Edited with the time 1:44pm'),
(185, 'create', 'inventory', 45, NULL, NULL, 'DFGDFG', '2025-05-09 01:35:38', 'FGERER'),
(186, 'edit', 'inventory', 32, NULL, NULL, 'THEBIGONE', '2025-05-09 01:46:16', 'the grand property desk'),
(187, 'edit', 'inventory', 32, NULL, NULL, 'THEBIGONE', '2025-05-09 02:02:10', 'sfjklajskdlf'),
(188, 'edit', 'inventory', 32, NULL, NULL, 'THEBIGONE', '2025-05-09 08:07:46', 'the big big'),
(189, 'edit', 'inventory', 32, NULL, NULL, 'THEBIGONE', '2025-05-09 08:08:34', 'the big big'),
(190, 'edit', 'inventory', 32, NULL, NULL, 'THEBIGONE', '2025-05-09 08:08:40', 'the big big'),
(191, 'edit', 'inventory', 32, NULL, NULL, 'THEBIGONE', '2025-05-09 08:09:07', 'the big big'),
(192, 'edit', 'inventory', 32, NULL, NULL, 'THEBIGONE', '2025-05-13 01:59:01', 'the small one'),
(193, 'edit', 'inventory', 32, NULL, NULL, 'THEBIGONE', '2025-05-13 06:11:00', 'the small one'),
(194, 'edit', 'inventory', 32, NULL, NULL, 'THEBIGONE', '2025-05-13 06:15:33', 'the small one'),
(195, 'edit', 'inventory', 32, NULL, NULL, 'the small one', '2025-05-13 06:40:32', 'the small one'),
(196, 'edit', 'inventory', 33, NULL, NULL, 'THE NUMBER 3', '2025-05-13 06:43:15', 'asldjfklasjl_01'),
(197, 'edit', 'inventory', 33, NULL, NULL, 'THE NUMBER 3', '2025-05-13 06:44:12', 'asldjfklasjl_01'),
(198, 'edit', 'inventory', 33, NULL, NULL, 'THE NUMBER 3', '2025-05-13 06:45:03', 'asldjfklasjl_01'),
(199, 'create', 'inventory', 46, NULL, NULL, 'jasper', '2025-05-13 07:08:10', 'asdfasdf'),
(200, 'create', 'inventory', 47, NULL, NULL, 'safasdfasdf', '2025-05-13 07:15:45', 'jrfkljfklasdjfklasdjfkl28349283490'),
(201, 'create', 'inventory', 48, NULL, NULL, 'erroljohnpardillo', '2025-05-13 07:46:47', 'we continued our projects i created new function and added remarks on every page of code ');

-- --------------------------------------------------------

--
-- Table structure for table `inventory`
--

CREATE TABLE `inventory` (
  `id` int(11) NOT NULL,
  `property_number` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `model_number` varchar(255) NOT NULL,
  `acquisition_date` date DEFAULT NULL,
  `person_accountable` varchar(255) NOT NULL,
  `signature_of_inventory_team_date` date DEFAULT NULL,
  `cost` double DEFAULT NULL,
  `remarks` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inventory`
--

INSERT INTO `inventory` (`id`, `property_number`, `description`, `model_number`, `acquisition_date`, `person_accountable`, `signature_of_inventory_team_date`, `cost`, `remarks`) VALUES
(32, 'Grand Property Number', 'the small one', 'the model and serial are combined', '2025-05-01', 'the small one', '2025-05-13', NULL, 'service'),
(33, 'sdfasdfasf_01', 'asldjfklasjl_01', 'asdfasdf', '2025-05-01', 'THE NUMBER 3', '2025-04-29', NULL, 'service'),
(34, 'threeeditqr', 'tesla', '3', '0000-00-00', 'hkjhkjhkj', '2025-04-29', NULL, 'service'),
(35, 'BIG FIVE', 'small five geting biger ', 'small five', '0000-00-00', 'STRIKING', '2025-04-29', NULL, 'service'),
(36, 'no one is here', 'description no one ', '0908h', '0000-00-00', 'THE TREE root', '2018-04-30', NULL, 'service'),
(37, 'random property', 'Edited hesoyam', 'the modal number is changed ', '0000-00-00', 'NOLAN', '2025-04-25', NULL, 'unservice'),
(38, '2020-1-06-05-070-23-0001-2017', 'radio modem 300', 'none', '0000-00-00', 'none', '2020-12-21', NULL, 'service'),
(39, 'SPHV-2022-1-06-05-030-01-0004-2017', 'desktop cpmputer, LED Monitor', 'Hesoyam', '0000-00-00', 'none', '2024-09-27', NULL, 'service'),
(40, 'wertwertwert3423423u89', 'lolabay', '34567890', '0000-00-00', 'onebyone111', '2025-05-05', NULL, 'service'),
(41, 'starting', 'hesoyam23423', 'dfgsdfgsdfgsdfgsdfg', '0000-00-00', 'SIR', '2025-05-02', NULL, 'service'),
(44, 'May 6 2025', 'QR Error', 'qr stiker 5/6/2025', '0000-00-00', 'Errol John Pardillo', '2025-05-06', NULL, 'unservice'),
(45, 'DGERGERGER', 'FGERER', 'ESGER', '0000-00-00', 'DFGDFG', '2025-05-09', NULL, 'FGDFGFG'),
(46, 'asdfasdfa637812637812', 'asdfasdf', 'asdfasdfas', '2025-05-13', '23423423423', '0000-00-00', 0, '2025-05-13'),
(47, 'jfdkslajfklasdjfkla;sjdfklasjkld57342895734895798', 'jrfkljfklasdjfklasdjfkl28349283490', 'jfkljfklasjfklasjd29038492034890q', '2025-05-09', '23472389478239', '0000-00-00', 0, '2025-05-13'),
(48, 'journal 1', 'we continued our projects i created new function and added remarks on every page of code ', '8:17 am - 5 pm', '2025-05-13', '100', '0000-00-00', 0, '2025-05-13');

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL,
  `used` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `firstName` varchar(50) NOT NULL,
  `lastName` varchar(50) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','user') NOT NULL DEFAULT 'user',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `firstName`, `lastName`, `username`, `password`, `role`, `created_at`, `updated_at`) VALUES
(21, 'errol', 'pardillo', 'errolpardillo', '$2y$10$26uWJ2blCvfAfzkk3cuC4e0lUM0JGAYayTWF5Hq/TFBfnWk7lSrxK', 'user', '2025-05-06 15:03:27', NULL),
(22, 'operation', 'operation', 'operation', '$2y$10$CSThXNerKHZoqPFt0K16Q.b4.NUnrEisyazDRf5GsHfPf8Lu9kDwy', 'user', '2025-05-07 08:47:02', NULL),
(23, 'hello', 'hello', 'hello', '$2y$10$SPH3W8A3GHPN3xgPbwMEl.rKClJZjQpUIq8yjH1YY46PJ7B7nX5iS', 'user', '2025-05-08 09:05:27', NULL),
(24, 'GSU', 'OCD-CAR', 'OCD-CAR Inventory of PPE', '$2y$10$ozt4A2Zm7j4EciFd/7g2O.y2XxHVWbgvyqtPek2l9lGCt5JSzNmEC', 'user', '2025-05-09 09:33:25', NULL),
(25, 'errol', 'pardillo', 'pardillo', '$2y$10$ha45iOFrp2kl28hwuFRMu.H2aG8GY5K.zil3mfodeMQRVE5ghCgq.', 'user', '2025-05-13 09:57:26', NULL),
(26, 'office of', 'civil defense', 'OCD-car', '$2y$10$cB876P9YLx9l/N0/DQ398OkLMZu3o3CX6wDEZuKNtvf6XhuBpvwO.', 'user', '2025-05-14 10:01:40', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_log`
--
ALTER TABLE `activity_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_table_record` (`table_name`,`record_id`);

--
-- Indexes for table `inventory`
--
ALTER TABLE `inventory`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `property-number-index` (`property_number`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_log`
--
ALTER TABLE `activity_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=202;

--
-- AUTO_INCREMENT for table `inventory`
--
ALTER TABLE `inventory`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD CONSTRAINT `password_reset_tokens_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
