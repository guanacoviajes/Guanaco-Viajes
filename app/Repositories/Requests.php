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

    // ---------- panel de administración ----------

    public const ESTADOS = ['nueva' => 'Nueva', 'leida' => 'Leída', 'respondida' => 'Respondida', 'archivada' => 'Archivada'];

    public static function countNew(): int
    {
        return (int) Database::value("SELECT COUNT(*) FROM requests WHERE estado = 'nueva'");
    }

    /** @param array{estado?:string,origen?:string,q?:string} $f */
    private static function where(array $f): array
    {
        $w = [];
        $a = [];
        if (!empty($f['estado']) && isset(self::ESTADOS[$f['estado']])) {
            $w[] = 'estado = ?';
            $a[] = $f['estado'];
        }
        if (!empty($f['origen']) && in_array($f['origen'], ['presupuesto', 'contacto'], true)) {
            $w[] = 'origen = ?';
            $a[] = $f['origen'];
        }
        if (!empty($f['q'])) {
            $like = '%' . addcslashes(mb_substr($f['q'], 0, 80), '%_\\') . '%';
            $w[] = '(nombre LIKE ? OR email LIKE ? OR mensaje LIKE ? OR destinos LIKE ?)';
            array_push($a, $like, $like, $like, $like);
        }
        return [$w ? 'WHERE ' . implode(' AND ', $w) : '', $a];
    }

    /** @param array<string,string> $f @return array{rows: list<array>, total: int} */
    public static function search(array $f, int $page, int $per): array
    {
        [$where, $args] = self::where($f);
        $total = (int) Database::value("SELECT COUNT(*) FROM requests $where", $args);
        $rows = Database::all(
            "SELECT * FROM requests $where ORDER BY created_at DESC, id DESC LIMIT " . (int) $per . ' OFFSET ' . (int) max(0, ($page - 1) * $per),
            $args
        );
        return ['rows' => $rows, 'total' => $total];
    }

    /** @param array<string,string> $f @return list<array> */
    public static function export(array $f): array
    {
        [$where, $args] = self::where($f);
        return Database::all("SELECT * FROM requests $where ORDER BY created_at DESC, id DESC LIMIT 5000", $args);
    }

    public static function find(int $id): ?array
    {
        return Database::one('SELECT * FROM requests WHERE id = ?', [$id]);
    }

    public static function setEstado(int $id, string $estado): void
    {
        if (isset(self::ESTADOS[$estado])) {
            Database::run('UPDATE requests SET estado = ? WHERE id = ?', [$estado, $id]);
        }
    }

    public static function setNotas(int $id, string $notas): void
    {
        Database::run('UPDATE requests SET notas = ? WHERE id = ?', [$notas === '' ? null : mb_substr($notas, 0, 4000), $id]);
    }

    public static function delete(int $id): void
    {
        Database::run('DELETE FROM requests WHERE id = ?', [$id]);
    }

    /** Datos de una fila en la forma que usan los correos. @return array<string,mixed> */
    public static function toData(array $r): array
    {
        $dec = static fn($v): array => $v ? (json_decode((string) $v, true) ?: []) : [];
        return [
            'nombre' => $r['nombre'], 'email' => $r['email'], 'telefono' => $r['telefono'], 'canal' => $r['canal'],
            'destinos' => $dec($r['destinos']), 'meses' => $dec($r['meses']), 'flexible' => (bool) $r['flexible'],
            'duracion' => $r['duracion'], 'adultos' => (int) $r['adultos'], 'menores' => (int) $r['menores'],
            'edades_menores' => $r['edades_menores'], 'intereses' => $dec($r['intereses']), 'mensaje' => (string) $r['mensaje'],
        ];
    }

    public static function pendingNotices(): int
    {
        return (int) Database::value('SELECT COUNT(*) FROM requests WHERE aviso_enviado = 0');
    }

    public static function countSince(int $days): int
    {
        return (int) Database::value('SELECT COUNT(*) FROM requests WHERE created_at >= (NOW() - INTERVAL ' . (int) $days . ' DAY)');
    }
}
