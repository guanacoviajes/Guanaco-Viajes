<?php
declare(strict_types=1);

namespace App\Core;

use PHPMailer\PHPMailer\Exception as MailException;
use PHPMailer\PHPMailer\PHPMailer;

/** Envío de correo por SMTP (PHPMailer). Nunca lanza excepciones hacia fuera: devuelve true/false. */
final class Mailer
{
    public static function send(string $to, string $subject, string $text, string $html, ?string $replyTo = null, ?string $replyName = null): bool
    {
        $host = (string) Config::get('mail.host', '');
        if ($host === '') {
            self::log('SMTP sin configurar: no se envía «' . $subject . '»');
            return false;
        }
        try {
            $m = new PHPMailer(true);
            $m->isSMTP();
            $m->Host = $host;
            $m->Port = (int) Config::get('mail.port', 465);
            $secure = (string) Config::get('mail.secure', 'ssl');
            $m->SMTPSecure = $secure === 'ssl' ? PHPMailer::ENCRYPTION_SMTPS : ($secure === 'tls' ? PHPMailer::ENCRYPTION_STARTTLS : '');
            $m->SMTPAutoTLS = $secure !== '';
            $user = (string) Config::get('mail.user', '');
            $m->SMTPAuth = $user !== '';
            if ($user !== '') {
                $m->Username = $user;
                $m->Password = (string) Config::get('mail.pass', '');
            }
            $m->Timeout = 10;
            $m->CharSet = PHPMailer::CHARSET_UTF8;
            $m->setFrom((string) Config::get('mail.from'), (string) Config::get('mail.from_name', 'Guanaco Viajes'));
            $m->addAddress($to);
            if ($replyTo !== null && filter_var($replyTo, FILTER_VALIDATE_EMAIL)) {
                $m->addReplyTo($replyTo, $replyName ?? '');
            }
            $m->Subject = $subject;
            $m->isHTML(true);
            $m->Body = $html;
            $m->AltBody = $text;
            $m->send();
            return true;
        } catch (MailException | \Throwable $e) {
            // Se registra el motivo, sin contenido del mensaje ni datos personales del cliente.
            self::log('Fallo al enviar a ' . substr(strrchr($to, '@') ?: '@?', 1) . ': ' . $e->getMessage());
            return false;
        }
    }

    private static function log(string $line): void
    {
        @file_put_contents(ROOT . '/storage/logs/mail.log', '[' . date('Y-m-d H:i:s') . '] ' . $line . "\n", FILE_APPEND | LOCK_EX);
    }
}
