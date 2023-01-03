<?php

namespace zFramework\Core\Facades;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

class Mail
{
    static $mail;
    static $toMail = [];
    /**
     * Initalize settings.
     */
    public static function init()
    {
        $mailConfig = Config::get('mail');
        if (!$mailConfig['sending']) throw new \Exception(_l('errors.mail.sending-is-false'));

        self::$mail = new PHPMailer;
        self::$mail->isSMTP();
        self::$mail->CharSet = 'utf-8';

        if (@$mailConfig['debug'] == true) self::$mail->SMTPDebug = SMTP::DEBUG_SERVER;

        self::$mail->Host = $mailConfig['mail'];
        self::$mail->Port = $mailConfig['port'];

        self::$mail->SMTPAuth = ($mailConfig['SMTPAuth'] ?? false);
        if (@$mailConfig['SMTPAuth'] === true) {
            self::$mail->Username = @$mailConfig['username'];
            self::$mail->Password = @$mailConfig['password'];
        }

        if (isset($mailConfig['from'])) self::$mail->setFrom($mailConfig['from'][1], $mailConfig['from'][0]);
        if (isset($mailConfig['reply'])) self::$mail->addReplyTo($mailConfig['reply'][1], $mailConfig['reply'][0]);
    }

    /**
     * Select to mail
     * @param string $toMail
     * @return self
     */
    public static function to(string $toMail): self
    {
        if (!filter_var($toMail, FILTER_VALIDATE_EMAIL)) throw new \Exception(_l('errors.mail.not-validate-mail'));
        self::$toMail[] = $toMail;
        return new self();
    }

    /**
     * Send Mail
     * @param array $data
     * @return bool
     */
    public static function send(array $data): bool
    {
        if (!isset(self::$toMail)) throw new \Exception(_l('errors.mail.must-set-a-mail'));

        self::$mail->Subject = Config::get('mail.subject') . (@$data['subject']);
        self::$mail->msgHTML(@$data['message']);
        self::$mail->AltBody = @$data['altbody'];

        foreach (self::$toMail as $mail) self::$mail->addAddress($mail);
        foreach ($data['attachements'] ?? [] as $attach) self::$mail->addAttachment($attach);

        self::$toMail = [];
        if (self::$mail->send()) return true;
        return false;
    }
}
