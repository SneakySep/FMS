<?php
/**
 * =====================================================================
 * SwiftFreight — Secure PDO Database Bootstrap (template)
 * =====================================================================
 * This file is the SAFE starting point for the future backend layer.
 *
 * Rules enforced here (SQL-injection hardening):
 *   1. PDO with native prepared statements + bound parameters ONLY.
 *      NEVER concatenate user input into a SQL string.
 *   2. Exceptions on error -> nothing silently swallowed, no data leak.
 *   3. Emulated prepares DISABLED so real placeholders/bindings are used.
 *   4. Credentials are read from environment variables (see .env.example),
 *      NOT hardcoded. Never commit real credentials.
 *
 * EXAMPLE USAGE (always parameterized):
 *
 *   require __DIR__ . '/db.php';
 *
 *   // Placeholder style 1: anonymous parameter
 *   $row = DB::run('SELECT * FROM shipments WHERE waybill = ?', [$waybill])
 *              ->fetch();
 *
 *   // Placeholder style 2: named parameter
 *   $stmt = DB::run(
 *       'SELECT * FROM shipments WHERE waybill = :wb AND customer_id = :cid',
 *       [':wb' => $waybill, ':cid' => $customerId]
 *   );
 *
 *   // Write operations use the same run() helper.
 *   DB::run(
 *       'INSERT INTO tickets (subject, message, status) VALUES (?, ?, ?)',
 *       [$subject, $message, 'open']
 *   );
 *
 *   // NEVER do this:
 *   // $sql = "SELECT * FROM shipments WHERE waybill = '$waybill'";   // unsafe
 *   // DB::run($sql);                                                  // unsafe
 * =====================================================================
 */

declare(strict_types=1);

final class DB
{
    private static ?PDO $pdo = null;

    /**
     * Environment variable names used to resolve credentials.
     * Define these in a local `.env` file or in the web-server config;
     * the .gitignore keeps `.env` out of source control.
     */
    private const ENV = [
        'FMS_DB_HOST'    => '127.0.0.1',
        'FMS_DB_PORT'    => '3306',
        'FMS_DB_NAME'    => 'swiftfreight',
        'FMS_DB_USER'    => 'root',
        'FMS_DB_PASS'    => '',
        'FMS_DB_CHARSET' => 'utf8mb4',
    ];

    /**
     * Lazily creates and returns a shared PDO connection.
     */
    public static function pdo(): PDO
    {
        if (self::$pdo instanceof PDO) {
            return self::$pdo;
        }

        $host    = self::env('FMS_DB_HOST');
        $port    = self::env('FMS_DB_PORT');
        $name    = self::env('FMS_DB_NAME');
        $user    = self::env('FMS_DB_USER');
        $pass    = self::env('FMS_DB_PASS');
        $charset = self::env('FMS_DB_CHARSET');

        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=%s',
            $host,
            $port,
            $name,
            $charset
        );

        self::$pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // fail loudly, never silently
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // associative rows
            PDO::ATTR_EMULATE_PREPARES   => false,                  // true server-side prepared statements
        ]);

        return self::$pdo;
    }

    /**
     * Prepare + execute a parameterized statement in one call.
     * $params must be values ONLY (bound as data) — never SQL fragments.
     *
     * @param string             $sql     SQL with `?` or `:name` placeholders
     * @param array<int|string, mixed> $params values to bind
     */
    public static function run(string $sql, array $params = []): PDOStatement
    {
        $stmt = self::pdo()->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    /**
     * Convenience transaction wrapper.
     */
    public static function transaction(callable $callback): mixed
    {
        $pdo = self::pdo();
        $pdo->beginTransaction();
        try {
            $result = $callback($pdo);
            $pdo->commit();
            return $result;
        } catch (Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    private static function env(string $key): string
    {
        $value = getenv($key);
        return $value === false ? self::ENV[$key] : (string) $value;
    }
}