<?php
declare(strict_types=1);
namespace PmntCodeDB;
use PDO;
use PDOException;
use PmntCodeDB\Config\DatabaseConfig;
use PmntCodeDB\Contracts\ConnectionInterface;
use PmntCodeDB\Exceptions\DatabaseException;
final class Connection implements ConnectionInterface {
    private ?PDO $pdo = null;
    public function __construct(private readonly DatabaseConfig $config) {}
    public function getPdo(): PDO {
        if ($this->pdo === null) {
            try {
                $this->pdo = new PDO($this->config->dsn, $this->config->username, $this->config->password, $this->config->pdoOptions());
            } catch (PDOException $e) {
                throw new DatabaseException('Não foi possível estabelecer conexão com o banco de dados.', 0, $e);
            }
        }
        return $this->pdo;
    }
}
