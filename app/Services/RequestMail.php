<?php
declare(strict_types=1);

namespace App\Services;

use App\Core\Config;
use App\Core\Mailer;

/** Correos de una solicitud: aviso para el equipo y acuse de recibo para quien la envía. */
final class RequestMail
{
    /**
     * Resumen legible de una solicitud.
     * @param array<string,mixed>  $d
     * @param array<string,string> $names slug => nombre del país
     * @return array<string,string> etiqueta => valor
     */
    public static function summary(string $origen, array $d, array $names): array
    {
        $rows = [];
        if ($origen === 'presupuesto') {
            $dest = array_map(static fn($s) => $s === 'aun-no-lo-se' ? 'Aún no lo sé' : ($names[$s] ?? $s), $d['destinos'] ?? []);
            $rows['Destino'] = implode(', ', $dest);
            $meses = array_map([FormOptions::class, 'mesLabel'], $d['meses'] ?? []);
            $f = $meses ? implode(', ', $meses) : '';
            if (!empty($d['flexible'])) {
                $f .= ($f !== '' ? ' · ' : '') . 'Flexible con las fechas';
            }
            $rows['Fechas'] = $f;
            if (($d['duracion'] ?? '') !== '') {
                $rows['Duración'] = FormOptions::DURACIONES[$d['duracion']] ?? '';
            }
            $v = ($d['adultos'] ?? 0) . ' adulto' . (($d['adultos'] ?? 0) > 1 ? 's' : '');
            if (($d['menores'] ?? 0) > 0) {
                $v .= ' y ' . $d['menores'] . ' menor' . ($d['menores'] > 1 ? 'es' : '');
                if (($d['edades_menores'] ?? '') !== '') {
                    $v .= ' (' . $d['edades_menores'] . ')';
                }
            }
            $rows['Viajeros'] = $v;
            if (!empty($d['intereses'])) {
                $rows['Le apetece'] = implode(', ', $d['intereses']);
            }
        }
        if (($d['mensaje'] ?? '') !== '') {
            $rows['Mensaje'] = $d['mensaje'];
        }
        return array_filter($rows, static fn($v) => $v !== '');
    }

    /** @return array<string,string> */
    private static function contactRows(array $d): array
    {
        $rows = ['Nombre' => $d['nombre'], 'Correo' => $d['email']];
        if (($d['telefono'] ?? '') !== '') {
            $rows['Teléfono'] = $d['telefono'];
        }
        $rows['Prefiere'] = FormOptions::CANALES[$d['canal'] ?? 'correo'] ?? 'Correo';
        return $rows;
    }

    /**
     * Asunto del aviso interno: «Nueva solicitud de presupuesto · Ana Pérez · Chile, Perú».
     * @param array<string,mixed>  $d
     * @param array<string,string> $names slug => nombre del país
     */
    public static function subject(string $origen, array $d, array $names): string
    {
        if ($origen !== 'presupuesto') {
            return 'Nuevo mensaje de contacto · ' . $d['nombre'];
        }
        $dest = array_map(
            static fn($s) => $s === 'aun-no-lo-se' ? 'destino por decidir' : ($names[$s] ?? $s),
            $d['destinos'] ?? []
        );
        $subject = 'Nueva solicitud de presupuesto · ' . $d['nombre'] . ($dest ? ' · ' . implode(', ', $dest) : '');
        return mb_strlen($subject) > 120 ? mb_substr($subject, 0, 117) . '…' : $subject;
    }

    public static function notify(int $id, string $origen, array $d, array $names): bool
    {
        $to = (string) Config::get('mail.notify_to', Config::get('mail.from'));
        $subject = self::subject($origen, $d, $names);
        $rows = self::contactRows($d) + self::summary($origen, $d, $names);
        $head = $origen === 'presupuesto' ? 'Nueva solicitud de presupuesto' : 'Nuevo mensaje desde la página de contacto';
        $text = $head . " (n.º $id)\n\n" . self::asText($rows) . "\nResponde a este correo para escribir directamente a " . $d['nombre'] . ".\n";
        $html = self::wrap($head . ' <span style="opacity:.6">· n.º ' . $id . '</span>', self::asHtml($rows),
            'Puedes responder a este correo para escribir directamente a ' . e($d['nombre']) . '.');
        return Mailer::send($to, $subject, $text, $html, $d['email'], $d['nombre']);
    }

    public static function ack(string $origen, array $d, array $names): bool
    {
        $first = explode(' ', $d['nombre'])[0];
        $isB = $origen === 'presupuesto';
        $subject = $isB ? 'Hemos recibido tu idea de viaje' : 'Hemos recibido tu mensaje';
        $intro = $isB
            ? "Gracias, $first. Ya tenemos tu idea y empezaremos a darle forma. Te escribiremos por "
                . mb_strtolower(FormOptions::CANALES[$d['canal']] ?? 'correo') . '.'
            : "Gracias, $first. Hemos recibido tu mensaje y te responderemos en cuanto podamos.";
        $rows = self::summary($origen, $d, $names);
        $legal = 'Responsable del tratamiento: NADIDU SL (registro de agencia de viajes C.IAN-118115-2). Si no has enviado tú este formulario, '
            . 'puedes ignorar este correo.';
        $text = $intro . "\n\n" . ($rows ? "Esto es lo que nos has contado:\n\n" . self::asText($rows) . "\n" : '') . "—\nGuanaco Viajes\n\n" . $legal . "\n";
        $html = self::wrap(e($intro), $rows ? self::asHtml($rows) : '', e($legal));
        return Mailer::send($d['email'], $subject, $text, $html);
    }

    /** @param array<string,string> $rows */
    private static function asText(array $rows): string
    {
        $o = '';
        foreach ($rows as $k => $v) {
            $o .= $k . ': ' . str_replace("\n", "\n  ", $v) . "\n";
        }
        return $o;
    }

    /** @param array<string,string> $rows */
    private static function asHtml(array $rows): string
    {
        $o = '<table role="presentation" cellpadding="0" cellspacing="0" style="width:100%;border-collapse:collapse">';
        foreach ($rows as $k => $v) {
            $o .= '<tr><td style="padding:10px 16px 10px 0;border-top:1px solid #d9d3c6;font:600 13px Arial,sans-serif;color:#1F2D28;white-space:nowrap;vertical-align:top;opacity:.7">'
                . e($k) . '</td><td style="padding:10px 0;border-top:1px solid #d9d3c6;font:15px/1.5 Arial,sans-serif;color:#1F2D28">'
                . nl2br(e($v), false) . '</td></tr>';
        }
        return $o . '</table>';
    }

    private static function wrap(string $headHtml, string $bodyHtml, string $footHtml): string
    {
        return '<!doctype html><html lang="es"><body style="margin:0;background:#F3EEE4;padding:24px">'
            . '<div style="max-width:620px;margin:0 auto;background:#ffffff;border-radius:4px;overflow:hidden">'
            . '<div style="background:#1F2D28;color:#F3EEE4;padding:22px 28px;font:400 22px Georgia,serif">Guanaco <span style="color:#B86F50">viajes</span></div>'
            . '<div style="padding:28px">'
            . '<p style="margin:0 0 20px;font:400 20px/1.35 Georgia,serif;color:#1F2D28">' . $headHtml . '</p>' . $bodyHtml
            . '<p style="margin:26px 0 0;font:12px/1.5 Arial,sans-serif;color:#6b6f66">' . $footHtml . '</p>'
            . '</div></div></body></html>';
    }
}
