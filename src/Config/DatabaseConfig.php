<?php
declare(strict_types=1);
namespace PmntCodeDB\Config;
use InvalidArgumentException;
use PDO;
final readonly class DatabaseConfig {
    /** @param array<int,mixed> $options */
    public function __construct(public string $dsn, public ?string $username = null, public ?string $password = null, public array $options = []) {
        if (trim($dsn) === '') { throw new InvalidArgumentException('DSN não pode ser vazio.'); }
        foreach ($options as $key => $_) { if (!is_int($key)) { throw new InvalidArgumentException('Opções PDO devem usar chaves inteiras.'); } }
        foreach ([PDO::ATTR_ERRMODE, PDO::ATTR_EMULATE_PREPARES] as $forbidden) {
            if (array_key_exists($forbidden, $options)) { throw new InvalidArgumentException('Não é permitido sobrescrever opções PDO de segurança.'); }
        }
    }
    /** @return array<int,mixed> */
    public function pdoOptions(): array {
        return $this->options + [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
    }
}
