<?php
declare(strict_types=1);

use App\Admin\Auth;
use App\Core\Crypto;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class CryptoAuthTest extends TestCase
{
    public function testEncryptDecryptRoundTrip(): void
    {
        $blob = Crypto::encrypt('JBSWY3DPEHPK3PXP');
        $this->assertNotSame('JBSWY3DPEHPK3PXP', $blob);
        $this->assertStringNotContainsString('JBSWY3DPEHPK3PXP', $blob);
        $this->assertSame('JBSWY3DPEHPK3PXP', Crypto::decrypt($blob));
    }

    public function testEncryptionIsRandomised(): void
    {
        $this->assertNotSame(Crypto::encrypt('x'), Crypto::encrypt('x'));
    }

    public function testTamperedOrInvalidBlobsAreRejected(): void
    {
        $blob = base64_decode(Crypto::encrypt('secreto'));
        $bad = $blob;
        $bad[strlen($bad) - 1] = $bad[strlen($bad) - 1] ^ "\x01";
        $this->assertNull(Crypto::decrypt(base64_encode($bad)));
        $this->assertNull(Crypto::decrypt('no-es-base64!!'));
        $this->assertNull(Crypto::decrypt(''));
        $this->assertNull(Crypto::decrypt(base64_encode('corto')));
    }

    #[DataProvider('passwords')]
    public function testPasswordPolicy(string $pw, bool $acceptable): void
    {
        $this->assertSame($acceptable, Auth::passwordProblem($pw, 'daniel@example.com') === null, $pw);
    }

    public static function passwords(): array
    {
        return [
            ['corta', false], ['abcdefghijk', false], ['una frase larga y rara 42', true],
            ['aaaaaaaaaaaaaaaa', false], ['abababababab', false], ['guanaco-viajes-2026', false],
            ['MiContraseñaSegura1', false], ['password1234567', false], ['123456789012', false],
            ['daniel-viaja-por-el-mundo', false], ['Cielo-nublado-sobre-los-Andes-7', true],
            [str_repeat('x1', 120), false],
        ];
    }

    public function testHashIsArgon2idOrBcryptAndVerifies(): void
    {
        $h = Auth::hash('una frase larga y rara 42');
        $this->assertMatchesRegularExpression('/^\$(argon2id|2y)\$/', $h);
        $this->assertTrue(password_verify('una frase larga y rara 42', $h));
        $this->assertFalse(password_verify('otra', $h));
    }

    public function testRecoveryCodesFormatAndHashes(): void
    {
        [$plain, $hashes] = Auth::newRecoveryCodes();
        $this->assertCount(8, $plain);
        $this->assertCount(8, array_unique($plain));
        foreach ($plain as $c) {
            $this->assertMatchesRegularExpression('/^[A-HJ-NP-Z2-9]{5}-[A-HJ-NP-Z2-9]{5}$/', $c);
        }
        foreach ($hashes as $h) {
            $this->assertMatchesRegularExpression('/^[a-f0-9]{64}$/', $h);
        }
    }

    public function testVerifyPasswordWithUnknownUserIsFalse(): void
    {
        $this->assertFalse(Auth::verifyPassword(null, 'lo-que-sea'));
        $this->assertTrue(Auth::verifyPassword(['password_hash' => Auth::hash('abc 123 abc 123')], 'abc 123 abc 123'));
    }
}
