<?php
declare(strict_types=1);
namespace PmntCodeDB\Tests;
use InvalidArgumentException;
use LogicException;
use PDO;
use PHPUnit\Framework\TestCase;
use PmntCodeDB\Config\DatabaseConfig;
use PmntCodeDB\Factory\DatabaseFactory;
final class DatabaseTest extends TestCase {
    private function db(): \PmntCodeDB\Contracts\DatabaseInterface {
        if (!extension_loaded('pdo_sqlite')) { self::markTestSkipped('pdo_sqlite indisponível'); }
        $db = DatabaseFactory::create(new DatabaseConfig('sqlite::memory:'));
        $db->execute('CREATE TABLE users (id INTEGER PRIMARY KEY, name TEXT NOT NULL)');
        return $db;
    }
    public function testCrudAndNamedParameters(): void {
        $db = $this->db();
        self::assertSame(1, $db->execute('INSERT INTO users (name) VALUES (:name)', ['name' => 'Ana']));
        self::assertSame('Ana', $db->fetch('SELECT name FROM users WHERE id=:id', ['id' => 1])['name']);
        self::assertSame(1, $db->execute('UPDATE users SET name=? WHERE id=?', ['Bia', 1]));
        self::assertSame('Bia', $db->fetchAll('SELECT name FROM users')[0]['name']);
        self::assertSame(1, $db->execute('DELETE FROM users WHERE id=?', [1]));
        self::assertFalse($db->fetch('SELECT * FROM users WHERE id=?', [1]));
    }
    public function testRollbackOnException(): void {
        $db = $this->db();
        try {
            $db->transaction(function ($db): void {
                $db->execute('INSERT INTO users (name) VALUES (?)', ['Ana']);
                throw new \RuntimeException('falha');
            });
            self::fail('Exceção esperada');
        } catch (\RuntimeException $e) { self::assertSame('falha', $e->getMessage()); }
        self::assertSame([], $db->fetchAll('SELECT * FROM users'));
    }
    public function testCommit(): void {
        $db = $this->db();
        $value = $db->transaction(function ($db): int { $db->execute('INSERT INTO users (name) VALUES (?)', ['Ana']); return 42; });
        self::assertSame(42, $value);
        self::assertCount(1, $db->fetchAll('SELECT * FROM users'));
    }
    public function testNestedTransactionRejected(): void {
        $db = $this->db();
        $this->expectException(LogicException::class);
        $db->transaction(fn ($db) => $db->transaction(fn () => null));
    }
    public function testInvalidParameterTypeRejected(): void {
        $db = $this->db();
        $this->expectException(InvalidArgumentException::class);
        $db->query('SELECT ?', [['unsafe']]);
    }
    public function testConfigurationSecurity(): void {
        $this->expectException(InvalidArgumentException::class);
        new DatabaseConfig('sqlite::memory:', options: [PDO::ATTR_ERRMODE => PDO::ERRMODE_SILENT]);
    }
}
