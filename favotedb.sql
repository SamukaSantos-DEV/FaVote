-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 26/11/2025 às 01:20
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
-- Banco de dados: `favotedb`
--
CREATE DATABASE IF NOT EXISTS `favotedb` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
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
(10, '2781392513007', 'Pedro Henrique Cavenaghi', 'pedro.cavenaghi@fatec.sp.gov.br', '$2y$10$2XMjlBhCZxipHX.zcUBJIe8KpnY4k.9114Wjpzn5jSk8U5UqVanfC', 3, '2025-11-11 23:17:30', '2025-11-18 23:43:26'),
(11, '2781392323007', 'teste aluno', 'admin2@fatec.sp.gov.br', '$2y$10$CszZaDm4AIvzQLsJMkzcL.xpDxwQpwbkYmV8IhrfZ2OoYKQIR5dcS', 5, '2025-11-11 23:18:28', '2025-11-13 19:26:56'),
(12, '2780642323004', 'Pedro Mantoan', 'pedro.mantoan@fatec.sp.gov.br', '$2y$10$QrDpWnYHWnHTDOFna5pQhOmzVt3mT3RMJbsge60Gwz7HpaFFGjN3G', 11, '2025-11-11 23:19:17', '2025-11-11 23:19:17'),
(13, '2781392513014', 'João Pedro Baradelli Pavan', 'joao.pavan@fatec.sp.gov.br', '$2y$10$F2./y1Ph/j6NLSYB08uZeuLloqRMdeC.vsgHPTpL1Al1GCX9E8Zkq', 2, '2025-11-18 23:10:20', '2025-11-18 23:10:20'),
(17, '2781392513013', 'João Pedro Baradelli Pavan', 'joao.pavan0@fatec.sp.gov.br', '$2y$10$qqm5MGqSxdsA3zIrimKYCewfecXDsEEmwkiLSLvndNjt54B4TJ45a', 2, '2025-11-19 23:36:24', '2025-11-19 23:36:24'),
(19, '2025342343243', 'João Pedro', 'joao.pavan01@fatec.sp.gov.br', 'escolaestadual10', 2, '2025-11-19 23:58:13', '2025-11-19 23:58:51');

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
(10, 19, '2781392513007', 'as'),
(11, 19, '2781392513014', 'Proposta'),
(13, 17, '2781392513007', 'as'),
(14, 17, '2781392513014', 'Proposta'),
(15, 17, '2781392513007', 'as'),
(16, 17, '2781392513014', 'Proposta');

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
(17, 'teste turma 2', 'teste', '2025-11-16 14:21:35', '2025-11-12 14:23:00', '2025-11-27 18:22:00', 1, 2),
(19, 'teste turma 1 passada', 'asas', '2025-11-19 20:46:16', '2025-11-12 20:48:00', '2025-11-12 20:49:00', 0, 1);

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
(10, 'asas', 'asas', '2025-11-20 08:36:04');

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
(5, 19, '2780642323004', 10, '2025-11-20 08:14:51'),
(6, 19, '2781392323007', 11, '2025-11-20 08:14:51'),
(8, 19, '2781392513007', 11, '2025-11-20 08:14:51');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT de tabela `candidatos`
--
ALTER TABLE `candidatos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT de tabela `cursos`
--
ALTER TABLE `cursos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `eleicoes`
--
ALTER TABLE `eleicoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT de tabela `noticias`
--
ALTER TABLE `noticias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

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
