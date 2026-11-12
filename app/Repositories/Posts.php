<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;

final class Posts
{
    private const VISIBLE = 'p.publicado = 1 AND (p.publicado_en IS NULL OR p.publicado_en <= NOW())';

    public static function count(?int $countryId = null): int
    {
        $sql = 'SELECT COUNT(*) FROM posts p WHERE ' . self::VISIBLE;
        $args = [];
        if ($countryId !== null) {
            $sql .= ' AND p.country_id = ?';
            $args[] = $countryId;
        }
        return (int) Database::value($sql, $args);
    }

    /** @return list<array> */
    public static function page(int $page, int $perPage, ?int $countryId = null): array
    {
        $sql = 'SELECT p.slug, p.titulo, p.extracto, p.portada, p.portada_alt, p.publicado_en, c.nombre AS pais, c.slug AS pais_slug
                  FROM posts p LEFT JOIN countries c ON c.id = p.country_id WHERE ' . self::VISIBLE;
        $args = [];
        if ($countryId !== null) {
            $sql .= ' AND p.country_id = ?';
            $args[] = $countryId;
        }
        $sql .= ' ORDER BY p.publicado_en DESC, p.id DESC LIMIT ' . (int) $perPage . ' OFFSET ' . (int) max(0, ($page - 1) * $perPage);
        return Database::all($sql, $args);
    }

    public static function find(string $slug): ?array
    {
        return Database::one(
            'SELECT p.*, c.nombre AS pais, c.slug AS pais_slug, c.continente AS pais_cont
               FROM posts p LEFT JOIN countries c ON c.id = p.country_id
              WHERE p.slug = ? AND ' . self::VISIBLE,
            [$slug]
        );
    }

    /** @return list<array> */
    public static function related(int $exceptId, ?int $countryId, int $limit = 3): array
    {
        return Database::all(
            'SELECT p.slug, p.titulo, p.portada, p.portada_alt, p.publicado_en, c.nombre AS pais
               FROM posts p LEFT JOIN countries c ON c.id = p.country_id
              WHERE ' . self::VISIBLE . ' AND p.id <> ?
              ORDER BY (p.country_id <=> ?) DESC, p.publicado_en DESC LIMIT ' . (int) $limit,
            [$exceptId, $countryId]
        );
    }

    /** @return list<array> países que tienen al menos un artículo (para el filtro) */
    public static function countriesWithPosts(): array
    {
        return Database::all(
            'SELECT DISTINCT c.slug, c.nombre FROM posts p JOIN countries c ON c.id = p.country_id
              WHERE ' . self::VISIBLE . ' ORDER BY c.nombre'
        );
    }
}
