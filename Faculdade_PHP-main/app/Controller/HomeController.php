<?php
declare(strict_types=1);


class HomeController
{
    public function __construct(private PDO $pdo)
    {
    }

   
     * @return array{destaque: array|false, chamadas: array, recentes: array, eventos: array}
     */
    public function carregar(string $busca): array
    {
        $destaque = $busca === '' ? $this->buscaDestaque() : false;

        $publicacoes = $busca !== ''
            ? $this->buscaPublicacoes($busca)
            : $this->ultimasPublicacoes(9);

        if ($destaque) {
            $publicacoes = array_values(array_filter(
                $publicacoes,
                static function (array $publicacao) use ($destaque): bool {
                    return $publicacao['slug'] !== $destaque['slug'];
                }
            ));
        } elseif (!empty($publicacoes)) {
            $destaque = array_shift($publicacoes);
        }

        return [
            'destaque' => $destaque,
            'chamadas' => array_slice($publicacoes, 0, 2),
            'recentes' => array_slice($publicacoes, 2, 6),
            'eventos'  => $this->eventosProximos(3),
        ];
    }

    private function buscaDestaque(): ?array
    {
        $destaque = $this->pdo->query(
            "SELECT c.titulo, c.slug, c.resumo, c.imagem_capa, c.publicado_em,
                    cat.nome AS categoria_nome,
                    cat.slug AS categoria_slug,
                    u.nome AS autor_nome
             FROM conteudos c
             INNER JOIN categorias cat ON cat.id = c.categoria_id
             INNER JOIN usuarios u ON u.id = c.autor_id
             WHERE c.destaque = TRUE
               AND c.status = 'publicado'
               AND c.publicado_em IS NOT NULL
             ORDER BY c.publicado_em DESC
             LIMIT 1"
        )->fetch();

        return $destaque ?: null;
    }

    private function buscaPublicacoes(string $busca): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT c.titulo, c.slug, c.resumo, c.imagem_capa, c.publicado_em,
                    cat.nome AS categoria_nome,
                    cat.slug AS categoria_slug,
                    u.nome AS autor_nome
             FROM conteudos c
             INNER JOIN categorias cat ON cat.id = c.categoria_id
             INNER JOIN usuarios u ON u.id = c.autor_id
             WHERE c.status = 'publicado'
               AND c.publicado_em IS NOT NULL
               AND (c.titulo LIKE :busca OR c.resumo LIKE :busca)
             ORDER BY c.publicado_em DESC
             LIMIT 9"
        );
        $stmt->execute([':busca' => '%' . $busca . '%']);

        return $stmt->fetchAll();
    }

    private function ultimasPublicacoes(int $limite): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT c.titulo, c.slug, c.resumo, c.imagem_capa, c.publicado_em,
                    cat.nome AS categoria_nome,
                    cat.slug AS categoria_slug,
                    u.nome AS autor_nome
             FROM conteudos c
             INNER JOIN categorias cat ON cat.id = c.categoria_id
             INNER JOIN usuarios u ON u.id = c.autor_id
             WHERE c.status = 'publicado'
               AND c.publicado_em IS NOT NULL
             ORDER BY c.publicado_em DESC
             LIMIT :limite"
        );
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    private function eventosProximos(int $limite): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT id, titulo, data_inicio, local, link_inscricao
             FROM eventos
             WHERE status = 'publicado'
               AND data_inicio >= NOW()
             ORDER BY data_inicio ASC
             LIMIT :limite"
        );
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }
}
