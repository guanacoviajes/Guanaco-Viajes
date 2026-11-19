<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;

/** Ajustes y textos editables, cargados una sola vez por petición. */
final class Settings
{
    /** @var array<string,string>|null */
    private static ?array $all = null;

    /** @return array<string,string> */
    public static function all(): array
    {
        if (self::$all === null) {
            self::$all = [];
            foreach (Database::all('SELECT clave, valor FROM settings') as $row) {
                self::$all[$row['clave']] = (string) $row['valor'];
            }
        }
        return self::$all;
    }

    public static function get(string $key, string $default = ''): string
    {
        $all = self::all();
        return array_key_exists($key, $all) && $all[$key] !== '' ? $all[$key] : $default;
    }
}
