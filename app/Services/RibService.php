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

    public function __construct($conn)
    {
        $this->ribRepository = new RibRepository($conn);
    }

    public function getRibByUserId($id)
    {
        return $this->ribRepository->getRibByUserId($id);
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