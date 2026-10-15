<?php
declare(strict_types=1);

class ConteudoController
{
    public function __construct(private PDO $pdo)
    {
    }

    public function porCategoriaSlug(string $slug): ?array
    {
        $stmtCategoria = $this->pdo->prepare(
            "SELECT * FROM categorias WHERE slug = :slug AND ativo = TRUE"
        );
        $stmtCategoria->execute([':slug' => $slug]);
        $categoria = $stmtCategoria->fetch();

        if (!$categoria) {
            return null;
        }

        $stmtConteudos = $this->pdo->prepare(
            "SELECT c.titulo, c.slug, c.resumo, c.imagem_capa, c.publicado_em,
                    u.nome AS autor_nome
             FROM conteudos c
             INNER JOIN usuarios u ON u.id = c.autor_id
             WHERE c.categoria_id = :categoria_id
               AND c.status = 'publicado'
               AND c.publicado_em IS NOT NULL
             ORDER BY c.publicado_em DESC"
        );
        $stmtConteudos->execute([':categoria_id' => $categoria['id']]);

        return [
            'categoria' => $categoria,
            'conteudos' => $stmtConteudos->fetchAll(),
        ];
    }

    public function porSlug(string $slug): ?array
    {
        $stmt = $this->pdo->prepare(
            "SELECT c.*,
                    cat.nome AS categoria_nome,
                    cat.slug AS categoria_slug,
                    u.nome AS autor_nome
             FROM conteudos c
             INNER JOIN categorias cat ON cat.id = c.categoria_id
             INNER JOIN usuarios u ON u.id = c.autor_id
             WHERE c.slug = :slug
               AND c.status = 'publicado'
               AND c.publicado_em IS NOT NULL"
        );
        $stmt->execute([':slug' => $slug]);
        $conteudo = $stmt->fetch();

        return $conteudo ?: null;
    }

    public function tagsDoConteudo(int $conteudoId): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT t.nome
             FROM tags t
             INNER JOIN conteudo_tags ct ON ct.tag_id = t.id
             WHERE ct.conteudo_id = :id
             ORDER BY t.nome"
        );
        $stmt->execute([':id' => $conteudoId]);

        return $stmt->fetchAll();
    }

    public static function extrairVideoId(?string $url): ?string
    {
        if (empty($url)) {
            return null;
        }

        $partes = parse_url($url);

        if ($partes === false) {
            return null;
        }

        $host = strtolower($partes['host'] ?? '');
        $caminho = trim($partes['path'] ?? '', '/');
        $videoId = null;

        if (str_contains($host, 'youtu.be')) {
            $videoId = explode('/', $caminho)[0] ?? null;
        } elseif (str_contains($host, 'youtube.com')) {
            parse_str($partes['query'] ?? '', $parametros);

            if (!empty($parametros['v']) && is_string($parametros['v'])) {
                $videoId = $parametros['v'];
            } elseif (str_starts_with($caminho, 'embed/')) {
                $videoId = substr($caminho, 6);
            } elseif (str_starts_with($caminho, 'shorts/')) {
                $videoId = substr($caminho, 7);
            }
        }

        if (!is_string($videoId) || !preg_match('/^[a-zA-Z0-9_-]{6,20}$/', $videoId)) {
            return null;
        }

        return $videoId;
    }
}
