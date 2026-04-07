<?php
namespace App\Services;

use App\Repositories\RibRepository;
use App\Entities\Rib;
use App\Traits\FileHandler;
use Exception;

class RibService
{
    use FileHandler;
    private $ribRepository;
    private $emailService;

    public function __construct($conn)
    {
        $this->ribRepository = new RibRepository($conn);
        $this->emailService = new EmailService();
    }

    public function getRibByUserId($id)
    {
        return $this->ribRepository->getRibByUserId($id);
    }

    public function getPendingRequests()
    {
        return $this->ribRepository->getPendingRibs();
    }

    public function verify($userId, $action, $reason = '')
    {
        $status = ($action === 'approve') ? 'approuvee' : 'refuse';
        $res = $this->ribRepository->updateStatus($userId, $status);

        if ($res) {
            $rib = $this->ribRepository->getRibByUserId($userId);
            $adherent = $rib->getAdherent();

            if ($action === 'approve') {
                $subject = "Validation de vos coordonnées bancaires - Tamghrabit";
                $body = "
                    <h2>Bonjour {$adherent->getPrenom()},</h2>
                    <p>Nous vous informons que votre <strong>RIB</strong> a été approuvé avec succès.</p>
                    <p>Vous pouvez désormais recevoir les transferts relatifs à vos campagnes.</p>
                ";
            } else {
                $subject = "Action requise : RIB refusé";
                $body = "
                    <h2>Bonjour {$adherent->getPrenom()},</h2>
                    <p>Après examen de votre document RIB, votre demande a été <strong>refusée</strong> pour la raison suivante :</p>
                    <div style='background: #fff5f5; padding: 15px; border-left: 5px solid #ef4444; margin: 10px 0; color: #b91c1c;'>
                        <strong>Motif :</strong> $reason
                    </div>
                    <p>Veuillez corriger les informations et soumettre à nouveau le document via votre profil.</p>
                ";
            }

            $this->emailService->send($adherent->getEmail(), $adherent->getPrenom(), $subject, $body);
        }
        return $res;
    }

    public function update($rib)
    {
        $utilisateur = $rib->getAdherent();
        $dossierBase = dirname(__DIR__, 2) . "/public/storage/ribs/";

        $ancienRib = $this->ribRepository->getRibByUserId($utilisateur->getId());
        $fichier = $rib->getAttestationRib();
        $nomFichierFinal = $ancienRib ? $ancienRib->getAttestationRib() : null;

        try {
            if (isset($fichier['tmp_name']) && !empty($fichier['tmp_name'])) {
                if ($nomFichierFinal) {
                    $this->deleteFile($dossierBase . $nomFichierFinal);
                }

                $extension = strtolower(pathinfo($fichier['name'], PATHINFO_EXTENSION));
                $nomFichierFinal = "rib_" . $utilisateur->getId() . "_" . time() . "." . $extension;
                $this->uploadFile($fichier, $dossierBase . $nomFichierFinal);
            }

            $rib->setAttestationRib($nomFichierFinal);

            return $this->ribRepository->update($rib);
        } catch (Exception $e) {
            throw $e;
        }
    }
}