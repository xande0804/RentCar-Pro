-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3308:3308
-- Tempo de geração: 26-Nov-2025 às 03:53
-- Versão do servidor: 10.4.32-MariaDB
-- versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `locarbd`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `tbl_carros`
--

CREATE TABLE `tbl_carros` (
  `cod_carro` int(11) NOT NULL,
  `marca` varchar(100) NOT NULL,
  `modelo` varchar(100) NOT NULL,
  `categoria` enum('Hatch','Sedan','SUV','Pickup','Minivan','Van','Perua','Conversivel','Coupe','Esportivo','Luxo','Utilitario','OffRoad') NOT NULL,
  `ano` year(4) NOT NULL,
  `cor` varchar(50) NOT NULL,
  `combustivel` enum('gasolina','alcool','flex','diesel') NOT NULL,
  `cambio` enum('manual','automatico') NOT NULL,
  `ar_condicionado` tinyint(1) DEFAULT 1,
  `preco_diaria` decimal(10,2) NOT NULL,
  `status` enum('disponivel','alugado','reservado','manutencao','documento_atrasado') DEFAULT 'disponivel',
  `km_total` int(11) DEFAULT 0,
  `descricao` text DEFAULT NULL,
  `imagem_url` varchar(1024) DEFAULT NULL,
  `data_cadastro` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `tbl_carros`
--

INSERT INTO `tbl_carros` (`cod_carro`, `marca`, `modelo`, `categoria`, `ano`, `cor`, `combustivel`, `cambio`, `ar_condicionado`, `preco_diaria`, `status`, `km_total`, `descricao`, `imagem_url`, `data_cadastro`) VALUES
(1, 'Honda', 'Civic', 'Sedan', '2024', 'Preto', 'flex', 'automatico', 1, 237.00, 'disponivel', 2500000, '', 'https://mlqt0se4pk9p.i.optimole.com/q:85/https://www.autodata.com.br/admin/imagens/noticias/honda-civic-2020-tem-nova-versao-de-entrada_68fb894f909bff337c86609fe56aea1b.jpg', '2025-10-01 15:13:14'),
(3, 'Bicicleta com motor', 'Multilaser', 'Luxo', '2010', 'amarela', 'alcool', 'automatico', 0, 1.00, 'disponivel', 100, '', 'https://img.odcdn.com.br/wp-content/uploads/2023/03/ebike-flash.jpg', '2025-10-02 14:24:09'),
(8, 'Honda', 'HRV', 'SUV', '2025', 'Vinho', 'flex', 'automatico', 0, 250.87, 'disponivel', 2000, '', 'https://cdn.motor1.com/images/mgl/nAylgy/s3/honda-hr-v-touring-2023.jpg', '2025-10-09 16:55:46'),
(9, 'Chevrolet', 'Onix', 'Hatch', '2024', 'Branco', 'gasolina', 'manual', 1, 127.00, 'disponivel', 1234, '', 'https://cdn.motor1.com/images/mgl/xqowy2/s3/chevrolet-onix-plus-premier-2023-vs.-hyundai-hb20s-platinum-plus-2023.jpg', '2025-10-15 15:07:53'),
(13, 'Ford', 'Ranger Raptor', 'OffRoad', '2025', 'Preto', 'flex', 'automatico', 1, 324.88, 'disponivel', 20000, '', 'https://cdn.motor1.com/images/mgl/NGjEPY/s3/2024-ford-ranger-raptor.jpg', '2025-11-05 21:18:26'),
(14, 'Volkswagem', 'Nivus', 'Luxo', '2026', 'Preto', 'flex', 'automatico', 1, 1000.00, 'disponivel', 10000, '', 'https://img.olx.com.br/thumbs700x500/78/787576948178139.webp', '2025-11-17 14:38:32');

-- --------------------------------------------------------

--
-- Estrutura da tabela `tbl_enderecos`
--

CREATE TABLE `tbl_enderecos` (
  `cod_endereco` int(11) NOT NULL,
  `cod_usuario` int(11) NOT NULL,
  `cep` varchar(9) DEFAULT NULL,
  `logradouro` varchar(255) DEFAULT NULL,
  `numero` varchar(20) DEFAULT NULL,
  `complemento` varchar(100) DEFAULT NULL,
  `bairro` varchar(100) DEFAULT NULL,
  `cidade` varchar(100) DEFAULT NULL,
  `estado` varchar(2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `tbl_enderecos`
--

INSERT INTO `tbl_enderecos` (`cod_endereco`, `cod_usuario`, `cep`, `logradouro`, `numero`, `complemento`, `bairro`, `cidade`, `estado`) VALUES
(4, 101, '72260-631', 'Quadra QNO 16 Conjunto 31', '16', 'as', 'Ceilândia Norte (Ceilândia)', 'Brasília', 'DF'),
(6, 138, '72260-631', 'Quadra QNO 16 Conjunto 31', '16', 'casa 1', 'Ceilândia Norte (Ceilândia)', 'Brasília', 'DF'),
(7, 139, '72260-631', 'Quadra QNO 16 Conjunto 31', '16', 'casa 04', 'Ceilândia Norte (Ceilândia)', 'Brasília', 'DF'),
(9, 146, '72260-631', 'Quadra QNO 16 Conjunto 31', '04', 'casa 04', 'Ceilândia Norte (Ceilândia)', 'Brasília', 'DF'),
(10, 148, '72260-631', 'Quadra QNO 16 Conjunto 31', '04', 'casa 04', 'Ceilândia Norte (Ceilândia)', 'Brasília', 'DF'),
(11, 149, '72260-631', 'Quadra QNO 16 Conjunto 31', 'casa 04', '04', 'Ceilândia Norte (Ceilândia)', 'Brasília', 'DF'),
(12, 150, '72160-804', 'Quadra QNL 18 Conjunto D', '15', 'barraco de madeira', 'Taguatinga Norte (Taguatinga)', 'Brasília', 'DF'),
(13, 151, '72240-823', 'Quadra QNP 9 Conjunto X', '10', '', 'Ceilândia Norte (Ceilândia)', 'Brasília', 'DF'),
(14, 152, '72260-631', 'Quadra QNO 16 Conjunto 31', 'casa 04', '4', 'Ceilândia Norte (Ceilândia)', 'Brasília', 'DF'),
(15, 153, '72015-565', 'Quadra CSB 6', '10', '', 'Taguatinga Sul (Taguatinga)', 'Brasília', 'DF'),
(16, 154, '72260-631', 'Quadra QNO 16 Conjunto 31', '04', '', 'Ceilândia Norte (Ceilândia)', 'Brasília', 'DF'),
(17, 155, '72260-631', 'Quadra QNO 16 Conjunto 31', '04', '', 'Ceilândia Norte (Ceilândia)', 'Brasília', 'DF'),
(18, 156, '72260-631', 'Quadra QNO 16 Conjunto 31', '04', '', 'Ceilândia Norte (Ceilândia)', 'Brasília', 'DF'),
(19, 157, '72260-631', 'Quadra QNO 16 Conjunto 31', '04', '', 'Ceilândia Norte (Ceilândia)', 'Brasília', 'DF'),
(20, 158, '72260-631', 'Quadra QNO 16 Conjunto 31', '04', '0', 'Ceilândia Norte (Ceilândia)', 'Brasília', 'DF'),
(21, 159, '72260-631', 'Quadra QNO 16 Conjunto 31', '16', 'casa 04', 'Ceilândia Norte (Ceilândia)', 'Brasília', 'DF'),
(22, 160, '72260-631', 'Quadra QNO 16 Conjunto 31', '16', '1', 'Ceilândia Norte (Ceilândia)', 'Brasília', 'DF'),
(23, 161, '72260-631', 'Quadra QNO 16 Conjunto 31', '16', '1', 'Ceilândia Norte (Ceilândia)', 'Brasília', 'DF'),
(24, 162, '72260-631', 'Quadra QNO 16 Conjunto 31', '16', '1', 'Ceilândia Norte (Ceilândia)', 'Brasília', 'DF'),
(25, 163, '72260-631', 'Quadra QNO 16 Conjunto 31', '04', '04', 'Ceilândia Norte (Ceilândia)', 'Brasília', 'DF'),
(26, 164, '72260-631', 'Quadra QNO 16 Conjunto 31', '04', '', 'Ceilândia Norte (Ceilândia)', 'Brasília', 'DF'),
(27, 168, '72260-631', 'Quadra QNO 16 Conjunto 31', '04', '04', 'Ceilândia Norte (Ceilândia)', 'Brasília', 'DF'),
(28, 176, '72260-631', 'Quadra QNO 16 Conjunto 31', '16', '', 'Ceilândia Norte (Ceilândia)', 'Brasília', 'DF'),
(29, 177, '72260-631', 'Quadra QNO 16 Conjunto 31', '05', '', 'Ceilândia Norte (Ceilândia)', 'Brasília', 'DF'),
(30, 178, '72260-631', 'Quadra QNO 16 Conjunto 31', '04', '', 'Ceilândia Norte (Ceilândia)', 'Brasília', 'DF'),
(31, 179, '72260-631', 'Quadra QNO 16 Conjunto 31', '4', '', 'Ceilândia Norte (Ceilândia)', 'Brasília', 'DF'),
(32, 182, '72260-631', 'Quadra QNO 16 Conjunto 31', '04', 'casa 04', 'Ceilândia Norte (Ceilândia)', 'Brasília', 'DF'),
(33, 183, '72260-631', 'Quadra QNO 16 Conjunto 31', '2', '', 'Ceilândia Norte (Ceilândia)', 'Brasília', 'DF'),
(34, 184, '72260-631', 'Quadra QNO 16 Conjunto 31', '4', '', 'Ceilândia Norte (Ceilândia)', 'Brasília', 'DF'),
(35, 185, '72260-631', 'Quadra QNO 16 Conjunto 31', '04', '', 'Ceilândia Norte (Ceilândia)', 'Brasília', 'DF'),
(36, 187, '72260-631', 'Quadra QNO 16 Conjunto 31', '04', '', 'Ceilândia Norte (Ceilândia)', 'Brasília', 'DF'),
(37, 188, '72160-804', 'Quadra QNL 18 Conjunto D', '89', 'bvxfc', 'Taguatinga Norte (Taguatinga)', 'Brasília', 'DF'),
(38, 189, '72260-631', 'Quadra QNO 16 Conjunto 31', '16', '', 'Ceilândia Norte (Ceilândia)', 'Brasília', 'DF'),
(39, 190, '72260-631', 'Quadra QNO 16 Conjunto 31', '16', '', 'Ceilândia Norte (Ceilândia)', 'Brasília', 'DF'),
(40, 191, '72260-631', '', '7', 'Conjunto H', '', '', ''),
(41, 140, '72260-631', 'Quadra QNO 16 Conjunto 31', '7', 'Conjunto H', 'Ceilândia Norte (Ceilândia)', 'Brasília', 'DF'),
(42, 193, '72260-631', 'Quadra QNO 16 Conjunto 31', '16', '', 'Ceilândia Norte (Ceilândia)', 'Brasília', 'DF');

-- --------------------------------------------------------

--
-- Estrutura da tabela `tbl_favoritos`
--

CREATE TABLE `tbl_favoritos` (
  `cod_favorito` int(11) NOT NULL,
  `cod_usuario` int(11) NOT NULL,
  `cod_carro` int(11) NOT NULL,
  `data_adicionado` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `tbl_favoritos`
--

INSERT INTO `tbl_favoritos` (`cod_favorito`, `cod_usuario`, `cod_carro`, `data_adicionado`) VALUES
(5, 139, 9, '2025-11-18 00:40:51');

-- --------------------------------------------------------

--
-- Estrutura da tabela `tbl_logs`
--

CREATE TABLE `tbl_logs` (
  `cod_log` int(11) NOT NULL,
  `cod_usuario` int(11) DEFAULT NULL,
  `acao_realizada` varchar(255) NOT NULL,
  `detalhes` text DEFAULT NULL,
  `data_hora` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Extraindo dados da tabela `tbl_logs`
--

INSERT INTO `tbl_logs` (`cod_log`, `cod_usuario`, `acao_realizada`, `detalhes`, `data_hora`) VALUES
(1, 101, 'LOGIN_SUCESSO', 'Usuário \'administrador\' realizou login no sistema.', '2025-10-16 20:04:54'),
(2, 101, 'LOGIN_SUCESSO', 'Usuário \'administrador\' realizou login no sistema.', '2025-10-18 02:06:58'),
(3, 101, 'CADASTRO_MULTA', 'Usuário \'administrador\' registrou uma multa de R$ 130 para a reserva #19.', '2025-10-18 02:30:14'),
(4, 101, 'ATUALIZACAO_RESERVA', 'Usuário \'administrador\' atualizou a reserva #19. Novo status: cancelada.', '2025-10-18 02:44:44'),
(5, 101, 'ATUALIZACAO_RESERVA', 'Usuário \'administrador\' atualizou a reserva #19. Novo status: concluida.', '2025-10-18 02:44:49'),
(6, 101, 'LOGOUT', 'Usuário \'administrador\' realizou logout do sistema.', '2025-10-18 02:47:00'),
(7, 139, 'LOGIN_SUCESSO', 'Usuário \'cliente\' realizou login no sistema.', '2025-10-18 02:47:05'),
(8, 139, 'LOGOUT', 'Usuário \'cliente\' realizou logout do sistema.', '2025-10-18 02:47:14'),
(9, 101, 'LOGIN_SUCESSO', 'Usuário \'administrador\' realizou login no sistema.', '2025-10-18 02:47:20'),
(10, 101, 'CADASTRO_MULTA', 'Usuário \'administrador\' registrou uma multa de R$ 120 para a reserva #19.', '2025-10-18 02:54:46'),
(11, 101, 'ATUALIZACAO_MULTA', 'Usuário \'administrador\' atualizou a multa #2. Novo status: cancelada.', '2025-10-18 02:55:01'),
(12, 101, 'ATUALIZACAO_USUARIO', 'Admin \'administrador\' atualizou os dados do usuário \'da\' (ID: 162).', '2025-10-18 15:56:44'),
(13, 101, 'ATUALIZACAO_USUARIO', 'Admin \'administrador\' atualizou os dados do usuário \'daa\' (ID: 162).', '2025-10-18 15:57:24'),
(14, 101, 'CADASTRO_CARRO', 'Usuário \'\' adicionou o novo carro: 1 1.', '2025-10-18 16:32:36'),
(15, 101, 'CADASTRO_ADMIN', 'Admin \'administrador\' criou o novo usuário \'a\' (aasd@gmail.com) com o perfil \'usuario\'.', '2025-10-20 17:54:19'),
(16, 101, 'ATUALIZACAO_USUARIO', 'Admin \'administrador\' atualizou os dados do usuário \'aasd\' (ID: 165).', '2025-10-20 17:54:27'),
(17, 101, 'DESATIVACAO_USUARIO', 'Admin \'administrador\' desativou a conta \'aasd\'.', '2025-10-20 17:54:32'),
(18, 168, 'CRIACAO_RESERVA', 'Usuário \'ney\' criou a reserva #1 para o carro ID 10.', '2025-10-20 20:10:48'),
(19, 168, 'CANCELAMENTO_RESERVA', 'Usuário \'ney\' cancelou a reserva #21.', '2025-10-20 20:10:52'),
(20, 139, 'CRIACAO_RESERVA', 'Usuário \'cliente\' criou a reserva #1 para o carro ID 3.', '2025-10-21 18:20:30'),
(21, 139, 'CANCELAMENTO_RESERVA', 'Usuário \'cliente\' cancelou a reserva #22.', '2025-10-21 18:20:31'),
(22, 139, 'CRIACAO_RESERVA', 'Usuário \'cliente\' criou a reserva #1 para o carro ID 3.', '2025-10-21 18:40:54'),
(23, 176, 'LOGIN_SUCESSO', 'Usuário \'xande\' realizou login no sistema.', '2025-10-22 16:08:02'),
(24, 176, 'CRIACAO_RESERVA', 'Usuário \'xande\' criou a reserva #1 para o carro ID 1.', '2025-10-22 16:08:12'),
(25, 176, 'CANCELAMENTO_RESERVA', 'Usuário \'xande\' cancelou a reserva #24.', '2025-10-22 16:08:14'),
(26, 176, 'CRIACAO_RESERVA', 'Usuário \'xande\' criou a reserva #1 para o carro ID 1.', '2025-10-22 16:08:28'),
(27, 176, 'CANCELAMENTO_RESERVA', 'Usuário \'xande\' cancelou a reserva #25.', '2025-10-22 16:08:30'),
(28, 176, 'LOGIN_SUCESSO', 'Usuário \'xande\' realizou login no sistema.', '2025-10-22 16:08:53'),
(29, 176, 'CRIACAO_RESERVA', 'Usuário \'xande\' criou a reserva #1 para o carro ID 1.', '2025-10-22 16:08:55'),
(30, 176, 'CANCELAMENTO_RESERVA', 'Usuário \'xande\' cancelou a reserva #26.', '2025-10-22 16:08:58'),
(31, 101, 'LOGIN_SUCESSO', 'Usuário \'administrador\' realizou login no sistema.', '2025-10-22 16:09:40'),
(32, 156, 'CRIACAO_RESERVA', 'Usuário \'administrador\' criou a reserva #1 para o carro ID 1.', '2025-10-22 16:09:56'),
(33, 101, 'ACESSO_NEGADO', 'Tentativa de acesso não autorizado pelo usuário \'administrador\' à página \'/Projeto/view/reservas/minhasReservas.php\'. Perfil requerido: cliente, usuario', '2025-10-22 16:09:56'),
(34, 101, 'ATUALIZACAO_RESERVA', 'Usuário \'administrador\' atualizou a reserva #23. Novo status: cancelada.', '2025-10-22 16:10:07'),
(35, 101, 'ATUALIZACAO_RESERVA', 'Usuário \'administrador\' atualizou a reserva #27. Novo status: cancelada.', '2025-10-22 16:10:11'),
(36, 101, 'ATUALIZACAO_RESERVA', 'Usuário \'administrador\' atualizou a reserva #20. Novo status: cancelada.', '2025-10-22 16:10:21'),
(37, 101, 'ATUALIZACAO_RESERVA', 'Usuário \'administrador\' atualizou a reserva #17. Novo status: cancelada.', '2025-10-22 16:10:27'),
(38, 101, 'LOGIN_SUCESSO', 'Usuário \'administrador\' realizou login no sistema.', '2025-10-22 17:58:52'),
(39, 101, 'EXCLUSAO_CARRO', 'Usuário \'\' excluiu o carro \'1 1\'.', '2025-10-22 18:31:25'),
(40, 101, 'EXCLUSAO_CARRO', 'Usuário \'\' excluiu o carro \'A a\'.', '2025-10-22 18:31:27'),
(41, 101, 'LOGIN_SUCESSO', 'Usuário \'administrador\' realizou login no sistema.', '2025-10-22 22:23:38'),
(42, 101, 'LOGIN_SUCESSO', 'Usuário \'administrador\' realizou login no sistema.', '2025-10-23 17:20:51'),
(43, 101, 'ATUALIZACAO_CARRO', 'Usuário \'administrador\' atualizou os dados do carro \'Bicicleta Multilaser\' (ID: 3).', '2025-10-23 17:33:14'),
(44, 101, 'ATUALIZACAO_CARRO', 'Usuário \'administrador\' atualizou os dados do carro \'Honda HRV\' (ID: 8).', '2025-10-23 17:33:35'),
(45, 101, 'LOGIN_SUCESSO', 'Usuário \'administrador\' realizou login no sistema.', '2025-10-23 17:35:38'),
(46, 101, 'ATUALIZACAO_USUARIO', 'Admin \'administrador\' atualizou os dados do usuário \'13\' (ID: 171).', '2025-10-23 17:36:21'),
(47, 101, 'ATUALIZACAO_CARRO', 'Usuário \'administrador\' atualizou os dados do carro \'Bicicleta Multilaser\' (ID: 3).', '2025-10-23 18:05:07'),
(48, 101, 'ATUALIZACAO_CARRO', 'Usuário \'administrador\' atualizou os dados do carro \'Chevrolet Onix\' (ID: 9).', '2025-10-23 18:05:18'),
(49, 101, 'ATUALIZACAO_CARRO', 'Usuário \'administrador\' atualizou os dados do carro \'Honda Civic\' (ID: 1).', '2025-10-23 18:05:22'),
(50, 101, 'ATUALIZACAO_CARRO', 'Usuário \'administrador\' atualizou os dados do carro \'Honda HRV\' (ID: 8).', '2025-10-23 18:05:33'),
(51, 101, 'ATUALIZACAO_CARRO', 'Usuário \'administrador\' atualizou os dados do carro \'Honda HRV\' (ID: 8).', '2025-10-23 18:05:37'),
(52, 101, 'ATUALIZACAO_CARRO', 'Usuário \'administrador\' atualizou os dados do carro \'Honda HRV\' (ID: 8).', '2025-10-23 18:06:00'),
(53, 101, 'ATUALIZACAO_CARRO', 'Usuário \'administrador\' atualizou os dados do carro \'Honda HRV\' (ID: 8).', '2025-10-23 18:06:04'),
(54, 101, 'ATUALIZACAO_CARRO', 'Usuário \'administrador\' atualizou os dados do carro \'Honda HRV\' (ID: 8).', '2025-10-23 18:06:12'),
(55, 101, 'ATUALIZACAO_CARRO', 'Usuário \'administrador\' atualizou os dados do carro \'Honda HRV\' (ID: 8).', '2025-10-23 18:06:16'),
(56, 101, 'ATUALIZACAO_CARRO', 'Usuário \'administrador\' atualizou os dados do carro \'Honda HRV\' (ID: 8).', '2025-10-23 18:06:24'),
(57, 101, 'ATUALIZACAO_CARRO', 'Usuário \'administrador\' atualizou os dados do carro \'Honda HRV\' (ID: 8).', '2025-10-23 18:06:33'),
(58, 101, 'ATUALIZACAO_CARRO', 'Usuário \'administrador\' atualizou os dados do carro \'Honda HRV\' (ID: 8).', '2025-10-23 18:06:37'),
(59, 101, 'ATUALIZACAO_CARRO', 'Usuário \'administrador\' atualizou os dados do carro \'Honda HRV\' (ID: 8).', '2025-10-23 18:06:43'),
(60, 101, 'ATUALIZACAO_CARRO', 'Usuário \'administrador\' atualizou os dados do carro \'Honda HRV\' (ID: 8).', '2025-10-23 18:06:50'),
(61, 101, 'ATUALIZACAO_CARRO', 'Usuário \'administrador\' atualizou os dados do carro \'Honda HRV\' (ID: 8).', '2025-10-23 18:06:53'),
(62, 101, 'ATUALIZACAO_CARRO', 'Usuário \'administrador\' atualizou os dados do carro \'Honda Civic\' (ID: 1).', '2025-10-23 18:07:02'),
(63, 101, 'ATUALIZACAO_CARRO', 'Usuário \'administrador\' atualizou os dados do carro \'Honda Civic\' (ID: 1).', '2025-10-23 18:07:06'),
(64, 101, 'ATUALIZACAO_CARRO', 'Usuário \'administrador\' atualizou os dados do carro \'Chevrolet Onix\' (ID: 9).', '2025-10-23 18:07:11'),
(65, 101, 'ATUALIZACAO_CARRO', 'Usuário \'administrador\' atualizou os dados do carro \'Chevrolet Onix\' (ID: 9).', '2025-10-23 18:07:16'),
(66, 101, 'ATUALIZACAO_CARRO', 'Usuário \'administrador\' atualizou os dados do carro \'Chevrolet Onix\' (ID: 9).', '2025-10-23 18:07:21'),
(67, 101, 'ATUALIZACAO_CARRO', 'Usuário \'administrador\' atualizou os dados do carro \'Chevrolet Onix\' (ID: 9).', '2025-10-23 18:07:28'),
(68, 101, 'ATUALIZACAO_CARRO', 'Usuário \'administrador\' atualizou os dados do carro \'Chevrolet Onix\' (ID: 9).', '2025-10-23 18:07:31'),
(69, 101, 'ATUALIZACAO_CARRO', 'Usuário \'administrador\' atualizou os dados do carro \'Chevrolet Onix\' (ID: 9).', '2025-10-23 18:07:34'),
(70, 101, 'ATUALIZACAO_CARRO', 'Usuário \'administrador\' atualizou os dados do carro \'Bicicleta Multilaser\' (ID: 3).', '2025-10-23 18:11:51'),
(71, 101, 'ATUALIZACAO_CARRO', 'Usuário \'administrador\' atualizou os dados do carro \'Bicicleta Multilaser\' (ID: 3).', '2025-10-23 18:11:58'),
(72, 101, 'ATUALIZACAO_CARRO', 'Usuário \'administrador\' atualizou os dados do carro \'Bicicleta Multilaser\' (ID: 3).', '2025-10-23 18:12:01'),
(73, 101, 'ATUALIZACAO_CARRO', 'Usuário \'administrador\' atualizou os dados do carro \'Bicicleta Multilaser\' (ID: 3).', '2025-10-23 18:12:06'),
(74, 101, 'ATUALIZACAO_CARRO', 'Usuário \'administrador\' atualizou os dados do carro \'Bicicleta Multilaser\' (ID: 3).', '2025-10-23 18:12:10'),
(75, 101, 'ATUALIZACAO_CARRO', 'Usuário \'administrador\' atualizou os dados do carro \'Bicicleta Multilaser\' (ID: 3).', '2025-10-23 18:12:14'),
(76, 101, 'ATUALIZACAO_CARRO', 'Usuário \'administrador\' atualizou os dados do carro \'Bicicleta Multilaser\' (ID: 3).', '2025-10-23 18:12:20'),
(77, 101, 'ATUALIZACAO_CARRO', 'Usuário \'administrador\' atualizou os dados do carro \'Bicicleta Multilaser\' (ID: 3).', '2025-10-23 18:12:24'),
(78, 101, 'ATUALIZACAO_CARRO', 'Usuário \'administrador\' atualizou os dados do carro \'Bicicleta Multilaser\' (ID: 3).', '2025-10-23 18:19:24'),
(79, 101, 'ATUALIZACAO_CARRO', 'Usuário \'administrador\' atualizou os dados do carro \'Bicicleta Multilaser\' (ID: 3).', '2025-10-23 18:19:28'),
(80, 101, 'ATUALIZACAO_CARRO', 'Usuário \'administrador\' atualizou os dados do carro \'Bicicleta Multilaser\' (ID: 3).', '2025-10-23 18:29:07'),
(81, 101, 'ATUALIZACAO_CARRO', 'Usuário \'administrador\' atualizou os dados do carro \'Bicicleta Multilaser\' (ID: 3).', '2025-10-23 18:29:11'),
(82, 101, 'ATUALIZACAO_CARRO', 'Usuário \'administrador\' atualizou os dados do carro \'Bicicleta Multilaser\' (ID: 3).', '2025-10-23 18:29:16'),
(83, 101, 'LOGIN_SUCESSO', 'Usuário \'administrador\' realizou login no sistema.', '2025-10-23 19:23:54'),
(84, 177, 'LOGIN_SUCESSO', 'Usuário \'weyller\' realizou login no sistema.', '2025-10-23 19:26:03'),
(85, 177, 'CRIACAO_RESERVA', 'Usuário \'weyller\' criou a reserva #1 para o carro ID 9.', '2025-10-23 19:26:31'),
(86, 178, 'LOGIN_SUCESSO', 'Usuário \'weyller2\' realizou login no sistema.', '2025-10-23 19:32:21'),
(87, 177, 'LOGIN_SUCESSO', 'Usuário \'weyller\' realizou login no sistema.', '2025-10-23 19:41:30'),
(88, 101, 'LOGIN_SUCESSO', 'Usuário \'administrador\' realizou login no sistema.', '2025-10-23 20:38:11'),
(89, 101, 'LOGIN_SUCESSO', 'Usuário \'administrador\' realizou login no sistema.', '2025-10-23 23:32:12'),
(90, 101, 'ATUALIZACAO_USUARIO', 'Admin \'administrador\' atualizou os dados do usuário \'13\' (ID: 171).', '2025-10-23 23:32:44'),
(91, 101, 'ATUALIZACAO_USUARIO', 'Admin \'administrador\' atualizou os dados do usuário \'13\' (ID: 171).', '2025-10-23 23:32:54'),
(92, 179, 'LOGIN_SUCESSO', 'Usuário \'vailson\' realizou login no sistema.', '2025-10-24 19:55:22'),
(93, 179, 'CRIACAO_RESERVA', 'Usuário \'vailson\' criou a reserva #1 para o carro ID 8.', '2025-10-24 19:55:52'),
(94, 139, 'LOGIN_SUCESSO', 'Usuário \'cliente\' realizou login no sistema.', '2025-10-27 17:31:16'),
(95, 180, 'LOGIN_SUCESSO', 'Usuário \'juquinha\' realizou login no sistema.', '2025-10-27 19:49:35'),
(96, 101, 'LOGIN_SUCESSO', 'Usuário \'administrador\' realizou login no sistema.', '2025-10-27 19:56:16'),
(97, NULL, 'LOGIN_FALHA', 'Tentativa de login falhou para o e-mail \'cliente@gmail.com\'.', '2025-10-27 20:08:50'),
(98, 139, 'LOGIN_SUCESSO', 'Usuário \'cliente\' realizou login no sistema.', '2025-10-27 20:08:58'),
(99, 181, 'LOGIN_SUCESSO', 'Usuário \'aa\' realizou login no sistema.', '2025-10-27 20:15:04'),
(100, 101, 'LOGIN_SUCESSO', 'Usuário \'administrador\' realizou login no sistema.', '2025-10-28 18:04:31'),
(101, 139, 'LOGIN_SUCESSO', 'Usuário \'cliente\' realizou login no sistema.', '2025-10-28 18:24:05'),
(102, 139, 'LOGIN_SUCESSO', 'Usuário \'cliente\' realizou login no sistema.', '2025-10-29 17:19:04'),
(103, 139, 'LOGIN_SUCESSO', 'Usuário \'cliente\' realizou login no sistema.', '2025-10-30 02:35:43'),
(104, 139, 'CRIACAO_RESERVA', 'Usuário \'cliente\' criou a reserva #1 para o carro ID 1.', '2025-10-30 02:36:09'),
(105, 141, 'LOGIN_SUCESSO', 'Usuário \'funcionario\' realizou login no sistema.', '2025-10-30 02:42:06'),
(106, 140, 'LOGIN_SUCESSO', 'Usuário \'gerente\' realizou login no sistema.', '2025-10-30 02:42:18'),
(107, 101, 'LOGIN_SUCESSO', 'Usuário \'administrador\' realizou login no sistema.', '2025-10-30 02:42:30'),
(108, 101, 'LOGIN_SUCESSO', 'Usuário \'administrador\' realizou login no sistema.', '2025-10-30 02:58:19'),
(109, 101, 'ATUALIZACAO_RESERVA', 'Usuário \'administrador\' atualizou a reserva #30. Novo status: concluida.', '2025-10-30 02:58:26'),
(110, 101, 'ATUALIZACAO_RESERVA', 'Usuário \'administrador\' atualizou a reserva #29. Novo status: concluida.', '2025-10-30 02:58:28'),
(111, 101, 'ATUALIZACAO_RESERVA', 'Usuário \'administrador\' atualizou a reserva #28. Novo status: concluida.', '2025-10-30 02:58:32'),
(112, 101, 'LOGIN_SUCESSO', 'Usuário \'administrador\' realizou login no sistema.', '2025-10-30 11:14:35'),
(113, 101, 'ATUALIZACAO_CARRO', 'Usuário \'administrador\' atualizou os dados do carro \'Honda HRV\' (ID: 8).', '2025-10-30 11:15:11'),
(114, 101, 'ATUALIZACAO_CARRO', 'Usuário \'administrador\' atualizou os dados do carro \'Honda HRV\' (ID: 8).', '2025-10-30 11:15:16'),
(115, 101, 'ATUALIZACAO_CARRO', 'Usuário \'administrador\' atualizou os dados do carro \'Honda HRV\' (ID: 8).', '2025-10-30 11:15:22'),
(116, 101, 'ATUALIZACAO_CARRO', 'Usuário \'administrador\' atualizou os dados do carro \'Honda Civic\' (ID: 1).', '2025-10-30 11:15:48'),
(117, 101, 'ATUALIZACAO_CARRO', 'Usuário \'administrador\' atualizou os dados do carro \'Chevrolet Onix\' (ID: 9).', '2025-10-30 11:15:53'),
(118, 101, 'ATUALIZACAO_CARRO', 'Usuário \'administrador\' atualizou os dados do carro \'Bicicleta Multilaser\' (ID: 3).', '2025-10-30 11:16:47'),
(119, 139, 'LOGIN_SUCESSO', 'Usuário \'cliente\' realizou login no sistema.', '2025-10-30 11:34:42'),
(120, 139, 'CRIACAO_RESERVA', 'Usuário \'cliente\' criou a reserva #1 para o carro ID 9.', '2025-10-30 11:36:07'),
(121, 139, 'CANCELAMENTO_RESERVA', 'Usuário \'cliente\' cancelou a reserva #32.', '2025-10-30 11:36:11'),
(122, 101, 'LOGIN_SUCESSO', 'Usuário \'administrador\' realizou login no sistema.', '2025-10-30 11:36:23'),
(123, 101, 'EXCLUSAO_RESERVA', 'Usuário \'administrador\' excluiu permanentemente a reserva #31.', '2025-10-30 11:36:36'),
(124, 139, 'LOGIN_SUCESSO', 'Usuário \'cliente\' realizou login no sistema.', '2025-10-30 11:54:57'),
(125, 139, 'CRIACAO_RESERVA', 'Usuário \'cliente\' criou a reserva #1 para o carro ID 3.', '2025-10-30 11:55:15'),
(126, 139, 'PAGAMENTO_RESERVA', 'Usuário \'cliente\' (ID: 139) pagou a reserva #33 via pix. Status alterado para \'aguardando_retirada\'.', '2025-10-30 12:16:01'),
(127, 101, 'LOGIN_SUCESSO', 'Usuário \'administrador\' realizou login no sistema.', '2025-10-30 12:16:39'),
(128, 139, 'LOGIN_SUCESSO', 'Usuário \'cliente\' realizou login no sistema.', '2025-10-30 12:21:31'),
(129, 139, 'CRIACAO_RESERVA', 'Usuário \'cliente\' criou a reserva #1 (valor R$ 4500) para o carro ID 1. Status: Pendente.', '2025-10-30 12:21:58'),
(130, 139, 'PAGAMENTO_RESERVA', 'Usuário \'cliente\' (ID: 139) pagou a reserva #34 via pix. Status alterado para \'aguardando_retirada\'.', '2025-10-30 12:22:08'),
(131, 139, 'CRIACAO_RESERVA', 'Usuário \'cliente\' criou a reserva #1 (valor R$ 990) para o carro ID 8. Status: Pendente.', '2025-10-30 12:59:08'),
(132, 101, 'LOGIN_SUCESSO', 'Usuário \'administrador\' realizou login no sistema.', '2025-10-30 13:04:21'),
(133, 101, 'EXCLUSAO_RESERVA', 'Usuário \'administrador\' excluiu permanentemente a reserva #34.', '2025-10-30 13:04:51'),
(134, 101, 'EXCLUSAO_RESERVA', 'Usuário \'administrador\' excluiu permanentemente a reserva #33.', '2025-10-30 13:04:53'),
(135, 139, 'LOGIN_SUCESSO', 'Usuário \'cliente\' realizou login no sistema.', '2025-10-30 13:05:09'),
(136, 139, 'CRIACAO_RESERVA', 'Usuário \'cliente\' criou a reserva #1 (valor R$ 101.25) para o carro ID 3. Status: Pendente.', '2025-10-30 13:05:30'),
(137, 139, 'PAGAMENTO_RESERVA', 'Usuário \'cliente\' (ID: 139) pagou a reserva #36 via cartao. Status alterado para \'aguardando_retirada\'.', '2025-10-30 13:05:38'),
(138, 139, 'PAGAMENTO_RESERVA', 'Usuário \'cliente\' (ID: 139) pagou a reserva #35 via cartao. Status alterado para \'aguardando_retirada\'.', '2025-10-30 13:13:21'),
(139, 101, 'LOGIN_SUCESSO', 'Usuário \'administrador\' realizou login no sistema.', '2025-10-30 13:30:25'),
(140, 101, 'MUDAR_STATUS_ATIVA', 'Usuário \'administrador\' marcou a reserva #35 como ativa.', '2025-10-30 13:30:39'),
(141, 139, 'LOGIN_SUCESSO', 'Usuário \'cliente\' realizou login no sistema.', '2025-10-30 13:32:59'),
(142, 139, 'CRIACAO_RESERVA', 'Usuário \'cliente\' criou a reserva #1 (valor R$ 4000) para o carro ID 1. Status: Pendente.', '2025-10-30 13:33:14'),
(143, 101, 'LOGIN_SUCESSO', 'Usuário \'administrador\' realizou login no sistema.', '2025-10-30 13:33:23'),
(144, 139, 'LOGIN_SUCESSO', 'Usuário \'cliente\' realizou login no sistema.', '2025-10-30 13:36:10'),
(145, 139, 'PAGAMENTO_RESERVA', 'Usuário \'cliente\' (ID: 139) pagou a reserva #37 via cartao. Status alterado para \'aguardando_retirada\'.', '2025-10-30 13:36:21'),
(146, 101, 'LOGIN_SUCESSO', 'Usuário \'administrador\' realizou login no sistema.', '2025-10-30 13:36:31'),
(147, 139, 'LOGIN_SUCESSO', 'Usuário \'cliente\' realizou login no sistema.', '2025-10-30 17:11:39'),
(148, 139, 'CRIACAO_RESERVA', 'Usuário \'cliente\' criou a reserva #1 (valor R$ 100) para o carro ID 9. Status: Pendente.', '2025-10-30 17:11:55'),
(149, 139, 'CANCELAMENTO_RESERVA', 'Usuário \'cliente\' cancelou a reserva #38.', '2025-10-30 17:21:41'),
(150, 101, 'LOGIN_SUCESSO', 'Usuário \'administrador\' realizou login no sistema.', '2025-10-30 17:28:12'),
(151, 101, 'ATUALIZACAO_RESERVA', 'Usuário \'administrador\' atualizou a reserva #35. Novo status: concluida.', '2025-10-30 17:28:28'),
(152, 139, 'LOGIN_SUCESSO', 'Usuário \'cliente\' realizou login no sistema.', '2025-10-30 17:37:01'),
(153, 139, 'CRIACAO_RESERVA', 'Usuário \'cliente\' criou a reserva #1 (valor R$ 123.75) para o carro ID 8. Status: Pendente.', '2025-10-30 17:37:18'),
(154, 139, 'PAGAMENTO_RESERVA', 'Usuário \'cliente\' (ID: 139) pagou a reserva #39 via cartao. Status alterado para \'aguardando_retirada\'.', '2025-10-30 17:37:39'),
(155, 101, 'LOGIN_SUCESSO', 'Usuário \'administrador\' realizou login no sistema.', '2025-10-30 17:37:50'),
(156, 101, 'MUDAR_STATUS_ATIVA', 'Usuário \'administrador\' marcou a reserva #37 como ativa.', '2025-10-30 17:38:04'),
(157, 101, 'MUDAR_STATUS_ATIVA', 'Usuário \'administrador\' marcou a reserva #39 como ativa.', '2025-10-30 17:38:11'),
(158, NULL, 'LOGIN_FALHA', 'Tentativa de login falhou para o e-mail \'lulu@gmail.com\'.', '2025-10-30 17:47:34'),
(159, 139, 'LOGIN_SUCESSO', 'Usuário \'cliente\' realizou login no sistema.', '2025-10-30 17:47:53'),
(160, 182, 'LOGIN_SUCESSO', 'Usuário \'aa\' realizou login no sistema.', '2025-10-30 17:49:04'),
(161, 182, 'CRIACAO_RESERVA', 'Usuário \'aa\' criou a reserva #1 (valor R$ 100) para o carro ID 9. Status: Pendente.', '2025-10-30 17:51:20'),
(162, 101, 'LOGIN_SUCESSO', 'Usuário \'administrador\' realizou login no sistema.', '2025-10-30 17:51:34'),
(163, 101, 'ATUALIZACAO_RESERVA', 'Usuário \'administrador\' atualizou a reserva #42. Novo status: concluida.', '2025-10-30 17:51:49'),
(164, 101, 'ATUALIZACAO_CARRO', 'Usuário \'administrador\' atualizou os dados do carro \'Honda Civic\' (ID: 1).', '2025-10-30 17:52:01'),
(165, 101, 'ATUALIZACAO_CARRO', 'Usuário \'administrador\' atualizou os dados do carro \'Honda HRV\' (ID: 8).', '2025-10-30 17:52:05'),
(166, 101, 'ATUALIZACAO_CARRO', 'Usuário \'administrador\' atualizou os dados do carro \'Bicicleta Multilaser\' (ID: 3).', '2025-10-30 17:52:09'),
(167, 183, 'LOGIN_SUCESSO', 'Usuário \'lu\' realizou login no sistema.', '2025-10-30 17:52:42'),
(168, 183, 'CRIACAO_RESERVA', 'Usuário \'lu\' criou a reserva #1 (valor R$ 90) para o carro ID 3. Status: Pendente.', '2025-10-30 17:53:19'),
(169, 183, 'PAGAMENTO_RESERVA', 'Usuário \'lu\' (ID: 183) pagou a reserva #44 via cartao. Status alterado para \'aguardando_retirada\'.', '2025-10-30 17:54:11'),
(170, 184, 'LOGIN_SUCESSO', 'Usuário \'aa\' realizou login no sistema.', '2025-10-30 17:54:40'),
(171, 101, 'LOGIN_SUCESSO', 'Usuário \'administrador\' realizou login no sistema.', '2025-10-30 17:55:50'),
(172, 101, 'ATUALIZACAO_RESERVA', 'Usuário \'administrador\' atualizou a reserva #37. Novo status: concluida.', '2025-10-30 17:55:56'),
(173, 101, 'ATUALIZACAO_RESERVA', 'Usuário \'administrador\' atualizou a reserva #39. Novo status: concluida.', '2025-10-30 17:56:04'),
(174, 184, 'LOGIN_SUCESSO', 'Usuário \'aa\' realizou login no sistema.', '2025-10-30 17:56:11'),
(175, 184, 'CRIACAO_RESERVA', 'Usuário \'aa\' criou a reserva #1 (valor R$ 4000) para o carro ID 1. Status: Pendente.', '2025-10-30 17:56:51'),
(176, 184, 'LOGIN_SUCESSO', 'Usuário \'aa\' realizou login no sistema.', '2025-10-30 17:57:05'),
(177, 184, 'PAGAMENTO_RESERVA', 'Usuário \'aa\' (ID: 184) pagou a reserva #45 via cartao. Status alterado para \'aguardando_retirada\'.', '2025-10-30 17:57:44'),
(178, 101, 'LOGIN_SUCESSO', 'Usuário \'administrador\' realizou login no sistema.', '2025-10-30 17:57:50'),
(179, 101, 'ATUALIZACAO_RESERVA', 'Usuário \'administrador\' atualizou a reserva #45. Novo status: concluida.', '2025-10-30 17:58:01'),
(180, 101, 'ATUALIZACAO_RESERVA', 'Usuário \'administrador\' atualizou a reserva #44. Novo status: concluida.', '2025-10-30 17:58:04'),
(181, 185, 'LOGIN_SUCESSO', 'Usuário \'chapeu\' realizou login no sistema.', '2025-10-30 17:58:55'),
(182, 185, 'CRIACAO_RESERVA', 'Usuário \'chapeu\' criou a reserva #1 (valor R$ 80) para o carro ID 9. Status: Pendente.', '2025-10-30 18:00:36'),
(183, 186, 'LOGIN_SUCESSO', 'Usuário \'madrid\' realizou login no sistema.', '2025-10-30 18:01:44'),
(184, 187, 'LOGIN_SUCESSO', 'Usuário \'madrid\' realizou login no sistema.', '2025-10-30 18:02:40'),
(185, 187, 'CRIACAO_RESERVA', 'Usuário \'madrid\' criou a reserva #1 (valor R$ 90) para o carro ID 3. Status: Pendente.', '2025-10-30 18:03:40'),
(186, 187, 'PAGAMENTO_RESERVA', 'Usuário \'madrid\' (ID: 187) pagou a reserva #48 via cartao. Status alterado para \'aguardando_retirada\'.', '2025-10-30 18:04:09'),
(187, 101, 'LOGIN_SUCESSO', 'Usuário \'administrador\' realizou login no sistema.', '2025-10-30 18:05:01'),
(188, 101, 'LOGIN_SUCESSO', 'Usuário \'administrador\' realizou login no sistema.', '2025-10-30 18:07:25'),
(189, 101, 'LOGIN_SUCESSO', 'Usuário \'administrador\' realizou login no sistema.', '2025-10-30 19:02:04'),
(190, 188, 'LOGIN_SUCESSO', 'Usuário \'Pele\' realizou login no sistema.', '2025-10-30 19:40:30'),
(191, 188, 'CRIACAO_RESERVA', 'Usuário \'Pele\' criou a reserva #1 (valor R$ 4000) para o carro ID 1. Status: Pendente.', '2025-10-30 19:42:36'),
(192, 188, 'PAGAMENTO_RESERVA', 'Usuário \'Pele\' (ID: 188) pagou a reserva #49 via cartao. Status alterado para \'aguardando_retirada\'.', '2025-10-30 19:43:00'),
(193, 101, 'LOGIN_SUCESSO', 'Usuário \'administrador\' realizou login no sistema.', '2025-10-30 19:43:38'),
(194, 101, 'MUDAR_STATUS_ATIVA', 'Usuário \'administrador\' marcou a reserva #49 como ativa.', '2025-10-30 19:44:10'),
(195, 101, 'LOGIN_SUCESSO', 'Usuário \'administrador\' realizou login no sistema.', '2025-10-31 01:33:38'),
(196, 189, 'LOGIN_SUCESSO', 'Usuário \'ana\' realizou login no sistema.', '2025-11-02 19:49:28'),
(197, 139, 'LOGIN_SUCESSO', 'Usuário \'cliente\' realizou login no sistema.', '2025-11-02 19:51:55'),
(198, 139, 'LOGIN_SUCESSO', 'Usuário \'cliente\' realizou login no sistema.', '2025-11-03 15:28:53'),
(199, 139, 'CRIACAO_RESERVA', 'Usuário \'cliente\' criou a reserva #1 (valor R$ 1856.25) para o carro ID 8. Status: Pendente.', '2025-11-03 15:29:02'),
(200, 139, 'CANCELAMENTO_RESERVA', 'Usuário \'cliente\' cancelou a reserva #52.', '2025-11-03 15:29:05'),
(201, 101, 'LOGIN_SUCESSO', 'Usuário \'administrador\' realizou login no sistema.', '2025-11-03 15:29:44'),
(202, 101, 'ATUALIZACAO_RESERVA', 'Usuário \'administrador\' atualizou a reserva #49. Novo status: concluida.', '2025-11-03 15:29:55'),
(203, 101, 'ATUALIZACAO_RESERVA', 'Usuário \'administrador\' atualizou a reserva #47. Novo status: concluida.', '2025-11-03 15:30:11'),
(204, 139, 'LOGIN_SUCESSO', 'Usuário \'cliente\' realizou login no sistema.', '2025-11-03 15:32:56'),
(205, 139, 'CRIACAO_RESERVA', 'Usuário \'cliente\' criou a reserva #1 (valor R$ 8000) para o carro ID 1. Status: Pendente.', '2025-11-03 15:33:04'),
(206, 190, 'LOGIN_SUCESSO', 'Usuário \'xande\' realizou login no sistema.', '2025-11-03 15:33:34'),
(207, 190, 'CRIACAO_RESERVA', 'Usuário \'xande\' criou a reserva #1 (valor R$ 2846.25) para o carro ID 8. Status: Pendente.', '2025-11-03 15:33:46'),
(208, 190, 'CANCELAMENTO_RESERVA', 'Usuário \'xande\' cancelou a reserva #54.', '2025-11-03 15:33:49'),
(209, 139, 'LOGIN_SUCESSO', 'Usuário \'cliente\' realizou login no sistema.', '2025-11-03 15:34:00'),
(210, 139, 'CANCELAMENTO_RESERVA', 'Usuário \'cliente\' cancelou a reserva #53.', '2025-11-03 15:34:11'),
(211, 101, 'LOGIN_SUCESSO', 'Usuário \'administrador\' realizou login no sistema.', '2025-11-03 15:34:22'),
(212, 101, 'CRIACAO_RESERVA', 'Usuário \'administrador\' criou a reserva #1 (valor R$ 4500) para o carro ID 1. Status: Pendente.', '2025-11-03 15:34:34'),
(213, 101, 'ACESSO_NEGADO', 'Tentativa de acesso não autorizado pelo usuário \'administrador\' à página \'/Projeto/view/reservas/minhasReservas.php\'. Perfil requerido: cliente, usuario', '2025-11-03 15:34:34'),
(214, 101, 'ATUALIZACAO_RESERVA', 'Usuário \'administrador\' atualizou a reserva #55. Novo status: concluida.', '2025-11-03 15:35:08'),
(215, 101, 'ATUALIZACAO_CARRO', 'Usuário \'administrador\' atualizou os dados do carro \'Chevrolet Onix\' (ID: 9).', '2025-11-03 16:34:41'),
(216, 101, 'ATUALIZACAO_CARRO', 'Usuário \'administrador\' atualizou os dados do carro \'Honda Civic\' (ID: 1).', '2025-11-03 16:34:49'),
(217, 101, 'ATUALIZACAO_CARRO', 'Usuário \'administrador\' atualizou os dados do carro \'Honda HRV\' (ID: 8).', '2025-11-03 16:34:57'),
(218, 191, 'LOGIN_SUCESSO', 'Usuário \'andre\' realizou login no sistema.', '2025-11-03 18:29:10'),
(219, 191, 'CRIACAO_RESERVA', 'Usuário \'andre\' criou a reserva #1 (valor R$ 1270) para o carro ID 9. Status: Pendente.', '2025-11-03 18:30:15'),
(220, 191, 'PAGAMENTO_RESERVA', 'Usuário \'andre\' (ID: 191) pagou a reserva #56 via cartao. Status alterado para \'aguardando_retirada\'.', '2025-11-03 18:30:42'),
(221, 140, 'LOGIN_SUCESSO', 'Usuário \'gerente\' realizou login no sistema.', '2025-11-03 18:31:54'),
(222, 140, 'CADASTRO_ADMIN', 'Admin \'gerente\' criou o novo usuário \'lucas\' (lucas@gmail.com) com o perfil \'funcionario\'.', '2025-11-03 18:33:38'),
(223, 140, 'DESATIVACAO_USUARIO', 'Admin \'gerente\' desativou a conta \'2\'.', '2025-11-03 18:34:41'),
(224, 140, 'CADASTRO_CARRO', 'Usuário \'gerente\' adicionou o novo carro: bmw sedam.', '2025-11-03 18:35:44'),
(225, 140, 'EXCLUSAO_CARRO', 'Usuário \'gerente\' excluiu o carro \'bmw sedam\'.', '2025-11-03 18:35:59'),
(226, 140, 'ATUALIZACAO_CARRO', 'Usuário \'gerente\' atualizou os dados do carro \'Bicicleta com motor Multilaser\' (ID: 3).', '2025-11-03 18:36:19'),
(227, 140, 'CRIACAO_RESERVA', 'Usuário \'gerente\' criou a reserva #1 (valor R$ 237) para o carro ID 1. Status: Pendente.', '2025-11-03 18:37:38'),
(228, 140, 'ACESSO_NEGADO', 'Tentativa de acesso não autorizado pelo usuário \'gerente\' à página \'/Projeto/view/reservas/minhasReservas.php\'. Perfil requerido: cliente, usuario', '2025-11-03 18:37:39'),
(229, 140, 'MUDAR_STATUS_ATIVA', 'Usuário \'gerente\' marcou a reserva #56 como ativa.', '2025-11-03 18:38:07'),
(230, 140, 'MUDAR_STATUS_ATIVA', 'Usuário \'gerente\' marcou a reserva #56 como ativa.', '2025-11-03 18:38:10'),
(231, 140, 'CADASTRO_MULTA', 'Usuário \'gerente\' registrou uma multa de R$ R$ 89.99 para a reserva #55.', '2025-11-03 18:38:57'),
(232, 101, 'ATUALIZACAO_CARRO', 'Usuário \'administrador\' atualizou os dados do carro \'Bicicleta com motor Multilaser\' (ID: 3).', '2025-11-04 04:10:59'),
(233, 101, 'ATUALIZACAO_CARRO', 'Usuário \'administrador\' atualizou os dados do carro \'Honda HRV\' (ID: 8).', '2025-11-04 04:27:26'),
(234, 101, 'ATUALIZACAO_CARRO', 'Usuário \'administrador\' atualizou o carro \'Honda HRV\' (ID: 8).', '2025-11-04 04:27:26'),
(235, 101, 'ATUALIZACAO_CARRO', 'Usuário \'administrador\' atualizou os dados do carro \'Honda HRV\' (ID: 8).', '2025-11-04 04:28:11'),
(236, 101, 'ATUALIZACAO_CARRO', 'Usuário \'administrador\' atualizou o carro \'Honda HRV\' (ID: 8).', '2025-11-04 04:28:11'),
(237, 101, 'ATUALIZACAO_CARRO', 'Usuário \'administrador\' atualizou os dados do carro \'Honda HRV\' (ID: 8).', '2025-11-04 04:28:29'),
(238, 101, 'ATUALIZACAO_CARRO', 'Usuário \'administrador\' atualizou o carro \'Honda HRV\' (ID: 8).', '2025-11-04 04:28:29'),
(239, 101, 'DESATIVACAO_USUARIO', 'Admin \'administrador\' desativou a conta \'13\'.', '2025-11-04 04:36:36'),
(240, 101, 'DESATIVACAO_USUARIO', 'Admin \'administrador\' desativou a conta \'4\'.', '2025-11-04 04:36:43'),
(241, 101, 'DESATIVACAO_USUARIO', 'Admin \'administrador\' desativou a conta \'6\'.', '2025-11-04 04:36:45'),
(242, 139, 'CRIACAO_RESERVA', 'Usuário \'cliente\' criou a reserva #1 (valor R$ 2508.7) para o carro ID 8. Status: Pendente.', '2025-11-04 04:40:05'),
(243, 139, 'CANCELAMENTO_RESERVA', 'Usuário \'cliente\' cancelou a reserva #58.', '2025-11-04 04:46:54'),
(244, 101, 'ATUALIZACAO_CARRO', 'Usuário \'administrador\' atualizou os dados do carro \'Honda Civic\' (ID: 1).', '2025-11-06 00:15:12'),
(245, 101, 'ATUALIZACAO_CARRO', 'Usuário \'administrador\' atualizou o carro \'Honda Civic\' (ID: 1).', '2025-11-06 00:15:12'),
(246, 101, 'ATUALIZACAO_CARRO', 'Usuário \'administrador\' atualizou os dados do carro \'Chevrolet Onix\' (ID: 9).', '2025-11-06 00:15:55'),
(247, 101, 'ATUALIZACAO_CARRO', 'Usuário \'administrador\' atualizou o carro \'Chevrolet Onix\' (ID: 9).', '2025-11-06 00:15:55'),
(248, 101, 'ATUALIZACAO_CARRO', 'Usuário \'administrador\' atualizou os dados do carro \'Bicicleta com motor Multilaser\' (ID: 3).', '2025-11-06 00:16:30'),
(249, 101, 'ATUALIZACAO_CARRO', 'Usuário \'administrador\' atualizou o carro \'Bicicleta com motor Multilaser\' (ID: 3).', '2025-11-06 00:16:30'),
(250, 101, 'ATUALIZACAO_RESERVA', 'Usuário \'administrador\' atualizou a reserva #57. Novo status: concluida.', '2025-11-06 00:16:39'),
(251, 101, 'ATUALIZACAO_RESERVA', 'Usuário \'administrador\' atualizou a reserva #56. Novo status: concluida.', '2025-11-06 00:16:45'),
(252, 101, 'CADASTRO_CARRO', 'Usuário \'administrador\' adicionou o novo carro: Ford Ranger Raptor.', '2025-11-06 00:18:26'),
(253, 101, 'CADASTRO_CARRO', 'Usuário \'administrador\' adicionou o novo carro: Ford Ranger Raptor.', '2025-11-06 00:18:26'),
(254, 101, 'EXCLUSAO_CARRO', 'Usuário \'administrador\' excluiu o carro \'Ford Ranger Raptor\'.', '2025-11-06 00:18:39'),
(255, 101, 'EXCLUSAO_CARRO', 'Usuário \'administrador\' excluiu o carro \'ID 12\'.', '2025-11-06 00:18:39'),
(256, 101, 'ATUALIZACAO_CARRO', 'Usuário \'administrador\' atualizou os dados do carro \'Ford Ranger Raptor\' (ID: 13).', '2025-11-06 00:18:45'),
(257, 101, 'ATUALIZACAO_CARRO', 'Usuário \'administrador\' atualizou o carro \'Ford Ranger Raptor\' (ID: 13).', '2025-11-06 00:18:45'),
(258, 101, 'CADASTRO_CARRO', 'Usuário \'administrador\' adicionou o novo carro: Volkswagem Nivus.', '2025-11-17 17:38:33'),
(259, 101, 'CADASTRO_CARRO', 'Usuário \'administrador\' adicionou o novo carro: Volkswagem Nivus.', '2025-11-17 17:38:33'),
(260, 101, 'ATUALIZACAO_CARRO', 'Usuário \'administrador\' atualizou os dados do carro \'Volkswagem Nivus\' (ID: 14).', '2025-11-17 17:38:44'),
(261, 101, 'ATUALIZACAO_CARRO', 'Usuário \'administrador\' atualizou o carro \'Volkswagem Nivus\' (ID: 14).', '2025-11-17 17:38:44'),
(262, 101, 'EXCLUSAO_CARRO', 'Usuário \'administrador\' excluiu o carro \'Volkswagem Nivus\'.', '2025-11-17 17:38:51'),
(263, 101, 'EXCLUSAO_CARRO', 'Usuário \'administrador\' excluiu o carro \'ID 15\'.', '2025-11-17 17:38:51'),
(264, 101, 'LOGIN_FALHA', 'Tentativa de login falhou para o e-mail \'admin@gmail.com\'.', '2025-11-18 01:21:44'),
(265, 139, 'CRIACAO_RESERVA', 'Usuário \'cliente\' criou a reserva #1 (valor R$ 5) para o carro ID 3. Status: Pendente.', '2025-11-25 18:12:27'),
(266, 139, 'CANCELAMENTO_RESERVA', 'Usuário \'cliente\' cancelou a reserva #59.', '2025-11-25 18:13:49'),
(267, 193, 'CRIACAO_RESERVA', 'Usuário \'xande\' criou a reserva #1 (valor R$ 1587.5) para o carro ID 9. Status: Pendente.', '2025-11-26 02:48:02'),
(268, 101, 'ATUALIZACAO_RESERVA', 'Usuário \'administrador\' atualizou a reserva #60. Novo status: concluida.', '2025-11-26 02:50:10');

-- --------------------------------------------------------

--
-- Estrutura da tabela `tbl_multas`
--

CREATE TABLE `tbl_multas` (
  `cod_multa` int(11) NOT NULL,
  `cod_reserva` int(11) NOT NULL,
  `descricao` varchar(255) NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `status` enum('pendente','paga','cancelada') NOT NULL DEFAULT 'pendente',
  `data_vencimento` date NOT NULL,
  `data_registro` timestamp NOT NULL DEFAULT current_timestamp(),
  `data_resolucao` datetime DEFAULT NULL,
  `cod_usuario_registro` int(11) DEFAULT NULL,
  `observacoes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Extraindo dados da tabela `tbl_multas`
--

INSERT INTO `tbl_multas` (`cod_multa`, `cod_reserva`, `descricao`, `valor`, `status`, `data_vencimento`, `data_registro`, `data_resolucao`, `cod_usuario_registro`, `observacoes`) VALUES
(1, 19, 'exceço de velocidade', 130.00, 'pendente', '2025-10-30', '2025-10-18 02:30:13', NULL, 101, ''),
(2, 19, 'exceço de velocidade', 120.00, 'cancelada', '2025-10-23', '2025-10-18 02:54:46', NULL, 101, ''),
(3, 55, 'velocidade', 0.00, 'pendente', '2025-11-14', '2025-11-03 18:38:57', NULL, 140, '');

-- --------------------------------------------------------

--
-- Estrutura da tabela `tbl_planos_aluguel`
--

CREATE TABLE `tbl_planos_aluguel` (
  `cod_plano` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `descricao` text DEFAULT NULL,
  `dias_minimos` int(11) NOT NULL DEFAULT 1,
  `multiplicador_valor` decimal(5,2) DEFAULT 1.00,
  `data_criacao` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `tbl_planos_aluguel`
--

INSERT INTO `tbl_planos_aluguel` (`cod_plano`, `nome`, `descricao`, `dias_minimos`, `multiplicador_valor`, `data_criacao`) VALUES
(1, 'Plano Semanal', 'Desconto para aluguéis de 7 dias ou mais.', 7, 0.90, '2025-10-04 01:36:58'),
(2, 'Plano Mensal', 'Desconto para aluguéis de 30 dias ou mais.', 30, 0.80, '2025-10-04 01:36:58');

-- --------------------------------------------------------

--
-- Estrutura da tabela `tbl_reservas`
--

CREATE TABLE `tbl_reservas` (
  `cod_reserva` int(11) NOT NULL,
  `cod_usuario` int(11) NOT NULL,
  `cod_carro` int(11) NOT NULL,
  `cod_plano` int(11) DEFAULT NULL,
  `data_inicio` datetime NOT NULL,
  `data_fim` datetime NOT NULL,
  `status` enum('pendente','ativa','aguardando_retirada','concluida','cancelada') DEFAULT 'pendente',
  `quilometragem_inicial` int(11) DEFAULT 0,
  `quilometragem_final` int(11) DEFAULT NULL,
  `valor_total` decimal(10,2) DEFAULT 0.00,
  `assinatura_digital` blob DEFAULT NULL,
  `pagamento_realizado` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `tbl_reservas`
--

INSERT INTO `tbl_reservas` (`cod_reserva`, `cod_usuario`, `cod_carro`, `cod_plano`, `data_inicio`, `data_fim`, `status`, `quilometragem_inicial`, `quilometragem_final`, `valor_total`, `assinatura_digital`, `pagamento_realizado`) VALUES
(9, 146, 3, NULL, '2025-10-08 00:00:00', '2025-10-15 00:00:00', 'cancelada', 0, NULL, 756.00, NULL, 0),
(10, 148, 1, NULL, '2025-10-09 00:00:00', '2025-10-15 00:00:00', 'cancelada', 0, NULL, 2400.00, NULL, 0),
(12, 150, 3, NULL, '2025-10-09 00:00:00', '2025-10-19 00:00:00', 'cancelada', 0, NULL, 1080.00, NULL, 0),
(13, 101, 3, NULL, '2025-10-09 00:00:00', '2025-10-19 00:00:00', 'cancelada', 0, NULL, 1080.00, NULL, 0),
(15, 152, 3, NULL, '2025-10-09 00:00:00', '2025-10-22 00:00:00', 'cancelada', 0, NULL, 1404.00, NULL, 0),
(16, 152, 3, NULL, '2025-10-15 00:00:00', '2025-10-27 00:00:00', 'cancelada', 0, NULL, 1296.00, NULL, 0),
(17, 153, 8, NULL, '2025-10-11 00:00:00', '2025-10-18 00:00:00', 'cancelada', 0, NULL, 630.00, NULL, 0),
(18, 156, 1, NULL, '2025-10-09 00:00:00', '2025-10-20 00:00:00', 'cancelada', 0, NULL, 3960.00, NULL, 0),
(19, 163, 3, NULL, '2025-10-16 00:00:00', '2025-10-22 00:00:00', 'concluida', 0, NULL, 720.00, NULL, 0),
(20, 164, 9, NULL, '2025-10-16 00:00:00', '2025-10-21 00:00:00', 'cancelada', 0, NULL, 600.00, NULL, 0),
(22, 139, 3, NULL, '2025-10-23 00:00:00', '2025-10-30 00:00:00', 'cancelada', 0, NULL, 756.00, NULL, 0),
(23, 139, 3, NULL, '2025-10-28 00:00:00', '2025-10-30 00:00:00', 'cancelada', 0, NULL, 240.00, NULL, 0),
(24, 176, 1, NULL, '2025-10-22 00:00:00', '2025-10-23 00:00:00', 'cancelada', 0, NULL, 400.00, NULL, 0),
(25, 176, 1, NULL, '2025-10-22 00:00:00', '2025-10-23 00:00:00', 'cancelada', 0, NULL, 400.00, NULL, 0),
(26, 176, 1, NULL, '2025-10-22 00:00:00', '2025-10-23 00:00:00', 'cancelada', 0, NULL, 400.00, NULL, 0),
(27, 156, 1, NULL, '2025-10-22 00:00:00', '2025-10-23 00:00:00', 'cancelada', 0, NULL, 400.00, NULL, 0),
(28, 177, 9, NULL, '2025-10-23 00:00:00', '2025-10-28 00:00:00', 'concluida', 0, NULL, 50000.00, NULL, 0),
(29, 179, 8, NULL, '2025-10-27 00:00:00', '2025-11-04 00:00:00', 'concluida', 0, NULL, 71280.00, NULL, 0),
(30, 139, 1, NULL, '2025-10-30 00:00:00', '2025-10-31 00:00:00', 'concluida', 0, NULL, 4000.00, NULL, 0),
(32, 139, 9, NULL, '2025-10-30 00:00:00', '2025-11-07 00:00:00', 'cancelada', 0, NULL, 72.00, NULL, 0),
(35, 139, 8, NULL, '2025-10-30 00:00:00', '2025-11-07 00:00:00', 'concluida', 0, NULL, 712.80, NULL, 0),
(36, 139, 3, NULL, '2025-10-30 00:00:00', '2025-11-08 00:00:00', 'concluida', 0, NULL, 101.25, NULL, 0),
(37, 139, 1, NULL, '2025-10-30 00:00:00', '2025-11-07 00:00:00', 'concluida', 0, NULL, 2880.00, NULL, 0),
(38, 139, 9, NULL, '2025-10-30 00:00:00', '2025-11-07 00:00:00', 'cancelada', 0, NULL, 100.00, NULL, 0),
(39, 139, 8, NULL, '2025-10-30 00:00:00', '2025-10-31 00:00:00', 'concluida', 0, NULL, 99.00, NULL, 0),
(42, 182, 9, NULL, '2025-10-30 00:00:00', '2025-11-07 00:00:00', 'concluida', 0, NULL, 72.00, NULL, 0),
(44, 183, 3, NULL, '2025-10-30 00:00:00', '2025-11-07 00:00:00', 'concluida', 0, NULL, 64.80, NULL, 0),
(45, 184, 1, NULL, '2025-10-30 00:00:00', '2025-11-07 00:00:00', 'concluida', 0, NULL, 2880.00, NULL, 0),
(47, 185, 9, NULL, '2025-10-30 00:00:00', '2025-11-07 00:00:00', 'concluida', 0, NULL, 72.00, NULL, 0),
(48, 187, 3, NULL, '2025-10-30 00:00:00', '2025-11-07 00:00:00', 'aguardando_retirada', 0, NULL, 90.00, NULL, 0),
(49, 188, 1, NULL, '2025-10-30 00:00:00', '2025-11-07 00:00:00', 'concluida', 0, NULL, 2880.00, NULL, 0),
(52, 139, 8, NULL, '2025-11-04 00:00:00', '2025-11-19 00:00:00', 'cancelada', 0, NULL, 1856.25, NULL, 0),
(53, 139, 1, NULL, '2025-11-04 00:00:00', '2025-11-20 00:00:00', 'cancelada', 0, NULL, 8000.00, NULL, 0),
(54, 190, 8, NULL, '2025-11-04 00:00:00', '2025-11-27 00:00:00', 'cancelada', 0, NULL, 2846.25, NULL, 0),
(55, 101, 1, NULL, '2025-11-03 00:00:00', '2025-11-12 00:00:00', 'concluida', 0, NULL, 3240.00, NULL, 0),
(56, 191, 9, NULL, '2025-11-04 00:00:00', '2025-11-12 00:00:00', 'concluida', 0, NULL, 914.40, NULL, 0),
(57, 140, 1, NULL, '2025-11-12 00:00:00', '2025-11-13 00:00:00', 'concluida', 0, NULL, 237.00, NULL, 0),
(58, 139, 8, NULL, '2025-11-04 00:00:00', '2025-11-12 00:00:00', 'cancelada', 0, NULL, 2508.70, NULL, 0),
(59, 139, 3, NULL, '2025-11-25 00:00:00', '2025-11-29 00:00:00', 'cancelada', 0, NULL, 5.00, NULL, 0),
(60, 193, 9, NULL, '2025-11-26 00:00:00', '2025-12-06 00:00:00', 'concluida', 0, NULL, 1143.00, NULL, 0);

-- --------------------------------------------------------

--
-- Estrutura da tabela `tbl_usuarios`
--

CREATE TABLE `tbl_usuarios` (
  `cod_usuario` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `perfil` enum('usuario','cliente','funcionario','gerente','admin') NOT NULL DEFAULT 'usuario',
  `data_cadastro` datetime DEFAULT current_timestamp(),
  `telefone` varchar(20) DEFAULT NULL,
  `cpf` varchar(14) DEFAULT NULL,
  `cadastro_completo` tinyint(1) NOT NULL DEFAULT 0,
  `status` enum('ativo','inativo') NOT NULL DEFAULT 'ativo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `tbl_usuarios`
--

INSERT INTO `tbl_usuarios` (`cod_usuario`, `nome`, `email`, `senha`, `perfil`, `data_cadastro`, `telefone`, `cpf`, `cadastro_completo`, `status`) VALUES
(101, 'administrador', 'admin@gmail.com', '$2y$10$F83YSjrqye9SNu6.7yZSgufsjVy41z2AEHSs5mR4Z1X9WPGz8wzde', 'admin', '2025-09-10 15:40:18', '(61) 99577-0554', '080.608.401-42', 1, 'ativo'),
(137, 'usuario', 'usuario@gmail.com', '$2y$10$48wAwUUb8U.2jwZxz1HI1ulRJGSGrMmWNrZkkgZlAKgJedMRQnb9O', 'usuario', '2025-10-06 15:53:39', NULL, NULL, 0, 'ativo'),
(138, 'Usuario Completo', 'usuariocompleto@gmail.com', '$2y$10$KHJYKvA0uq2knlR.871FsOEETiCJDBbam7IGrKznAmtWaudjj9DRu', 'usuario', '2025-10-06 15:54:01', NULL, NULL, 1, 'ativo'),
(139, 'cliente', 'cliente@gmail.com', '$2y$10$jcY7JP8LdXr888EAgK.vNek8S0S1mrbXtsmy6TksiFNjygwiwLfpu', 'cliente', '2025-10-06 15:54:37', '(61) 9957-7067', '080.608.401-42', 1, 'ativo'),
(140, 'gerente', 'gerente@gmail.com', '$2y$10$4iXCvP6GFq9/ytduHiTPM.ebRvlG/IDriFdlbMda6mUI5XMLe7MgO', 'gerente', '2025-10-06 15:54:48', '(61) 8888-8999', '983.187.630-09', 1, 'ativo'),
(141, 'funcionario', 'funcionario@gmail.com', '$2y$10$pW1jbSeNDBvzPDcGQG1jpu5fLC5JqqQmQM7oWNy.KRmx9Q0oj1qVG', 'funcionario', '2025-10-06 15:54:57', NULL, NULL, 0, 'ativo'),
(146, 'Suco de uva', 'suco@gmail.com', '$2y$10$pD5mcnxBLArNxalMAjRUQ.QOCKt3sqYzEOlBrSYSIAIrehWb6rHLS', 'cliente', '2025-10-08 14:23:51', '(66) 66666-6666', '080.608.401-42', 1, 'ativo'),
(147, 'asdasd', 'asd@gmail.com', '$2y$10$C0Nhw6YOJZbutoqH7L2YbOfGXbjtlajzdioqStKxjdjocM.xDuz6W', 'usuario', '2025-10-08 14:28:37', NULL, NULL, 0, 'inativo'),
(148, 'carro', 'carro@gmail.com', '$2y$10$W5EV2EtjUbGFGJX5RVqWKOtLOdbMv0NbuM1aDQ.FzIQ4Q1SDvbKuK', 'cliente', '2025-10-09 08:11:02', '(66) 66666-6666', '080.608.401-42', 1, 'inativo'),
(149, 'teste', 'teste@gmail.com', '$2y$10$8VRO5Lj78R0kmizpF5b8YOotczqCldrFGJfX0rghDqWNRrrflpOj.', 'cliente', '2025-10-09 14:22:19', '(66) 66666-6666', '080.608.401-42', 1, 'ativo'),
(150, 'luilindo', 'luis@gmail.com', '$2y$10$wanm4APCNLE2dwFry51JROZrSeMWhOv7k7WYrbf7bKjt6NnAV3jsG', 'cliente', '2025-10-09 14:49:55', '(61) 99588-1077', '054.185.238-80', 1, 'ativo'),
(151, 'nome teste', 'adminteste@gmail.com', '$2y$10$JLUr6Ppwen0SpX1IFuGTo./KNKIo1tUWro0/9hZm0m335iV1kaQr.', 'admin', '2025-10-09 14:57:24', '(61) 88888-8888', '617.749.270-39', 1, 'ativo'),
(152, 'teste', 'teste1@gmail.com', '$2y$10$81a7viYpDfDYu2KN1xGRC.0CKmw7ySzd8mhNI8eaVKDpsARdha9e.', 'cliente', '2025-10-09 15:33:02', '(33) 33333-3333', '080.608.401-42', 1, 'ativo'),
(153, 'hially', 'hially@gmail.com', '$2y$10$qDAH2njiRlbz/o.VllYNmes6/P0NBFlR.wovIxHly/363lXSAZ77.', 'cliente', '2025-10-09 16:58:27', '(67) 33333-3333', '096.332.536-16', 1, 'ativo'),
(154, 'criou', 'aconta@gmail.com', '$2y$10$j5T8bDj7LxcdsUT9o0dSz.INSC83BJrbIWSdTTMn/LbzZdSSUuXSq', 'usuario', '2025-10-09 17:07:47', '(66) 66666-6666', '080.608.401-42', 1, 'ativo'),
(155, 'a', 'a@gmail.com', '$2y$10$0HptGO6o7esHgKm6FyTTHeL.KWQ8eq9ahbECGMjk9ANDeGstnm.Le', 'usuario', '2025-10-09 17:11:12', '(06) 66655-6565', '080.608.401-42', 1, 'inativo'),
(156, 'a', 'b@gmail.com', '$2y$10$O/prLrrAFMOpuivmQQxH2e/Kb/lCZi5Vz02OSvKDqffLBssW7HMBG', 'cliente', '2025-10-09 17:15:48', '(66) 66666-6666', '080.608.401-42', 1, 'inativo'),
(157, 'c', 'c@gmail.com', '$2y$10$77TioWztCz682cIzra7EVe3eteb4HS2nlndft9FVRFhFy0Im4RPP2', 'usuario', '2025-10-09 17:26:53', '(66) 66666-6666', '080.608.401-42', 1, 'inativo'),
(158, 'd', 'd@gmail.com', '$2y$10$oAEWEcVewPoQSFd7i7FBiegfHSEdhNAQBnl9LQNWaC2qTOMtdx.Hi', 'usuario', '2025-10-09 17:33:18', '(66) 66666-6666', '080.608.401-42', 1, 'inativo'),
(159, 'a', 'a@gmail.com', '$2y$10$UeJamvNMFzZ0tYuyAVSJZeUF4n/uUCPr/1JWd44o4aUwaXu/vBx/.', 'usuario', '2025-10-10 17:01:10', '(61) 99577-0554', '080.608.401-42', 1, 'ativo'),
(160, 'b', 'b@gmail.com', '$2y$10$OS5CsJg.UV4qrorjVFK4xOv4/7owQx3bSsgD7.5gz2mjcqnobhiF.', 'usuario', '2025-10-10 17:10:47', '(61) 99577-0554', '080.608.401-42', 1, 'ativo'),
(161, 'c', 'c@gmail.com', '$2y$10$HKa/pT7JBq.IL0NQqJu57.1mmtQRhw8UXBSclbDnqAjDsKC9GBRCa', 'usuario', '2025-10-10 17:12:43', '(61) 99577-0554', '080.608.401-42', 1, 'ativo'),
(162, 'daa', 'd@gmail.com', '$2y$10$u316u9vmKV07I.FBVLaZTu6oXUEZxIbQHAlHjiDZJ5Q.fQncEa0zu', 'usuario', '2025-10-10 17:21:26', NULL, NULL, 1, 'ativo'),
(163, 'aaa', 'aa@gmail.com', '$2y$10$8IvYX3RrMn288xOK/3LfNelvAJBhxVbluo38nIvLGAhOKyEqKBuMS', 'cliente', '2025-10-16 14:20:50', '(66) 6666-6666', '080.608.401-42', 1, 'ativo'),
(164, 'a', 'ab@gmail.com', '$2y$10$e6NtHukSuqwEsaiqFxh/Jetn7KDs4mkDaF/b2AzPO8SinF7Fgn4Xa', 'cliente', '2025-10-16 14:53:41', '(66) 6666-6666', '080.608.401-42', 1, 'ativo'),
(165, 'aasd', 'aasd@gmail.com', '$2y$10$UMvCZIKedNaXDd2YxPu6sOqaWR2KLU.OFN3jLcyWRNLAnrmTz9GKS', 'usuario', '2025-10-20 14:54:18', NULL, NULL, 0, 'inativo'),
(166, 'neymer', 'ney@gmail.com', '$2y$10$UQp0.sICGEGLFBROwnoQoOZJTXPyBuPOH8IwrBByp3U2.v8mf5/KC', 'usuario', '2025-10-20 16:40:27', NULL, NULL, 0, 'ativo'),
(167, 'ney', 'neymar@gmail.com', '$2y$10$kyw5h9SzP.7SG4cwdalle.LaggzUCiiJydj0Rx8isfrIwv1Gs8xje', 'usuario', '2025-10-20 16:49:23', NULL, NULL, 0, 'ativo'),
(168, 'ney', 'neymar1@gmail.com', '$2y$10$yplVN5v5fo7nLs042BBjZOd8WovRhg2S0nTjiDCEnTtULJxtWi/ei', 'cliente', '2025-10-20 16:51:40', '(61) 99577-0554', '080.608.401-42', 1, 'ativo'),
(169, 'a', 'neimito@gmail.com', '$2y$10$FGiwKyJe2Mi7kAq/CN3LZe6PFWW1oCu5bj3vjgSDNVPSOAoY0bpx6', 'usuario', '2025-10-20 17:40:24', NULL, NULL, 0, 'ativo'),
(170, 'ney', '1@gmail.com', '$2y$10$aMGbDZsK0Acz/GMmHT.OCuP.TxfX2iaj9onAWNoIOB/j6nkLSVn0C', 'usuario', '2025-10-21 14:30:09', NULL, NULL, 0, 'ativo'),
(171, '13', '2@gmail.com', '$2y$10$rczbYjD/jDwSqOjRBGgq4uai.KibH2UrtExFIGBBYLFaGtGJ96.Ii', 'usuario', '2025-10-21 14:34:18', NULL, NULL, 0, 'inativo'),
(172, '2', '3@gmail.com', '$2y$10$FMymygZr8htjyRXdr4DN1e6wmqSUDsC3PLuxIUC7lLw0xaqzC2th2', 'usuario', '2025-10-21 14:38:39', NULL, NULL, 0, 'inativo'),
(173, '4', '4@gmail.com', '$2y$10$EAfUZO.KHOoD6cTT8tGsWOz.7TrXHTPdCMf6ZKlfRBFXpEhRrfkzC', 'usuario', '2025-10-21 14:58:06', NULL, NULL, 0, 'inativo'),
(174, 'a', 'n@gmail.com', '$2y$10$iBlyWYEBH1VqzRERtD60meMNgh5Nqa6BEJzmhVdxKNRa3dRnbW9Qq', 'usuario', '2025-10-21 14:59:40', NULL, NULL, 0, 'ativo'),
(175, '6', '6@gmail.com', '$2y$10$6fjB.AHeF7jV4VVPoionruh654jsiVnaOGTtUkH2vxU76/Jj3B1WG', 'usuario', '2025-10-21 15:11:32', NULL, NULL, 0, 'inativo'),
(176, 'xande', 't1@gmail.com', '$2y$10$N0NVQbTAmCCSBnJG9EL44.8ekiTTaqEHtW0O0uPrkUpYPbP3XIvhu', 'cliente', '2025-10-22 13:07:59', '(61) 99577-0554', '080.608.401-42', 1, 'ativo'),
(177, 'weyller', 'weyller@gmail.com', '$2y$10$h1dWyk6aB4ElB1AcxsSfcOtD8oqqt1M7rc.C8ZHXI6i326EaJoEN2', 'cliente', '2025-10-23 16:25:57', '(66) 6666-6666', '080.608.401-42', 1, 'ativo'),
(178, 'weyller2', 'weyller2@gmail.com', '$2y$10$1bHugaaKBtDQ7KaA2zOl/ukbN6viUVx1wiY4J9Lk1UCg4TKnICJCW', 'usuario', '2025-10-23 16:32:17', '(66) 6666-6666', '080.608.401-42', 1, 'ativo'),
(179, 'vailson', 'vailson@gmail.com', '$2y$10$zSITDDHzH8PheMIXi1C7T.6i3wwzBDi991iwu6daOS0x4DuVsHpqa', 'cliente', '2025-10-24 16:55:19', '(61) 99577-0554', '080.608.401-42', 1, 'ativo'),
(180, 'juquinha', 'juquinha@gmail.com', '$2y$10$DFVUg837v5f6cNwHuj1rn.XrGzbDBDol4yJyAf4DcmyexiqhsuFBe', 'usuario', '2025-10-27 16:49:19', NULL, NULL, 0, 'ativo'),
(181, 'aa', 'asa@gmail.com', '$2y$10$W2PABN4uxf8Lh2XUQsnU1OMTRwW7vyXtZPhligXdhj1nYMUB..6CG', 'usuario', '2025-10-27 17:15:01', NULL, NULL, 0, 'ativo'),
(182, 'aa', 'asjd@gmail.com', '$2y$10$PYpY9WB2u0NllpE/fa2NCuMOIvMrBO/WCe6Q6b2RSV7XtJCN8jjIe', 'cliente', '2025-10-30 14:49:01', '(66) 66666-6666', '080.608.401-42', 1, 'ativo'),
(183, 'lu', 'lia@gmail.com', '$2y$10$WgU5NtRpI2jspjJ0rY6PpOR0Ich3npWWMYV/O/AFVvJcqT9jgolVK', 'cliente', '2025-10-30 14:52:38', '(11) 11111-1111', '080.608.401-42', 1, 'ativo'),
(184, 'aa', 'asdas@gmail.com', '$2y$10$Xs9AaPLtASqv2emHgGwMveYL4XWBRoZyXbPIxxheZ5diyiZJqA9hG', 'cliente', '2025-10-30 14:54:37', '(66) 66666-6666', '080.608.401-42', 1, 'ativo'),
(185, 'chapeu', 'chapeu@gmail.com', '$2y$10$gR/kL0bQZB.GWy3TiiAGgeaVMw4dkhRwXcYLhm/6QC5zIsEjaGpOG', 'cliente', '2025-10-30 14:58:51', '(66) 66666-6666', '080.608.401-42', 1, 'ativo'),
(186, 'madrid', 'madrid@gmail.com', '$2y$10$N8vHKKxM2srMWzFNW6CX4e9eWLbqulStsVE1aIBoKcJFedNfaqo6a', 'usuario', '2025-10-30 15:01:40', NULL, NULL, 0, 'ativo'),
(187, 'madrid', 'madrid2@gmail.com', '$2y$10$CEcpCogKLd9NIHPUNM.3cekkaNPmVKArQODLrE3NFWaaguKMqaNuC', 'cliente', '2025-10-30 15:02:34', '(66) 66666-6666', '080.608.401-42', 1, 'ativo'),
(188, 'Pele', 'pele@gmail.com', '$2y$10$CQVimIdXTrn9Xh1pff0WCugoxp9RCnpPz9yJAtmFwf.pQss9DTFR2', 'cliente', '2025-10-30 16:40:21', '(98) 43923-8394', '054.187.281-80', 1, 'ativo'),
(189, 'ana', 'ana@gmail.com', '$2y$10$6NrO1ziS5mfucaVdUAGcOuhosJbPfscsj2IkB8wr17KPp2h1GBty6', 'usuario', '2025-11-02 16:49:24', '(61) 99577-0554', '080.608.401-42', 1, 'ativo'),
(190, 'xande', 'iop@gmail.com', '$2y$10$jHEhVq1T5upI17wjdZykvO5Y6fwqokHpklxQia7TlQ2jIrml3gIWi', 'cliente', '2025-11-03 12:33:28', '(61) 99577-0554', '080.608.401-42', 1, 'ativo'),
(191, 'andre', 'andre@gmail.com', '$2y$10$CNm7jwe34pcOyvua6YLWm.O1oknlBMKekUmDYmwN3ig2po/IJjPZW', 'cliente', '2025-11-03 15:29:02', '(61) 96857-4455', '080.608.401-42', 1, 'ativo'),
(192, 'lucas', 'lucas@gmail.com', '$2y$10$EGz2RxPcE6FpHTi42RpH0.xX8OF6ZAupHkYno1nOcHiba6HRMoQJm', 'funcionario', '2025-11-03 15:33:38', NULL, NULL, 0, 'ativo'),
(193, 'xande', 'xandinho@gmail.com', '$2y$10$fgUmUlvNl6.gV5fSCrO/S.TGFcsE5coOJM/MuOWBdqQKxzLUX.kvW', 'cliente', '2025-11-25 15:21:09', '(61) 99577-0554', '080.608.401-42', 1, 'ativo');

--
-- Índices para tabelas despejadas
--

--
-- Índices para tabela `tbl_carros`
--
ALTER TABLE `tbl_carros`
  ADD PRIMARY KEY (`cod_carro`);

--
-- Índices para tabela `tbl_enderecos`
--
ALTER TABLE `tbl_enderecos`
  ADD PRIMARY KEY (`cod_endereco`),
  ADD UNIQUE KEY `cod_usuario` (`cod_usuario`);

--
-- Índices para tabela `tbl_favoritos`
--
ALTER TABLE `tbl_favoritos`
  ADD PRIMARY KEY (`cod_favorito`),
  ADD KEY `cod_usuario` (`cod_usuario`),
  ADD KEY `cod_carro` (`cod_carro`);

--
-- Índices para tabela `tbl_logs`
--
ALTER TABLE `tbl_logs`
  ADD PRIMARY KEY (`cod_log`),
  ADD KEY `cod_usuario` (`cod_usuario`);

--
-- Índices para tabela `tbl_multas`
--
ALTER TABLE `tbl_multas`
  ADD PRIMARY KEY (`cod_multa`),
  ADD KEY `cod_reserva` (`cod_reserva`),
  ADD KEY `cod_usuario_registro` (`cod_usuario_registro`);

--
-- Índices para tabela `tbl_planos_aluguel`
--
ALTER TABLE `tbl_planos_aluguel`
  ADD PRIMARY KEY (`cod_plano`);

--
-- Índices para tabela `tbl_reservas`
--
ALTER TABLE `tbl_reservas`
  ADD PRIMARY KEY (`cod_reserva`),
  ADD KEY `cod_usuario` (`cod_usuario`),
  ADD KEY `cod_carro` (`cod_carro`),
  ADD KEY `cod_plano` (`cod_plano`);

--
-- Índices para tabela `tbl_usuarios`
--
ALTER TABLE `tbl_usuarios`
  ADD PRIMARY KEY (`cod_usuario`);

--
-- AUTO_INCREMENT de tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `tbl_carros`
--
ALTER TABLE `tbl_carros`
  MODIFY `cod_carro` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de tabela `tbl_enderecos`
--
ALTER TABLE `tbl_enderecos`
  MODIFY `cod_endereco` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT de tabela `tbl_favoritos`
--
ALTER TABLE `tbl_favoritos`
  MODIFY `cod_favorito` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `tbl_logs`
--
ALTER TABLE `tbl_logs`
  MODIFY `cod_log` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=269;

--
-- AUTO_INCREMENT de tabela `tbl_multas`
--
ALTER TABLE `tbl_multas`
  MODIFY `cod_multa` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `tbl_planos_aluguel`
--
ALTER TABLE `tbl_planos_aluguel`
  MODIFY `cod_plano` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `tbl_reservas`
--
ALTER TABLE `tbl_reservas`
  MODIFY `cod_reserva` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT de tabela `tbl_usuarios`
--
ALTER TABLE `tbl_usuarios`
  MODIFY `cod_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=194;

--
-- Restrições para despejos de tabelas
--

--
-- Limitadores para a tabela `tbl_enderecos`
--
ALTER TABLE `tbl_enderecos`
  ADD CONSTRAINT `fk_endereco_usuario` FOREIGN KEY (`cod_usuario`) REFERENCES `tbl_usuarios` (`cod_usuario`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `tbl_favoritos`
--
ALTER TABLE `tbl_favoritos`
  ADD CONSTRAINT `tbl_favoritos_ibfk_1` FOREIGN KEY (`cod_usuario`) REFERENCES `tbl_usuarios` (`cod_usuario`) ON DELETE CASCADE,
  ADD CONSTRAINT `tbl_favoritos_ibfk_2` FOREIGN KEY (`cod_carro`) REFERENCES `tbl_carros` (`cod_carro`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `tbl_logs`
--
ALTER TABLE `tbl_logs`
  ADD CONSTRAINT `tbl_logs_ibfk_1` FOREIGN KEY (`cod_usuario`) REFERENCES `tbl_usuarios` (`cod_usuario`) ON DELETE SET NULL;

--
-- Limitadores para a tabela `tbl_multas`
--
ALTER TABLE `tbl_multas`
  ADD CONSTRAINT `tbl_multas_ibfk_1` FOREIGN KEY (`cod_reserva`) REFERENCES `tbl_reservas` (`cod_reserva`),
  ADD CONSTRAINT `tbl_multas_ibfk_2` FOREIGN KEY (`cod_usuario_registro`) REFERENCES `tbl_usuarios` (`cod_usuario`) ON DELETE SET NULL;

--
-- Limitadores para a tabela `tbl_reservas`
--
ALTER TABLE `tbl_reservas`
  ADD CONSTRAINT `tbl_reservas_ibfk_1` FOREIGN KEY (`cod_usuario`) REFERENCES `tbl_usuarios` (`cod_usuario`) ON DELETE CASCADE,
  ADD CONSTRAINT `tbl_reservas_ibfk_2` FOREIGN KEY (`cod_carro`) REFERENCES `tbl_carros` (`cod_carro`) ON DELETE CASCADE,
  ADD CONSTRAINT `tbl_reservas_ibfk_3` FOREIGN KEY (`cod_plano`) REFERENCES `tbl_planos_aluguel` (`cod_plano`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
