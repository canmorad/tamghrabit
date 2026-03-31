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
}