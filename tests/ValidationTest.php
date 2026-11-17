<?php
declare(strict_types=1);
namespace PmntCodeDB\Tests;
use InvalidArgumentException;
use PDO;
use PHPUnit\Framework\TestCase;
use PmntCodeDB\Contracts\ConnectionInterface;
use PmntCodeDB\Database;
final class ValidationTest extends TestCase {
    private function databaseWithoutConnection(): Database {
        $connection = new class implements ConnectionInterface {
            public function getPdo(): PDO { throw new \RuntimeException('Connection must not be opened for invalid input'); }
        };
        return new Database($connection);
    }
    public function testRejectsBlankSqlWithoutConnecting(): void {
        $this->expectException(InvalidArgumentException::class);
        $this->databaseWithoutConnection()->query('  ');
    }
    public function testRejectsMixedParametersWithoutConnecting(): void {
        $this->expectException(InvalidArgumentException::class);
        $this->databaseWithoutConnection()->query('SELECT ?', [0 => 1, 'name' => 'Ana']);
    }
    public function testRejectsNonSequentialPositionsWithoutConnecting(): void {
        $this->expectException(InvalidArgumentException::class);
        $this->databaseWithoutConnection()->query('SELECT ?', [1 => 'Ana']);
    }
    public function testRejectsArrayParametersWithoutConnecting(): void {
        $this->expectException(InvalidArgumentException::class);
        $this->databaseWithoutConnection()->query('SELECT ?', [['bad']]);
    }
}
