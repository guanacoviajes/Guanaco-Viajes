<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;

final class Departures
{
    /** Salidas publicadas que aún no han terminado. @return list<array> */
    public static function published(?int $countryId = null): array
    {
        $sql = 'SELECT d.*, c.nombre AS pais, c.slug AS pais_slug, c.continente AS pais_cont
                  FROM departures d LEFT JOIN countries c ON c.id = d.country_id
                 WHERE d.publicada = 1 AND (d.fecha_fin IS NULL OR d.fecha_fin >= CURDATE())';
        $args = [];
        if ($countryId !== null) {
            $sql .= ' AND d.country_id = ?';
            $args[] = $countryId;
        }
        return Database::all($sql . ' ORDER BY d.fecha_inicio IS NULL, d.fecha_inicio, d.orden, d.id', $args);
    }
}
