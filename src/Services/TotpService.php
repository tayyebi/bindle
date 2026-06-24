<?php

namespace App\Services;

class TotpService
{
    private const SECRET_LENGTH = 20;
    private const CODE_DIGITS = 6;
    private const TIME_STEP = 30;
    private const WINDOW = 1;

    public static function generateSecret(): string
    {
        return self::base32Encode(random_bytes(self::SECRET_LENGTH));
    }

    public static function verify(string $secret, string $code): bool
    {
        $counter = (int) floor(time() / self::TIME_STEP);
        for ($i = -self::WINDOW; $i <= self::WINDOW; $i++) {
            if (self::generateCode($secret, $counter + $i) === $code) {
                return true;
            }
        }
        return false;
    }

    public static function getProvisioningUri(string $secret, string $label, string $issuer = 'بقچه'): string
    {
        $params = http_build_query([
            'secret' => $secret,
            'issuer' => $issuer,
            'algorithm' => 'SHA1',
            'digits' => self::CODE_DIGITS,
            'period' => self::TIME_STEP,
        ]);
        return 'otpauth://totp/' . rawurlencode($label) . '?' . $params;
    }

    public static function getQrCodeUrl(string $provisioningUri): string
    {
        return 'https://chart.googleapis.com/chart?chs=200x200&chld=M|0&cht=qr&chl=' . urlencode($provisioningUri);
    }

    private static function generateCode(string $secret, int $counter): string
    {
        $decoded = self::base32Decode($secret);
        $time = pack('NN', 0, $counter);
        $hash = hash_hmac('sha1', $time, $decoded, true);
        $offset = ord($hash[19]) & 0xf;
        $code = (
            ((ord($hash[$offset]) & 0x7f) << 24) |
            ((ord($hash[$offset + 1]) & 0xff) << 16) |
            ((ord($hash[$offset + 2]) & 0xff) << 8) |
            (ord($hash[$offset + 3]) & 0xff)
        ) % 1000000;
        return str_pad((string) $code, self::CODE_DIGITS, '0', STR_PAD_LEFT);
    }

    private static function base32Encode(string $data): string
    {
        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $binary = '';
        foreach (str_split($data) as $char) {
            $binary .= str_pad(decbin(ord($char)), 8, '0', STR_PAD_LEFT);
        }
        $encoded = '';
        foreach (str_split($binary, 5) as $chunk) {
            $encoded .= $alphabet[bindec(str_pad($chunk, 5, '0'))];
        }
        return $encoded;
    }

    private static function base32Decode(string $data): string
    {
        $data = strtoupper($data);
        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $binary = '';
        foreach (str_split($data) as $char) {
            $pos = strpos($alphabet, $char);
            if ($pos === false) continue;
            $binary .= str_pad(decbin($pos), 5, '0', STR_PAD_LEFT);
        }
        $decoded = '';
        foreach (str_split($binary, 8) as $chunk) {
            if (strlen($chunk) < 8) break;
            $decoded .= chr(bindec($chunk));
        }
        return $decoded;
    }
}
