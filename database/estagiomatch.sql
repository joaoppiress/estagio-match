-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 12/06/2026 às 06:56
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `estagiomatch`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `applications`
--

CREATE TABLE `applications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `vacancy_id` bigint(20) UNSIGNED NOT NULL,
  `student_id` bigint(20) UNSIGNED NOT NULL,
  `status` enum('enviada','visualizada','em_analise','entrevista','aprovada','reprovada','cancelada') NOT NULL DEFAULT 'enviada',
  `cover_letter` text DEFAULT NULL,
  `match_score` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `applications`
--

INSERT INTO `applications` (`id`, `vacancy_id`, `student_id`, `status`, `cover_letter`, `match_score`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'aprovada', NULL, 99, '2026-06-11 23:25:53', '2026-06-11 23:58:44'),
(2, 1, 4, 'entrevista', NULL, 65, '2026-06-12 00:02:28', '2026-06-12 01:01:34'),
(3, 4, 8, 'cancelada', NULL, 68, '2026-06-12 01:07:18', '2026-06-12 01:08:11');

-- --------------------------------------------------------

--
-- Estrutura para tabela `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `event` varchar(80) NOT NULL,
  `context` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`context`)),
  `ip_address` varbinary(16) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `audit_logs`
--

INSERT INTO `audit_logs` (`id`, `user_id`, `event`, `context`, `ip_address`, `created_at`) VALUES
(1, 1, 'register_success', '{\"role\":\"estudante\"}', 0x00000000000000000000000000000001, '2026-06-11 23:20:08'),
(2, 1, 'profile_updated', '[]', 0x00000000000000000000000000000001, '2026-06-11 23:22:09'),
(3, 1, 'logout', '[]', 0x00000000000000000000000000000001, '2026-06-11 23:22:31'),
(4, 2, 'register_success', '{\"role\":\"empresa\"}', 0x00000000000000000000000000000001, '2026-06-11 23:23:47'),
(5, 2, 'vacancy_created', '{\"vacancy_id\":1}', 0x00000000000000000000000000000001, '2026-06-11 23:25:00'),
(6, 2, 'logout', '[]', 0x00000000000000000000000000000001, '2026-06-11 23:25:16'),
(7, 1, 'login_success', '{\"email\":\"jorge@gmail.com\"}', 0x00000000000000000000000000000001, '2026-06-11 23:25:46'),
(8, 1, 'application_created', '{\"vacancy_id\":1,\"match_score\":99}', 0x00000000000000000000000000000001, '2026-06-11 23:25:53'),
(9, 1, 'logout', '[]', 0x00000000000000000000000000000001, '2026-06-11 23:42:36'),
(10, 1, 'login_success', '{\"email\":\"jorge@gmail.com\"}', 0x00000000000000000000000000000001, '2026-06-11 23:54:07'),
(11, 1, 'logout', '[]', 0x00000000000000000000000000000001, '2026-06-11 23:56:08'),
(12, 3, 'register_success', '{\"role\":\"estudante\",\"verification_url\":\"/estagio-match/controlador.php?rota=verificar-email&token=ecf50868dad61596d29b155ec984724e56e716638462a9a2a680d134caf4f073\"}', 0x00000000000000000000000000000001, '2026-06-11 23:56:56'),
(13, 3, 'logout', '[]', 0x00000000000000000000000000000001, '2026-06-11 23:57:33'),
(14, 2, 'login_success', '{\"email\":\"delta@gmail.com\"}', 0x00000000000000000000000000000001, '2026-06-11 23:58:22'),
(15, 2, 'vacancy_boosted', '{\"vacancy_id\":1}', 0x00000000000000000000000000000001, '2026-06-11 23:58:27'),
(16, 2, 'application_status_updated', '{\"application_id\":1,\"status\":\"aprovada\"}', 0x00000000000000000000000000000001, '2026-06-11 23:58:44'),
(17, 2, 'rating_created', '{\"application_id\":1,\"type\":\"empresa_para_estudante\"}', 0x00000000000000000000000000000001, '2026-06-11 23:58:53'),
(18, 2, 'company_premium_activated', '{\"company_id\":1}', 0x00000000000000000000000000000001, '2026-06-11 23:58:56'),
(19, 2, 'logout', '[]', 0x00000000000000000000000000000001, '2026-06-12 00:01:08'),
(20, 4, 'register_success', '{\"role\":\"estudante\",\"verification_url\":\"/estagio-match/controlador.php?rota=verificar-email&token=687d2d035bd7354defbfa8955b0f0441af005ccd9e16d58f92ddb09c1120218c\"}', 0x00000000000000000000000000000001, '2026-06-12 00:02:14'),
(21, 4, 'application_created', '{\"vacancy_id\":1,\"match_score\":65}', 0x00000000000000000000000000000001, '2026-06-12 00:02:28'),
(22, 4, 'logout', '[]', 0x00000000000000000000000000000001, '2026-06-12 00:03:01'),
(23, 1, 'login_success', '{\"email\":\"jorge@gmail.com\"}', 0x00000000000000000000000000000001, '2026-06-12 00:03:33'),
(24, 1, 'rating_created', '{\"application_id\":1,\"type\":\"estudante_para_empresa\"}', 0x00000000000000000000000000000001, '2026-06-12 00:04:13'),
(25, 1, 'logout', '[]', 0x00000000000000000000000000000001, '2026-06-12 00:04:36'),
(26, 4, 'password_reset_requested', '{\"reset_url\":\"/estagio-match/controlador.php?rota=redefinir-senha&token=22896d0df5a705066ccd0ef99a97b25e9c4a67fb79fcad6db6c73581e853d8c2\"}', 0x00000000000000000000000000000001, '2026-06-12 00:04:53'),
(27, 4, 'password_reset_requested', '{\"reset_url\":\"/estagio-match/controlador.php?rota=redefinir-senha&token=17755420e4c06685a547863d2ec555f09885f4109813c1d07b4a1e1c75a68f1f\"}', 0x00000000000000000000000000000001, '2026-06-12 00:06:32'),
(28, 4, 'password_reset_completed', '[]', 0x00000000000000000000000000000001, '2026-06-12 00:14:11'),
(29, 4, 'login_success', '{\"email\":\"joaopedropiressa@gmail.com\"}', 0x00000000000000000000000000000001, '2026-06-12 00:14:26'),
(30, 1, 'password_reset_requested', '{\"reset_url\":\"http://localhost/estagio-match/controlador.php?rota=redefinir-senha&token=fdcde26d36c297eebe0535a87f853a3d5f102e2e8d54302c0d90a2b2d23e3ef0\"}', 0x00000000000000000000000000000001, '2026-06-12 00:28:32'),
(31, 1, 'password_reset_email_failed', '{\"email\":\"jorge@gmail.com\",\"error\":\"Configuração SMTP incompleta: MAIL_HOST e MAIL_FROM_ADDRESS são obrigatórios.\"}', 0x00000000000000000000000000000001, '2026-06-12 00:28:32'),
(32, 4, 'logout', '[]', 0x00000000000000000000000000000001, '2026-06-12 00:34:22'),
(33, 4, 'password_reset_requested', '{\"reset_url\":\"http://localhost/estagio-match/controlador.php?rota=redefinir-senha&token=29b855c8515150417030991f76f1d2c59351326a4a6bc3af3634c23fcaaeec71\"}', 0x00000000000000000000000000000001, '2026-06-12 00:34:38'),
(34, 4, 'password_reset_email_failed', '{\"email\":\"joaopedropiressa@gmail.com\",\"error\":\"Configuração SMTP incompleta: MAIL_HOST e MAIL_FROM_ADDRESS são obrigatórios.\"}', 0x00000000000000000000000000000001, '2026-06-12 00:34:38'),
(35, 4, 'password_reset_requested', '{\"reset_url\":\"http://localhost/estagio-match/controlador.php?rota=redefinir-senha&token=4801acdd2de0ca9e14fcb86e068b7512c93ce0bf93b5dd35e964ef5f48884f63\"}', 0x00000000000000000000000000000001, '2026-06-12 00:41:16'),
(36, 4, 'password_reset_email_sent', '{\"email\":\"joaopedropiressa@gmail.com\"}', 0x00000000000000000000000000000001, '2026-06-12 00:41:22'),
(37, 4, 'password_reset_completed', '[]', 0x00000000000000000000000000000001, '2026-06-12 00:42:24'),
(38, 4, 'login_failed', '{\"email\":\"joaopedropiressa@gmail.com\"}', 0x00000000000000000000000000000001, '2026-06-12 00:42:46'),
(39, 4, 'login_success', '{\"email\":\"joaopedropiressa@gmail.com\"}', 0x00000000000000000000000000000001, '2026-06-12 00:42:58'),
(40, 4, 'logout', '[]', 0x00000000000000000000000000000001, '2026-06-12 00:43:03'),
(41, 5, 'register_success', '{\"role\":\"empresa\",\"verification_url\":\"http://localhost/estagio-match/controlador.php?rota=verificar-email&token=12feb455668c8a322744b002d23aaa3c9825d2768eeb9f94259f48e5e3eb377a\"}', 0x00000000000000000000000000000001, '2026-06-12 00:46:43'),
(42, 6, 'register_success', '{\"role\":\"estudante\",\"verification_url\":\"http://localhost/estagio-match/controlador.php?rota=verificar-email&token=fb42e21c7b97fcf920a76bdb39ec0d63defb5c7e35021f263e2d3237ed8710f9\"}', 0x00000000000000000000000000000001, '2026-06-12 00:55:00'),
(43, 6, 'email_verification_sent', '{\"role\":\"estudante\",\"email\":\"teste.estudante.20260612005459@example.com\"}', 0x00000000000000000000000000000001, '2026-06-12 00:55:05'),
(44, 6, 'email_verified', '[]', 0x00000000000000000000000000000001, '2026-06-12 00:55:06'),
(45, 7, 'register_success', '{\"role\":\"empresa\",\"verification_url\":\"http://localhost/estagio-match/controlador.php?rota=verificar-email&token=3936185b6599ad47cedd57eab622bb0668c1ee8964818d283add5b7362e84dd6\"}', 0x00000000000000000000000000000001, '2026-06-12 00:55:31'),
(46, 7, 'email_verification_sent', '{\"role\":\"empresa\",\"email\":\"teste.empresa.20260612005531@example.com\"}', 0x00000000000000000000000000000001, '2026-06-12 00:55:36'),
(47, 7, 'email_verified', '[]', 0x00000000000000000000000000000001, '2026-06-12 00:55:36'),
(48, 7, 'vacancy_created', '{\"vacancy_id\":2}', 0x00000000000000000000000000000001, '2026-06-12 00:55:36'),
(49, 5, 'logout', '[]', 0x00000000000000000000000000000001, '2026-06-12 00:56:59'),
(50, 5, 'login_success', '{\"email\":\"chefemcasa8@gmail.com\"}', 0x00000000000000000000000000000001, '2026-06-12 00:57:26'),
(51, 5, 'vacancy_created', '{\"vacancy_id\":3}', 0x00000000000000000000000000000001, '2026-06-12 00:59:25'),
(52, 5, 'vacancy_created', '{\"vacancy_id\":4}', 0x00000000000000000000000000000001, '2026-06-12 01:00:49'),
(53, 5, 'logout', '[]', 0x00000000000000000000000000000001, '2026-06-12 01:01:01'),
(54, 2, 'login_success', '{\"email\":\"delta@gmail.com\"}', 0x00000000000000000000000000000001, '2026-06-12 01:01:23'),
(55, 2, 'application_status_updated', '{\"application_id\":2,\"status\":\"entrevista\"}', 0x00000000000000000000000000000001, '2026-06-12 01:01:34'),
(56, 2, 'logout', '[]', 0x00000000000000000000000000000001, '2026-06-12 01:01:42'),
(57, 4, 'password_reset_requested', '{\"reset_url\":\"http://localhost/estagio-match/controlador.php?rota=redefinir-senha&token=35c1fb89bda5b328ab44f35c468e85b035988e82c8b4c6725a697f84f238f0d9\"}', 0x00000000000000000000000000000001, '2026-06-12 01:02:27'),
(58, 4, 'password_reset_email_sent', '{\"email\":\"joaopedropiressa@gmail.com\"}', 0x00000000000000000000000000000001, '2026-06-12 01:02:32'),
(59, 4, 'password_reset_completed', '[]', 0x00000000000000000000000000000001, '2026-06-12 01:03:31'),
(60, 4, 'login_success', '{\"email\":\"joaopedropiressa@gmail.com\"}', 0x00000000000000000000000000000001, '2026-06-12 01:03:43'),
(61, 4, 'logout', '[]', 0x00000000000000000000000000000001, '2026-06-12 01:05:13'),
(62, 8, 'register_success', '{\"role\":\"estudante\",\"verification_url\":\"http://localhost/estagio-match/controlador.php?rota=verificar-email&token=543e28826e8f8dff9338f4d39928dcca29e5f00cf768cf510824dc0e4b875b58\"}', 0x00000000000000000000000000000001, '2026-06-12 01:06:26'),
(63, 8, 'email_verification_sent', '{\"role\":\"estudante\",\"email\":\"joaozinho@gmail.com\"}', 0x00000000000000000000000000000001, '2026-06-12 01:06:30'),
(64, 8, 'application_created', '{\"vacancy_id\":4,\"match_score\":68}', 0x00000000000000000000000000000001, '2026-06-12 01:07:18'),
(65, 8, 'logout', '[]', 0x00000000000000000000000000000001, '2026-06-12 01:07:30'),
(66, 5, 'login_success', '{\"email\":\"chefemcasa8@gmail.com\"}', 0x00000000000000000000000000000001, '2026-06-12 01:07:55'),
(67, 5, 'vacancy_boosted', '{\"vacancy_id\":4}', 0x00000000000000000000000000000001, '2026-06-12 01:08:00'),
(68, 5, 'application_status_updated', '{\"application_id\":3,\"status\":\"cancelada\"}', 0x00000000000000000000000000000001, '2026-06-12 01:08:11'),
(69, 5, 'logout', '[]', 0x00000000000000000000000000000001, '2026-06-12 01:08:17'),
(70, 8, 'login_success', '{\"email\":\"joaozinho@gmail.com\"}', 0x00000000000000000000000000000001, '2026-06-12 01:08:31'),
(71, 5, 'password_reset_requested', '{\"reset_url\":\"http://localhost/estagio-match/controlador.php?rota=redefinir-senha&token=577ef902a65232d01611747af8f6cc89905aebcd3bf0eb67c36832764341ef9f\"}', 0x00000000000000000000000000000001, '2026-06-12 01:55:06'),
(72, 5, 'password_reset_email_sent', '{\"email\":\"chefemcasa8@gmail.com\"}', 0x00000000000000000000000000000001, '2026-06-12 01:55:11'),
(73, 8, 'logout', '[]', 0x00000000000000000000000000000001, '2026-06-12 01:55:24'),
(74, 5, 'password_reset_completed', '[]', 0x00000000000000000000000000000001, '2026-06-12 01:56:09'),
(75, 5, 'login_success', '{\"email\":\"chefemcasa8@gmail.com\"}', 0x00000000000000000000000000000001, '2026-06-12 01:56:32');

-- --------------------------------------------------------

--
-- Estrutura para tabela `companies`
--

CREATE TABLE `companies` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `trade_name` varchar(160) NOT NULL,
  `legal_name` varchar(190) DEFAULT NULL,
  `cnpj_hash` char(64) DEFAULT NULL,
  `sector` varchar(120) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `state` char(2) DEFAULT NULL,
  `address` varchar(190) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `logo_initials` varchar(4) DEFAULT NULL,
  `is_premium` tinyint(1) NOT NULL DEFAULT 0,
  `rating_avg` decimal(2,1) NOT NULL DEFAULT 0.0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `companies`
--

INSERT INTO `companies` (`id`, `user_id`, `trade_name`, `legal_name`, `cnpj_hash`, `sector`, `city`, `state`, `address`, `description`, `logo_initials`, `is_premium`, `rating_avg`, `created_at`, `updated_at`) VALUES
(1, 2, 'Delta', NULL, NULL, 'Tecnologia', 'Presidente Prudente', 'SP', NULL, 'Empresa cadastrada no EstágioMatch.', 'D', 1, 5.0, '2026-06-11 23:23:47', '2026-06-12 00:04:13'),
(2, 5, 'Chef Em Casa', NULL, '153b561a9a989720f981d636b9bac2b68b489c72408cadf0064b2f816be63e25', 'Técnologia', 'Presidente Prudente', 'SP', NULL, 'Empresa cadastrada no EstagioMatch.', 'CE', 0, 0.0, '2026-06-12 00:46:43', '2026-06-12 00:46:43'),
(3, 7, 'Empresa Verificada LTDA', NULL, '74fcb98ff7bb1884c6d648b7f1eb54668aef98b0758a425ee16ea0757209454d', 'Tecnologia', 'Presidente Prudente', 'SP', NULL, 'Empresa cadastrada no EstagioMatch.', 'EV', 0, 0.0, '2026-06-12 00:55:31', '2026-06-12 00:55:31');

-- --------------------------------------------------------

--
-- Estrutura para tabela `email_verifications`
--

CREATE TABLE `email_verifications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `token_hash` char(64) NOT NULL,
  `expires_at` datetime NOT NULL,
  `used_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `email_verifications`
--

INSERT INTO `email_verifications` (`id`, `user_id`, `token_hash`, `expires_at`, `used_at`, `created_at`) VALUES
(1, 3, '109769b394e5c795bf7fde269863916177fc636ec26f3959db052c262592fd45', '2026-06-12 23:56:56', NULL, '2026-06-11 23:56:56'),
(2, 4, 'ece5a59122a019f3abc4917b734bcfcc2871ae04914840ef267c37a5b8df5ebe', '2026-06-13 00:02:14', NULL, '2026-06-12 00:02:14'),
(3, 5, '79d6dfd36913a39141d8579f5599eb4229c642740cebc2c6f1c118fe46d9e867', '2026-06-13 00:46:43', NULL, '2026-06-12 00:46:43'),
(4, 6, 'a28ad6ce8884fe0c28a6cc82029036ef0b83ed6f429653229aede1153ee00f6d', '2026-06-13 00:55:00', '2026-06-12 00:55:06', '2026-06-12 00:55:00'),
(5, 7, '34969eeb69a6fc8171fa76772dd67e447db7b3f87a5f9545ec78342be252d227', '2026-06-13 00:55:31', '2026-06-12 00:55:36', '2026-06-12 00:55:31'),
(6, 8, '36aafaa6a69b38be1091b89e74658a3071da4df563ad46f2771ff4ac94ed3a9b', '2026-06-13 01:06:26', NULL, '2026-06-12 01:06:26');

-- --------------------------------------------------------

--
-- Estrutura para tabela `login_attempts`
--

CREATE TABLE `login_attempts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `email` varchar(190) NOT NULL,
  `ip_address` varbinary(16) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `successful` tinyint(1) NOT NULL DEFAULT 0,
  `attempted_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `login_attempts`
--

INSERT INTO `login_attempts` (`id`, `email`, `ip_address`, `user_id`, `successful`, `attempted_at`) VALUES
(1, 'jorge@gmail.com', 0x00000000000000000000000000000001, 1, 1, '2026-06-11 23:25:46'),
(2, 'jorge@gmail.com', 0x00000000000000000000000000000001, 1, 1, '2026-06-11 23:54:07'),
(3, 'cadastro:bruno@gmail.com', 0x00000000000000000000000000000001, NULL, 1, '2026-06-11 23:56:55'),
(4, 'delta@gmail.com', 0x00000000000000000000000000000001, 2, 1, '2026-06-11 23:58:22'),
(5, 'cadastro:joaopedropiressa@gmail.com', 0x00000000000000000000000000000001, NULL, 1, '2026-06-12 00:02:14'),
(6, 'candidatura:4', 0x00000000000000000000000000000001, NULL, 1, '2026-06-12 00:02:28'),
(7, 'jorge@gmail.com', 0x00000000000000000000000000000001, 1, 1, '2026-06-12 00:03:33'),
(8, 'senha:joaopedropiressa@gmail.com', 0x00000000000000000000000000000001, NULL, 1, '2026-06-12 00:04:53'),
(9, 'senha:joaopedropiressa@gmail.com', 0x00000000000000000000000000000001, NULL, 1, '2026-06-12 00:06:32'),
(10, 'joaopedropiressa@gmail.com', 0x00000000000000000000000000000001, 4, 1, '2026-06-12 00:14:26'),
(11, 'senha:teste@example.com', 0x00000000000000000000000000000001, NULL, 1, '2026-06-12 00:28:01'),
(12, 'senha:jorge@gmail.com', 0x00000000000000000000000000000001, NULL, 1, '2026-06-12 00:28:32'),
(13, 'senha:joaopedropiressa@gmail.com', 0x00000000000000000000000000000001, NULL, 1, '2026-06-12 00:34:38'),
(14, 'senha:joaopedropiressa@gmail.com', 0x00000000000000000000000000000001, NULL, 1, '2026-06-12 00:41:16'),
(15, 'joaopedropiressa@gmail.com', 0x00000000000000000000000000000001, 4, 0, '2026-06-12 00:42:46'),
(16, 'joaopedropiressa@gmail.com', 0x00000000000000000000000000000001, 4, 1, '2026-06-12 00:42:58'),
(17, 'cadastro:chefemcasa8@gmail.com', 0x00000000000000000000000000000001, NULL, 1, '2026-06-12 00:44:43'),
(18, 'cadastro:chefemcasa8@gmail.com', 0x00000000000000000000000000000001, NULL, 1, '2026-06-12 00:46:43'),
(19, 'cadastro:teste.estudante.20260612005459@example.com', 0x00000000000000000000000000000001, NULL, 1, '2026-06-12 00:54:59'),
(20, 'cadastro:teste.empresa.20260612005531@example.com', 0x00000000000000000000000000000001, NULL, 1, '2026-06-12 00:55:31'),
(21, 'chefemcasa8@gmail.com', 0x00000000000000000000000000000001, 5, 1, '2026-06-12 00:57:25'),
(22, 'delta@gmail.com', 0x00000000000000000000000000000001, 2, 1, '2026-06-12 01:01:23'),
(23, 'senha:joaopedropiressa@gmail.com', 0x00000000000000000000000000000001, NULL, 1, '2026-06-12 01:02:27'),
(24, 'joaopedropiressa@gmail.com', 0x00000000000000000000000000000001, 4, 1, '2026-06-12 01:03:43'),
(25, 'cadastro:joaozinho@gmail.com', 0x00000000000000000000000000000001, NULL, 1, '2026-06-12 01:06:26'),
(26, 'candidatura:8', 0x00000000000000000000000000000001, NULL, 1, '2026-06-12 01:07:18'),
(27, 'chefemcasa8@gmail.com', 0x00000000000000000000000000000001, 5, 1, '2026-06-12 01:07:55'),
(28, 'joaozinho@gmail.com', 0x00000000000000000000000000000001, 8, 1, '2026-06-12 01:08:31'),
(29, 'senha:chefemcasa8@gmail.com', 0x00000000000000000000000000000001, NULL, 1, '2026-06-12 01:55:06'),
(30, 'chefemcasa8@gmail.com', 0x00000000000000000000000000000001, 5, 1, '2026-06-12 01:56:32');

-- --------------------------------------------------------

--
-- Estrutura para tabela `notificacoes`
--

CREATE TABLE `notificacoes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `usuario_id` bigint(20) UNSIGNED NOT NULL,
  `tipo` varchar(60) NOT NULL,
  `titulo` varchar(160) NOT NULL,
  `mensagem` varchar(500) NOT NULL,
  `lida_em` datetime DEFAULT NULL,
  `criado_em` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `notificacoes`
--

INSERT INTO `notificacoes` (`id`, `usuario_id`, `tipo`, `titulo`, `mensagem`, `lida_em`, `criado_em`) VALUES
(1, 1, 'candidatura_status', 'Status da candidatura atualizado', 'Sua candidatura para Estágio em Desenvolvimento Web agora esta como Aprovada.', NULL, '2026-06-11 23:58:44'),
(2, 4, 'candidatura_status', 'Status da candidatura atualizado', 'Sua candidatura para Estágio em Desenvolvimento Web agora esta como Entrevista.', '2026-06-12 01:04:47', '2026-06-12 01:01:34'),
(3, 8, 'candidatura_status', 'Status da candidatura atualizado', 'Sua candidatura para Estagiário em Desenvolvimento Web agora esta como Cancelada.', NULL, '2026-06-12 01:08:11');

-- --------------------------------------------------------

--
-- Estrutura para tabela `password_resets`
--

CREATE TABLE `password_resets` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `token_hash` char(64) NOT NULL,
  `expires_at` datetime NOT NULL,
  `used_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `password_resets`
--

INSERT INTO `password_resets` (`id`, `user_id`, `token_hash`, `expires_at`, `used_at`, `created_at`) VALUES
(1, 4, '519f767d1c5c03dcec102a086c93e11300b78caeb956e8643a95c1f599977bc9', '2026-06-12 01:04:53', '2026-06-12 00:06:32', '2026-06-12 00:04:53'),
(2, 4, 'd74394cfa69bf3167ccc33abb09a7f17e455f91eca2b38dd5a16e4a3c6eef433', '2026-06-12 01:06:32', '2026-06-12 00:14:11', '2026-06-12 00:06:32'),
(3, 1, 'bdb36c9b1cab5663610b483e7a3a937e83a4b3ea0e7ebcc9d530a467349b92a9', '2026-06-12 01:28:32', NULL, '2026-06-12 00:28:32'),
(4, 4, 'f29feb55a90e00afbf3fdec5060c64ceb592439560f8ba30b435907142aca57b', '2026-06-12 01:34:38', '2026-06-12 00:41:16', '2026-06-12 00:34:38'),
(5, 4, '89443a1058752473391cc9ade53776b1dffc741c81053a36daa9ce7c0c44a28c', '2026-06-12 01:41:16', '2026-06-12 00:42:24', '2026-06-12 00:41:16'),
(6, 4, 'b67e78f5ae28920e96f1c460a5f0d5df9b4dc81ed0291c676e2a5500bc5abe3b', '2026-06-12 02:02:27', '2026-06-12 01:03:31', '2026-06-12 01:02:27'),
(7, 5, 'e6e808328c411c06e9d2632e6a869ea03fcd9c2fbf1b48a7dacd1d666a0b4b4a', '2026-06-12 02:55:06', '2026-06-12 01:56:09', '2026-06-12 01:55:06');

-- --------------------------------------------------------

--
-- Estrutura para tabela `ratings`
--

CREATE TABLE `ratings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `application_id` bigint(20) UNSIGNED DEFAULT NULL,
  `rater_user_id` bigint(20) UNSIGNED NOT NULL,
  `rated_user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `rated_company_id` bigint(20) UNSIGNED DEFAULT NULL,
  `rating_type` enum('empresa_para_estudante','estudante_para_empresa') NOT NULL,
  `score` decimal(2,1) NOT NULL,
  `score_learning` decimal(2,1) DEFAULT NULL,
  `score_mentorship` decimal(2,1) DEFAULT NULL,
  `score_environment` decimal(2,1) DEFAULT NULL,
  `comment` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `ratings`
--

INSERT INTO `ratings` (`id`, `application_id`, `rater_user_id`, `rated_user_id`, `rated_company_id`, `rating_type`, `score`, `score_learning`, `score_mentorship`, `score_environment`, `comment`, `created_at`) VALUES
(1, 1, 2, 1, NULL, 'empresa_para_estudante', 5.0, NULL, NULL, NULL, NULL, '2026-06-11 23:58:53'),
(2, 1, 1, NULL, 1, 'estudante_para_empresa', 5.0, 4.0, 5.0, 4.0, NULL, '2026-06-12 00:04:13');

-- --------------------------------------------------------

--
-- Estrutura para tabela `student_profiles`
--

CREATE TABLE `student_profiles` (
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `course` varchar(140) DEFAULT NULL,
  `institution` varchar(140) DEFAULT NULL,
  `current_period` tinyint(3) UNSIGNED DEFAULT NULL,
  `graduation_forecast` varchar(80) DEFAULT NULL,
  `performance_index` decimal(3,1) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `state` char(2) DEFAULT NULL,
  `neighborhood` varchar(100) DEFAULT NULL,
  `cep` varchar(12) DEFAULT NULL,
  `latitude` decimal(10,7) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `interests` varchar(255) DEFAULT NULL,
  `availability` varchar(80) DEFAULT NULL,
  `preferred_modality` enum('presencial','remoto','hibrido','qualquer') NOT NULL DEFAULT 'qualquer',
  `max_distance_km` smallint(5) UNSIGNED NOT NULL DEFAULT 15,
  `min_scholarship` decimal(10,2) NOT NULL DEFAULT 800.00,
  `bio` text DEFAULT NULL,
  `portfolio_url` varchar(255) DEFAULT NULL,
  `accessibility_libras` tinyint(1) NOT NULL DEFAULT 0,
  `profile_completeness` tinyint(3) UNSIGNED NOT NULL DEFAULT 25,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `student_profiles`
--

INSERT INTO `student_profiles` (`user_id`, `course`, `institution`, `current_period`, `graduation_forecast`, `performance_index`, `city`, `state`, `neighborhood`, `cep`, `latitude`, `longitude`, `interests`, `availability`, `preferred_modality`, `max_distance_km`, `min_scholarship`, `bio`, `portfolio_url`, `accessibility_libras`, `profile_completeness`, `created_at`, `updated_at`) VALUES
(1, 'Ciência da Computação', 'Unesp', 1, '2029', NULL, 'Presidente Prudente', 'SP', 'Centro', '19100-000', NULL, NULL, 'Programação', 'Matutina', 'hibrido', 15, 800.00, 'Sou muito bom', 'https://github.com/joaoppiress/estagio-match', 0, 100, '2026-06-11 23:20:08', '2026-06-11 23:22:09'),
(3, 'Ciência da Computação', '', 1, NULL, NULL, 'Presidente Prudente', 'SP', NULL, NULL, -22.1225167, -51.3882528, '', NULL, 'qualquer', 15, 800.00, NULL, NULL, 0, 44, '2026-06-11 23:56:56', '2026-06-11 23:56:56'),
(4, 'Análise e Desenvolvimento de Sistemas', '', 1, NULL, NULL, 'Presidente Prudente', 'SP', NULL, NULL, -22.1225167, -51.3882528, '', NULL, 'qualquer', 15, 800.00, NULL, NULL, 0, 44, '2026-06-12 00:02:14', '2026-06-12 00:02:14'),
(6, 'Sistemas de Informacao', '', 3, NULL, NULL, 'Presidente Prudente', 'SP', NULL, NULL, -22.1225167, -51.3882528, '', NULL, 'qualquer', 15, 800.00, NULL, NULL, 0, 44, '2026-06-12 00:55:00', '2026-06-12 00:55:00'),
(8, 'Ciência da Computação', '', 1, NULL, NULL, 'Presidente Prudente', 'SP', NULL, NULL, -22.1225167, -51.3882528, '', NULL, 'qualquer', 15, 800.00, NULL, NULL, 0, 44, '2026-06-12 01:06:26', '2026-06-12 01:06:26');

-- --------------------------------------------------------

--
-- Estrutura para tabela `student_skills`
--

CREATE TABLE `student_skills` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `skill` varchar(80) NOT NULL,
  `level` enum('basico','intermediario','avancado') NOT NULL DEFAULT 'basico',
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `student_skills`
--

INSERT INTO `student_skills` (`id`, `user_id`, `skill`, `level`, `created_at`) VALUES
(1, 1, 'Python', 'intermediario', '2026-06-11 23:22:09');

-- --------------------------------------------------------

--
-- Estrutura para tabela `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `role` enum('estudante','empresa','admin') NOT NULL DEFAULT 'estudante',
  `name` varchar(140) NOT NULL,
  `email` varchar(190) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `status` enum('active','blocked','deleted') NOT NULL DEFAULT 'active',
  `email_verified_at` datetime DEFAULT NULL,
  `lgpd_accepted_at` datetime DEFAULT NULL,
  `last_login_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `users`
--

INSERT INTO `users` (`id`, `role`, `name`, `email`, `password_hash`, `status`, `email_verified_at`, `lgpd_accepted_at`, `last_login_at`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'estudante', 'Jorge Silva', 'jorge@gmail.com', '$argon2id$v=19$m=65536,t=4,p=2$QUlCQnNkTVYwZldrV0diYg$JrDLUpfXxhrODKmLs7yKzPsZjdz/P5yqVsDr1bjD/qs', 'active', '2026-06-12 00:58:38', '2026-06-11 23:20:08', '2026-06-12 00:03:33', '2026-06-11 23:20:08', '2026-06-12 00:58:38', NULL),
(2, 'empresa', 'José', 'delta@gmail.com', '$argon2id$v=19$m=65536,t=4,p=2$d1pUaXMvbEZDQkFKay9GZg$sZ/j8eesucG13uTGHNZkbwvhJwL/nWvigq4sTTvUsAY', 'active', '2026-06-12 00:58:38', '2026-06-11 23:23:47', '2026-06-12 01:01:23', '2026-06-11 23:23:47', '2026-06-12 01:01:23', NULL),
(3, 'estudante', 'Bruno', 'bruno@gmail.com', '$argon2id$v=19$m=65536,t=4,p=2$T1cyVlNCcTlVT0VYdW5EYw$I4OvPlROSD5qagLz3ne2RiEnR6MonJN1K2C3SyCYoNI', 'active', '2026-06-12 00:58:38', '2026-06-11 23:56:55', NULL, '2026-06-11 23:56:55', '2026-06-12 00:58:38', NULL),
(4, 'estudante', 'João Pires', 'joaopedropiressa@gmail.com', '$argon2id$v=19$m=65536,t=4,p=2$bERHLm1uSjloQWFTYi9CRg$CUnpjxlHn6znXN/tJm//5ygPSxI89irzy0HzAL4ZM3E', 'active', '2026-06-12 00:58:38', '2026-06-12 00:02:14', '2026-06-12 01:03:43', '2026-06-12 00:02:14', '2026-06-12 01:03:43', NULL),
(5, 'empresa', 'João', 'chefemcasa8@gmail.com', '$argon2id$v=19$m=65536,t=4,p=2$dE8wNzFYNkMxcVhTekMwRg$0A1028X8aD2zLJU3WdQIzLdSAE+BExV0j9meM0Ix7Gw', 'active', '2026-06-12 00:58:38', '2026-06-12 00:46:43', '2026-06-12 01:56:32', '2026-06-12 00:46:43', '2026-06-12 01:56:32', NULL),
(6, 'estudante', 'Estudante Verificacao', 'teste.estudante.20260612005459@example.com', '$argon2id$v=19$m=65536,t=4,p=2$UUdmeTIzM05TLk4zcmZXYg$NdYvrTy9cRvS1euxwSr3KNnhe+OM2JTp6C6DFl0KySo', 'active', '2026-06-12 00:58:38', '2026-06-12 00:54:59', NULL, '2026-06-12 00:54:59', '2026-06-12 00:58:38', NULL),
(7, 'empresa', 'Responsavel Empresa', 'teste.empresa.20260612005531@example.com', '$argon2id$v=19$m=65536,t=4,p=2$WkhDZTEvTUc4Vy9NdlJJcQ$cGu9du5zeX+pnBP1loSYrCUUGD2SBk2rU4M47ZCIBoU', 'active', '2026-06-12 00:58:38', '2026-06-12 00:55:31', NULL, '2026-06-12 00:55:31', '2026-06-12 00:58:38', NULL),
(8, 'estudante', 'João Silva', 'joaozinho@gmail.com', '$argon2id$v=19$m=65536,t=4,p=2$Z1lobXY2aXAvbjg3c1JhRg$uFDvo5cmG9Sshk3pQ+oQ3Q2xD2S+eKwJG5ldA2GQNpI', 'active', NULL, '2026-06-12 01:06:26', '2026-06-12 01:08:31', '2026-06-12 01:06:26', '2026-06-12 01:08:31', NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `vacancies`
--

CREATE TABLE `vacancies` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(160) NOT NULL,
  `area` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `responsibilities` text DEFAULT NULL,
  `requirements` text DEFAULT NULL,
  `modality` enum('presencial','remoto','hibrido') NOT NULL DEFAULT 'presencial',
  `city` varchar(100) DEFAULT NULL,
  `state` char(2) DEFAULT NULL,
  `address` varchar(190) DEFAULT NULL,
  `latitude` decimal(10,7) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `period` varchar(80) DEFAULT NULL,
  `duration_months` tinyint(3) UNSIGNED DEFAULT NULL,
  `start_date_label` varchar(80) DEFAULT NULL,
  `scholarship` decimal(10,2) NOT NULL DEFAULT 0.00,
  `workload` varchar(80) DEFAULT NULL,
  `transport_included` tinyint(1) NOT NULL DEFAULT 0,
  `status` enum('draft','active','paused','expired','closed') NOT NULL DEFAULT 'active',
  `is_boosted` tinyint(1) NOT NULL DEFAULT 0,
  `published_at` datetime DEFAULT NULL,
  `expires_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `vacancies`
--

INSERT INTO `vacancies` (`id`, `company_id`, `title`, `area`, `description`, `responsibilities`, `requirements`, `modality`, `city`, `state`, `address`, `latitude`, `longitude`, `period`, `duration_months`, `start_date_label`, `scholarship`, `workload`, `transport_included`, `status`, `is_boosted`, `published_at`, `expires_at`, `created_at`, `updated_at`) VALUES
(1, 1, 'Estágio em Desenvolvimento Web', 'Tecnologia', 'Quero gente boa', 'Não ser vibe coder', 'Saber Programar', 'hibrido', 'Presidente Prudente', 'SP', 'Rua Arlindo Lindoso Bonito', NULL, NULL, 'Manhã', 12, 'Imediato', 1000.00, '6h/dia', 1, 'active', 1, '2026-06-11 23:25:00', '2026-07-11 23:25:00', '2026-06-11 23:25:00', '2026-06-11 23:58:27'),
(2, 3, 'Estagio QA Verificacao', 'Tecnologia', 'Vaga criada para testar empresa com e-mail verificado.', '', 'PHP, SQL', 'remoto', 'Presidente Prudente', 'SP', '', -22.1225167, -51.3882528, '', 6, 'Imediato', 1200.00, '', 0, 'active', 0, '2026-06-12 00:55:36', '2026-07-12 00:55:36', '2026-06-12 00:55:36', '2026-06-12 00:55:36'),
(3, 2, 'Suporte', 'Tecnologia', 'Suporte', 'Aprender', 'Saber', 'presencial', 'Presidente Prudente', 'SP', '', -22.1225167, -51.3882528, 'Manhã', 12, 'Imediato', 1000.00, '6h/dia', 1, 'active', 0, '2026-06-12 00:59:25', '2026-07-12 00:59:25', '2026-06-12 00:59:25', '2026-06-12 00:59:25'),
(4, 2, 'Estagiário em Desenvolvimento Web', 'Tecnologia', 'Desenvolvimento', 'Desenvolver', 'Saber desenvolver', 'presencial', 'Presidente Prudente', 'SP', '', -22.1225167, -51.3882528, 'Manhã', 12, 'Imediato', 1200.00, '6h/dia', 1, 'active', 1, '2026-06-12 01:00:49', '2026-07-12 01:00:49', '2026-06-12 01:00:49', '2026-06-12 01:08:00');

-- --------------------------------------------------------

--
-- Estrutura para tabela `vacancy_skills`
--

CREATE TABLE `vacancy_skills` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `vacancy_id` bigint(20) UNSIGNED NOT NULL,
  `skill` varchar(80) NOT NULL,
  `is_required` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `vacancy_skills`
--

INSERT INTO `vacancy_skills` (`id`, `vacancy_id`, `skill`, `is_required`) VALUES
(1, 1, 'Python', 1),
(2, 3, 'PHP', 1),
(3, 4, 'PHP', 1);

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `applications`
--
ALTER TABLE `applications`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `applications_unique` (`vacancy_id`,`student_id`),
  ADD KEY `applications_student_status_idx` (`student_id`,`status`),
  ADD KEY `applications_vacancy_status_idx` (`vacancy_id`,`status`);

--
-- Índices de tabela `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `audit_logs_event_idx` (`event`,`created_at`),
  ADD KEY `audit_logs_user_idx` (`user_id`,`created_at`);

--
-- Índices de tabela `companies`
--
ALTER TABLE `companies`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `companies_user_unique` (`user_id`),
  ADD KEY `companies_location_idx` (`city`,`state`);

--
-- Índices de tabela `email_verifications`
--
ALTER TABLE `email_verifications`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email_verifications_token_unique` (`token_hash`),
  ADD KEY `email_verifications_user_fk` (`user_id`);

--
-- Índices de tabela `login_attempts`
--
ALTER TABLE `login_attempts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `login_attempts_email_ip_idx` (`email`,`attempted_at`),
  ADD KEY `login_attempts_user_idx` (`user_id`);

--
-- Índices de tabela `notificacoes`
--
ALTER TABLE `notificacoes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notificacoes_usuario_lida_idx` (`usuario_id`,`lida_em`);

--
-- Índices de tabela `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `password_resets_token_unique` (`token_hash`),
  ADD KEY `password_resets_user_fk` (`user_id`);

--
-- Índices de tabela `ratings`
--
ALTER TABLE `ratings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ratings_application_fk` (`application_id`),
  ADD KEY `ratings_rater_fk` (`rater_user_id`),
  ADD KEY `ratings_company_idx` (`rated_company_id`,`rating_type`),
  ADD KEY `ratings_user_idx` (`rated_user_id`,`rating_type`);

--
-- Índices de tabela `student_profiles`
--
ALTER TABLE `student_profiles`
  ADD PRIMARY KEY (`user_id`),
  ADD KEY `student_profiles_location_idx` (`city`,`state`),
  ADD KEY `student_profiles_course_idx` (`course`);

--
-- Índices de tabela `student_skills`
--
ALTER TABLE `student_skills`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `student_skills_unique` (`user_id`,`skill`);

--
-- Índices de tabela `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `users_role_status_idx` (`role`,`status`);

--
-- Índices de tabela `vacancies`
--
ALTER TABLE `vacancies`
  ADD PRIMARY KEY (`id`),
  ADD KEY `vacancies_company_fk` (`company_id`),
  ADD KEY `vacancies_status_idx` (`status`,`published_at`),
  ADD KEY `vacancies_area_idx` (`area`),
  ADD KEY `vacancies_location_idx` (`city`,`state`),
  ADD KEY `vacancies_status_boosted_published_idx` (`status`,`is_boosted`,`published_at`);
ALTER TABLE `vacancies` ADD FULLTEXT KEY `vacancies_search_fulltext` (`title`,`description`,`requirements`,`area`);

--
-- Índices de tabela `vacancy_skills`
--
ALTER TABLE `vacancy_skills`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `vacancy_skills_unique` (`vacancy_id`,`skill`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `applications`
--
ALTER TABLE `applications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=76;

--
-- AUTO_INCREMENT de tabela `companies`
--
ALTER TABLE `companies`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `email_verifications`
--
ALTER TABLE `email_verifications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `login_attempts`
--
ALTER TABLE `login_attempts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT de tabela `notificacoes`
--
ALTER TABLE `notificacoes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de tabela `ratings`
--
ALTER TABLE `ratings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `student_skills`
--
ALTER TABLE `student_skills`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de tabela `vacancies`
--
ALTER TABLE `vacancies`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `vacancy_skills`
--
ALTER TABLE `vacancy_skills`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `applications`
--
ALTER TABLE `applications`
  ADD CONSTRAINT `applications_student_fk` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `applications_vacancy_fk` FOREIGN KEY (`vacancy_id`) REFERENCES `vacancies` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD CONSTRAINT `audit_logs_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Restrições para tabelas `companies`
--
ALTER TABLE `companies`
  ADD CONSTRAINT `companies_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `email_verifications`
--
ALTER TABLE `email_verifications`
  ADD CONSTRAINT `email_verifications_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `login_attempts`
--
ALTER TABLE `login_attempts`
  ADD CONSTRAINT `login_attempts_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Restrições para tabelas `notificacoes`
--
ALTER TABLE `notificacoes`
  ADD CONSTRAINT `notificacoes_usuario_fk` FOREIGN KEY (`usuario_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `password_resets`
--
ALTER TABLE `password_resets`
  ADD CONSTRAINT `password_resets_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `ratings`
--
ALTER TABLE `ratings`
  ADD CONSTRAINT `ratings_application_fk` FOREIGN KEY (`application_id`) REFERENCES `applications` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `ratings_company_fk` FOREIGN KEY (`rated_company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ratings_rater_fk` FOREIGN KEY (`rater_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ratings_user_fk` FOREIGN KEY (`rated_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `student_profiles`
--
ALTER TABLE `student_profiles`
  ADD CONSTRAINT `student_profiles_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `student_skills`
--
ALTER TABLE `student_skills`
  ADD CONSTRAINT `student_skills_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `vacancies`
--
ALTER TABLE `vacancies`
  ADD CONSTRAINT `vacancies_company_fk` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `vacancy_skills`
--
ALTER TABLE `vacancy_skills`
  ADD CONSTRAINT `vacancy_skills_vacancy_fk` FOREIGN KEY (`vacancy_id`) REFERENCES `vacancies` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
