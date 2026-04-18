<?php
namespace App\Services;

use App\Repositories\ProfileRepository;
use App\Traits\FileHandler;
use App\Traits\DatabaseTransaction;
use App\Traits\LoggableException; 
use App\Services\EmailService;
use Exception;
use Throwable;

class ProfileService
{
    use FileHandler;
    use DatabaseTransaction;
    use LoggableException; 

    private $profileRepository;
    private $emailService;
    private $conn;

    public function __construct($conn)
    {
        $this->profileRepository = new ProfileRepository($conn);
        $this->emailService = new EmailService();
        $this->conn = $conn;
    }

    public function preparerMiseAjourEmail($user, $nouveauEmail, $motDePasseActuel)
    {
        if ($this->profileRepository->existEmail($nouveauEmail)) {
            throw new Exception("Cet email est déjà utilisé par un autre compte.");
        }

        if (!password_verify($motDePasseActuel, $user->getPassword())) {
            throw new Exception("Mot de passe incorrect.");
        }

        $codeVerification = rand(10000000, 99999999);

        $_SESSION['modification_email_temp'] = [
            'idUtilisateur' => $user->getId(),
            'nouveauEmail' => $nouveauEmail,
            'codeOtp' => $codeVerification,
            'dateExpiration' => time() + 600
        ];

        $sujet = "Vérification de votre nouvelle adresse email";
        $corpsMessage = "
                        <h2>Confirmation de changement d'email</h2>
                        <p>Pour finaliser le changement de votre adresse email, veuillez utiliser le code suivant :</p>
                        <h1 style='color: #22C55E;'>$codeVerification</h1>
                        <p>Ce code est valable pendant 10 minutes.</p>
                        <p>Si vous n'êtes pas à l'origine de cette demande, veuillez ignorer cet email.</p>
                    ";

        try {
            $this->emailService->send(
                $nouveauEmail,
                $user->getPrenom() . " " . $user->getNom(),
                $sujet,
                $corpsMessage
            );
        } catch (Throwable $e) {
            $this->handle($e, "Erreur lors de l'envoi de l'email. Veuillez réessayer.");
        }
    }

    public function finaliserMiseAjourEmail($codeSaisi)
    {
        $temp = $_SESSION['modification_email_temp'] ?? null;

        if (!$temp) {
            throw new Exception("Session expirée ou invalide.");
        }

        if (time() > $temp['dateExpiration']) {
            unset($_SESSION['modification_email_temp']);
            throw new Exception("Le code a expiré.");
        }

        if ($codeSaisi != $temp['codeOtp']) {
            throw new Exception("Code de vérification incorrect.");
        }

        try {
            return $this->profileRepository->confirmEmailUpdate($temp['idUtilisateur'], $temp['nouveauEmail']);
        } catch (Throwable $e) {
            $this->handle($e, "Erreur lors de la confirmation de l'email.");
        }
    }

    public function updatePassword($user, $ancienPassword, $nouvellePassword)
    {
        if (!password_verify($ancienPassword, $user->getPassword())) {
            throw new Exception('L\'ancien mot de passe est incorrect.');
        }

        try {
            $this->beginTransaction();
            $user->setPassword(password_hash($nouvellePassword, PASSWORD_DEFAULT));
            $this->profileRepository->updatePassword($user);
            $this->commit();
        } catch (Throwable $e) {
            $this->rollBack();
            $this->handle($e, 'Erreur lors de la modification du mot de passe.');
        }
    }

    public function updateProfile($adherent)
    {
        try {
            $this->beginTransaction();
            $this->profileRepository->updateProfile($adherent);
            $this->commit();
            return true;
        } catch (Throwable $e) {
            $this->rollBack();
            $this->handle($e, "Une erreur est survenue lors de la mise à jour du profil.");
        }
    }

    public function updateImageProfile($userSession, $file)
    {
        $baseDossier = dirname(__DIR__, 2) . "/public/storage/profiles/";
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $nomFichierUnique = "profile_" . $userSession->getId() . "_" . time() . "." . $extension;
        $fullDestination = $baseDossier . $nomFichierUnique;

        try {
            $this->beginTransaction();

            $this->uploadFile($file, $fullDestination);
            $this->profileRepository->updateImageProfile($userSession->getId(), $nomFichierUnique);

            if ($userSession->getImageProfile()) {
                $oldImagePath = $baseDossier . $userSession->getImageProfile();
                if (file_exists($oldImagePath)) {
                    $this->deleteFile($oldImagePath);
                }
            }

            $this->commit();
            return $nomFichierUnique;

        } catch (Throwable $e) {
            $this->rollBack();

            if (file_exists($fullDestination)) {
                unlink($fullDestination);
            }

            $this->handle($e, "Erreur lors de la mise à jour de l'image.");
        }
    }
}