--
-- Banco de dados: `projeto`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `carrinho`
--

CREATE TABLE `carrinho` (
  `id` bigint(20) NOT NULL,
  `data_criacao` datetime DEFAULT NULL,
  `quantidade` INT DEFAULT 1,
  `usuario_id` bigint(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `categoria`
--

CREATE TABLE `categoria` (
  `id` bigint(20) NOT NULL,
  `nome` varchar(50) DEFAULT NULL,
  `ativo` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `endereco`
--

CREATE TABLE `endereco` (
  `id` bigint(20) NOT NULL,
  `cep` varchar(10) DEFAULT NULL,
  `rua` varchar(100) DEFAULT NULL,
  `numero` varchar(10) DEFAULT NULL,
  `complemento` varchar(50) DEFAULT NULL,
  `bairro` varchar(50) DEFAULT NULL,
  `estado` varchar(2) DEFAULT NULL,
  `cidade` varchar(50) DEFAULT NULL,
  `usuario_id` bigint(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `imagemproduto`
--

CREATE TABLE `imagemproduto` (
  `id` bigint(20) NOT NULL,
  `url_imagem` varchar(255) DEFAULT NULL,
  `principal` tinyint(1) DEFAULT NULL,
  `produto_id` bigint(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `itemcarrinho`
--

CREATE TABLE `itemcarrinho` (
  `id` bigint(20) NOT NULL,
  `quantidade` int(11) DEFAULT NULL,
  `produto_id` bigint(20) DEFAULT NULL,
  `carrinho_id` bigint(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `itempedidos`
--

CREATE TABLE `itempedidos` (
  `id` bigint(20) NOT NULL,
  `preco` decimal(10,2) DEFAULT NULL,
  `quantidade` int(11) DEFAULT NULL,
  `produto_id` bigint(20) DEFAULT NULL,
  `pedidos_id` bigint(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `pagamento`
--

CREATE TABLE `pagamento` (
  `id` bigint(20) NOT NULL,
  `metodo` varchar(50) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `valor` decimal(10,2) DEFAULT NULL,
  `data_pagamento` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `pedidos`
--

CREATE TABLE `pedidos` (
  `id` bigint(20) NOT NULL,
  `status` varchar(50) DEFAULT NULL,
  `total` decimal(10,2) DEFAULT NULL,
  `data_pedidos` datetime DEFAULT NULL,
  `usuario_id` bigint(20) DEFAULT NULL,
  `endereco_id` bigint(20) DEFAULT NULL,
  `pagamento_id` bigint(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `produto`
--

CREATE TABLE `produto` (
  `id` bigint(20) NOT NULL,
  `nome` varchar(100) DEFAULT NULL,
  `descricao` text DEFAULT NULL,
  `preco` decimal(10,2) DEFAULT NULL,
  `estoque` int(11) DEFAULT NULL,
  `data_cadastro` datetime DEFAULT NULL,
  `ativo` tinyint(1) DEFAULT NULL,
  `categoria_id` bigint(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` bigint(20) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `telefone` varchar(15) DEFAULT NULL,
  `endereco_id` bigint(20) DEFAULT NULL,
  `nivel` enum('admin','user') DEFAULT 'user',
  `senha` varchar(255) NULL,
  `status` ENUM('ativo','pendente','inativo') NOT NULL DEFAULT 'ativo',
  `ativo` enum('Sim','Não') DEFAULT 'Sim',
  `data` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `carrinho`
--
ALTER TABLE `carrinho`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Índices de tabela `categoria`
--
ALTER TABLE `categoria`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `endereco`
--
ALTER TABLE `endereco`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Índices de tabela `imagemproduto`
--
ALTER TABLE `imagemproduto`
  ADD PRIMARY KEY (`id`),
  ADD KEY `produto_id` (`produto_id`);

--
-- Índices de tabela `itemcarrinho`
--
ALTER TABLE `itemcarrinho`
  ADD PRIMARY KEY (`id`),
  ADD KEY `produto_id` (`produto_id`),
  ADD KEY `carrinho_id` (`carrinho_id`);

--
-- Índices de tabela `itempedidos`
--
ALTER TABLE `itempedidos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `produto_id` (`produto_id`),
  ADD KEY `pedidos_id` (`pedidos_id`);

--
-- Índices de tabela `pagamento`
--
ALTER TABLE `pagamento`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `pedidos`
--
ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`),
  ADD KEY `endereco_id` (`endereco_id`),
  ADD KEY `pagamento_id` (`pagamento_id`);

--
-- Índices de tabela `produto`
--
ALTER TABLE `produto`
  ADD PRIMARY KEY (`id`),
  ADD KEY `categoria_id` (`categoria_id`);

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `endereco_id` (`endereco_id`);

--
-- AUTO_INCREMENT de tabela `carrinho`
--
ALTER TABLE `carrinho`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `categoria`
--
ALTER TABLE `categoria`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `endereco`
--
ALTER TABLE `endereco`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `imagemproduto`
--
ALTER TABLE `imagemproduto`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `itemcarrinho`
--
ALTER TABLE `itemcarrinho`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `itempedidos`
--
ALTER TABLE `itempedidos`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `pagamento`
--
ALTER TABLE `pagamento`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `produto`
--
ALTER TABLE `produto`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT; 

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `usuarios`
--
ALTER TABLE `usuarios`
 ADD CONSTRAINT `usuario_ibfk_1` FOREIGN KEY (`endereco_id`) REFERENCES `endereco` (`id`);

--
-- Restrições para tabelas `carrinho`
--
ALTER TABLE `carrinho`
  ADD CONSTRAINT `carrinho_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);

--
-- Restrições para tabelas `endereco`
--
ALTER TABLE `endereco`
  ADD CONSTRAINT `endereco_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);

--
-- Restrições para tabelas `imagemproduto`
--
ALTER TABLE `imagemproduto`
  ADD CONSTRAINT `imagemproduto_ibfk_1` FOREIGN KEY (`produto_id`) REFERENCES `produto` (`id`);

--
-- Restrições para tabelas `itemcarrinho`
--
ALTER TABLE `itemcarrinho`
  ADD CONSTRAINT `itemcarrinho_ibfk_1` FOREIGN KEY (`produto_id`) REFERENCES `produto` (`id`),
  ADD CONSTRAINT `itemcarrinho_ibfk_2` FOREIGN KEY (`carrinho_id`) REFERENCES `carrinho` (`id`);

--
-- Restrições para tabelas `itempedidos`
--
ALTER TABLE `itempedidos`
  ADD CONSTRAINT `itempedidos_ibfk_1` FOREIGN KEY (`produto_id`) REFERENCES `produto` (`id`),
  ADD CONSTRAINT `itempedidos_ibfk_2` FOREIGN KEY (`pedidos_id`) REFERENCES `pedidos` (`id`);


--
-- Restrições para tabelas `pedidos`
--
ALTER TABLE `pedidos`
  ADD CONSTRAINT `pedidos_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `pedidos_ibfk_2` FOREIGN KEY (`endereco_id`) REFERENCES `endereco` (`id`),
  ADD CONSTRAINT `pedidos_ibfk_3` FOREIGN KEY (`pagamento_id`) REFERENCES `pagamento` (`id`);

--
-- Restrições para tabelas `produto`
--
ALTER TABLE `produto`
  ADD CONSTRAINT `produto_ibfk_1` FOREIGN KEY (`categoria_id`) REFERENCES `categoria` (`id`);

--
-- Inserções de dados para tabelas 
-- 

-- Inserções de dados para tabela `categoria`
INSERT INTO `categoria`(`nome`, `ativo`) 
VALUES 
('aneis','1'), 
('brincos','1'), 
('colares','1'), 
('pulseiras','1');

-- Inserções de dados para tabela `produto`
INSERT INTO `produto`(`id`, `nome`, `descricao`, `preco`, `estoque`, `data_cadastro`, `ativo`, `categoria_id`) 
VALUES 
(null,'Anel','Anel em Prata 925 com Ródio Negro e Calcedonia Verde','1000','12', NOW(), 1, 1),
(null,'Brincos','Brincos em Prata 925 com Ródio Negro','350','20', NOW(), 1, 2), 
(null,'Corrente','Corrente Cadeado em Ouro Branco 18k, 60cm','6290','2', NOW(), 1, 3), 
(null,'Pulseira','Pulseira em Prata 925','1350','45', NOW(), 1, 4);

-- Inserções de dados para tabela `imagemproduto
INSERT INTO `imagemproduto`(`id`, `url_imagem`, `principal`, `produto_id`) 
VALUES 
(null,'_images/Anel-em-Prata-925-com-Rodio-Negro-e-Calcedonia-Verde.webp','1','1'), 
(null,'_images/Brinco-Argola-em-Prata-925-com-Rodio-Negro.webp','1','2'), 
(null,'_images/Corrente-Cadeado-em-Ouro-Branco-18k-60cm.webp','1','3'), 
(null,'_images/Pulseira-em-Prata-925.webp','1','4'); 

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
