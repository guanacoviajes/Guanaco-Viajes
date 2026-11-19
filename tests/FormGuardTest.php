<?php
declare(strict_types=1);

use App\Services\FormGuard;
use PHPUnit\Framework\TestCase;

final class FormGuardTest extends TestCase
{
    public function testFreshTokenIsTooFast(): void
    {
        $now = 1_800_000_000;
        $this->assertSame('rapido', FormGuard::checkTime(FormGuard::timeToken($now), $now + 1));
    }

    public function testTokenIsOkAfterMinimumTime(): void
    {
        $now = 1_800_000_000;
        $this->assertSame('ok', FormGuard::checkTime(FormGuard::timeToken($now), $now + 5));
    }

    public function testBoundaryAtMinimumSeconds(): void
    {
        $now = 1_800_000_000;
        $t = FormGuard::timeToken($now);
        $this->assertSame('rapido', FormGuard::checkTime($t, $now + FormGuard::MIN_SECONDS - 1));
        $this->assertSame('ok', FormGuard::checkTime($t, $now + FormGuard::MIN_SECONDS));
    }

    public function testExpiredToken(): void
    {
        $now = 1_800_000_000;
        $this->assertSame('caducado', FormGuard::checkTime(FormGuard::timeToken($now), $now + FormGuard::MAX_SECONDS + 1));
    }

    public function testTamperedOrMalformedTokensAreInvalid(): void
    {
        $now = 1_800_000_000;
        $t = FormGuard::timeToken($now);
        [$ts, $sig] = explode('.', $t);
        foreach ([null, '', 'x', 'a.b', $ts . '.' . str_repeat('0', 64), ($ts - 100) . '.' . $sig, $ts . '.' . strtoupper($sig), $t . 'x'] as $bad) {
            $this->assertSame('invalido', FormGuard::checkTime($bad, $now + 10), (string) $bad);
        }
    }

    public function testIpHashIsStableAndDoesNotRevealIp(): void
    {
        $h = FormGuard::ipHash('203.0.113.7');
        $this->assertSame($h, FormGuard::ipHash('203.0.113.7'));
        $this->assertNotSame($h, FormGuard::ipHash('203.0.113.8'));
        $this->assertSame(64, strlen($h));
        $this->assertMatchesRegularExpression('/^[a-f0-9]{64}$/', $h);
    }
}
