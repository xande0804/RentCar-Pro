-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3308:3308
-- Tempo de geração: 07/10/2025 às 04:30
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.2.12

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
-- Estrutura para tabela `tbl_avaliacoes_carro`
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
-- Estrutura para tabela `tbl_carros`
--

CREATE TABLE `tbl_carros` (
  `cod_carro` int(11) NOT NULL,
  `marca` varchar(100) NOT NULL,
  `modelo` varchar(100) NOT NULL,
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
-- Despejando dados para a tabela `tbl_carros`
--

INSERT INTO `tbl_carros` (`cod_carro`, `marca`, `modelo`, `ano`, `cor`, `combustivel`, `cambio`, `ar_condicionado`, `preco_diaria`, `status`, `km_total`, `descricao`, `data_cadastro`) VALUES
(1, 'Honda', 'Civic', '2024', 'Preto', 'flex', 'automatico', 1, 400.00, 'disponivel', 0, NULL, '2025-10-01 15:13:14'),
(3, 'Bicicleta', 'Multilaser', '2010', 'amarela', 'gasolina', 'manual', 1, 120.00, 'disponivel', 0, NULL, '2025-10-02 14:24:09'),
(7, 'A', 'a', '0000', 'asd', 'gasolina', 'manual', 1, 120.00, 'reservado', 0, NULL, '2025-10-04 03:04:43');

-- --------------------------------------------------------

--
-- Estrutura para tabela `tbl_documentos_carro`
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
-- Estrutura para tabela `tbl_enderecos`
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
-- Despejando dados para a tabela `tbl_enderecos`
--

INSERT INTO `tbl_enderecos` (`cod_endereco`, `cod_usuario`, `cep`, `logradouro`, `numero`, `complemento`, `bairro`, `cidade`, `estado`) VALUES
(4, 101, '72260-631', 'Quadra QNO 16 Conjunto 31', '16', 'as', 'Ceilândia Norte (Ceilândia)', 'Brasília', 'DF'),
(6, 138, '72260-631', 'Quadra QNO 16 Conjunto 31', '16', 'casa 1', 'Ceilândia Norte (Ceilândia)', 'Brasília', 'DF'),
(7, 139, '72260-631', 'Quadra QNO 16 Conjunto 31', '16', 'casa 04', 'Ceilândia Norte (Ceilândia)', 'Brasília', 'DF');

-- --------------------------------------------------------

--
-- Estrutura para tabela `tbl_favoritos`
--

CREATE TABLE `tbl_favoritos` (
  `cod_favorito` int(11) NOT NULL,
  `cod_usuario` int(11) NOT NULL,
  `cod_carro` int(11) NOT NULL,
  `data_adicionado` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tbl_fotos_carros`
--

CREATE TABLE `tbl_fotos_carros` (
  `cod_foto` int(11) NOT NULL,
  `cod_carro` int(11) NOT NULL,
  `url_foto` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tbl_manutencoes`
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
-- Estrutura para tabela `tbl_multas`
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
-- Estrutura para tabela `tbl_planos_aluguel`
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
-- Despejando dados para a tabela `tbl_planos_aluguel`
--

INSERT INTO `tbl_planos_aluguel` (`cod_plano`, `nome`, `descricao`, `dias_minimos`, `multiplicador_valor`, `data_criacao`) VALUES
(1, 'Plano Semanal', 'Desconto para aluguéis de 7 dias ou mais.', 7, 0.90, '2025-10-04 01:36:58'),
(2, 'Plano Mensal', 'Desconto para aluguéis de 30 dias ou mais.', 30, 0.80, '2025-10-04 01:36:58');

-- --------------------------------------------------------

--
-- Estrutura para tabela `tbl_reservas`
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
-- Despejando dados para a tabela `tbl_reservas`
--

INSERT INTO `tbl_reservas` (`cod_reserva`, `cod_usuario`, `cod_carro`, `cod_plano`, `data_inicio`, `data_fim`, `status`, `quilometragem_inicial`, `quilometragem_final`, `valor_total`, `assinatura_digital`, `pagamento_realizado`) VALUES
(7, 101, 7, NULL, '2025-10-07 00:00:00', '2025-10-21 00:00:00', 'pendente', 0, NULL, 1512.00, NULL, 0);

-- --------------------------------------------------------

--
-- Estrutura para tabela `tbl_usuarios`
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
  `cadastro_completo` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `tbl_usuarios`
--

INSERT INTO `tbl_usuarios` (`cod_usuario`, `nome`, `email`, `senha`, `perfil`, `data_cadastro`, `telefone`, `cpf`, `cadastro_completo`) VALUES
(101, 'administrador', 'admin@gmail.com', '$2y$10$F83YSjrqye9SNu6.7yZSgufsjVy41z2AEHSs5mR4Z1X9WPGz8wzde', 'admin', '2025-09-10 15:40:18', '(61) 99577-0554', '080.608.401-42', 1),
(137, 'usuario', 'usuario@gmail.com', '$2y$10$48wAwUUb8U.2jwZxz1HI1ulRJGSGrMmWNrZkkgZlAKgJedMRQnb9O', 'usuario', '2025-10-06 15:53:39', NULL, NULL, 0),
(138, 'Usuario Completo', 'usuariocompleto@gmail.com', '$2y$10$KHJYKvA0uq2knlR.871FsOEETiCJDBbam7IGrKznAmtWaudjj9DRu', 'usuario', '2025-10-06 15:54:01', NULL, NULL, 1),
(139, 'cliente', 'cliente@gmail.com', '$2y$10$jcY7JP8LdXr888EAgK.vNek8S0S1mrbXtsmy6TksiFNjygwiwLfpu', 'cliente', '2025-10-06 15:54:37', '(61) 9957-7067', '080.608.401-42', 1),
(140, 'gerente', 'gerente@gmail.com', '$2y$10$4iXCvP6GFq9/ytduHiTPM.ebRvlG/IDriFdlbMda6mUI5XMLe7MgO', 'gerente', '2025-10-06 15:54:48', NULL, NULL, 0),
(141, 'funcionario', 'funcionario@gmail.com', '$2y$10$pW1jbSeNDBvzPDcGQG1jpu5fLC5JqqQmQM7oWNy.KRmx9Q0oj1qVG', 'funcionario', '2025-10-06 15:54:57', NULL, NULL, 0);

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `tbl_avaliacoes_carro`
--
ALTER TABLE `tbl_avaliacoes_carro`
  ADD PRIMARY KEY (`cod_avaliacao`),
  ADD KEY `cod_carro` (`cod_carro`),
  ADD KEY `cod_reserva` (`cod_reserva`);

--
-- Índices de tabela `tbl_carros`
--
ALTER TABLE `tbl_carros`
  ADD PRIMARY KEY (`cod_carro`);

--
-- Índices de tabela `tbl_documentos_carro`
--
ALTER TABLE `tbl_documentos_carro`
  ADD PRIMARY KEY (`cod_documento`),
  ADD KEY `cod_carro` (`cod_carro`);

--
-- Índices de tabela `tbl_enderecos`
--
ALTER TABLE `tbl_enderecos`
  ADD PRIMARY KEY (`cod_endereco`),
  ADD UNIQUE KEY `cod_usuario` (`cod_usuario`);

--
-- Índices de tabela `tbl_favoritos`
--
ALTER TABLE `tbl_favoritos`
  ADD PRIMARY KEY (`cod_favorito`),
  ADD KEY `cod_usuario` (`cod_usuario`),
  ADD KEY `cod_carro` (`cod_carro`);

--
-- Índices de tabela `tbl_fotos_carros`
--
ALTER TABLE `tbl_fotos_carros`
  ADD PRIMARY KEY (`cod_foto`),
  ADD KEY `cod_carro` (`cod_carro`);

--
-- Índices de tabela `tbl_manutencoes`
--
ALTER TABLE `tbl_manutencoes`
  ADD PRIMARY KEY (`cod_manutencao`),
  ADD KEY `cod_carro` (`cod_carro`);

--
-- Índices de tabela `tbl_multas`
--
ALTER TABLE `tbl_multas`
  ADD PRIMARY KEY (`cod_multa`),
  ADD KEY `cod_usuario` (`cod_usuario`),
  ADD KEY `cod_carro` (`cod_carro`);

--
-- Índices de tabela `tbl_planos_aluguel`
--
ALTER TABLE `tbl_planos_aluguel`
  ADD PRIMARY KEY (`cod_plano`);

--
-- Índices de tabela `tbl_reservas`
--
ALTER TABLE `tbl_reservas`
  ADD PRIMARY KEY (`cod_reserva`),
  ADD KEY `cod_usuario` (`cod_usuario`),
  ADD KEY `cod_carro` (`cod_carro`),
  ADD KEY `cod_plano` (`cod_plano`);

--
-- Índices de tabela `tbl_usuarios`
--
ALTER TABLE `tbl_usuarios`
  ADD PRIMARY KEY (`cod_usuario`);

--
-- AUTO_INCREMENT para tabelas despejadas
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
  MODIFY `cod_carro` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de tabela `tbl_documentos_carro`
--
ALTER TABLE `tbl_documentos_carro`
  MODIFY `cod_documento` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `tbl_enderecos`
--
ALTER TABLE `tbl_enderecos`
  MODIFY `cod_endereco` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

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
  MODIFY `cod_reserva` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de tabela `tbl_usuarios`
--
ALTER TABLE `tbl_usuarios`
  MODIFY `cod_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=146;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `tbl_avaliacoes_carro`
--
ALTER TABLE `tbl_avaliacoes_carro`
  ADD CONSTRAINT `tbl_avaliacoes_carro_ibfk_1` FOREIGN KEY (`cod_carro`) REFERENCES `tbl_carros` (`cod_carro`) ON DELETE CASCADE,
  ADD CONSTRAINT `tbl_avaliacoes_carro_ibfk_2` FOREIGN KEY (`cod_reserva`) REFERENCES `tbl_reservas` (`cod_reserva`) ON DELETE CASCADE;

--
-- Restrições para tabelas `tbl_documentos_carro`
--
ALTER TABLE `tbl_documentos_carro`
  ADD CONSTRAINT `tbl_documentos_carro_ibfk_1` FOREIGN KEY (`cod_carro`) REFERENCES `tbl_carros` (`cod_carro`) ON DELETE CASCADE;

--
-- Restrições para tabelas `tbl_enderecos`
--
ALTER TABLE `tbl_enderecos`
  ADD CONSTRAINT `fk_endereco_usuario` FOREIGN KEY (`cod_usuario`) REFERENCES `tbl_usuarios` (`cod_usuario`) ON DELETE CASCADE;

--
-- Restrições para tabelas `tbl_favoritos`
--
ALTER TABLE `tbl_favoritos`
  ADD CONSTRAINT `tbl_favoritos_ibfk_1` FOREIGN KEY (`cod_usuario`) REFERENCES `tbl_usuarios` (`cod_usuario`) ON DELETE CASCADE,
  ADD CONSTRAINT `tbl_favoritos_ibfk_2` FOREIGN KEY (`cod_carro`) REFERENCES `tbl_carros` (`cod_carro`) ON DELETE CASCADE;

--
-- Restrições para tabelas `tbl_fotos_carros`
--
ALTER TABLE `tbl_fotos_carros`
  ADD CONSTRAINT `tbl_fotos_carros_ibfk_1` FOREIGN KEY (`cod_carro`) REFERENCES `tbl_carros` (`cod_carro`) ON DELETE CASCADE;

--
-- Restrições para tabelas `tbl_manutencoes`
--
ALTER TABLE `tbl_manutencoes`
  ADD CONSTRAINT `tbl_manutencoes_ibfk_1` FOREIGN KEY (`cod_carro`) REFERENCES `tbl_carros` (`cod_carro`) ON DELETE CASCADE;

--
-- Restrições para tabelas `tbl_multas`
--
ALTER TABLE `tbl_multas`
  ADD CONSTRAINT `tbl_multas_ibfk_1` FOREIGN KEY (`cod_usuario`) REFERENCES `tbl_usuarios` (`cod_usuario`) ON DELETE CASCADE,
  ADD CONSTRAINT `tbl_multas_ibfk_2` FOREIGN KEY (`cod_carro`) REFERENCES `tbl_carros` (`cod_carro`) ON DELETE CASCADE;

--
-- Restrições para tabelas `tbl_reservas`
--
ALTER TABLE `tbl_reservas`
  ADD CONSTRAINT `tbl_reservas_ibfk_1` FOREIGN KEY (`cod_usuario`) REFERENCES `tbl_usuarios` (`cod_usuario`) ON DELETE CASCADE,
  ADD CONSTRAINT `tbl_reservas_ibfk_2` FOREIGN KEY (`cod_carro`) REFERENCES `tbl_carros` (`cod_carro`) ON DELETE CASCADE,
  ADD CONSTRAINT `tbl_reservas_ibfk_3` FOREIGN KEY (`cod_plano`) REFERENCES `tbl_planos_aluguel` (`cod_plano`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
