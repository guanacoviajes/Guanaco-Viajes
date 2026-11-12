<?php
declare(strict_types=1);

namespace App\Admin;

use App\Core\Csrf;
use App\Core\Database;
use App\Core\Request;
use App\Services\ImageService;

/** Biblioteca de imágenes subidas: dónde se usa cada una y borrado de las que sobran. */
final class MediaController extends AdminController
{
    private const PATTERN = '#^uploads/\d{4}/\d{2}/[a-f0-9]{24}\.jpg$#';

    /** @return array<string,list<string>> ruta => lugares donde se usa */
    private function usage(): array
    {
        $use = [];
        $add = static function (?string $path, string $where) use (&$use): void {
            if ($path !== null && $path !== '') {
                $use[ltrim($path, '/')][] = $where;
            }
        };
        foreach (Database::all('SELECT nombre, foto FROM countries') as $r) { $add($r['foto'], 'Destino: ' . $r['nombre']); }
        foreach (Database::all('SELECT c.nombre, p.archivo FROM country_photos p JOIN countries c ON c.id = p.country_id') as $r) { $add($r['archivo'], 'Galería de ' . $r['nombre']); }
        foreach (Database::all('SELECT titular, foto FROM carousel_slides') as $r) { $add($r['foto'], 'Carrusel: ' . $r['titular']); }
        foreach (Database::all('SELECT titulo, foto FROM trip_types') as $r) { $add($r['foto'], 'Tipo de viaje: ' . $r['titulo']); }
        foreach (Database::all('SELECT titulo, portada FROM posts') as $r) { $add($r['portada'], 'Artículo: ' . $r['titulo']); }
        foreach (Database::all('SELECT titulo, foto FROM departures') as $r) { $add($r['foto'], 'Salida: ' . $r['titulo']); }
        foreach (Database::all("SELECT clave, valor FROM settings WHERE valor LIKE 'uploads/%'") as $r) { $add($r['valor'], 'Ajuste: ' . $r['clave']); }
        return $use;
    }

    public function index(Request $r): void
    {
        $use = $this->usage();
        $files = [];
        foreach (glob(PUBLIC_PATH . '/uploads/*/*/*.jpg') ?: [] as $f) {
            if (str_contains($f, '-' . ImageService::SMALL . '.jpg')) {
                continue;
            }
            $rel = ltrim(substr($f, strlen(PUBLIC_PATH)), '/');
            if (!preg_match(self::PATTERN, $rel)) {
                continue;
            }
            $files[] = ['path' => $rel, 'time' => (int) filemtime($f), 'size' => (int) filesize($f), 'use' => $use[$rel] ?? []];
        }
        usort($files, static fn($a, $b) => $b['time'] <=> $a['time']);
        $this->render('media', ['files' => $files, 'csrf' => Csrf::field()], 'Imágenes', 'imagenes');
    }

    public function delete(Request $r): void
    {
        $this->csrf($r, '/admin/imagenes');
        $path = $r->post('path');
        if (!preg_match(self::PATTERN, $path)) {
            $this->flash('error', 'Imagen no válida.');
            $this->redirect('/admin/imagenes');
        }
        if (isset($this->usage()[$path])) {
            $this->flash('error', 'Esa imagen se está usando en la web y no se puede borrar.');
            $this->redirect('/admin/imagenes');
        }
        foreach (ImageService::files($path) as $f) {
            if (is_file($f)) {
                @unlink($f);
            }
        }
        $this->flash('ok', 'Imagen borrada.');
        $this->redirect('/admin/imagenes');
    }
}
