<?php
declare(strict_types=1);
namespace PmntCodeDB\Factory;
use InvalidArgumentException;
use PmntCodeDB\Config\DatabaseConfig;
use PmntCodeDB\Connection;
use PmntCodeDB\Contracts\DatabaseInterface;
use PmntCodeDB\Database;
final class DatabaseFactory {
    public static function create(DatabaseConfig $config): DatabaseInterface { return new Database(new Connection($config)); }
    /** @param array{dsn:string,username?:?string,password?:?string,options?:array<int,mixed>} $config */
    public static function fromArray(array $config): DatabaseInterface {
        if (!isset($config['dsn']) || !is_string($config['dsn'])) { throw new InvalidArgumentException('DSN obrigatório.'); }
        return self::create(new DatabaseConfig($config['dsn'], $config['username'] ?? null, $config['password'] ?? null, $config['options'] ?? []));
    }
}
