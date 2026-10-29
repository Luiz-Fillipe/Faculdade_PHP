<?php
declare(strict_types=1);

class ConteudoController
{
    public function __construct(private PDO $pdo)
    {
    }

    /**
     * Categoria ativa com suas publicações paginadas.
     * Paginação: COUNT(*) para o total e LIMIT/OFFSET (vinculados como inteiros).
     */
    public function porCategoriaSlug(string $slug, int $pagina = 1, int $porPagina = 6): ?array
    {
        $stmtCategoria = $this->pdo->prepare(
            "SELECT * FROM categorias WHERE slug = :slug AND ativo = TRUE"
        );
        $stmtCategoria->execute([':slug' => $slug]);
        $categoria = $stmtCategoria->fetch();

        if (!$categoria) {
            return null;
        }

        $stmtTotal = $this->pdo->prepare(
            "SELECT COUNT(*)
             FROM conteudos
             WHERE categoria_id = :categoria_id
               AND status = 'publicado'
               AND publicado_em IS NOT NULL"
        );
        $stmtTotal->execute([':categoria_id' => $categoria['id']]);
        $total = (int) $stmtTotal->fetchColumn();

        $paginacao = self::calcularPaginacao($total, $pagina, $porPagina);

        $stmtConteudos = $this->pdo->prepare(
            "SELECT c.titulo, c.slug, c.resumo, c.imagem_capa, c.publicado_em,
                    u.nome AS autor_nome
             FROM conteudos c
             INNER JOIN usuarios u ON u.id = c.autor_id
             WHERE c.categoria_id = :categoria_id
               AND c.status = 'publicado'
               AND c.publicado_em IS NOT NULL
             ORDER BY c.publicado_em DESC
             LIMIT :limite OFFSET :offset"
        );
        $stmtConteudos->bindValue(':categoria_id', (int) $categoria['id'], PDO::PARAM_INT);
        $stmtConteudos->bindValue(':limite', $porPagina, PDO::PARAM_INT);
        $stmtConteudos->bindValue(':offset', $paginacao['offset'], PDO::PARAM_INT);
        $stmtConteudos->execute();

        return [
            'categoria' => $categoria,
            'conteudos' => $stmtConteudos->fetchAll(),
            'total' => $total,
            'pagina' => $paginacao['paginaAtual'],
            'totalPaginas' => $paginacao['totalPaginas'],
        ];
    }

    /**
     * OFFSET = (página - 1) * registros por página. A página é ajustada ao
     * intervalo válido (1 até o total de páginas).
     */
    public static function calcularPaginacao(int $total, int $pagina, int $porPagina): array
    {
        $totalPaginas = (int) ceil($total / max(1, $porPagina));
        $pagina = max(1, min($pagina, max(1, $totalPaginas)));

        return [
            'offset' => ($pagina - 1) * $porPagina,
            'totalPaginas' => $totalPaginas,
            'paginaAtual' => $pagina,
        ];
    }

    public function porSlug(string $slug): ?array
    {
        return $this->buscarPublicado('c.slug = :valor', [':valor' => $slug]);
    }

    public function porId(int $id): ?array
    {
        return $this->buscarPublicado('c.id = :valor', [':valor' => $id]);
    }

    private function buscarPublicado(string $condicao, array $parametros): ?array
    {
        $stmt = $this->pdo->prepare(
            "SELECT c.*,
                    cat.nome AS categoria_nome,
                    cat.slug AS categoria_slug,
                    u.nome AS autor_nome
             FROM conteudos c
             INNER JOIN categorias cat ON cat.id = c.categoria_id
             INNER JOIN usuarios u ON u.id = c.autor_id
             WHERE {$condicao}
               AND c.status = 'publicado'
               AND c.publicado_em IS NOT NULL"
        );
        $stmt->execute($parametros);
        $conteudo = $stmt->fetch();

        return $conteudo ?: null;
    }

    /**
     * Todas as categorias ativas com o total de publicações. LEFT JOIN mantém
     * as categorias que ainda não têm nenhum conteúdo (total 0).
     */
    public function categoriasComTotal(): array
    {
        return $this->pdo->query(
            "SELECT cat.id, cat.nome, cat.slug, COUNT(c.id) AS total
             FROM categorias cat
             LEFT JOIN conteudos c
                    ON c.categoria_id = cat.id
                   AND c.status = 'publicado'
                   AND c.publicado_em IS NOT NULL
             WHERE cat.ativo = TRUE
             GROUP BY cat.id, cat.nome, cat.slug
             ORDER BY cat.ordem, cat.nome"
        )->fetchAll();
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
