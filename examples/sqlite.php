<?php
declare(strict_types=1);
require dirname(__DIR__) . '/vendor/autoload.php';
use PmntCodeDB\Factory\DatabaseFactory;
$db = DatabaseFactory::fromArray(['dsn' => 'sqlite:' . __DIR__ . '/example.sqlite']);
$db->execute('CREATE TABLE IF NOT EXISTS users (id INTEGER PRIMARY KEY AUTOINCREMENT, name TEXT NOT NULL, email TEXT NOT NULL UNIQUE)');
$email = 'ana.' . bin2hex(random_bytes(4)) . '@example.com';
$db->transaction(function ($db) use ($email): void {
    $db->execute('INSERT INTO users (name,email) VALUES (:name,:email)', ['name' => 'Ana', 'email' => $email]);
});
print_r($db->fetchAll('SELECT id,name,email FROM users ORDER BY id'));
