<?php
namespace App\Services;

use App\Repositories\CampagneRepository;
use App\Traits\FileHandler;
use App\Entities\CampagneArgent;
use App\Entities\CampagneAssociation;
use App\Entities\CampagneParrainage;
use App\Entities\CampagneNature;
use App\Entities\CampagneFinanciere;

class CampagneService
{
    use FileHandler;
    private $campagneRepo;

    public function __construct($conn)
    {
        $this->campagneRepo = new CampagneRepository($conn);
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

    public function store($campagne)
    {
        try {
            $this->campagneRepo->beginTransaction();

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

            $this->campagneRepo->commit();
        } catch (\Exception $e) {
            $this->campagneRepo->rollBack();
            throw $e;
        }
    }

    public function getAllCampagnes()
    {
        $campagnes = $this->campagneRepo->findAll();

        foreach ($campagnes as &$c) {
            if (in_array($c['type'], ['argent', 'parrainage', 'associationassociation'])) {
                $c['percentage'] = ($c['objectifMontant'] > 0) ? round(($c['montantCollecte'] / $c['objectifMontant']) * 100) : 0;
            }

            $dateFin = new \DateTime($c['datefin']);
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

        $dateFin = new \DateTime($c['datefin']);
        $now = new \DateTime();
        $interval = $now->diff($dateFin);
        $c['days_left'] = ($dateFin > $now) ? $interval->days : 0;

        return $c;
    }
}