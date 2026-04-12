<?php
namespace App\Services;

use App\Repositories\RegisterRepository;
use App\Services\EmailService;
use App\Traits\DatabaseTransaction;

class RegisterService
{
    use DatabaseTransaction;

    private $registerRepository;
    private $emailService;
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
        $this->registerRepository = new RegisterRepository($conn);
        $this->emailService = new EmailService();
    }

    public function store($adherent)
    {
        try {

            $token = bin2hex(random_bytes(32));
            $adherent->setPassword(password_hash($adherent->getPassword(), PASSWORD_DEFAULT));
            $adherent->setTokenVerification($token);
            $this->beginTransaction();

            $this->registerRepository->store($adherent);

            $verificationLink = "http://localhost/Tamghrabit/verify/email?token=" . $token;
            $subject = "Activez votre compte Tamghrabit";
            $messageBody = "<h2>Bienvenue " . $adherent->getPrenom() . " !</h2><p>Veuillez activer votre compte via ce lien : <a href='$verificationLink'>Activer</a></p>";

            $this->emailService->send(
                $adherent->getEmail(),
                $adherent->getNom() . ' ' . $adherent->getPrenom(),
                $subject,
                $messageBody
            );

            $this->commit();
        } catch (\PDOException $e) {
            $this->rollBack();
            $error = (isset($e->errorInfo[2])) ? $e->errorInfo[2] : $e->getMessage();
            throw new \Exception($error);
        } catch (\Exception $e) {
            $this->rollBack();
            throw new \Exception($e->getMessage());
        }
    }
}