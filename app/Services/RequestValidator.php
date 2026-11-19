<?php
declare(strict_types=1);

namespace App\Services;

/**
 * Validación de los formularios. Sin acceso a base de datos ni a PHP global:
 * recibe los datos y el contexto (países, intereses, meses) y devuelve errores y valores limpios.
 */
final class RequestValidator
{
    public const UNKNOWN = 'aun-no-lo-se';

    /**
     * @param array<string,mixed>   $in  datos recibidos (normalmente $_POST)
     * @param array<string,string>  $countries  slug => continente de los países publicados
     * @param array<int,string>     $interests  id => nombre de las opciones activas
     * @param array<string,string>  $months     'YYYY-MM' => etiqueta
     * @return array{errors: array<string,string>, data: array<string,mixed>}
     */
    public static function budget(array $in, array $countries, array $interests, array $months): array
    {
        $err = [];
        $d = [];

        // ---- 1 · destino
        $dest = self::strList($in['destinos'] ?? []);
        $dest = array_values(array_unique(array_filter($dest, static fn($s) => isset($countries[$s]) || $s === self::UNKNOWN)));
        if (in_array(self::UNKNOWN, $dest, true)) {
            $dest = [self::UNKNOWN];
        }
        if (!$dest) {
            $err['destinos'] = 'Elige al menos una opción para continuar.';
        }
        $d['destinos'] = $dest;
        $sur = false;
        foreach ($dest as $s) {
            if (($countries[$s] ?? '') === 'sudamerica') {
                $sur = true;
            }
        }

        // ---- 2 · fechas
        $meses = array_values(array_unique(array_filter(self::strList($in['meses'] ?? []), static fn($m) => isset($months[$m]))));
        sort($meses);
        $flex = !empty($in['flexible']) && $in['flexible'] !== '0';
        if (!$meses && !$flex) {
            $err['fechas'] = 'Elige un mes o marca que eres flexible.';
        }
        $dur = self::str($in['duracion'] ?? '');
        if ($dur !== '' && !isset(FormOptions::DURACIONES[$dur])) {
            $dur = '';
        }
        if ($dur === 'finde' && $sur) {
            // Con algún destino de Sudamérica no es viable un fin de semana largo.
            $err['duracion'] = 'Para Sudamérica hace falta algo más de tiempo. Elige otra duración.';
            $dur = '';
        }
        $d['meses'] = $meses;
        $d['flexible'] = $flex;
        $d['duracion'] = $dur;

        // ---- 3 · viajeros
        $ad = self::intIn($in['adultos'] ?? '2', 1, 20);
        $me = self::intIn($in['menores'] ?? '0', 0, 10);
        if ($ad === null) {
            $err['viajeros'] = 'Indica entre 1 y 20 adultos.';
            $ad = 2;
        }
        if ($me === null) {
            $err['viajeros'] = 'Indica entre 0 y 10 menores.';
            $me = 0;
        }
        $edades = self::oneLine($in['edades_menores'] ?? '');
        if ($me === 0) {
            $edades = '';
        }
        if (mb_strlen($edades) > 120) {
            $err['edades'] = 'Las edades son demasiado largas (máximo 120 caracteres).';
            $edades = mb_substr($edades, 0, 120);
        }
        $d['adultos'] = $ad;
        $d['menores'] = $me;
        $d['edades_menores'] = $edades;

        // ---- 4 · tu idea
        $ints = [];
        foreach (self::strList($in['intereses'] ?? []) as $id) {
            if (ctype_digit($id) && isset($interests[(int) $id])) {
                $ints[(int) $id] = $interests[(int) $id];
            }
        }
        $d['intereses'] = array_values($ints);
        $msg = self::multiLine($in['mensaje'] ?? '');
        if (mb_strlen($msg) > 2000) {
            $err['mensaje'] = 'El mensaje es demasiado largo (máximo 2000 caracteres).';
            $msg = mb_substr($msg, 0, 2000);
        }
        $d['mensaje'] = $msg;

        // ---- 5 · contacto
        self::contact($in, $err, $d);
        $canal = self::str($in['canal'] ?? 'correo');
        if (!isset(FormOptions::CANALES[$canal])) {
            $canal = 'correo';
        }
        if (in_array($canal, ['whatsapp', 'llamada'], true) && $d['telefono'] === '' && !isset($err['telefono'])) {
            $err['telefono'] = 'Para escribirte por WhatsApp o llamarte necesitamos un teléfono.';
        }
        $d['canal'] = $canal;
        self::consent($in, $err);

        return ['errors' => $err, 'data' => $d];
    }

    /**
     * @param array<string,mixed> $in
     * @return array{errors: array<string,string>, data: array<string,mixed>}
     */
    public static function message(array $in): array
    {
        $err = [];
        $d = [];
        self::contact($in, $err, $d);
        $msg = self::multiLine($in['mensaje'] ?? '');
        if (mb_strlen($msg) < 5) {
            $err['mensaje'] = 'Escribe tu mensaje.';
        } elseif (mb_strlen($msg) > 2000) {
            $err['mensaje'] = 'El mensaje es demasiado largo (máximo 2000 caracteres).';
            $msg = mb_substr($msg, 0, 2000);
        }
        $d['mensaje'] = $msg;
        $d['canal'] = 'correo';
        self::consent($in, $err);
        return ['errors' => $err, 'data' => $d];
    }

    /** Nombre, correo y teléfono (comunes a los dos formularios). */
    private static function contact(array $in, array &$err, array &$d): void
    {
        $nombre = self::oneLine($in['nombre'] ?? '');
        if (mb_strlen($nombre) < 2) {
            $err['nombre'] = 'Escribe tu nombre.';
        } elseif (mb_strlen($nombre) > 100) {
            $err['nombre'] = 'El nombre es demasiado largo.';
            $nombre = mb_substr($nombre, 0, 100);
        }
        $d['nombre'] = $nombre;

        $email = self::str($in['email'] ?? '');
        if ($email === '' || mb_strlen($email) > 150 || preg_match('/[\r\n,;<>\s]/', $email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $err['email'] = 'Revisa el correo: parece que falta algo.';
        }
        $d['email'] = $email;

        $tel = self::oneLine($in['telefono'] ?? '');
        if ($tel !== '' && !preg_match('/^[0-9 +().-]{6,20}$/', $tel)) {
            $err['telefono'] = 'Revisa el teléfono: usa solo números, espacios y el prefijo con +.';
        }
        $d['telefono'] = $tel;
    }

    private static function consent(array $in, array &$err): void
    {
        if (empty($in['consentimiento']) || $in['consentimiento'] === '0') {
            $err['consentimiento'] = 'Necesitamos tu consentimiento para poder responderte.';
        }
    }

    // ---------- utilidades de limpieza ----------

    private static function str(mixed $v): string
    {
        return is_string($v) ? trim($v) : '';
    }

    /** Una sola línea: sin saltos ni caracteres de control. */
    private static function oneLine(mixed $v): string
    {
        $s = self::str($v);
        $s = preg_replace('/[\x00-\x1F\x7F]+/u', ' ', $s) ?? '';
        return trim(preg_replace('/\s{2,}/u', ' ', $s) ?? '');
    }

    /** Varias líneas: se conservan los saltos, se quitan los demás caracteres de control. */
    private static function multiLine(mixed $v): string
    {
        $s = str_replace(["\r\n", "\r"], "\n", self::str($v));
        $s = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/u', '', $s) ?? '';
        return trim(preg_replace("/\n{3,}/", "\n\n", $s) ?? '');
    }

    /** @return list<string> */
    private static function strList(mixed $v): array
    {
        if (!is_array($v)) {
            return [];
        }
        $out = [];
        foreach ($v as $x) {
            if (is_string($x) && $x !== '') {
                $out[] = trim($x);
            }
        }
        return $out;
    }

    private static function intIn(mixed $v, int $min, int $max): ?int
    {
        if (!is_string($v) && !is_int($v)) {
            return null;
        }
        $s = trim((string) $v);
        if (!preg_match('/^\d{1,3}$/', $s)) {
            return null;
        }
        $n = (int) $s;
        return ($n >= $min && $n <= $max) ? $n : null;
    }
}
