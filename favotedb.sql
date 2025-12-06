-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 06/12/2025 às 20:14
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
CREATE DATABASE IF NOT EXISTS `favotedb` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
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
(15, '2781392523007', 'Pedro Cavenaghi', 'pedro.santos@fatec.sp.gov.br', '$2y$10$KcicdnmVfV7zy9uKZLUDAeMyZsNH8mqYptEb8fm6QEnuKDwaKy2Xm', 1, '2025-11-25 23:51:24', '2025-12-06 00:46:51'),
(17, '2780642323004', 'Pedro Mantoan', 'pedro.mantoan@fatec.sp.gov.br', '$2y$10$7oWXvahbeYsXRpl49PX7/uE5J4Z4Ye6SAr4bu91aHCKujAU9oe6Cm', 11, '2025-11-25 23:52:44', '2025-11-25 23:52:44'),
(18, '2781392513014', 'João Pedro Baradelli Pavan', 'joao.pavan01@fatec.sp.gov.br', '$2y$10$g27LgO9dbrpzWHNMNLMT/O99EISE7WhHtLym7Dl9.Xs0BjM0fWbWO', 2, '2025-11-25 23:53:41', '2025-11-25 23:53:41'),
(80, '2781392513034', 'Samuel Oliveira', 'samuel.oliveira@fatec.sp.gov.br', '$2y$10$kkzVyS5xCJ1ApscOawTQweUO5tpi/1FidpSUD7JME67cVV0KwaGQu', 2, '2025-11-26 23:56:45', '2025-12-02 21:16:41'),
(82, '2781392513019', 'João Lázaro', 'joao.vieira71@fatec.sp.gov.br', '$2y$10$OedLWPlpVStSOhMqQIo/.Olgqxv01sJxxbQ8UuM/Th.fc78Jpv.sW', 2, '2025-11-28 22:39:40', '2025-12-02 20:44:58'),
(92, '2781392513111', 'João Paulo Gomes', 'joao.paulo@fatec.sp.gov.br', '$2y$10$QZusF/m6MbjRZjYzUPJcF.MKJwVp2keC/6h58r7cKXYucXVrBSBsS', 2, '2025-12-03 00:55:30', '2025-12-03 00:55:30'),
(93, '2781392513112', 'Ana Silva', 'ana.silva@fatec.sp.gov.br', '$2y$10$KcicdnmVfV7zy9uKZLUDAeMyZsNH8mqYptEb8fm6QEnuKDwaKy2Xm', 1, '2025-12-05 10:00:00', '2025-12-05 23:44:44'),
(94, '2781392513113', 'Carlos Santos', 'carlos.santos@fatec.sp.gov.br', '$2y$10$KcicdnmVfV7zy9uKZLUDAeMyZsNH8mqYptEb8fm6QEnuKDwaKy2Xm', 2, '2025-12-05 10:01:00', '2025-12-05 23:44:48'),
(95, '2781392513114', 'Maria Oliveira', 'maria.oliveira@fatec.sp.gov.br', '$2y$10$KcicdnmVfV7zy9uKZLUDAeMyZsNH8mqYptEb8fm6QEnuKDwaKy2Xm', 3, '2025-12-05 10:02:00', '2025-12-05 23:44:52'),
(96, '2781392513115', 'José Pereira', 'jose.pereira@fatec.sp.gov.br', '$2y$10$KcicdnmVfV7zy9uKZLUDAeMyZsNH8mqYptEb8fm6QEnuKDwaKy2Xm', 4, '2025-12-05 10:03:00', '2025-12-05 23:44:55'),
(97, '2781392513116', 'Fernanda Lima', 'fernanda.lima@fatec.sp.gov.br', '$2y$10$KcicdnmVfV7zy9uKZLUDAeMyZsNH8mqYptEb8fm6QEnuKDwaKy2Xm', 5, '2025-12-05 10:04:00', '2025-12-06 00:34:31'),
(98, '2781392513117', 'Rafael Costa', 'rafael.costa@fatec.sp.gov.br', '$2y$10$KcicdnmVfV7zy9uKZLUDAeMyZsNH8mqYptEb8fm6QEnuKDwaKy2Xm', 6, '2025-12-05 10:05:00', '2025-12-06 00:34:36'),
(99, '2781392513118', 'Juliana Alves', 'juliana.alves@fatec.sp.gov.br', '$2y$10$KcicdnmVfV7zy9uKZLUDAeMyZsNH8mqYptEb8fm6QEnuKDwaKy2Xm', 7, '2025-12-05 10:06:00', '2025-12-06 00:34:39'),
(100, '2781392513119', 'Lucas Rodrigues', 'lucas.rodrigues@fatec.sp.gov.br', '$2y$10$KcicdnmVfV7zy9uKZLUDAeMyZsNH8mqYptEb8fm6QEnuKDwaKy2Xm', 8, '2025-12-05 10:07:00', '2025-12-06 00:34:42'),
(101, '2781392513120', 'Patrícia Ferreira', 'patricia.ferreira@fatec.sp.gov.br', '$2y$10$KcicdnmVfV7zy9uKZLUDAeMyZsNH8mqYptEb8fm6QEnuKDwaKy2Xm', 9, '2025-12-05 10:08:00', '2025-12-06 00:34:45'),
(102, '2781392513121', 'Thiago Martins', 'thiago.martins@fatec.sp.gov.br', '$2y$10$KcicdnmVfV7zy9uKZLUDAeMyZsNH8mqYptEb8fm6QEnuKDwaKy2Xm', 10, '2025-12-05 10:09:00', '2025-12-06 00:34:49'),
(103, '2781392513122', 'Camila Souza', 'camila.souza@fatec.sp.gov.br', '$2y$10$KcicdnmVfV7zy9uKZLUDAeMyZsNH8mqYptEb8fm6QEnuKDwaKy2Xm]', 11, '2025-12-05 10:10:00', '2025-12-06 00:34:51'),
(104, '2781392513123', 'Gustavo Carvalho', 'gustavo.carvalho@fatec.sp.gov.br', '$2y$10$KcicdnmVfV7zy9uKZLUDAeMyZsNH8mqYptEb8fm6QEnuKDwaKy2Xm', 12, '2025-12-05 10:11:00', '2025-12-06 00:34:55'),
(105, '2781392513124', 'Isabela Mendes', 'isabela.mendes@fatec.sp.gov.br', '$2y$10$KcicdnmVfV7zy9uKZLUDAeMyZsNH8mqYptEb8fm6QEnuKDwaKy2Xm', 13, '2025-12-05 10:12:00', '2025-12-06 00:35:00'),
(106, '2780772413001', 'Arthur Ribeiro', 'arthur.ribeiro@fatec.sp.gov.br', '$2y$10$KcicdnmVfV7zy9uKZLUDAeMyZsNH8mqYptEb8fm6QEnuKDwaKy2Xm', 10, '2025-12-05 13:20:00', '2025-12-05 13:20:00'),
(107, '2780772413002', 'Beatriz Gomes', 'beatriz.gomes@fatec.sp.gov.br', '$2y$10$KcicdnmVfV7zy9uKZLUDAeMyZsNH8mqYptEb8fm6QEnuKDwaKy2Xm', 16, '2025-12-05 13:21:00', '2025-12-06 14:21:53'),
(108, '2780772413003', 'Diego Matos', 'diego.matos@fatec.sp.gov.br', '$2y$10$KcicdnmVfV7zy9uKZLUDAeMyZsNH8mqYptEb8fm6QEnuKDwaKy2Xm', 16, '2025-12-05 13:22:00', '2025-12-06 14:21:53'),
(109, '2780772413004', 'Elisa Fernandes', 'elisa.fernandes@fatec.sp.gov.br', '$2y$10$KcicdnmVfV7zy9uKZLUDAeMyZsNH8mqYptEb8fm6QEnuKDwaKy2Xm', 16, '2025-12-05 13:23:00', '2025-12-06 14:21:53'),
(110, '2780772413005', 'Fábio Teixeira', 'fabio.teixeira@fatec.sp.gov.br', '$2y$10$KcicdnmVfV7zy9uKZLUDAeMyZsNH8mqYptEb8fm6QEnuKDwaKy2Xm', 16, '2025-12-05 13:24:00', '2025-12-06 14:21:53'),
(111, '2780772413006', 'Gabriela Moreira', 'gabriela.moreira@fatec.sp.gov.br', '$2y$10$KcicdnmVfV7zy9uKZLUDAeMyZsNH8mqYptEb8fm6QEnuKDwaKy2Xm', 16, '2025-12-05 13:25:00', '2025-12-06 14:21:53'),
(112, '2780772413007', 'Henrique Ramos', 'henrique.ramos@fatec.sp.gov.br', '$2y$10$KcicdnmVfV7zy9uKZLUDAeMyZsNH8mqYptEb8fm6QEnuKDwaKy2Xm', 16, '2025-12-05 13:26:00', '2025-12-06 14:21:53'),
(113, '2780772413008', 'Ingrid Barros', 'ingrid.barros@fatec.sp.gov.br', '$2y$10$KcicdnmVfV7zy9uKZLUDAeMyZsNH8mqYptEb8fm6QEnuKDwaKy2Xm', 16, '2025-12-05 13:27:00', '2025-12-06 14:21:53'),
(114, '2780772413009', 'João Correia', 'joao.correia@fatec.sp.gov.br', '$2y$10$KcicdnmVfV7zy9uKZLUDAeMyZsNH8mqYptEb8fm6QEnuKDwaKy2Xm', 16, '2025-12-05 13:28:00', '2025-12-06 14:21:53'),
(115, '2780772413010', 'Karina Duarte', 'karina.duarte@fatec.sp.gov.br', '$2y$10$KcicdnmVfV7zy9uKZLUDAeMyZsNH8mqYptEb8fm6QEnuKDwaKy2Xm', 16, '2025-12-05 13:29:00', '2025-12-06 14:21:53'),
(116, '2780772413011', 'Leonardo Nunes', 'leonardo.nunes@fatec.sp.gov.br', '$2y$10$KcicdnmVfV7zy9uKZLUDAeMyZsNH8mqYptEb8fm6QEnuKDwaKy2Xm', 16, '2025-12-05 13:30:00', '2025-12-06 14:22:34'),
(117, '2780772413012', 'Mariana Prado', 'mariana.prado@fatec.sp.gov.br', '$2y$10$KcicdnmVfV7zy9uKZLUDAeMyZsNH8mqYptEb8fm6QEnuKDwaKy2Xm', 16, '2025-12-05 13:31:00', '2025-12-06 14:22:34'),
(118, '2780772413013', 'Nathan Lopes', 'nathan.lopes@fatec.sp.gov.br', '$2y$10$KcicdnmVfV7zy9uKZLUDAeMyZsNH8mqYptEb8fm6QEnuKDwaKy2Xm', 16, '2025-12-05 13:32:00', '2025-12-06 14:22:34'),
(119, '2780772413014', 'Olívia Araujo', 'olivia.araujo@fatec.sp.gov.br', '$2y$10$KcicdnmVfV7zy9uKZLUDAeMyZsNH8mqYptEb8fm6QEnuKDwaKy2Xm', 16, '2025-12-05 13:33:00', '2025-12-06 14:22:34'),
(120, '2780772413015', 'Paulo Vieira', 'paulo.vieira@fatec.sp.gov.br', '$2y$10$KcicdnmVfV7zy9uKZLUDAeMyZsNH8mqYptEb8fm6QEnuKDwaKy2Xm', 16, '2025-12-05 13:34:00', '2025-12-06 14:22:34'),
(121, '2780772413016', 'Renata Assis', 'renata.assis@fatec.sp.gov.br', '$2y$10$KcicdnmVfV7zy9uKZLUDAeMyZsNH8mqYptEb8fm6QEnuKDwaKy2Xm', 16, '2025-12-05 13:35:00', '2025-12-06 14:22:34'),
(122, '2780772413017', 'Samuel Castro', 'samuel.castro@fatec.sp.gov.br', '$2y$10$KcicdnmVfV7zy9uKZLUDAeMyZsNH8mqYptEb8fm6QEnuKDwaKy2Xm', 16, '2025-12-05 13:36:00', '2025-12-06 14:22:34'),
(123, '2780772413018', 'Tatiane Brito', 'tatiane.brito@fatec.sp.gov.br', '$2y$10$KcicdnmVfV7zy9uKZLUDAeMyZsNH8mqYptEb8fm6QEnuKDwaKy2Xm', 16, '2025-12-05 13:37:00', '2025-12-06 14:22:34'),
(124, '2780772413019', 'Victor Silva', 'victor.silva@fatec.sp.gov.br', '$2y$10$KcicdnmVfV7zy9uKZLUDAeMyZsNH8mqYptEb8fm6QEnuKDwaKy2Xm', 16, '2025-12-05 13:38:00', '2025-12-06 14:22:34'),
(125, '2780772413020', 'Yasmin Rocha', 'yasmin.rocha@fatec.sp.gov.br', '$2y$10$KcicdnmVfV7zy9uKZLUDAeMyZsNH8mqYptEb8fm6QEnuKDwaKy2Xm', 16, '2025-12-05 13:39:00', '2025-12-06 14:22:34'),
(127, '2781392523006', 'Ana Luiza', 'ana.luiza@fatec.sp.gov.br', '$2y$10$KcicdnmVfV7zy9uKZLUDAeMyZsNH8mqYptEb8fm6QEnuKDwaKy2Xm', 1, '2025-11-25 23:51:24', '2025-12-06 14:51:13'),
(128, '2781392523008', 'Betraiz Amaro', 'beatriz.amaro@fatec.sp.gov.br', '$2y$10$KcicdnmVfV7zy9uKZLUDAeMyZsNH8mqYptEb8fm6QEnuKDwaKy2Xm', 1, '2025-11-25 23:51:24', '2025-12-06 00:46:51'),
(129, '2781392523009', 'Yslan Luiz', 'yslan.luiz@fatec.sp.gov.br', '$2y$10$KcicdnmVfV7zy9uKZLUDAeMyZsNH8mqYptEb8fm6QEnuKDwaKy2Xm', 1, '2025-11-25 23:51:24', '2025-12-06 14:51:13'),
(130, '2780772423017', 'Julia Rodrigues', 'julia.rodrigues@fatec.sp.gov.br', '$2y$10$KcicdnmVfV7zy9uKZLUDAeMyZsNH8mqYptEb8fm6QEnuKDwaKy2Xm', 15, '2025-12-05 13:36:00', '2025-12-06 14:22:34'),
(131, '2780772513020', 'Lucas Cardoso', 'lucas.cardoso@fatec.sp.gov.br', '$2y$10$KcicdnmVfV7zy9uKZLUDAeMyZsNH8mqYptEb8fm6QEnuKDwaKy2Xm', 14, '2025-12-05 13:39:00', '2025-12-06 14:22:34'),
(132, '2780772323017', 'Graziela Dilany', 'graziela.dilany@fatec.sp.gov.br', '$2y$10$KcicdnmVfV7zy9uKZLUDAeMyZsNH8mqYptEb8fm6QEnuKDwaKy2Xm', 17, '2025-12-05 13:36:00', '2025-12-06 14:22:34'),
(133, '2780772313013', 'Manuel Gomes', 'manuel.gomes@fatec.sp.gov.br', '$2y$10$uNNf2ty0OVUO14/aYY3OOekZxL5.YjKz50jWhb5V2aqzl8DDILLwu', 18, '2025-12-06 15:15:33', '2025-12-06 15:15:33');

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
(15, 12, '2781392513034', 'MInha proposta é mehorarr a fatec'),
(16, 12, '2781392513019', 'quero um povo capitalista, uma nova sala, formar novas pessoas para esse País, esse mundo....'),
(19, 12, '2781392513111', 'Melhorar a fatec'),
(20, 12, '2781392513112', 'Promover mais eventos culturais na faculdade.'),
(21, 12, '2781392513113', 'Implementar programas de sustentabilidade.'),
(22, 12, '2781392513114', 'Aumentar o acesso a recursos de estudo.'),
(23, 12, '2781392513115', 'Melhorar a comunicação entre alunos e professores.'),
(24, 12, '2781392513116', 'Criar mais oportunidades de estágio.'),
(25, 12, '2781392513117', 'Fomentar atividades esportivas.'),
(26, 12, '2781392513118', 'Expandir bibliotecas digitais.'),
(27, 12, '2781392513119', 'Oferecer mais cursos extracurriculares.'),
(28, 12, '2781392513120', 'Apoiar iniciativas de inclusão.'),
(29, 12, '2781392513121', 'Melhorar infraestrutura de laboratórios.'),
(30, 12, '2781392513122', 'Promover saúde mental entre estudantes.'),
(31, 12, '2781392513123', 'Incentivar pesquisa acadêmica.'),
(32, 12, '2781392513124', 'Desenvolver parcerias com empresas.'),
(33, 12, '2781392513112', 'Focar em educação tecnológica.'),
(34, 12, '2781392513113', 'Aumentar diversidade no currículo.'),
(35, 12, '2781392513114', 'Implementar feedback contínuo.'),
(36, 16, '2780772413001', 'Melhorar a fatec'),
(37, 16, '2780772413002', 'Melhorar a fatec'),
(38, 14, '2781392523006', 'Disbonilizar alimento'),
(39, 14, '2781392523009', 'Melhorias adicionais'),
(40, 14, '2781392523008', 'Secretaria Digital'),
(41, 16, '2780772413020', 'Melhorias adicionais'),
(42, 16, '2780772413019', 'Melhorias adicionais'),
(43, 16, '2780772413018', 'Melhorias adicionais'),
(44, 16, '2780772413017', 'Melhorias adicionais');

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
(12, 'Eleição Representante 2º DSM', 'Votação para eleição de representante de turma.', '2025-11-25 23:51:49', '2025-11-20 23:59:00', '2025-12-21 23:59:00', 1, 2),
(14, 'Eleição de representante do 1 DSM', 'Votação para eleição de representante de turma', '2025-12-02 20:30:44', '2025-12-01 17:32:00', '2025-12-14 17:32:00', 1, 1),
(15, 'Eleição de representante do 3° DSM', 'Votação para eleição de representante de turma', '2025-12-02 20:30:44', '2025-11-29 17:32:00', '2025-12-01 17:32:00', 0, 3),
(16, 'Eleição de representante do 4° GPI', 'Votação para eleição de representante de turma', '2025-12-02 20:30:44', '2025-11-29 17:32:00', '2025-12-05 17:32:00', 0, 16);

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
(13, 'Eleição Representante 2º DSM', 'INSCRIÇÕES ABERTAS! DIVULGUE!', '2025-11-26 23:52:59'),
(14, 'Nova Eleição para 1º DSM', 'Inscrições abertas para representante.', '2025-12-05 12:00:00'),
(15, 'Evento Cultural na FATEC', 'Participe dos eventos culturais organizados.', '2025-12-05 12:01:00'),
(16, 'Atualização no Sistema de Votação', 'Melhorias implementadas no sistema.', '2025-11-21 12:02:00'),
(17, 'Palestra sobre Tecnologia', 'Venha aprender sobre inovações tecnológicas.', '2025-12-05 12:03:00'),
(18, 'Feira de Empregos', 'Oportunidades de emprego para estudantes.', '2025-12-05 12:04:00'),
(19, 'Campanha de Doação de Sangue', 'Ajude a salvar vidas doando sangue.', '2025-12-05 12:05:00'),
(20, 'Workshop de Programação', 'Aprenda novas linguagens de programação.', '2025-10-14 12:06:00'),
(21, 'Inauguração da Biblioteca Digital', 'Acesse recursos online gratuitamente.', '2025-12-05 12:07:00'),
(22, 'Dia do Estudante', 'Celebração com atividades especiais.', '2025-12-05 12:08:00');

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
(9, 12, '2781392513034', 16, '2025-11-27 00:00:58'),
(10, 12, '2781392513014', 21, '2025-12-02 20:44:46'),
(12, 12, '2781392513111', 33, '2025-12-03 00:59:12'),
(13, 15, '2781392513112', 20, '2025-12-05 13:00:00'),
(14, 15, '2781392513113', 32, '2025-12-05 13:01:00'),
(15, 15, '2781392513115', 23, '2025-12-05 13:02:00'),
(16, 15, '2781392513116', 24, '2025-12-05 13:03:00'),
(17, 15, '2781392513117', 25, '2025-12-05 13:04:00'),
(18, 15, '2781392513118', 26, '2025-12-05 13:05:00'),
(19, 15, '2781392513119', 27, '2025-12-05 13:06:00'),
(20, 15, '2781392513120', 28, '2025-12-05 13:07:00'),
(21, 15, '2781392513121', 15, '2025-12-05 13:08:00'),
(22, 15, '2781392513122', 19, '2025-12-05 13:09:00'),
(23, 15, '2781392513123', 23, '2025-12-05 13:10:00'),
(24, 15, '2781392513124', 24, '2025-12-05 13:11:00'),
(25, 15, '2781392513111', 34, '2025-12-05 13:12:00'),
(26, 19, '2781392513113', 26, '2025-12-05 13:13:00'),
(27, 15, '2781392513114', 27, '2025-12-05 13:14:00'),
(28, 21, '2781392513115', 28, '2025-12-05 13:15:00'),
(29, 12, '2781392513116', 16, '2025-12-05 13:16:00'),
(30, 14, '2781392513117', 21, '2025-12-05 13:17:00'),
(46, 16, '2780772413001', 36, '2025-12-06 11:25:30'),
(47, 16, '2780772413002', 37, '2025-12-06 11:25:30'),
(76, 16, '2780642323004', 1, '2025-12-06 12:02:33'),
(77, 16, '2780772413005', 43, '2025-12-06 12:02:33'),
(78, 16, '2780772413006', 44, '2025-12-06 12:02:33'),
(79, 16, '2780772413016', 42, '2025-12-06 12:02:33');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=134;

--
-- AUTO_INCREMENT de tabela `candidatos`
--
ALTER TABLE `candidatos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT de tabela `cursos`
--
ALTER TABLE `cursos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `eleicoes`
--
ALTER TABLE `eleicoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de tabela `noticias`
--
ALTER TABLE `noticias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=80;

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
