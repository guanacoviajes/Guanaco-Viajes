<?php
declare(strict_types=1);

namespace App\Services;

/** Opciones fijas del formulario de presupuesto. */
final class FormOptions
{
    public const DURACIONES = [
        'finde'        => 'Un fin de semana largo',
        'semana'       => 'Una semana',
        'dos-semanas'  => 'Dos semanas',
        'tres-semanas' => 'Tres semanas o más',
        'aun-no'       => 'Aún no lo sé',
    ];

    public const CANALES = [
        'correo'   => 'Correo',
        'whatsapp' => 'WhatsApp',
        'llamada'  => 'Llamada',
    ];

    private const MESES = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];

    /**
     * Los 12 meses siguientes al actual: ['2026-12' => 'Dic 2026', …].
     * @return array<string,string>
     */
    public static function meses(\DateTimeImmutable $now): array
    {
        $out = [];
        $first = $now->modify('first day of this month')->setTime(0, 0);
        for ($i = 1; $i <= 12; $i++) {
            $m = $first->modify("+$i month");
            $out[$m->format('Y-m')] = self::MESES[(int) $m->format('n') - 1] . ' ' . $m->format('Y');
        }
        return $out;
    }

    /** '2027-05' → 'May 2027' (también para meses que ya no están en la lista). */
    public static function mesLabel(string $ym): string
    {
        return preg_match('/^(\d{4})-(0[1-9]|1[0-2])$/', $ym, $m) ? self::MESES[(int) $m[2] - 1] . ' ' . $m[1] : $ym;
    }

    /** Texto exacto de consentimiento que se muestra y se guarda con cada solicitud. */
    public static function consentimiento(): string
    {
        return 'He leído y acepto la política de privacidad. Responsable del tratamiento: NADIDU SL, agencia de viajes '
            . 'con registro C.IAN-118115-2. Mis datos se usarán para preparar mi propuesta y responderme.';
    }

    public static function consentimientoContacto(): string
    {
        return 'He leído y acepto la política de privacidad. Responsable del tratamiento: NADIDU SL, agencia de viajes '
            . 'con registro C.IAN-118115-2. Mis datos se usarán para responder a mi mensaje.';
    }
}
