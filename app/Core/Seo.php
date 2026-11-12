<?php
declare(strict_types=1);

namespace App\Core;

use App\Repositories\Settings;

/** Utilidades de SEO: textos para buscadores, URLs absolutas y datos estructurados (JSON-LD). */
final class Seo
{
    public static function base(): string
    {
        return rtrim((string) Config::get('app.url', ''), '/');
    }

    public static function abs(string $path): string
    {
        return preg_match('~^https?://~i', $path) ? $path : self::base() . '/' . ltrim($path, '/');
    }

    /** Recorta en el límite de una palabra, sin cortar frases a mitad. */
    public static function clip(string $text, int $max): string
    {
        $text = trim((string) preg_replace('/\s+/u', ' ', strip_tags($text)));
        if (mb_strlen($text) <= $max) {
            return $text;
        }
        $cut = mb_substr($text, 0, $max - 1);
        $sp = mb_strrpos($cut, ' ');
        if ($sp !== false && $sp > $max * 0.6) {
            $cut = mb_substr($cut, 0, $sp);
        }
        return rtrim($cut, " ,.;:–-") . '…';
    }

    /** Título + descripción editables desde el panel (clave seo.<pagina>.title / .description). */
    public static function page(string $key, string $title, string $description): array
    {
        return [
            'title'       => Settings::get("seo.$key.title", $title),
            'description' => Settings::get("seo.$key.description", $description),
        ];
    }

    public static function json(array $data): string
    {
        return json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
    }

    /** @return list<array<string,mixed>> nodos comunes a todas las páginas */
    public static function siteNodes(): array
    {
        $base = self::base();
        $same = [];
        foreach (['social.instagram', 'social.facebook', 'social.x', 'social.tiktok'] as $k) {
            $u = Settings::get($k);
            if (preg_match('~^https://~i', $u)) {
                $same[] = $u;
            }
        }
        $org = [
            '@type'       => 'TravelAgency',
            '@id'         => $base . '/#organizacion',
            'name'        => 'Guanaco Viajes',
            'url'         => $base . '/',
            'logo'        => ['@type' => 'ImageObject', 'url' => $base . '/assets/img/logo/guanaco-completo-claro.svg'],
            'description' => Settings::get('seo.home.description', 'Diseñamos viajes a medida y en grupo por Sudamérica y Europa.'),
            'founder'     => ['@type' => 'Person', 'name' => 'Daniel Conti', 'url' => $base . '/quienes-somos'],
            'knowsAbout'  => ['Viajes a medida', 'Viajes en grupo', 'Viajes por Sudamérica', 'Viajes por Europa'],
            'availableLanguage' => 'es',
        ];
        if (($v = Settings::get('site.email')) !== '') {
            $org['email'] = $v;
        }
        if (($v = Settings::get('site.telefono')) !== '') {
            $org['telephone'] = $v;
        }
        if ($same) {
            $org['sameAs'] = $same;
        }
        return [
            $org,
            [
                '@type'      => 'WebSite',
                '@id'        => $base . '/#web',
                'url'        => $base . '/',
                'name'       => 'Guanaco Viajes',
                'inLanguage' => 'es-ES',
                'publisher'  => ['@id' => $base . '/#organizacion'],
            ],
        ];
    }

    /** Dirección del feed del blog. */
    public static function feedUrl(): string
    {
        return self::base() . '/blog/feed.xml';
    }
}
