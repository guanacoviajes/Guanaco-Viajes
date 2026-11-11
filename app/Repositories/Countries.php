<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;

final class Countries
{
    /** @return array{sudamerica: list<array>, europa: list<array>} */
    public static function byContinent(): array
    {
        $out = ['sudamerica' => [], 'europa' => []];
        $rows = Database::all(
            'SELECT id, slug, nombre, continente, resumen, foto, foto_pos, foto_alt
               FROM countries WHERE publicado = 1 ORDER BY continente, orden, nombre'
        );
        foreach ($rows as $r) {
            $out[$r['continente']][] = $r;
        }
        return $out;
    }

    /** @return list<array> */
    public static function all(): array
    {
        return Database::all(
            'SELECT id, slug, nombre, continente, resumen, foto, foto_pos, foto_alt
               FROM countries WHERE publicado = 1
              ORDER BY FIELD(continente, "sudamerica", "europa"), orden, nombre'
        );
    }

    public static function find(string $slug): ?array
    {
        return Database::one(
            'SELECT * FROM countries WHERE slug = ? AND publicado = 1',
            [$slug]
        );
    }

    /** @return list<array> otros países del mismo continente */
    public static function others(string $continente, int $exceptId): array
    {
        return Database::all(
            'SELECT slug, nombre, continente, foto, foto_pos, foto_alt
               FROM countries WHERE publicado = 1 AND continente = ? AND id <> ?
              ORDER BY orden, nombre',
            [$continente, $exceptId]
        );
    }

    public static function continentLabel(string $c): string
    {
        return $c === 'sudamerica' ? 'Sudamérica' : 'Europa';
    }
}
