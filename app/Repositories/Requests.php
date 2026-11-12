<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;

final class Requests
{
    /** Número de solicitudes de esa IP en los últimos $minutes minutos. */
    public static function recentFrom(string $ipHash, int $minutes): int
    {
        return (int) Database::value(
            'SELECT COUNT(*) FROM requests WHERE ip_hash = ? AND created_at >= (NOW() - INTERVAL ' . (int) $minutes . ' MINUTE)',
            [$ipHash]
        );
    }

    /**
     * Guarda una solicitud y devuelve su id.
     * @param array<string,mixed> $d datos ya validados
     */
    public static function insert(string $origen, array $d, string $consentTexto, string $ipHash): int
    {
        $json = static fn($v): ?string => ($v === null || $v === []) ? null : json_encode($v, JSON_UNESCAPED_UNICODE);
        Database::run(
            'INSERT INTO requests (origen, nombre, email, telefono, canal, destinos, meses, flexible, duracion, adultos, menores,
                                   edades_menores, intereses, mensaje, consent_texto, consent_at, ip_hash)
             VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,NOW(),?)',
            [
                $origen, $d['nombre'], $d['email'], $d['telefono'] ?? '', $d['canal'] ?? 'correo',
                $json($d['destinos'] ?? null), $json($d['meses'] ?? null), !empty($d['flexible']) ? 1 : 0, $d['duracion'] ?? '',
                $d['adultos'] ?? null, $d['menores'] ?? null, $d['edades_menores'] ?? '',
                $json($d['intereses'] ?? null), ($d['mensaje'] ?? '') === '' ? null : $d['mensaje'],
                $consentTexto, $ipHash,
            ]
        );
        return (int) Database::lastId();
    }

    public static function markSent(int $id, bool $aviso, bool $acuse): void
    {
        Database::run('UPDATE requests SET aviso_enviado = ?, acuse_enviado = ? WHERE id = ?', [$aviso ? 1 : 0, $acuse ? 1 : 0, $id]);
    }
}
