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
        $this->mailer->isSMTP();
        $this->mailer->Host = 'smtp.gmail.com';
        $this->mailer->SMTPAuth = true;
        $this->mailer->Username = 'benaissamorad559@gmail.com';
        $this->mailer->Password = 'sqnm gqoj cqlm gcqh';
        $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $this->mailer->Port = 587;
        $this->mailer->setFrom('benaissamorad559@gmail.com', 'Tamghrabit');
        $this->mailer->isHTML(true);
        $this->mailer->CharSet = 'UTF-8';
    }

    public function send($toEmail, $toName, $subject, $messageBody)
    {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->clearAttachments();

            $this->mailer->addAddress($toEmail, $toName);
            $this->mailer->Subject = $subject;

            $logoPath = $_SERVER['DOCUMENT_ROOT'] . '/Tamghrabit/public/images/logo.png';

            $this->mailer->addEmbeddedImage($logoPath, 'app_logo');

            $fullHtmlBody = "
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: auto; border: 1px solid #eee;'>
                <div style='text-align: center; padding: 20px;'>
                    <img src='cid:app_logo' alt='Logo' style='max-width: 150px;'>
                </div>
                <div style='padding: 20px;'>
                    $messageBody
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