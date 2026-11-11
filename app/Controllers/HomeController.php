<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\View;
use App\Repositories\Content;
use App\Repositories\Countries;
use App\Repositories\Settings;

final class HomeController
{
    public function index(Request $request): void
    {
        $slides = Content::slides();
        echo View::render('pages/home', [
            'slides'    => $slides,
            'tipos'     => Content::tripTypes(),
            'paises'    => Countries::byContinent(),
            'posts'     => Content::latestPosts(3),
        ], [
            'title'       => Settings::get('seo.home.title', 'Guanaco Viajes'),
            'description' => Settings::get('seo.home.description'),
            'overlay'     => count($slides) > 0,
            'og_image'    => $slides[0]['foto'] ?? '/assets/img/seed/foto-5.jpg',
        ]);
    }
}
