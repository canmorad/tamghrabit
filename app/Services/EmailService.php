<?php

namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class EmailService
{
    protected $mailer;

    public function __construct()
    {
        $this->mailer = new PHPMailer(true);
        $this->setupConfig();
    }

    protected function setupConfig()
    {
        $config = require __DIR__ . '/../Helpers/config.php';
        $mailConfig = $config['mail'];

        $this->mailer->isSMTP();
        $this->mailer->Host = $mailConfig['host'];
        $this->mailer->SMTPAuth = true;
        $this->mailer->Username = $mailConfig['user'];
        $this->mailer->Password = $mailConfig['pass'];
        $this->mailer->Port = $mailConfig['port'];
        $this->mailer->setFrom($mailConfig['user'], $mailConfig['from_name']);
        $this->mailer->isHTML(true);
        $this->mailer->CharSet = 'UTF-8';
    }

    public function send($toEmail, $toName, $subject, $messageBody, $replyToEmail = null)
    {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->clearAttachments();
            $this->mailer->clearReplyTos();

            $this->mailer->addAddress($toEmail, $toName);

            if ($replyToEmail) {
                $this->mailer->addReplyTo($replyToEmail);
            }

            $this->mailer->Subject = $subject;

            $logoPath = $_SERVER['DOCUMENT_ROOT'] . '/Tamghrabit/public/images/logo.png';
            if (file_exists($logoPath)) {
                $this->mailer->addEmbeddedImage($logoPath, 'app_logo');
            }

            $fullHtmlBody = "
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: auto; border: 1px solid #eee; border-radius: 8px; overflow: hidden;'>
                <div style='text-align: center; padding: 20px; background-color: #f8f9fa;'>
                    <img src='cid:app_logo' alt='Logo' style='max-width: 150px;'>
                </div>
                <div style='padding: 30px; line-height: 1.6; color: #333;'>
                    $messageBody
                </div>
                <div style='text-align: center; padding: 15px; background-color: #f8f9fa; font-size: 12px; color: #777;'>
                    &copy; " . date('Y') . " Tamghrabit - Système de Support
                </div>
            </div>
            ";

            $this->mailer->Body = $fullHtmlBody;
            return $this->mailer->send();
        } catch (Exception $e) {
            error_log("Mail Error: " . $this->mailer->ErrorInfo);
            return false;
        }
    }
}