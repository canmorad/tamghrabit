<?php
namespace App\Services;

use App\Repositories\IdentifierRepository;
use App\Entities\Identifier;
use App\Traits\FileHandler;
use App\Services\EmailService;
use Exception;

class IdentifierService
{
    use FileHandler;
    private $identifierRepository;
    private $emailService;

    public function __construct($conn)
    {
        $this->identifierRepository = new IdentifierRepository($conn);
        $this->emailService = new EmailService();
    }

    public function getIdentifierByUserId($id)
    {
        return $this->identifierRepository->getIdentifierByUserId($id);
    }

    public function getPendingRequests()
    {
        return $this->identifierRepository->getPendingIdentifiers();
    }

    public function verify($userId, $action, $reason = '')
    {
        $status = ($action === 'approve') ? 'approuve' : 'refuse';
        $res = $this->identifierRepository->updateStatus($userId, $status);

        if ($res) {
            $user = $this->identifierRepository->getIdentifierByUserId($userId);
            $adherent = $user->getAdherent();

            if ($action === 'approve') {
                $subject = "Félicitations ! Votre identité est vérifiée";
                $body = "
                    <h2>Bonjour {$adherent->getNom()},</h2>
                    <p>Bonne nouvelle ! Votre compte est désormais <strong>vérifié</strong> sur Tamghrabit.</p>
                    <p>Vous pouvez maintenant accéder à toutes les fonctionnalités du site.</p>
                ";
            } else {
                $subject = "Action requise : Vérification d'identité refusée";
                $body = "
                    <h2>Bonjour {$adherent->getNom()},</h2>
                    <p>Nous avons examiné vos documents d'identité.</p>
                    <p>Malheureusement, votre demande a été <strong>refusée</strong> pour la raison suivante :</p>
                    <div style='background: #fff5f5; padding: 15px; border-left: 5px solid #e53e3e; margin: 10px 0;'>
                        $reason
                    </div>
                    <p>Merci de corriger cela et de renvoyer vos documents via votre profil.</p>
                ";
            }

            $this->emailService->send($adherent->getEmail(), $adherent->getNom(), $subject, $body);
        }
        return $res;
    }

    public function update($identifier)
    {
        $utilisateur = $identifier->getAdherent();
        $dossierBase = dirname(__DIR__, 2) . "/public/storage/identifiers/";

        $ancienDoc = $this->identifierRepository->getIdentifierByUserId($utilisateur->getId());

        $files = [
            'cniRecto' => $identifier->getCniRecto(),
            'cniVerso' => $identifier->getCniVerso(),
            'passport' => $identifier->getPassport()
        ];

        $finalPaths = [
            'cniRecto' => $ancienDoc ? $ancienDoc->getCniRecto() : null,
            'cniVerso' => $ancienDoc ? $ancienDoc->getCniVerso() : null,
            'passport' => $ancienDoc ? $ancienDoc->getPassport() : null
        ];

        try {
            foreach ($files as $key => $file) {
                if (isset($file['tmp_name']) && !empty($file['tmp_name'])) {
                    if ($finalPaths[$key]) {
                        $this->deleteFile($dossierBase . $finalPaths[$key]);
                    }
                    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                    $newName = $key . "_" . $utilisateur->getId() . "_" . time() . "." . $extension;
                    $this->uploadFile($file, $dossierBase . $newName);
                    $finalPaths[$key] = $newName;
                }
            }

            $identifier->setCniRecto($finalPaths['cniRecto']);
            $identifier->setCniVerso($finalPaths['cniVerso']);
            $identifier->setPassport($finalPaths['passport']);

            return $this->identifierRepository->update($identifier);
        } catch (Exception $e) {
            throw $e;
        }
    }
}