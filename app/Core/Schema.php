<?php
declare(strict_types=1);

namespace App\Core;

/**
 * Comprueba qué columnas y tablas existen en la base de datos.
 * Sirve para que la web siga funcionando aunque se suba el código antes de importar una actualización de la
 * base de datos: lo nuevo simplemente no se muestra hasta que existan las columnas.
 */
final class Schema
{
    /** @var array<string,bool> */
    private static array $cache = [];

    public static function hasColumn(string $table, string $column): bool
    {
        return self::cached("c:$table.$column", static fn(): bool => (int) Database::value(
            'SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?',
            [$table, $column]
        ) > 0);
    }

    public static function hasTable(string $table): bool
    {
        return self::cached("t:$table", static fn(): bool => (int) Database::value(
            'SELECT COUNT(*) FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ?',
            [$table]
        ) > 0);
    }

    /** Solo para pruebas. */
    public static function forget(): void
    {
        self::$cache = [];
    }

    private static function cached(string $key, callable $check): bool
    {
        if (!isset(self::$cache[$key])) {
            try {
                self::$cache[$key] = $check();
            } catch (\Throwable) {
                self::$cache[$key] = false; // ante la duda, se comporta como «aún no existe»
            }
        }
        return self::$cache[$key];
    }
}
