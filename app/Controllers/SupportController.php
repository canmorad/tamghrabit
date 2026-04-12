<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Services\EmailService;
use App\Helpers\Session;

class SupportController extends Controller
{

    public function index()
    {
        return $this->view('admin/support', [
            'current_uri' => 'support'
        ]);
    }

    public function send()
    {
        $subject = $_POST['subject'] ?? 'Sans sujet';
        $message = $_POST['message'] ?? '';
        $user = Session::get('user'); 

        $emailService = new EmailService();

        $adminEmail = 'benaissamorad559@gmail.com';

        $mailSent = $emailService->send(
            $adminEmail,
            'Administrateur',
            "Support: " . $subject,
            "<h3>Nouveau message de support</h3>
            <p><strong>De:</strong> {$user->getPrenom()} {$user->getNom()}</p>
            <p><strong>Email:</strong> {$user->getEmail()}</p>
            <hr>
            <p><strong>Message:</strong><br>" . nl2br(htmlspecialchars($message)) . "</p>",
            $user->getEmail() 
        );

        if ($mailSent) {
            Session::flush('success', 'Votre message a été bien envoyé.');
        } else {
            Session::flush('error', 'Erreur lors de l\'envoi.');
        }

        $this->index();
    }
}