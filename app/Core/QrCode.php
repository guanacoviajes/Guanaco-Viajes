<?php
declare(strict_types=1);

namespace App\Core;

use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

/** Código QR como imagen SVG, generado en el propio servidor (nada sale a servicios externos). */
final class QrCode
{
    public static function svg(string $text, int $size = 220): ?string
    {
        try {
            $svg = (new Writer(new ImageRenderer(new RendererStyle($size, 1), new SvgImageBackEnd())))->writeString($text);
            // quita la cabecera XML para poder incrustarlo en la página
            return preg_replace('/^<\?xml[^>]*\?>\s*/', '', $svg);
        } catch (\Throwable $e) {
            error_log('QR no disponible: ' . $e->getMessage());
            return null;
        }
    }
}
