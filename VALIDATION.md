# Validação e homologação — PmntCodeDB

## Verificações executadas no ambiente de geração

- PHP CLI 8.4.24 disponível.
- Sintaxe PHP verificada para arquivos `src/`, `tests/` e `examples/`.
- Verificação de configuração e validações sem banco executada com script de smoke test.
- Composer, PHPUnit e drivers PDO de banco não estão disponíveis neste ambiente. Portanto, **a suíte PHPUnit e os testes de integração não foram executados aqui**.

## Instalação no Linux Mint

```bash
sudo apt update
sudo apt install php-cli php-sqlite3 php-mysql php-pgsql php-xml php-mbstring composer unzip
php -v
php -m | grep -Ei 'PDO|sqlite|mysql|pgsql'
composer install
composer validate --strict
composer test
php examples/sqlite.php
```

> Os pacotes de repositório podem instalar uma versão PHP diferente da desejada. Confirme `php -v` (mínimo 8.2) e a versão do PHP usada pelo Composer.

## Verificações adicionais antes da produção

1. `composer audit` e `composer validate --strict`.
2. `composer test` com zero falhas e sem testes SQLite ignorados.
3. Teste de integração com cada SGBD utilizado em produção, em instância isolada com permissões mínimas.
4. Verificar TLS, credenciais, timeout, backups, logs restritos e políticas de rotação.
5. Executar pipeline CI em PHP 8.2, 8.3 e 8.4.
6. Fazer teste de falha de conexão, concorrência, deadlocks e recuperação em ambiente de staging.

## Observações

- O teste externo só roda quando `PMNTCODEDB_TEST_DSN` está configurado.
- A ausência de falhas nos testes não constitui auditoria de segurança.
- `composer install` sem `composer.lock` resolve versões compatíveis no momento da execução.
- O comando `composer test` depende da instalação das dependências de desenvolvimento.
