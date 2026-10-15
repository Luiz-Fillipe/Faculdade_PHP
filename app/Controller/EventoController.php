<?php
declare(strict_types=1);

class EventoController
{
    public function __construct(private PDO $pdo)
    {
    }

    public function detalhe(int $id): ?array
    {
        $stmt = $this->pdo->prepare(
            "SELECT e.*, u.nome AS autor_nome
             FROM eventos e
             INNER JOIN usuarios u ON u.id = e.autor_id
             WHERE e.id = :id
               AND e.status = 'publicado'"
        );
        $stmt->execute([':id' => $id]);
        $evento = $stmt->fetch();

        return $evento ?: null;
    }

    public function proximos(): array
    {
        return $this->pdo->query(
            "SELECT e.*, u.nome AS autor_nome
             FROM eventos e
             INNER JOIN usuarios u ON u.id = e.autor_id
             WHERE e.status = 'publicado'
               AND e.data_inicio >= NOW()
             ORDER BY e.data_inicio ASC"
        )->fetchAll();
    }

    public function encerrados(int $limite = 6): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT e.*, u.nome AS autor_nome
             FROM eventos e
             INNER JOIN usuarios u ON u.id = e.autor_id
             WHERE e.status = 'publicado'
               AND e.data_inicio < NOW()
             ORDER BY e.data_inicio DESC
             LIMIT :limite"
        );
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }
}
