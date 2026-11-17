<?php
declare(strict_types=1);
namespace PmntCodeDB\Tests\Integration;
use PHPUnit\Framework\TestCase;
use PmntCodeDB\Factory\DatabaseFactory;
final class ExternalDatabaseTest extends TestCase {
    public function testConnectionAndPreparedQuery(): void {
        $dsn = getenv('PMNTCODEDB_TEST_DSN');
        if (!$dsn) { self::markTestSkipped('Defina PMNTCODEDB_TEST_DSN para habilitar teste externo.'); }
        $db = DatabaseFactory::fromArray([
            'dsn' => $dsn,
            'username' => getenv('PMNTCODEDB_TEST_USER') ?: null,
            'password' => getenv('PMNTCODEDB_TEST_PASSWORD') ?: null,
        ]);
        $row = $db->fetch('SELECT :value AS result', ['value' => 'ok']);
        self::assertSame('ok', $row['result']);
    }
}
