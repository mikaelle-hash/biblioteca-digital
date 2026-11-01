-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 01/11/2026 às 19:06
-- Versão do servidor: 10.11.16-MariaDB
-- Versão do PHP: 8.3.36

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `biblioteca_digital`
--

DELIMITER $$
--
-- Procedimentos
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_consultar_emprestimos_usuario` (IN `p_id_usuario` CHAR(36))   BEGIN
    SELECT
        e.id_emprestimo,
        u.nome AS usuario,
        e.data_emprestimo,
        e.data_prevista_devolucao,
        e.data_devolucao,
        e.status
    FROM emprestimos e
    INNER JOIN usuarios u
        ON e.id_usuario = u.id_usuario
    WHERE e.id_usuario = p_id_usuario;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_registrar_devolucao` (IN `p_id_emprestimo` CHAR(36))   BEGIN
    UPDATE emprestimos
    SET
        status = 'DEVOLVIDO',
        data_devolucao = NOW()
    WHERE id_emprestimo = p_id_emprestimo
      AND status = 'ATIVO';
END$$

--
-- Funções
--
CREATE DEFINER=`root`@`localhost` FUNCTION `fn_calcular_atraso` (`p_data_prevista` DATE, `p_status` VARCHAR(20)) RETURNS INT(11) DETERMINISTIC BEGIN
    IF p_status = 'DEVOLVIDO' OR CURDATE() <= p_data_prevista THEN
        RETURN 0;
    ELSE
        RETURN DATEDIFF(CURDATE(), p_data_prevista);
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `fn_gerar_id` () RETURNS CHAR(36) CHARSET utf8mb4 COLLATE utf8mb4_unicode_ci NO SQL RETURN UUID()$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estrutura para tabela `autores`
--

CREATE TABLE `autores` (
  `id_autor` char(36) NOT NULL,
  `nome` varchar(120) NOT NULL,
  `nacionalidade` varchar(60) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `autores`
--

INSERT INTO `autores` (`id_autor`, `nome`, `nacionalidade`) VALUES
('699b62f7-d495-11f1-a673-8cb0e9370401', 'Robert C. Martin', 'Americana'),
('699b6625-d495-11f1-a673-8cb0e9370401', 'Antoine de Saint-Exupéry', 'Francesa'),
('699b6699-d495-11f1-a673-8cb0e9370401', 'Pedro Demo', 'Brasileira'),
('699b66b3-d495-11f1-a673-8cb0e9370401', 'Chiavenato', 'Brasileira'),
('699b66c7-d495-11f1-a673-8cb0e9370401', 'Elmasri', 'Americana');

-- --------------------------------------------------------

--
-- Estrutura para tabela `categorias`
--

CREATE TABLE `categorias` (
  `id_categoria` char(36) NOT NULL,
  `nome` varchar(80) NOT NULL,
  `descricao` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `categorias`
--

INSERT INTO `categorias` (`id_categoria`, `nome`, `descricao`) VALUES
('04bf0b67-d495-11f1-a673-8cb0e9370401', 'Tecnologia', 'Livros de informática e tecnologia'),
('04bf1116-d495-11f1-a673-8cb0e9370401', 'Literatura', 'Livros literários e romances'),
('04bf1354-d495-11f1-a673-8cb0e9370401', 'Ciencias', 'Livros científicos e acadêmicos'),
('04bf141c-d495-11f1-a673-8cb0e9370401', 'Engenharia', 'Livros de engenharia e computação'),
('04bf1466-d495-11f1-a673-8cb0e9370401', 'Administracao', 'Livros de gestão e negócios');

-- --------------------------------------------------------

--
-- Estrutura para tabela `emprestimos`
--

CREATE TABLE `emprestimos` (
  `id_emprestimo` char(36) NOT NULL,
  `id_usuario` char(36) NOT NULL,
  `data_emprestimo` datetime NOT NULL DEFAULT current_timestamp(),
  `data_prevista_devolucao` date NOT NULL,
  `data_devolucao` date DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'ATIVO'
) ;

--
-- Despejando dados para a tabela `emprestimos`
--

INSERT INTO `emprestimos` (`id_emprestimo`, `id_usuario`, `data_emprestimo`, `data_prevista_devolucao`, `data_devolucao`, `status`) VALUES
('461d6c13-d496-11f1-a673-8cb0e9370401', '5e541a42-d494-11f1-a673-8cb0e9370401', '2026-10-30 16:15:31', '2026-11-06', NULL, 'ATIVO'),
('461dac8f-d496-11f1-a673-8cb0e9370401', '6a4e6e1c-d494-11f1-a673-8cb0e9370401', '2026-10-30 16:15:31', '2026-11-06', NULL, 'ATIVO'),
('a8406649-d56e-11f1-b3b2-8cb0e9370401', '78e1f7af-d564-11f1-b3b2-8cb0e9370401', '2026-10-31 18:04:27', '2026-11-17', NULL, 'ATIVO'),
('f6e0c1b6-d55a-11f1-b3b2-8cb0e9370401', '40b3b941-d494-11f1-a673-8cb0e9370401', '2026-10-31 15:43:29', '2026-11-07', '2026-10-31', 'DEVOLVIDO');

--
-- Acionadores `emprestimos`
--
DELIMITER $$
CREATE TRIGGER `trg_devolver_livros` AFTER UPDATE ON `emprestimos` FOR EACH ROW BEGIN
    IF OLD.status <> 'DEVOLVIDO'
       AND NEW.status = 'DEVOLVIDO' THEN

        UPDATE livros l
        INNER JOIN itens_emprestimo i
            ON l.id_livro = i.id_livro
        SET l.quantidade_disponivel =
            l.quantidade_disponivel + i.quantidade
        WHERE i.id_emprestimo = NEW.id_emprestimo;

    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estrutura para tabela `grupos_usuarios`
--

CREATE TABLE `grupos_usuarios` (
  `id_grupo` char(36) NOT NULL,
  `nome` varchar(50) NOT NULL,
  `descricao` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `grupos_usuarios`
--

INSERT INTO `grupos_usuarios` (`id_grupo`, `nome`, `descricao`) VALUES
('dd5688bb-d493-11f1-a673-8cb0e9370401', 'Administradores', 'Grupo responsável pelo gerenciamento do sistema.'),
('dd56b7b7-d493-11f1-a673-8cb0e9370401', 'Bibliotecários', 'Responsáveis pelo gerenciamento do acervo e empréstimos.'),
('fbda968d-d493-11f1-a673-8cb0e9370401', 'Leitores', 'Usuários que podem consultar e realizar empréstimos de livros.');

-- --------------------------------------------------------

--
-- Estrutura para tabela `historico`
--

CREATE TABLE `historico` (
  `id_historico` char(36) NOT NULL,
  `id_usuario` char(36) DEFAULT NULL,
  `acao` varchar(50) NOT NULL,
  `tabela_afetada` varchar(50) NOT NULL,
  `id_registro` char(36) DEFAULT NULL,
  `descricao` varchar(255) DEFAULT NULL,
  `data_hora` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `itens_emprestimo`
--

CREATE TABLE `itens_emprestimo` (
  `id_emprestimo` char(36) NOT NULL,
  `id_livro` char(36) NOT NULL,
  `quantidade` int(11) NOT NULL DEFAULT 1
) ;

--
-- Despejando dados para a tabela `itens_emprestimo`
--

INSERT INTO `itens_emprestimo` (`id_emprestimo`, `id_livro`, `quantidade`) VALUES
('461d6c13-d496-11f1-a673-8cb0e9370401', '38ad2215-d495-11f1-a673-8cb0e9370401', 1),
('461dac8f-d496-11f1-a673-8cb0e9370401', '38b17139-d495-11f1-a673-8cb0e9370401', 1),
('a8406649-d56e-11f1-b3b2-8cb0e9370401', '38b17139-d495-11f1-a673-8cb0e9370401', 1),
('f6e0c1b6-d55a-11f1-b3b2-8cb0e9370401', '9315cb56-d559-11f1-b3b2-8cb0e9370401', 1);

--
-- Acionadores `itens_emprestimo`
--
DELIMITER $$
CREATE TRIGGER `trg_diminuir_livros` AFTER INSERT ON `itens_emprestimo` FOR EACH ROW BEGIN
    UPDATE livros
    SET quantidade_disponivel =
        quantidade_disponivel - NEW.quantidade
    WHERE id_livro = NEW.id_livro;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_validar_estoque` BEFORE INSERT ON `itens_emprestimo` FOR EACH ROW BEGIN
    DECLARE estoque INT;

    SELECT quantidade_disponivel
    INTO estoque
    FROM livros
    WHERE id_livro = NEW.id_livro;

    IF estoque < NEW.quantidade THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Quantidade indisponivel no estoque';
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estrutura para tabela `livros`
--

CREATE TABLE `livros` (
  `id_livro` char(36) NOT NULL,
  `id_categoria` char(36) NOT NULL,
  `isbn` varchar(20) DEFAULT NULL,
  `titulo` varchar(150) NOT NULL,
  `editora` varchar(100) DEFAULT NULL,
  `ano_publicacao` year(4) DEFAULT NULL,
  `quantidade_total` int(11) NOT NULL DEFAULT 0,
  `quantidade_disponivel` int(11) NOT NULL DEFAULT 0
) ;

--
-- Despejando dados para a tabela `livros`
--

INSERT INTO `livros` (`id_livro`, `id_categoria`, `isbn`, `titulo`, `editora`, `ano_publicacao`, `quantidade_total`, `quantidade_disponivel`) VALUES
('1f9c4734-d562-11f1-b3b2-8cb0e9370401', '04bf0b67-d495-11f1-a673-8cb0e9370401', '9788539900012', 'Programação em PHP', 'Editora Teste', '2024', 2, 2),
('20205fef-d495-11f1-a673-8cb0e9370401', '04bf0b67-d495-11f1-a673-8cb0e9370401', NULL, 'Banco de Dados: Fundamentos', 'Editora Acadêmica', NULL, 5, 5),
('38ad2215-d495-11f1-a673-8cb0e9370401', '04bf141c-d495-11f1-a673-8cb0e9370401', NULL, 'Engenharia de Software', 'Bookman', NULL, 3, 2),
('38b17139-d495-11f1-a673-8cb0e9370401', '04bf1116-d495-11f1-a673-8cb0e9370401', NULL, 'O Pequeno Príncipe', 'Agir', NULL, 4, 2),
('38b3bd10-d495-11f1-a673-8cb0e9370401', '04bf1354-d495-11f1-a673-8cb0e9370401', NULL, 'Introdução à Ciência', 'Moderna', NULL, 2, 2),
('38b6944a-d495-11f1-a673-8cb0e9370401', '04bf1466-d495-11f1-a673-8cb0e9370401', NULL, 'Administração Geral', 'Atlas', NULL, 3, 3),
('9315cb56-d559-11f1-b3b2-8cb0e9370401', '04bf0b67-d495-11f1-a673-8cb0e9370401', '9781234567897', 'Programação Web', 'Editora Teste', '2023', 2, 2),
('9c4ae61d-d551-11f1-b3b2-8cb0e9370401', '04bf0b67-d495-11f1-a673-8cb0e9370401', '9780132350884', 'O Código Limpo', 'Alta Books', '2008', 2, 2);

-- --------------------------------------------------------

--
-- Estrutura para tabela `livros_autores`
--

CREATE TABLE `livros_autores` (
  `id_livro` char(36) NOT NULL,
  `id_autor` char(36) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `livros_autores`
--

INSERT INTO `livros_autores` (`id_livro`, `id_autor`) VALUES
('20205fef-d495-11f1-a673-8cb0e9370401', '699b66c7-d495-11f1-a673-8cb0e9370401'),
('38ad2215-d495-11f1-a673-8cb0e9370401', '699b62f7-d495-11f1-a673-8cb0e9370401'),
('38b17139-d495-11f1-a673-8cb0e9370401', '699b6625-d495-11f1-a673-8cb0e9370401'),
('38b3bd10-d495-11f1-a673-8cb0e9370401', '699b6699-d495-11f1-a673-8cb0e9370401'),
('38b6944a-d495-11f1-a673-8cb0e9370401', '699b66b3-d495-11f1-a673-8cb0e9370401');

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` char(36) NOT NULL,
  `id_grupo` char(36) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(120) NOT NULL,
  `senha_hash` varchar(255) NOT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT 1,
  `data_cadastro` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `id_grupo`, `nome`, `email`, `senha_hash`, `ativo`, `data_cadastro`) VALUES
('40b3b941-d494-11f1-a673-8cb0e9370401', 'dd5688bb-d493-11f1-a673-8cb0e9370401', 'Ana Administradora', 'ana@biblioteca.com', '$2y$10$3Z8DYFYwOJWwM47BpYHDsu5yKQh6yNeak6h5oUUtv6Vaxg4AyCVpy', 1, '2026-10-30 16:01:03'),
('5e541a42-d494-11f1-a673-8cb0e9370401', 'dd56b7b7-d493-11f1-a673-8cb0e9370401', 'Carlos Bibliotecário', 'carlos@biblioteca.com', '$2y$10$CAyLRY7gcxSUj1Y3H2kJHOv4tYcCbMQH.Pda/A22RleuS8AZWICzK', 1, '2026-10-30 16:01:52'),
('6a4e6e1c-d494-11f1-a673-8cb0e9370401', 'fbda968d-d493-11f1-a673-8cb0e9370401', 'Maria Leitora', 'maria@biblioteca.com', '6b7a8a40eb42d9b6593930c0a4ca9946792e235709bc074ca056d9aefaa37b61', 1, '2026-10-30 16:02:12'),
('78e1f7af-d564-11f1-b3b2-8cb0e9370401', 'fbda968d-d493-11f1-a673-8cb0e9370401', 'João Teste', 'joao.teste2@biblioteca.com', '$2y$10$N/bKy0syGWs.reEgaPFnm./8p9/jB0eFTgjsLPKMXHgAyPuhgssWq', 1, '2026-10-31 16:51:32'),
('a62f2371-d55c-11f1-b3b2-8cb0e9370401', 'fbda968d-d493-11f1-a673-8cb0e9370401', 'João da Silva', 'joao.teste@biblioteca.com', '$2y$10$h2DuDwDuyqkJ5B2OcV3PK.jatgIIi1hrryICPNBnaVSfQh7fLAeSq', 1, '2026-10-31 15:55:32');

-- --------------------------------------------------------

--
-- Estrutura stand-in para view `vw_emprestimos_ativos`
-- (Veja abaixo para a visão atual)
--
CREATE TABLE `vw_emprestimos_ativos` (
`id_emprestimo` char(36)
,`usuario` varchar(100)
,`data_emprestimo` datetime
,`data_prevista_devolucao` date
,`status` varchar(20)
);

-- --------------------------------------------------------

--
-- Estrutura stand-in para view `vw_historico_emprestimos`
-- (Veja abaixo para a visão atual)
--
CREATE TABLE `vw_historico_emprestimos` (
`id_emprestimo` char(36)
,`usuario` varchar(100)
,`data_emprestimo` datetime
,`data_prevista_devolucao` date
,`data_devolucao` date
,`status` varchar(20)
);

-- --------------------------------------------------------

--
-- Estrutura stand-in para view `vw_livros_disponiveis`
-- (Veja abaixo para a visão atual)
--
CREATE TABLE `vw_livros_disponiveis` (
`id_livro` char(36)
,`titulo` varchar(150)
,`categoria` varchar(80)
,`editora` varchar(100)
,`quantidade_total` int(11)
,`quantidade_disponivel` int(11)
);

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `autores`
--
ALTER TABLE `autores`
  ADD PRIMARY KEY (`id_autor`);

--
-- Índices de tabela `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id_categoria`),
  ADD UNIQUE KEY `nome` (`nome`);

--
-- Índices de tabela `emprestimos`
--
ALTER TABLE `emprestimos`
  ADD PRIMARY KEY (`id_emprestimo`),
  ADD KEY `idx_emprestimos_usuario` (`id_usuario`),
  ADD KEY `idx_emprestimos_status` (`status`);

--
-- Índices de tabela `grupos_usuarios`
--
ALTER TABLE `grupos_usuarios`
  ADD PRIMARY KEY (`id_grupo`),
  ADD UNIQUE KEY `nome` (`nome`);

--
-- Índices de tabela `historico`
--
ALTER TABLE `historico`
  ADD PRIMARY KEY (`id_historico`),
  ADD KEY `fk_historico_usuario` (`id_usuario`),
  ADD KEY `idx_historico_data` (`data_hora`);

--
-- Índices de tabela `itens_emprestimo`
--
ALTER TABLE `itens_emprestimo`
  ADD PRIMARY KEY (`id_emprestimo`,`id_livro`),
  ADD KEY `fk_item_livro` (`id_livro`);

--
-- Índices de tabela `livros`
--
ALTER TABLE `livros`
  ADD PRIMARY KEY (`id_livro`),
  ADD UNIQUE KEY `isbn` (`isbn`),
  ADD KEY `fk_livro_categoria` (`id_categoria`),
  ADD KEY `idx_livros_titulo` (`titulo`);

--
-- Índices de tabela `livros_autores`
--
ALTER TABLE `livros_autores`
  ADD PRIMARY KEY (`id_livro`,`id_autor`),
  ADD KEY `fk_livro_autor_autor` (`id_autor`);

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `fk_usuario_grupo` (`id_grupo`);

-- --------------------------------------------------------

--
-- Estrutura para view `vw_emprestimos_ativos`
--
DROP TABLE IF EXISTS `vw_emprestimos_ativos`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_emprestimos_ativos`  AS SELECT `e`.`id_emprestimo` AS `id_emprestimo`, `u`.`nome` AS `usuario`, `e`.`data_emprestimo` AS `data_emprestimo`, `e`.`data_prevista_devolucao` AS `data_prevista_devolucao`, `e`.`status` AS `status` FROM (`emprestimos` `e` join `usuarios` `u` on(`e`.`id_usuario` = `u`.`id_usuario`)) WHERE `e`.`status` = 'ATIVO' ;

-- --------------------------------------------------------

--
-- Estrutura para view `vw_historico_emprestimos`
--
DROP TABLE IF EXISTS `vw_historico_emprestimos`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_historico_emprestimos`  AS SELECT `e`.`id_emprestimo` AS `id_emprestimo`, `u`.`nome` AS `usuario`, `e`.`data_emprestimo` AS `data_emprestimo`, `e`.`data_prevista_devolucao` AS `data_prevista_devolucao`, `e`.`data_devolucao` AS `data_devolucao`, `e`.`status` AS `status` FROM (`emprestimos` `e` join `usuarios` `u` on(`e`.`id_usuario` = `u`.`id_usuario`)) ;

-- --------------------------------------------------------

--
-- Estrutura para view `vw_livros_disponiveis`
--
DROP TABLE IF EXISTS `vw_livros_disponiveis`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_livros_disponiveis`  AS SELECT `l`.`id_livro` AS `id_livro`, `l`.`titulo` AS `titulo`, `c`.`nome` AS `categoria`, `l`.`editora` AS `editora`, `l`.`quantidade_total` AS `quantidade_total`, `l`.`quantidade_disponivel` AS `quantidade_disponivel` FROM (`livros` `l` join `categorias` `c` on(`l`.`id_categoria` = `c`.`id_categoria`)) WHERE `l`.`quantidade_disponivel` > 0 ;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `emprestimos`
--
ALTER TABLE `emprestimos`
  ADD CONSTRAINT `fk_emprestimo_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`);

--
-- Restrições para tabelas `historico`
--
ALTER TABLE `historico`
  ADD CONSTRAINT `fk_historico_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE SET NULL;

--
-- Restrições para tabelas `itens_emprestimo`
--
ALTER TABLE `itens_emprestimo`
  ADD CONSTRAINT `fk_item_emprestimo` FOREIGN KEY (`id_emprestimo`) REFERENCES `emprestimos` (`id_emprestimo`),
  ADD CONSTRAINT `fk_item_livro` FOREIGN KEY (`id_livro`) REFERENCES `livros` (`id_livro`);

--
-- Restrições para tabelas `livros`
--
ALTER TABLE `livros`
  ADD CONSTRAINT `fk_livro_categoria` FOREIGN KEY (`id_categoria`) REFERENCES `categorias` (`id_categoria`);

--
-- Restrições para tabelas `livros_autores`
--
ALTER TABLE `livros_autores`
  ADD CONSTRAINT `fk_livro_autor_autor` FOREIGN KEY (`id_autor`) REFERENCES `autores` (`id_autor`),
  ADD CONSTRAINT `fk_livro_autor_livro` FOREIGN KEY (`id_livro`) REFERENCES `livros` (`id_livro`);

--
-- Restrições para tabelas `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `fk_usuario_grupo` FOREIGN KEY (`id_grupo`) REFERENCES `grupos_usuarios` (`id_grupo`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
