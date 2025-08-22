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
-- Estrutura para tabela `credito`
--

CREATE TABLE `credito` (
  `id` bigint(20) NOT NULL,
  `numero_cartao` varchar(20) DEFAULT NULL,
  `nome_titular` varchar(100) DEFAULT NULL,
  `validade` varchar(7) DEFAULT NULL,
  `parcelas` int(11) DEFAULT NULL,
  `banco` varchar(50) DEFAULT NULL,
  `data` date DEFAULT NULL,
  `pagamento_id` bigint(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `debito`
--

CREATE TABLE `debito` (
  `id` bigint(20) NOT NULL,
  `numero_cartao` varchar(20) DEFAULT NULL,
  `nome_titular` varchar(100) DEFAULT NULL,
  `validade` varchar(7) DEFAULT NULL,
  `banco` varchar(50) DEFAULT NULL,
  `data` date DEFAULT NULL,
  `banco_destinatario` varchar(100) DEFAULT NULL,
  `chave` varchar(100) DEFAULT NULL,
  `pagamento_id` bigint(20) DEFAULT NULL
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
-- Estrutura para tabela `itempedido`
--

CREATE TABLE `itempedido` (
  `id` bigint(20) NOT NULL,
  `preco` decimal(10,2) DEFAULT NULL,
  `quantidade` int(11) DEFAULT NULL,
  `produto_id` bigint(20) DEFAULT NULL,
  `pedido_id` bigint(20) DEFAULT NULL
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
  `data_pagamento` datetime DEFAULT NULL,
  `carrinho_id` bigint(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `pedido`
--

CREATE TABLE `pedido` (
  `id` bigint(20) NOT NULL,
  `status` varchar(50) DEFAULT NULL,
  `total` decimal(10,2) DEFAULT NULL,
  `data_pedido` datetime DEFAULT NULL,
  `usuario_id` bigint(20) DEFAULT NULL,
  `endereco_id` bigint(20) DEFAULT NULL,
  `carrinho_id` bigint(20) DEFAULT NULL
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
  `endereco` text DEFAULT NULL,
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
-- Índices de tabela `credito`
--
ALTER TABLE `credito`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pagamento_id` (`pagamento_id`);

--
-- Índices de tabela `debito`
--
ALTER TABLE `debito`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pagamento_id` (`pagamento_id`);

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
-- Índices de tabela `itempedido`
--
ALTER TABLE `itempedido`
  ADD PRIMARY KEY (`id`),
  ADD KEY `produto_id` (`produto_id`),
  ADD KEY `pedido_id` (`pedido_id`);

--
-- Índices de tabela `pagamento`
--
ALTER TABLE `pagamento`
  ADD PRIMARY KEY (`id`),
  ADD KEY `carrinho_id` (`carrinho_id`);

--
-- Índices de tabela `pedido`
--
ALTER TABLE `pedido`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`),
  ADD KEY `endereco_id` (`endereco_id`),
  ADD KEY `carrinho_id` (`carrinho_id`);

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
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

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
-- AUTO_INCREMENT de tabela `credito`
--
ALTER TABLE `credito`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `debito`
--
ALTER TABLE `debito`
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
-- AUTO_INCREMENT de tabela `itempedido`
--
ALTER TABLE `itempedido`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `pagamento`
--
ALTER TABLE `pagamento`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `pedido`
--
ALTER TABLE `pedido`
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
-- Restrições para tabelas `carrinho`
--
ALTER TABLE `carrinho`
  ADD CONSTRAINT `carrinho_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);

--
-- Restrições para tabelas `credito`
--
ALTER TABLE `credito`
  ADD CONSTRAINT `credito_ibfk_1` FOREIGN KEY (`pagamento_id`) REFERENCES `pagamento` (`id`);

--
-- Restrições para tabelas `debito`
--
ALTER TABLE `debito`
  ADD CONSTRAINT `debito_ibfk_1` FOREIGN KEY (`pagamento_id`) REFERENCES `pagamento` (`id`);

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
-- Restrições para tabelas `itempedido`
--
ALTER TABLE `itempedido`
  ADD CONSTRAINT `itempedido_ibfk_1` FOREIGN KEY (`produto_id`) REFERENCES `produto` (`id`),
  ADD CONSTRAINT `itempedido_ibfk_2` FOREIGN KEY (`pedido_id`) REFERENCES `pedido` (`id`);

--
-- Restrições para tabelas `pagamento`
--
ALTER TABLE `pagamento`
  ADD CONSTRAINT `pagamento_ibfk_1` FOREIGN KEY (`carrinho_id`) REFERENCES `carrinho` (`id`);

--
-- Restrições para tabelas `pedido`
--
ALTER TABLE `pedido`
  ADD CONSTRAINT `pedido_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `pedido_ibfk_2` FOREIGN KEY (`endereco_id`) REFERENCES `endereco` (`id`),
  ADD CONSTRAINT `pedido_ibfk_3` FOREIGN KEY (`carrinho_id`) REFERENCES `carrinho` (`id`);

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
(null,'https://lojavivara.vtexassets.com/arquivos/ids/836005-1600-1600/Anel-Black-Iron-Man-em-Prata-925-com-Rodio-Negro-e-Calcedonia-Verde-9830_1_set.jpg?v=638470137685770000','1','1'), 
(null,'https://lojavivara.vtexassets.com/arquivos/ids/752926-1600-1600/Brinco-Argola-Forza-em-Prata-925-com-Rodio-Negro-79329_1_set.jpg?v=638437299595900000','1','2'), 
(null,'https://lojavivara.vtexassets.com/arquivos/ids/754277-1600-1600/Corrente-Cadeado-em-Ouro-Branco-18k-60cm-87784_1_set.jpg?v=638437301896300000','1','3'), 
(null,'https://lojavivara.vtexassets.com/arquivos/ids/777228-1600-1600/Pulseira-Cronos-em-Prata-925-80897_1_set.jpg?v=638437347892600000','1','4'); 

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
