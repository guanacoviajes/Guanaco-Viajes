<?php
declare(strict_types=1);

namespace App\Admin;

use App\Core\Request;

/** Pantallas de entrada que agrupan varias secciones relacionadas. */
final class HubController extends AdminController
{
    public function inicio(Request $r): void
    {
        $this->render('hub', [
            'title' => 'Inicio y carrusel',
            'intro' => 'Todo lo que se ve en la portada de la web.',
            'cards' => [
                ['/admin/inicio/carrusel', 'Carrusel', 'Las fotos grandes de la portada, sus titulares, botones y el color del menú sobre cada foto.'],
                ['/admin/inicio/tipos', 'Tipos de viaje', 'Texto y foto de «Viajes a medida» y «Viajes en grupo».'],
                ['/admin/ajustes/inicio', 'Textos de la portada', 'Títulos de cada bloque, los tres pasos de «Cómo funciona» y tu frase de «Quién está detrás».'],
            ],
        ], 'Inicio y carrusel', 'inicio');
    }

    public function formulario(Request $r): void
    {
        $this->render('hub', [
            'title' => 'Formulario de presupuesto',
            'intro' => 'Lo que se puede cambiar del formulario. Los cinco pasos y sus reglas son fijos.',
            'cards' => [
                ['/admin/formulario/opciones', 'Opciones de «¿Qué te apetece?»', 'Naturaleza, Cultura, Gastronomía… Añade, quita o reordena las opciones del paso 4.'],
                ['/admin/ajustes/formulario', 'Pregunta del primer paso', 'El texto de «¿A dónde te gustaría ir?» y su ayuda.'],
                ['/admin/solicitudes', 'Solicitudes recibidas', 'Lo que ha enviado la gente, con su estado y tus notas.'],
            ],
        ], 'Formulario', 'formulario');
    }
}
