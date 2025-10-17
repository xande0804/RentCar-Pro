-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3308:3308
-- Tempo de geração: 16-Out-2025 às 21:33
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
-- Estrutura da tabela `tbl_avaliacoes_carro`
--

CREATE TABLE `tbl_avaliacoes_carro` (
  `cod_avaliacao` int(11) NOT NULL,
  `cod_carro` int(11) NOT NULL,
  `cod_reserva` int(11) NOT NULL,
  `quilometragem_final` int(11) DEFAULT 0,
  `checklist` text DEFAULT NULL,
  `observacoes` text DEFAULT NULL,
  `data` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `tbl_carros`
--

CREATE TABLE `tbl_carros` (
  `cod_carro` int(11) NOT NULL,
  `marca` varchar(100) NOT NULL,
  `modelo` varchar(100) NOT NULL,
  `categoria` varchar(50) DEFAULT NULL,
  `ano` year(4) NOT NULL,
  `cor` varchar(50) NOT NULL,
  `combustivel` enum('gasolina','alcool','flex','diesel') NOT NULL,
  `cambio` enum('manual','automatico') NOT NULL,
  `ar_condicionado` tinyint(1) DEFAULT 1,
  `preco_diaria` decimal(10,2) NOT NULL,
  `status` enum('disponivel','alugado','reservado','manutencao','documento_atrasado') DEFAULT 'disponivel',
  `km_total` int(11) DEFAULT 0,
  `descricao` text DEFAULT NULL,
  `data_cadastro` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `tbl_carros`
--

INSERT INTO `tbl_carros` (`cod_carro`, `marca`, `modelo`, `categoria`, `ano`, `cor`, `combustivel`, `cambio`, `ar_condicionado`, `preco_diaria`, `status`, `km_total`, `descricao`, `data_cadastro`) VALUES
(1, 'Honda', 'Civic', 'Sedan', '2024', 'Preto', 'flex', 'automatico', 1, 400.00, 'disponivel', 0, NULL, '2025-10-01 15:13:14'),
(3, 'Bicicleta', 'Multilaser', NULL, '2010', 'amarela', 'gasolina', 'manual', 1, 120.00, 'disponivel', 0, NULL, '2025-10-02 14:24:09'),
(7, 'A', 'a', NULL, '0000', 'asd', 'gasolina', 'manual', 1, 120.00, 'reservado', 0, NULL, '2025-10-04 03:04:43'),
(8, 'Honda', 'HRV', NULL, '2025', 'Vinho', 'flex', 'automatico', 1, 100.00, 'reservado', 0, NULL, '2025-10-09 16:55:46'),
(9, 'Chevrolet', 'Onix', 'Hatch', '2024', 'Branco', 'gasolina', 'manual', 1, 120.00, 'reservado', 0, NULL, '2025-10-15 15:07:53');

-- --------------------------------------------------------

--
-- Estrutura da tabela `tbl_documentos_carro`
--

CREATE TABLE `tbl_documentos_carro` (
  `cod_documento` int(11) NOT NULL,
  `cod_carro` int(11) NOT NULL,
  `tipo_documento` enum('IPVA','Licenciamento','Seguro','Outro') NOT NULL,
  `numero_documento` varchar(100) NOT NULL,
  `data_validade` date NOT NULL,
  `status` enum('valido','vencido') DEFAULT 'valido',
  `observacoes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(26, 164, '72260-631', 'Quadra QNO 16 Conjunto 31', '04', '', 'Ceilândia Norte (Ceilândia)', 'Brasília', 'DF');

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

-- --------------------------------------------------------

--
-- Estrutura da tabela `tbl_fotos_carros`
--

CREATE TABLE `tbl_fotos_carros` (
  `cod_foto` int(11) NOT NULL,
  `cod_carro` int(11) NOT NULL,
  `url_foto` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `tbl_manutencoes`
--

CREATE TABLE `tbl_manutencoes` (
  `cod_manutencao` int(11) NOT NULL,
  `cod_carro` int(11) NOT NULL,
  `tipo_servico` varchar(255) NOT NULL,
  `custo` decimal(10,2) NOT NULL,
  `responsavel` varchar(255) NOT NULL,
  `data` datetime DEFAULT current_timestamp(),
  `observacoes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `tbl_multas`
--

CREATE TABLE `tbl_multas` (
  `cod_multa` int(11) NOT NULL,
  `cod_usuario` int(11) NOT NULL,
  `cod_carro` int(11) NOT NULL,
  `descricao` text NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `status` enum('pendente','paga','contestada') DEFAULT 'pendente',
  `data` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `status` enum('pendente','ativa','concluida','cancelada') DEFAULT 'pendente',
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
(11, 149, 7, NULL, '2025-10-16 00:00:00', '2025-10-29 00:00:00', 'cancelada', 0, NULL, 1404.00, NULL, 0),
(12, 150, 3, NULL, '2025-10-09 00:00:00', '2025-10-19 00:00:00', 'cancelada', 0, NULL, 1080.00, NULL, 0),
(13, 101, 3, NULL, '2025-10-09 00:00:00', '2025-10-19 00:00:00', 'cancelada', 0, NULL, 1080.00, NULL, 0),
(14, 151, 7, NULL, '2025-10-10 00:00:00', '2025-11-07 00:00:00', 'cancelada', 0, NULL, 3024.00, NULL, 0),
(15, 152, 3, NULL, '2025-10-09 00:00:00', '2025-10-22 00:00:00', 'cancelada', 0, NULL, 1404.00, NULL, 0),
(16, 152, 3, NULL, '2025-10-15 00:00:00', '2025-10-27 00:00:00', 'cancelada', 0, NULL, 1296.00, NULL, 0),
(17, 153, 8, NULL, '2025-10-11 00:00:00', '2025-10-18 00:00:00', 'pendente', 0, NULL, 630.00, NULL, 0),
(18, 156, 1, NULL, '2025-10-09 00:00:00', '2025-10-20 00:00:00', 'cancelada', 0, NULL, 3960.00, NULL, 0),
(19, 163, 3, NULL, '2025-10-16 00:00:00', '2025-10-21 00:00:00', 'cancelada', 0, NULL, 600.00, NULL, 0),
(20, 164, 9, NULL, '2025-10-16 00:00:00', '2025-10-21 00:00:00', 'pendente', 0, NULL, 600.00, NULL, 0);

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
(140, 'gerente', 'gerente@gmail.com', '$2y$10$4iXCvP6GFq9/ytduHiTPM.ebRvlG/IDriFdlbMda6mUI5XMLe7MgO', 'gerente', '2025-10-06 15:54:48', NULL, NULL, 0, 'ativo'),
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
(162, 'd', 'd@gmail.com', '$2y$10$u316u9vmKV07I.FBVLaZTu6oXUEZxIbQHAlHjiDZJ5Q.fQncEa0zu', 'usuario', '2025-10-10 17:21:26', '(61) 99577-0554', '080.608.401-42', 1, 'ativo'),
(163, 'aaa', 'aa@gmail.com', '$2y$10$8IvYX3RrMn288xOK/3LfNelvAJBhxVbluo38nIvLGAhOKyEqKBuMS', 'cliente', '2025-10-16 14:20:50', '(66) 6666-6666', '080.608.401-42', 1, 'ativo'),
(164, 'a', 'ab@gmail.com', '$2y$10$e6NtHukSuqwEsaiqFxh/Jetn7KDs4mkDaF/b2AzPO8SinF7Fgn4Xa', 'cliente', '2025-10-16 14:53:41', '(66) 6666-6666', '080.608.401-42', 1, 'ativo');

--
-- Índices para tabelas despejadas
--

--
-- Índices para tabela `tbl_avaliacoes_carro`
--
ALTER TABLE `tbl_avaliacoes_carro`
  ADD PRIMARY KEY (`cod_avaliacao`),
  ADD KEY `cod_carro` (`cod_carro`),
  ADD KEY `cod_reserva` (`cod_reserva`);

--
-- Índices para tabela `tbl_carros`
--
ALTER TABLE `tbl_carros`
  ADD PRIMARY KEY (`cod_carro`);

--
-- Índices para tabela `tbl_documentos_carro`
--
ALTER TABLE `tbl_documentos_carro`
  ADD PRIMARY KEY (`cod_documento`),
  ADD KEY `cod_carro` (`cod_carro`);

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
-- Índices para tabela `tbl_fotos_carros`
--
ALTER TABLE `tbl_fotos_carros`
  ADD PRIMARY KEY (`cod_foto`),
  ADD KEY `cod_carro` (`cod_carro`);

--
-- Índices para tabela `tbl_manutencoes`
--
ALTER TABLE `tbl_manutencoes`
  ADD PRIMARY KEY (`cod_manutencao`),
  ADD KEY `cod_carro` (`cod_carro`);

--
-- Índices para tabela `tbl_multas`
--
ALTER TABLE `tbl_multas`
  ADD PRIMARY KEY (`cod_multa`),
  ADD KEY `cod_usuario` (`cod_usuario`),
  ADD KEY `cod_carro` (`cod_carro`);

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
-- AUTO_INCREMENT de tabela `tbl_avaliacoes_carro`
--
ALTER TABLE `tbl_avaliacoes_carro`
  MODIFY `cod_avaliacao` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `tbl_carros`
--
ALTER TABLE `tbl_carros`
  MODIFY `cod_carro` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de tabela `tbl_documentos_carro`
--
ALTER TABLE `tbl_documentos_carro`
  MODIFY `cod_documento` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `tbl_enderecos`
--
ALTER TABLE `tbl_enderecos`
  MODIFY `cod_endereco` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT de tabela `tbl_favoritos`
--
ALTER TABLE `tbl_favoritos`
  MODIFY `cod_favorito` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `tbl_fotos_carros`
--
ALTER TABLE `tbl_fotos_carros`
  MODIFY `cod_foto` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `tbl_manutencoes`
--
ALTER TABLE `tbl_manutencoes`
  MODIFY `cod_manutencao` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `tbl_multas`
--
ALTER TABLE `tbl_multas`
  MODIFY `cod_multa` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `tbl_planos_aluguel`
--
ALTER TABLE `tbl_planos_aluguel`
  MODIFY `cod_plano` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `tbl_reservas`
--
ALTER TABLE `tbl_reservas`
  MODIFY `cod_reserva` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de tabela `tbl_usuarios`
--
ALTER TABLE `tbl_usuarios`
  MODIFY `cod_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=165;

--
-- Restrições para despejos de tabelas
--

--
-- Limitadores para a tabela `tbl_avaliacoes_carro`
--
ALTER TABLE `tbl_avaliacoes_carro`
  ADD CONSTRAINT `tbl_avaliacoes_carro_ibfk_1` FOREIGN KEY (`cod_carro`) REFERENCES `tbl_carros` (`cod_carro`) ON DELETE CASCADE,
  ADD CONSTRAINT `tbl_avaliacoes_carro_ibfk_2` FOREIGN KEY (`cod_reserva`) REFERENCES `tbl_reservas` (`cod_reserva`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `tbl_documentos_carro`
--
ALTER TABLE `tbl_documentos_carro`
  ADD CONSTRAINT `tbl_documentos_carro_ibfk_1` FOREIGN KEY (`cod_carro`) REFERENCES `tbl_carros` (`cod_carro`) ON DELETE CASCADE;

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
-- Limitadores para a tabela `tbl_fotos_carros`
--
ALTER TABLE `tbl_fotos_carros`
  ADD CONSTRAINT `tbl_fotos_carros_ibfk_1` FOREIGN KEY (`cod_carro`) REFERENCES `tbl_carros` (`cod_carro`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `tbl_manutencoes`
--
ALTER TABLE `tbl_manutencoes`
  ADD CONSTRAINT `tbl_manutencoes_ibfk_1` FOREIGN KEY (`cod_carro`) REFERENCES `tbl_carros` (`cod_carro`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `tbl_multas`
--
ALTER TABLE `tbl_multas`
  ADD CONSTRAINT `tbl_multas_ibfk_1` FOREIGN KEY (`cod_usuario`) REFERENCES `tbl_usuarios` (`cod_usuario`) ON DELETE CASCADE,
  ADD CONSTRAINT `tbl_multas_ibfk_2` FOREIGN KEY (`cod_carro`) REFERENCES `tbl_carros` (`cod_carro`) ON DELETE CASCADE;

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
