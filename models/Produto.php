<?php
require_once __DIR__ . "/../conexao.php";

class Produto
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        // Garante que exceções sejam lançadas caso não esteja setado no conexao.php
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->pdo = $pdo;
    }

    /* ---------------------- CRUD BÁSICO (do seu model anterior) ---------------------- */

    public function create($nome, $descricao, $preco, $estoque, $ativo, $categoria_id): bool
    {
        $sql = "INSERT INTO produto (nome, descricao, preco, estoque, data_cadastro, ativo, categoria_id)
                VALUES (:nome, :descricao, :preco, :estoque, NOW(), :ativo, :categoria_id)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':nome' => $nome,
            ':descricao' => $descricao,
            ':preco' => $preco,
            ':estoque' => $estoque,
            ':ativo' => $ativo,
            ':categoria_id' => $categoria_id
        ]);
    }

    public function findById($id): ?array
    {
        $sql = "SELECT * FROM produto WHERE id = :id LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function findAll(): array
    {
        $sql = "SELECT * FROM produto ORDER BY data_cadastro DESC, id DESC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function update($id, $nome, $descricao, $preco, $estoque, $ativo, $categoria_id): bool
    {
        $sql = "UPDATE produto
                   SET nome = :nome,
                       descricao = :descricao,
                       preco = :preco,
                       estoque = :estoque,
                       ativo = :ativo,
                       categoria_id = :categoria_id
                 WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':id' => $id,
            ':nome' => $nome,
            ':descricao' => $descricao,
            ':preco' => $preco,
            ':estoque' => $estoque,
            ':ativo' => $ativo,
            ':categoria_id' => $categoria_id
        ]);
    }

    public function delete($id): bool
    {
        $sql = "DELETE FROM produto WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    /* ---------------------- MÉTODOS ESPECÍFICOS PARA A LISTAGEM ---------------------- */

    // Retorna a URL da imagem principal ou a primeira imagem do produto
    public function getImagemPrincipal(int $produtoId): ?string
    {
        $sql = "SELECT url_imagem
                  FROM imagemproduto
                 WHERE produto_id = :id
                 ORDER BY principal DESC, id ASC
                 LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $produtoId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['url_imagem'] ?? null;
    }

    // Retorna todos os produtos ativos
    public function getAllAtivos(): array
    {
        $sql = "SELECT * FROM produto WHERE ativo = 1 ORDER BY data_cadastro DESC, id DESC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Retorna produtos ativos de uma categoria pelo NOME da categoria
    public function getByCategoriaNome(string $categoriaNome): array
    {
        $sql = "SELECT p.*
                  FROM produto p
                  JOIN categoria c ON c.id = p.categoria_id
                 WHERE p.ativo = 1
                   AND c.ativo = 1
                   AND c.nome = :categoria
                 ORDER BY p.data_cadastro DESC, p.id DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':categoria' => $categoriaNome]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Retorna produtos já com a imagem escolhida via subconsulta
     * (mais performático do que chamar getImagemPrincipal() dentro de um loop).
     * Se $categoriaNome vier nulo, traz todos ativos.
     */
    public function getListaParaGrid(?string $categoriaNome = null): array
    {
        if ($categoriaNome) {
            $sql = "
                SELECT p.id,
                       p.nome,
                       p.preco,
                       (SELECT i.url_imagem
                          FROM imagemproduto i
                         WHERE i.produto_id = p.id
                         ORDER BY i.principal DESC, i.id ASC
                         LIMIT 1) AS url_imagem
                  FROM produto p
                  JOIN categoria c ON c.id = p.categoria_id
                 WHERE p.ativo = 1
                   AND c.ativo = 1
                   AND c.nome = :categoria
                 ORDER BY p.data_cadastro DESC, p.id DESC
            ";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':categoria' => $categoriaNome]);
        } else {
            $sql = "
                SELECT p.id,
                       p.nome,
                       p.preco,
                       (SELECT i.url_imagem
                          FROM imagemproduto i
                         WHERE i.produto_id = p.id
                         ORDER BY i.principal DESC, i.id ASC
                         LIMIT 1) AS url_imagem
                  FROM produto p
                 WHERE p.ativo = 1
                 ORDER BY p.data_cadastro DESC, p.id DESC
            ";
            $stmt = $this->pdo->query($sql);
        }

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
