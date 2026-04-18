<?php
namespace App\Services;

use App\Repositories\CampagneRepository;
use App\Traits\FileHandler;
use App\Traits\DatabaseTransaction;
use App\Traits\LoggableException;
use App\Entities\CampagneArgent;
use App\Entities\CampagneAssociation;
use App\Entities\CampagneParrainage;
use App\Entities\CampagneNature;
use App\Entities\CampagneFinanciere;
use App\Services\EmailService;

class CampagneService
{
    use FileHandler;
    use DatabaseTransaction;
    use LoggableException;

    private $campagneRepo;
    private $conn;
    private $emailService;
    public function __construct($conn)
    {
        $this->campagneRepo = new CampagneRepository($conn);
        $this->emailService = new EmailService();
        $this->conn = $conn;
    }

    public function getCampagnesByUser($userId)
    {
        $campagnes = $this->campagneRepo->getCampagnesByUser($userId);

        foreach ($campagnes as &$c) {
            if (isset($c['objectifMontant']) && $c['objectifMontant'] > 0) {
                $c['percentage'] = round(($c['montantCollecte'] / $c['objectifMontant']) * 100);
            } else {
                $c['percentage'] = 0;
            }
        }

        return $campagnes;
    }

    public function getListActive()
    {
        return $this->campagneRepo->getActiveCampagnes();
    }

    public function toggleStatus($id, $status)
    {
        return $this->campagneRepo->updateStatus($id, $status);
    }
    
    public function delete($id)
    {
        try {
            return $this->campagneRepo->delete($id);
        } catch (\Exception $e) {
            $this->handle($e, "Erreur lors de la suppression de la campagne.");
        }
    }

    public function terminerCampagne($id)
    {
        try {
            return $this->campagneRepo->terminerCampagne($id);
        } catch (\Exception $e) {
            $this->handle($e, "Erreur lors de la clôture de la campagne.");
        }
    }

    public function getPendingCampagnes()
    {
        return $this->campagneRepo->getPendingCampagnes();
    }

    public function verifyCampagne($id, $action, $reason = '')
    {
        $status = ($action === 'approve') ? 'approuvee' : 'rejetee';
        $res = $this->campagneRepo->updateStatus($id, $status);

        if ($res) {
            $c = $this->campagneRepo->findById($id);

            $email = $c['email'] ?? null;
            $nomFull = $c['prenomDestinataire'] . ' ' . $c['nomDestinataire'];

            if ($action === 'approve') {
                $subject = "Félicitations ! Votre campagne est en ligne";
                $body = "
                <div style='font-family: sans-serif; line-height: 1.6;'>
                    <h2>Bonjour {$c['prenomDestinataire']},</h2>
                    <p>Votre campagne <strong>\"{$c['titre']}\"</strong> vient d'être approuvée.</p>
                    <p>Elle est désormais visible par tous les donateurs sur la plateforme.</p>
                    <p>Bonne chance pour votre collecte !</p>
                </div>
            ";
            } else {
                $subject = "Mise à jour concernant votre campagne";
                $body = "
                <div style='font-family: sans-serif; line-height: 1.6;'>
                    <h2>Bonjour {$c['prenomDestinataire']},</h2>
                    <p>Nous avons examiné votre campagne <strong>\"{$c['titre']}\"</strong>.</p>
                    <p>Malheureusement, elle n'a pas été validée pour la raison suivante :</p>
                    <div style='background: #FFF5F5; padding: 15px; border-left: 5px solid #E53E3E; margin: 15px 0; color: #C53030;'>
                        <strong>Motif du refus :</strong><br>
                        $reason
                    </div>
                    <p>Vous pouvez corriger les informations nécessaires et nous la soumettre à nouveau pour révision.</p>
                    <p>Cordialement,<br>L'équipe Tamghrabit</p>
                </div>
            ";
            }

            if ($email) {
                $this->emailService->send($email, $nomFull, $subject, $body);
            }
        }

        return $res;
    }

    public function update($campagne)
    {
        try {
            $this->beginTransaction();

            $oldData = $this->campagneRepo->findById($campagne->getId());
            $user = $campagne->getAdherent();
            $dossier = dirname(__DIR__, 2) . "/public/storage/campagnes/";

            $fileImg = $campagne->getImage();
            $nomImgFinal = $oldData['image'];

            if (isset($fileImg['tmp_name']) && !empty($fileImg['tmp_name'])) {
                if ($nomImgFinal) {
                    $this->deleteFile($dossier . $nomImgFinal);
                }
                $extensionImg = pathinfo($fileImg['name'], PATHINFO_EXTENSION);
                $nomImgFinal = 'img_' . $user->getId() . "_" . time() . "." . $extensionImg;
                $this->uploadFile($fileImg, $dossier . $nomImgFinal);
            }
            $campagne->setImage($nomImgFinal);

            $fileJustif = $campagne->getJustificatif();
            $nomJustifFinal = $oldData['justificatif'];

            if (isset($fileJustif['tmp_name']) && !empty($fileJustif['tmp_name'])) {
                if ($nomJustifFinal) {
                    $this->deleteFile($dossier . $nomJustifFinal);
                }
                $extensionJustif = pathinfo($fileJustif['name'], PATHINFO_EXTENSION);
                $nomJustifFinal = 'just_' . $user->getId() . "_" . time() . "." . $extensionJustif;
                $this->uploadFile($fileJustif, $dossier . $nomJustifFinal);
            }
            $campagne->setJustificatif($nomJustifFinal);

            $this->campagneRepo->updateBase($campagne);

            if (in_array($campagne->getType(), ['argent', 'parrainage', 'association'])) {
                $this->campagneRepo->updateFinanciere($campagne);

                if ($campagne->getType() === 'parrainage') {
                    $this->campagneRepo->updateParrainage($campagne);
                } elseif ($campagne->getType() === 'association') {
                    $this->campagneRepo->updateAssociation($campagne);
                }
            } elseif ($campagne->getType() === 'nature') {
                $this->campagneRepo->updateNature($campagne);
            }

            $this->commit();
            return true;

        } catch (\Exception $e) {
            $this->rollBack();
            $this->handle($e, "Erreur lors de la mise à jour de la campagne.");
        }
    }

    public function store($campagne)
    {
        try {
            $this->beginTransaction();

            $user = $campagne->getAdherent();
            $dossier = dirname(__DIR__, 2) . "/public/storage/campagnes/";

            $fileImg = $campagne->getImage();
            $nomImg = 'img_' . $user->getId() . "_" . time() . "." . pathinfo($fileImg['name'], PATHINFO_EXTENSION);
            $this->uploadFile($fileImg, $dossier . $nomImg);
            $campagne->setImage($nomImg);

            $fileJustif = $campagne->getJustificatif();
            $nomJustif = 'just_' . $user->getId() . "_" . time() . "." . pathinfo($fileJustif['name'], PATHINFO_EXTENSION);
            $this->uploadFile($fileJustif, $dossier . $nomJustif);
            $campagne->setJustificatif($nomJustif);

            $id = $this->campagneRepo->storeBase($campagne);
            $campagne->setId($id);

            if ($campagne instanceof CampagneFinanciere) {
                $this->campagneRepo->storeFinanciere($campagne);
                if ($campagne instanceof CampagneArgent) {
                    $this->campagneRepo->storeArgent($campagne);
                } elseif ($campagne instanceof CampagneAssociation) {
                    $this->campagneRepo->storeAssociation($campagne);
                } elseif ($campagne instanceof CampagneParrainage) {
                    $this->campagneRepo->storeParrainage($campagne);
                }
            } elseif ($campagne instanceof CampagneNature) {
                $this->campagneRepo->storeNature($campagne);
            }

            $this->commit();
        } catch (\Exception $e) {
            $this->rollBack();
            $this->handle($e, "Erreur lors de la création de la campagne.");
        }
    }

    public function getManageData($id, $currentUserId)
    {
        $campagne = $this->campagneRepo->findByIdForManage($id);

        if (!$campagne || $campagne['idAdherent'] != $currentUserId) {
            return null;
        }

        $donors = $this->campagneRepo->getDonorsByCampagne($id);

        return [
            'campagne' => $campagne,
            'donors' => $donors
        ];
    }

    public function getAllCampagnes()
    {
        $campagnes = $this->campagneRepo->findAll();

        foreach ($campagnes as &$c) {
            if (in_array($c['type'], ['argent', 'parrainage', 'association'])) {
                $c['percentage'] = ($c['objectifMontant'] > 0) ? round(($c['montantCollecte'] / $c['objectifMontant']) * 100) : 0;
            }

            $dateFin = new \DateTime($c['dateFin']);
            $now = new \DateTime();
            $c['days_left'] = ($dateFin > $now) ? $now->diff($dateFin)->days : 0;
        }

        return $campagnes;
    }

    public function getCampagneById($id)
    {
        $c = $this->campagneRepo->findById($id);

        if (!$c)
            return null;

        if (isset($c['objectifMontant']) && $c['objectifMontant'] > 0) {
            $c['percentage'] = round(($c['montantCollecte'] / $c['objectifMontant']) * 100);
        } else {
            $c['percentage'] = 0;
        }

        $dateFin = new \DateTime($c['dateFin']);
        $now = new \DateTime();
        $interval = $now->diff($dateFin);
        $c['days_left'] = ($dateFin > $now) ? $interval->days : 0;

        return $c;
    }
}