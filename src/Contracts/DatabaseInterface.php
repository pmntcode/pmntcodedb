<?php
declare(strict_types=1);
namespace PmntCodeDB\Contracts;
use PDOStatement;
interface DatabaseInterface {
    /** @param array<int|string,mixed> $params */
    public function query(string $sql, array $params = []): PDOStatement;
    /** @param array<int|string,mixed> $params @return array<string,mixed>|false */
    public function fetch(string $sql, array $params = []): array|false;
    /** @param array<int|string,mixed> $params @return list<array<string,mixed>> */
    public function fetchAll(string $sql, array $params = []): array;
    /** @param array<int|string,mixed> $params */
    public function execute(string $sql, array $params = []): int;
    public function lastInsertId(?string $name = null): string|false;
    /** @template T @param callable(self):T $callback @return T */
    public function transaction(callable $callback): mixed;
}
