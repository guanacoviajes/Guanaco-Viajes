<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;

final class Pages
{
    public static function find(string $slug): ?array
    {
        return Database::one('SELECT slug, titulo, cuerpo, seo_title, seo_desc, updated_at FROM pages WHERE slug = ?', [$slug]);
    }
}
