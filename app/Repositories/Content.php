<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;

/** Diapositivas, tipos de viaje, artículos y opciones del formulario. */
final class Content
{
    /** @return list<array> */
    public static function slides(): array
    {
        return Database::all('SELECT * FROM carousel_slides WHERE activo = 1 ORDER BY orden, id');
    }

    /** @return list<array> */
    public static function tripTypes(): array
    {
        return Database::all('SELECT * FROM trip_types ORDER BY orden, id');
    }

    /** @return list<array> */
    public static function latestPosts(int $limit = 3): array
    {
        return Database::all(
            'SELECT p.slug, p.titulo, p.extracto, p.portada, p.portada_alt, p.publicado_en, c.nombre AS pais
               FROM posts p LEFT JOIN countries c ON c.id = p.country_id
              WHERE p.publicado = 1 AND (p.publicado_en IS NULL OR p.publicado_en <= NOW())
              ORDER BY p.publicado_en DESC, p.id DESC LIMIT ' . (int) $limit
        );
    }

    /** @return list<array> */
    public static function interests(): array
    {
        return Database::all('SELECT id, nombre FROM interests WHERE activo = 1 ORDER BY orden, id');
    }
}
