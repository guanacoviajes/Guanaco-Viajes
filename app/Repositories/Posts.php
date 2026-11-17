<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use App\Core\Schema;

final class Posts
{
    private const VISIBLE = 'p.publicado = 1 AND (p.publicado_en IS NULL OR p.publicado_en <= NOW())';

    /** ¿Existe la tabla de países por artículo? Si aún no se ha importado la actualización, se usa el país principal. */
    public static function hasPivot(): bool
    {
        return Schema::hasTable('post_countries');
    }

    /**
     * Condición SQL «el artículo pertenece al país ?». Se cumple si es su país principal (posts.country_id) o si está en la
     * tabla de países del artículo: así nada se pierde aunque alguien toque los datos a mano y queden desincronizados.
     * Cada uso lleva DOS parámetros (el identificador del país, dos veces); ver bindCountry().
     */
    private static function inCountry(): string
    {
        return self::hasPivot()
            ? ' AND (p.country_id = ? OR EXISTS (SELECT 1 FROM post_countries pc WHERE pc.post_id = p.id AND pc.country_id = ?))'
            : ' AND p.country_id = ?';
    }

    /** @return list<int> parámetros que necesita inCountry() */
    private static function bindCountry(int $countryId): array
    {
        return self::hasPivot() ? [$countryId, $countryId] : [$countryId];
    }

    public static function count(?int $countryId = null): int
    {
        $sql = 'SELECT COUNT(*) FROM posts p WHERE ' . self::VISIBLE;
        $args = [];
        if ($countryId !== null) {
            $sql .= self::inCountry();
            $args = self::bindCountry($countryId);
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
            $sql .= self::inCountry();
            $args = self::bindCountry($countryId);
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
        if (self::hasPivot()) {
            return Database::all(
                'SELECT DISTINCT c.slug, c.nombre FROM posts p
                   JOIN countries c ON (c.id = p.country_id OR EXISTS (SELECT 1 FROM post_countries pc WHERE pc.post_id = p.id AND pc.country_id = c.id))
                  WHERE ' . self::VISIBLE . ' AND c.publicado = 1 ORDER BY c.nombre'
            );
        }
        return Database::all(
            'SELECT DISTINCT c.slug, c.nombre FROM posts p JOIN countries c ON c.id = p.country_id
              WHERE ' . self::VISIBLE . ' ORDER BY c.nombre'
        );
    }

    /**
     * Países publicados de un artículo, el principal primero.
     * @return list<array{id:int|string,slug:string,nombre:string,continente:string}>
     */
    public static function countriesOf(int $postId): array
    {
        if (self::hasPivot()) {
            return Database::all(
                'SELECT c.id, c.slug, c.nombre, c.continente FROM countries c
                   JOIN posts p ON p.id = ?
                   LEFT JOIN post_countries pc ON pc.country_id = c.id AND pc.post_id = p.id
                  WHERE c.publicado = 1 AND (pc.post_id IS NOT NULL OR c.id = p.country_id)
                  ORDER BY (c.id = p.country_id) DESC, pc.orden, c.nombre',
                [$postId]
            );
        }
        return Database::all(
            'SELECT c.id, c.slug, c.nombre, c.continente FROM posts p JOIN countries c ON c.id = p.country_id
              WHERE p.id = ? AND c.publicado = 1',
            [$postId]
        );
    }

    /** @return list<int> identificadores de TODOS los países de un artículo (también ocultos), en orden. Para el panel. */
    public static function countryIds(int $postId): array
    {
        if (self::hasPivot()) {
            $rows = Database::all('SELECT country_id FROM post_countries WHERE post_id = ? ORDER BY orden, country_id', [$postId]);
            return array_map(static fn($r) => (int) $r['country_id'], $rows);
        }
        $v = Database::value('SELECT country_id FROM posts WHERE id = ?', [$postId]);
        return $v === null ? [] : [(int) $v];
    }

    /**
     * Guarda los países de un artículo (el primero es el principal) y deja posts.country_id igual que el principal.
     * @param list<int> $ids
     */
    public static function syncCountries(int $postId, array $ids): void
    {
        $ids = array_values(array_unique(array_map('intval', $ids)));
        Database::run('UPDATE posts SET country_id = ? WHERE id = ?', [$ids[0] ?? null, $postId]);
        if (!self::hasPivot()) {
            return;
        }
        $pdo = Database::pdo();
        $pdo->beginTransaction();
        try {
            Database::run('DELETE FROM post_countries WHERE post_id = ?', [$postId]);
            foreach ($ids as $i => $cid) {
                Database::run('INSERT INTO post_countries (post_id, country_id, orden) VALUES (?,?,?)', [$postId, $cid, min(255, $i)]);
            }
            $pdo->commit();
        } catch (\Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }
}
