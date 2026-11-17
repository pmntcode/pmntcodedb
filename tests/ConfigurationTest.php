<?php
declare(strict_types=1);
namespace PmntCodeDB\Tests;
use InvalidArgumentException;
use PDO;
use PHPUnit\Framework\TestCase;
use PmntCodeDB\Config\DatabaseConfig;
use PmntCodeDB\Factory\DatabaseFactory;
final class ConfigurationTest extends TestCase {
    public function testRejectsBlankDsn(): void {
        $this->expectException(InvalidArgumentException::class);
        new DatabaseConfig('  ');
    }
    public function testEnforcesSecurePdoOptions(): void {
        $config = new DatabaseConfig('sqlite::memory:');
        self::assertSame(PDO::ERRMODE_EXCEPTION, $config->pdoOptions()[PDO::ATTR_ERRMODE]);
        self::assertFalse($config->pdoOptions()[PDO::ATTR_EMULATE_PREPARES]);
    }
    public function testRejectsOverrideOfSecurityOptions(): void {
        $this->expectException(InvalidArgumentException::class);
        new DatabaseConfig('sqlite::memory:', options: [PDO::ATTR_EMULATE_PREPARES => true]);
    }
    public function testFactoryRequiresDsn(): void {
        $this->expectException(InvalidArgumentException::class);
        DatabaseFactory::fromArray([]);
    }
}
