<?php
declare(strict_types=1);
namespace PmntCodeDB;
use InvalidArgumentException;
use LogicException;
use PDO;
use PDOStatement;
use Throwable;
use PmntCodeDB\Contracts\ConnectionInterface;
use PmntCodeDB\Contracts\DatabaseInterface;
final class Database implements DatabaseInterface {
    public function __construct(private readonly ConnectionInterface $connection) {}
    public function query(string $sql, array $params = []): PDOStatement {
        if (trim($sql) === '') { throw new InvalidArgumentException('SQL não pode ser vazio.'); }
        $numeric = array_filter(array_keys($params), 'is_int');
        if ($numeric !== [] && count($numeric) !== count($params)) {
            throw new InvalidArgumentException('Não misture parâmetros posicionais e nomeados.');
        }
        if ($numeric !== []) {
            if (array_keys($params) !== range(0, count($params) - 1)) {
                throw new InvalidArgumentException('Parâmetros posicionais devem ser uma lista indexada a partir de zero.');
            }
        }
        foreach ($params as $key => $value) {
            if (is_string($key) && ltrim($key, ':') === '') {
                throw new InvalidArgumentException('Nome de parâmetro inválido.');
            }
            if (!is_null($value) && !is_bool($value) && !is_int($value) && !is_string($value) && !is_float($value)) {
                throw new InvalidArgumentException('Tipo de parâmetro não suportado.');
            }
        }
        $statement = $this->connection->getPdo()->prepare($sql);
        foreach ($params as $key => $value) {
            $placeholder = is_int($key) ? $key + 1 : ':' . ltrim($key, ':');
            $type = match (true) {
                $value === null => PDO::PARAM_NULL,
                is_bool($value) => PDO::PARAM_BOOL,
                is_int($value) => PDO::PARAM_INT,
                default => PDO::PARAM_STR,
            };
            $statement->bindValue($placeholder, $value, $type);
        }
        $statement->execute();
        return $statement;
    }
    public function fetch(string $sql, array $params = []): array|false { return $this->query($sql, $params)->fetch(PDO::FETCH_ASSOC); }
    public function fetchAll(string $sql, array $params = []): array { return $this->query($sql, $params)->fetchAll(PDO::FETCH_ASSOC); }
    public function execute(string $sql, array $params = []): int { return $this->query($sql, $params)->rowCount(); }
    public function lastInsertId(?string $name = null): string|false { return $this->connection->getPdo()->lastInsertId($name); }
    public function transaction(callable $callback): mixed {
        $pdo = $this->connection->getPdo();
        if ($pdo->inTransaction()) { throw new LogicException('Transações aninhadas não são suportadas.'); }
        $pdo->beginTransaction();
        try {
            $result = $callback($this);
            $pdo->commit();
            return $result;
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) { $pdo->rollBack(); }
            throw $e;
        }
    }
}
