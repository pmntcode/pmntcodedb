# PmntCodeDB 1.0.0

Biblioteca independente de acesso a bancos relacionais usando PDO, PHP 8.2+ e Composer. Não inclui nem exige framework MVC, container DI ou ORM.

## Requisitos

- PHP 8.2+ e `ext-pdo`
- Driver PDO para o banco escolhido (`pdo_sqlite`, `pdo_mysql`, `pdo_pgsql` etc.)
- Composer

## Instalação local

```bash
composer install
php examples/sqlite.php
composer test
```

O exemplo grava em `examples/example.sqlite`. A suíte de testes usa SQLite em memória. Os testes de integração com banco externo são opcionais.

## Uso

```php
<?php
require 'vendor/autoload.php';
use PmntCodeDB\Factory\DatabaseFactory;
$db = DatabaseFactory::fromArray([
    'dsn' => getenv('DB_DSN') ?: 'sqlite::memory:',
    'username' => getenv('DB_USER') ?: null,
    'password' => getenv('DB_PASSWORD') ?: null,
]);
$db->execute('CREATE TABLE users (id INTEGER PRIMARY KEY, name TEXT NOT NULL)');
$db->execute('INSERT INTO users (name) VALUES (:name)', ['name' => 'Ana']);
$user = $db->fetch('SELECT * FROM users WHERE name=:name', ['name' => 'Ana']);
```

## Integração futura em MVC

Injete `PmntCodeDB\Contracts\DatabaseInterface` nos repositories e registre a instância criada pela factory no container de dependências da aplicação. Nenhum container é imposto pela biblioteca.

## Testes externos

Configure `PMNTCODEDB_TEST_DSN`, `PMNTCODEDB_TEST_USER` e `PMNTCODEDB_TEST_PASSWORD` no ambiente, depois execute `composer test`. O teste externo usa uma consulta somente leitura. Não use credenciais administrativas.

## Segurança e limites

- Consultas devem usar placeholders para **valores**; nomes de tabela, coluna e direção de ordenação devem vir de listas permitidas da aplicação.
- Erros PDO usam exceções; não exponha mensagens de erro, DSN, stack traces ou credenciais ao usuário final. O erro de conexão da biblioteca tem mensagem genérica, mas preserva a exceção anterior para logs protegidos.
- `ATTR_ERRMODE=EXCEPTION` e `ATTR_EMULATE_PREPARES=false` são impostos. Alguns drivers podem ter restrições próprias.
- Não suporta transações aninhadas; são rejeitadas explicitamente. DDL pode gerar commits implícitos em determinados bancos.
- `rowCount()` em SELECT não é portátil. `lastInsertId()` depende do driver e pode exigir nome de sequência.
- Não existe reconexão automática, pool, logging de SQL, retry ou ORM. Configuração TLS, privilégios mínimos, timeouts e backups são responsabilidade da implantação.
- Testes automatizados incluídos não equivalem a auditoria ou homologação de produção. Valide no banco e na infraestrutura de destino.

## Distribuição

Nome Composer provisório: `pmntcode/pmntcodedb` (verifique disponibilidade no Packagist). Crie uma tag `v1.0.0` no repositório após homologação. O pacote não é publicado automaticamente.

## Homologação

Consulte [VALIDATION.md](VALIDATION.md) para comandos e critérios de aceite antes de produção.
