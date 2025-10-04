<?php
declare(strict_types=1);
require_once __DIR__ . '/config.php';

final class Database {
    private static ?PDO $pdo = null;

    public static function conn(): PDO {
        if (self::$pdo instanceof PDO) {
            return self::$pdo;
        }

        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,     // throw exceptions
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,           // assoc arrays by default
            PDO::ATTR_EMULATE_PREPARES   => false,                      // use native prepares
            // Enable strict mode so invalid data is rejected rather than coerced
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET sql_mode='STRICT_ALL_TABLES,NO_ZERO_DATE,NO_ZERO_IN_DATE'",
        ];

        self::$pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        return self::$pdo;
    }

    /** Prepare + execute in one call (keeps page code tidy). */
    public static function run(string $sql, array $args = []): PDOStatement {
        $stmt = self::conn()->prepare($sql);
        $stmt->execute($args);
        return $stmt;
    }

    /** Transaction helpers (optional quality-of-life). */
    public static function begin(): void     { self::conn()->beginTransaction(); }
    public static function commit(): void    { self::conn()->commit(); }
    public static function rollBack(): void  { if (self::conn()->inTransaction()) self::conn()->rollBack(); }
    public static function inTx(): bool      { return self::conn()->inTransaction(); }
    public static function lastId(): string  { return self::conn()->lastInsertId(); }
}
