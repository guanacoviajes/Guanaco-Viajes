<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Http;
use App\Core\Request;
use App\Core\View;
use App\Repositories\Countries;
use App\Repositories\Posts;

final class BlogController
{
    private const PER_PAGE = 9;

    public function index(Request $r): void
    {
        $slug = isset($_GET['pais']) && is_string($_GET['pais']) && preg_match('/^[a-z0-9-]+$/', $_GET['pais']) ? $_GET['pais'] : null;
        $country = $slug ? Countries::find($slug) : null;
        $countryId = $country ? (int) $country['id'] : null;
        $total = Posts::count($countryId);
        $pages = max(1, (int) ceil($total / self::PER_PAGE));
        $page = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
        if ($page > $pages) {
            Http::redirect('/blog' . ($slug ? '?pais=' . $slug : ''), 302);
        }
        echo View::render('pages/blog', [
            'posts'   => Posts::page($page, self::PER_PAGE, $countryId),
            'filtro'  => Posts::countriesWithPosts(),
            'paisSel' => $country ? $country['slug'] : null,
            'page'    => $page,
            'pages'   => $pages,
        ], [
            'title'       => ($country ? 'Historias de ' . $country['nombre'] . ' · ' : '') . 'Blog · Guanaco Viajes',
            'description' => 'Historias, ideas y rincones para viajar con curiosidad.',
            // las páginas filtradas o paginadas no compiten con la principal
            'robots'      => ($slug || $page > 1) ? 'noindex,follow' : 'index,follow',
            'canonical'   => rtrim((string) config('app.url'), '/') . '/blog',
        ]);
    }

    public function show(Request $r, string $slug): void
    {
        $post = Posts::find($slug) ?? Http::notFound();
        echo View::render('pages/articulo', [
            'post'         => $post,
            'relacionados' => Posts::related((int) $post['id'], $post['country_id'] !== null ? (int) $post['country_id'] : null),
        ], [
            'title'       => $post['seo_title'] ?: ($post['titulo'] . ' · Guanaco Viajes'),
            'description' => $post['seo_desc'] ?: mb_substr(trim(strip_tags((string) $post['extracto'])), 0, 155),
            'og_image'    => $post['portada'] ?: '/assets/img/seed/foto-5.jpg',
        ]);
    }
}
