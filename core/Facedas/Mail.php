<?php

namespace Core\Facedas;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

class Mail
{
    public function __construct()
    {
        $mailConfig = Config::get('mail');
        if (!$mailConfig['sending']) return abort(400, _l('errors.mail-sending-is-false'));

        $this->mail = new PHPMailer;
        $this->mail->isSMTP();
        $this->mail->CharSet = 'utf-8';

        if (@$mailConfig['debug'] == true) $this->mail->SMTPDebug = SMTP::DEBUG_SERVER;

        $this->mail->Host = $mailConfig['mail'];
        $this->mail->Port = $mailConfig['port'];

        $this->mail->SMTPAuth = ($mailConfig['SMTPAuth'] ?? false);
        if (@$mailConfig['SMTPAuth'] === true) {
            $this->mail->Username = @$mailConfig['username'];
            $this->mail->Password = @$mailConfig['password'];
        }

        if (isset($mailConfig['from'])) $this->mail->setFrom($mailConfig['from'][1], $mailConfig['from'][0]);
        if (isset($mailConfig['reply'])) $this->mail->addReplyTo($mailConfig['reply'][1], $mailConfig['reply'][0]);
    }


    public function send($mail, $data)
    {
        $this->mail->addAddress($mail);
        $this->mail->Subject = @$data['subject'];
        $this->mail->msgHTML(@$data['message']);
        $this->mail->AltBody = @$data['altbody'];

        foreach ($data['attachements'] ?? [] as $attach) $this->mail->addAttachment($attach);
        if ($this->mail->send()) return true;
        return false;
    }
}
