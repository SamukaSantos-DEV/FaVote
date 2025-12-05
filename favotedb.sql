-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 26/11/2025 às 21:49
-- Versão do servidor: 11.8.3-MariaDB-log
-- Versão do PHP: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `favotedb`
--
CREATE DATABASE IF NOT EXISTS `favotedb` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `favotedb`;

-- --------------------------------------------------------

--
-- Estrutura para tabela `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `nome_usuario` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `ra` varchar(20) DEFAULT NULL,
  `senha` varchar(255) NOT NULL,
  `criado_data` timestamp NOT NULL DEFAULT current_timestamp(),
  `atualizado_data` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `curso_nome` varchar(250) NOT NULL,
  `semestre_nome` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `admin`
--

INSERT INTO `admin` (`id`, `nome_usuario`, `email`, `ra`, `senha`, `criado_data`, `atualizado_data`, `curso_nome`, `semestre_nome`) VALUES
(1, 'administrador', 'admin@fatec.sp.gov.br', '27800000000', '$2y$10$FZ17klPO.O9dQ8BLbQTxn.Tg09JjSCS8c0MQccWwAxigKD9sMutEC', '2025-11-11 00:38:21', '2025-11-12 01:11:37', 'Sem Curso', 'Sem Semestre');

-- --------------------------------------------------------

--
-- Estrutura para tabela `alunos`
--

CREATE TABLE `alunos` (
  `id` int(11) NOT NULL,
  `ra` varchar(20) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `email_institucional` varchar(100) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `turma_id` int(11) DEFAULT NULL,
  `criado_data` timestamp NOT NULL DEFAULT current_timestamp(),
  `atualizado_data` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `alunos`
--

INSERT INTO `alunos` (`id`, `ra`, `nome`, `email_institucional`, `senha`, `turma_id`, `criado_data`, `atualizado_data`) VALUES
(1, '1111111111111', 'Branco', 'branco@fatec.sp.gov.br', '1234', NULL, '2025-11-26 01:05:55', '2025-11-26 01:06:39'),
(14, '2781392513023', 'Samuel Santos Oliveira', 'samuel.santos@fatec.sp.gov.br', '$2y$10$22AJJ7uRtoDYNsYyB8tGjulIz15D1Oggge0S8x7IvdD9K2dOgTcGm', 2, '2025-11-25 23:50:06', '2025-11-25 23:50:06'),
(15, '2781392513007', 'Pedro esta testando desempenho', 'pedro.santos@fatec.sp.gov.br', '$2y$10$KcicdnmVfV7zy9uKZLUDAeMyZsNH8mqYptEb8fm6QEnuKDwaKy2Xm', 2, '2025-11-25 23:51:24', '2025-11-26 00:14:39'),
(16, '2781392523001', 'João Paulo Gomes', 'joao.gomes@fatec.sp.gov.br', '$2y$10$Z83zN1Ld6ljFP4pEeOhnj.pu1GxP9F5gc/.oFixTVlzC85Po/Geqq', 1, '2025-11-25 23:51:59', '2025-11-25 23:51:59'),
(17, '2780642323004', 'Pedro Mantoan', 'pedro.mantoan@fatec.sp.gov.br', '$2y$10$7oWXvahbeYsXRpl49PX7/uE5J4Z4Ye6SAr4bu91aHCKujAU9oe6Cm', 11, '2025-11-25 23:52:44', '2025-11-25 23:52:44'),
(18, '2781392513014', 'João Pedro Baradelli Pavan', 'joao.pavan01@fatec.sp.gov.br', '$2y$10$g27LgO9dbrpzWHNMNLMT/O99EISE7WhHtLym7Dl9.Xs0BjM0fWbWO', 2, '2025-11-25 23:53:41', '2025-11-25 23:53:41'),
(19, '2781392513019', 'João Lázaro Tavares Vieira', 'joao.vieira@fatec.sp.gov.br', '$2y$10$d.647Iy3FPAyosz30/pGB.1waMb2nXPuXlliOMtS9P5/uwJplYySS', 2, '2025-11-25 23:55:02', '2025-11-25 23:55:02');

-- --------------------------------------------------------

--
-- Estrutura para tabela `candidatos`
--

CREATE TABLE `candidatos` (
  `id` int(11) NOT NULL,
  `eleicao_id` int(11) NOT NULL,
  `aluno_ra` varchar(20) NOT NULL,
  `proposta` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `candidatos`
--

INSERT INTO `candidatos` (`id`, `eleicao_id`, `aluno_ra`, `proposta`) VALUES
(1, 1, '1111111111111', 'Voto em Branco'),
(10, 9, '2781392523001', 'Quero ser representante'),
(11, 8, '2781392513014', 'Quero ser representante');

-- --------------------------------------------------------

--
-- Estrutura para tabela `cursos`
--

CREATE TABLE `cursos` (
  `id` int(11) NOT NULL,
  `nome` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `cursos`
--

INSERT INTO `cursos` (`id`, `nome`) VALUES
(1, 'DSM'),
(2, 'GE'),
(3, 'GPI');

-- --------------------------------------------------------

--
-- Estrutura para tabela `eleicoes`
--

CREATE TABLE `eleicoes` (
  `id` int(11) NOT NULL,
  `titulo` varchar(100) NOT NULL,
  `descricao` text DEFAULT NULL,
  `dataPostagem` datetime NOT NULL DEFAULT current_timestamp(),
  `data_inicio` datetime NOT NULL,
  `data_fim` datetime NOT NULL,
  `ativa` tinyint(1) DEFAULT 1,
  `turma_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `eleicoes`
--

INSERT INTO `eleicoes` (`id`, `titulo`, `descricao`, `dataPostagem`, `data_inicio`, `data_fim`, `ativa`, `turma_id`) VALUES
(1, 'branco', 'branco', '2025-11-26 01:08:28', '2025-11-26 01:08:03', '2025-11-26 01:08:03', 2, 2),
(8, 'ELEIÇÃO DE REPRESENTANTE 2º DSM (N)', 'Votação para eleição de representante e vice-representante.', '2025-11-17 00:00:00', '2025-11-17 00:00:00', '2025-11-24 23:59:00', 0, 2),
(9, 'ELEIÇÃO DE REPRESENTANTE 1º DSM (N)', 'Votação para eleição de representante e vice-representante.', '2025-11-26 00:09:00', '2025-11-20 00:00:00', '2025-12-03 23:59:00', 1, 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `noticias`
--

CREATE TABLE `noticias` (
  `id` int(11) NOT NULL,
  `titulo` varchar(250) NOT NULL,
  `descricao` varchar(250) NOT NULL,
  `dataPublicacao` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `noticias`
--

INSERT INTO `noticias` (`id`, `titulo`, `descricao`, `dataPublicacao`) VALUES
(11, 'ELEIÇÃO DE REPRESENTANTE 1º DSM (N)', 'ELEIÇÕES ABERTAS! DIVULGUE!', '2025-11-26 00:09:58'),
(12, 'ELEIÇÃO DE REPRESENTANTE 2º DSM (N)', 'ELEIÇÕES ABERTAS! DIVULGUE!', '2025-11-26 00:10:56');

-- --------------------------------------------------------

--
-- Estrutura para tabela `semestres`
--

CREATE TABLE `semestres` (
  `id` int(11) NOT NULL,
  `nome` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `semestres`
--

INSERT INTO `semestres` (`id`, `nome`) VALUES
(1, '1° Semestre'),
(2, '2° Semestre'),
(3, '3° Semestre'),
(4, '4° Semestre'),
(5, '5° Semestre'),
(6, '6° Semestre');

-- --------------------------------------------------------

--
-- Estrutura para tabela `turmas`
--

CREATE TABLE `turmas` (
  `id` int(11) NOT NULL,
  `curso_id` int(11) NOT NULL,
  `semestre_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `turmas`
--

INSERT INTO `turmas` (`id`, `curso_id`, `semestre_id`) VALUES
(1, 1, 1),
(2, 1, 2),
(3, 1, 3),
(4, 1, 4),
(5, 1, 5),
(6, 1, 6),
(7, 2, 1),
(8, 2, 2),
(9, 2, 3),
(10, 2, 4),
(11, 2, 5),
(12, 2, 6),
(13, 3, 1),
(14, 3, 2),
(15, 3, 3),
(16, 3, 4),
(17, 3, 5),
(18, 3, 6);

-- --------------------------------------------------------

--
-- Estrutura para tabela `votos`
--

CREATE TABLE `votos` (
  `id` int(11) NOT NULL,
  `eleicao_id` int(11) NOT NULL,
  `aluno_ra` varchar(20) NOT NULL,
  `candidato_id` int(11) NOT NULL,
  `data_voto` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `votos`
--

INSERT INTO `votos` (`id`, `eleicao_id`, `aluno_ra`, `candidato_id`, `data_voto`) VALUES
(8, 8, '2781392513023', 1, '2025-11-26 01:11:02');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nome_usuario` (`nome_usuario`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `ra` (`ra`);

--
-- Índices de tabela `alunos`
--
ALTER TABLE `alunos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ra` (`ra`),
  ADD UNIQUE KEY `email_institucional` (`email_institucional`),
  ADD KEY `turma_id` (`turma_id`);

--
-- Índices de tabela `candidatos`
--
ALTER TABLE `candidatos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `eleicao_id` (`eleicao_id`),
  ADD KEY `aluno_ra` (`aluno_ra`);

--
-- Índices de tabela `cursos`
--
ALTER TABLE `cursos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nome` (`nome`);

--
-- Índices de tabela `eleicoes`
--
ALTER TABLE `eleicoes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_eleicoes_turma` (`turma_id`);

--
-- Índices de tabela `noticias`
--
ALTER TABLE `noticias`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `semestres`
--
ALTER TABLE `semestres`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nome` (`nome`);

--
-- Índices de tabela `turmas`
--
ALTER TABLE `turmas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `curso_semestre` (`curso_id`,`semestre_id`),
  ADD KEY `semestre_id` (`semestre_id`);

--
-- Índices de tabela `votos`
--
ALTER TABLE `votos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `eleicao_id` (`eleicao_id`,`aluno_ra`),
  ADD KEY `aluno_ra` (`aluno_ra`),
  ADD KEY `candidato_id` (`candidato_id`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `alunos`
--
ALTER TABLE `alunos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=72;

--
-- AUTO_INCREMENT de tabela `candidatos`
--
ALTER TABLE `candidatos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de tabela `cursos`
--
ALTER TABLE `cursos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `eleicoes`
--
ALTER TABLE `eleicoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de tabela `noticias`
--
ALTER TABLE `noticias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de tabela `semestres`
--
ALTER TABLE `semestres`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `turmas`
--
ALTER TABLE `turmas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de tabela `votos`
--
ALTER TABLE `votos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `alunos`
--
ALTER TABLE `alunos`
  ADD CONSTRAINT `alunos_ibfk_1` FOREIGN KEY (`turma_id`) REFERENCES `turmas` (`id`);

--
-- Restrições para tabelas `candidatos`
--
ALTER TABLE `candidatos`
  ADD CONSTRAINT `candidatos_ibfk_1` FOREIGN KEY (`eleicao_id`) REFERENCES `eleicoes` (`id`),
  ADD CONSTRAINT `candidatos_ibfk_2` FOREIGN KEY (`aluno_ra`) REFERENCES `alunos` (`ra`);

--
-- Restrições para tabelas `eleicoes`
--
ALTER TABLE `eleicoes`
  ADD CONSTRAINT `fk_eleicoes_turma` FOREIGN KEY (`turma_id`) REFERENCES `turmas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `turmas`
--
ALTER TABLE `turmas`
  ADD CONSTRAINT `turmas_ibfk_1` FOREIGN KEY (`curso_id`) REFERENCES `cursos` (`id`),
  ADD CONSTRAINT `turmas_ibfk_2` FOREIGN KEY (`semestre_id`) REFERENCES `semestres` (`id`);

--
-- Restrições para tabelas `votos`
--
ALTER TABLE `votos`
  ADD CONSTRAINT `votos_ibfk_1` FOREIGN KEY (`eleicao_id`) REFERENCES `eleicoes` (`id`),
  ADD CONSTRAINT `votos_ibfk_2` FOREIGN KEY (`aluno_ra`) REFERENCES `alunos` (`ra`),
  ADD CONSTRAINT `votos_ibfk_3` FOREIGN KEY (`candidato_id`) REFERENCES `candidatos` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
