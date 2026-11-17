<?php
declare(strict_types=1);
namespace PmntCodeDB\Contracts;
use PDO;
interface ConnectionInterface { public function getPdo(): PDO; }
