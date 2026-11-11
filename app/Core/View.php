<?php
declare(strict_types=1);

namespace App\Core;

/** Plantillas PHP simples: una vista dentro del diseño general. */
final class View
{
    /**
     * @param array<string,mixed> $data  variables para la vista
     * @param array<string,mixed> $meta  title, description, robots, overlay, franja, bubble, og_image, canonical
     */
    public static function render(string $view, array $data = [], array $meta = []): string
    {
        $meta += [
            'title'       => 'Guanaco Viajes · Viajes a medida y en grupo',
            'description' => '',
            'robots'      => 'index,follow',
            'overlay'     => false,
            'franja'      => true,
            'bubble'      => true,
            'og_image'    => '/assets/img/seed/foto-5.jpg',
            'canonical'   => null,
        ];
        $content = self::partial($view, $data);
        return self::partial('layouts/main', ['content' => $content, 'meta' => $meta]);
    }

    /** @param array<string,mixed> $data */
    public static function partial(string $name, array $data = []): string
    {
        $file = ROOT . '/app/Views/' . $name . '.php';
        if (!is_file($file)) {
            throw new \RuntimeException('Vista no encontrada: ' . $name);
        }
        extract($data, EXTR_SKIP);
        ob_start();
        try {
            include $file;
        } catch (\Throwable $e) {
            ob_end_clean();
            throw $e;
        }
        return (string) ob_get_clean();
    }
}
