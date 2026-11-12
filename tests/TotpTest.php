<?php
declare(strict_types=1);

use App\Core\Totp;
use PHPUnit\Framework\TestCase;

final class TotpTest extends TestCase
{
    /** Secreto ASCII «12345678901234567890» en base32, el de los vectores de prueba del RFC 6238. */
    private const RFC_SECRET = 'GEZDGNBVGY3TQOJQGEZDGNBVGY3TQOJQ';

    public function testRfc6238Vectors(): void
    {
        foreach ([59 => '94287082', 1111111109 => '07081804', 1111111111 => '14050471', 1234567890 => '89005924', 2000000000 => '69279037'] as $t => $expected) {
            $this->assertSame($expected, Totp::code(self::RFC_SECRET, intdiv($t, 30), 8, 'sha1'), "t=$t");
        }
    }

    public function testBase32RoundTrip(): void
    {
        $raw = random_bytes(20);
        $this->assertSame($raw, Totp::base32Decode(Totp::base32Encode($raw)));
        $this->assertSame('12345678901234567890', Totp::base32Decode(self::RFC_SECRET));
        $this->assertSame('', Totp::base32Decode('1!?'));
    }

    public function testSecretsAreRandomAndWellFormed(): void
    {
        $a = Totp::newSecret();
        $this->assertMatchesRegularExpression('/^[A-Z2-7]{32}$/', $a);
        $this->assertNotSame($a, Totp::newSecret());
    }

    public function testVerifyAcceptsCurrentAndNeighbouringStepsOnly(): void
    {
        $now = 1_800_000_000;
        $step = intdiv($now, 30);
        foreach ([-1, 0, 1] as $d) {
            $this->assertSame($step + $d, Totp::verify(self::RFC_SECRET, Totp::code(self::RFC_SECRET, $step + $d), 0, $now), "d=$d");
        }
        foreach ([-2, 2, 10] as $d) {
            $this->assertNull(Totp::verify(self::RFC_SECRET, Totp::code(self::RFC_SECRET, $step + $d), 0, $now), "d=$d");
        }
    }

    public function testReplayOfAUsedStepIsRejected(): void
    {
        $now = 1_800_000_000;
        $step = intdiv($now, 30);
        $code = Totp::code(self::RFC_SECRET, $step);
        $this->assertSame($step, Totp::verify(self::RFC_SECRET, $code, 0, $now));
        $this->assertNull(Totp::verify(self::RFC_SECRET, $code, $step, $now));
        $this->assertNull(Totp::verify(self::RFC_SECRET, Totp::code(self::RFC_SECRET, $step - 1), $step, $now));
    }

    public function testMalformedInputIsRejected(): void
    {
        foreach (['', 'abcdef', '12345', '1234567', '12 34 5', "123456\n7", '１２３４５６'] as $bad) {
            $this->assertNull(Totp::verify(self::RFC_SECRET, $bad, 0, 1_800_000_000), $bad);
        }
    }

    public function testCodeWithSpacesIsAccepted(): void
    {
        $now = 1_800_000_000;
        $c = Totp::code(self::RFC_SECRET, intdiv($now, 30));
        $this->assertNotNull(Totp::verify(self::RFC_SECRET, substr($c, 0, 3) . ' ' . substr($c, 3), 0, $now));
    }

    public function testProvisioningUri(): void
    {
        $u = Totp::uri('JBSWY3DPEHPK3PXP', 'daniel@example.com');
        $this->assertStringStartsWith('otpauth://totp/Guanaco%20Viajes%3Adaniel%40example.com?secret=JBSWY3DPEHPK3PXP', $u);
        $this->assertStringContainsString('digits=6', $u);
        $this->assertStringContainsString('period=30', $u);
    }
}
