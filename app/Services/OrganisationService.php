<?php
namespace App\Services;

use App\Repositories\OrganisationRepository;
use App\Traits\FileHandler;
use App\Entities\Organisation;


class OrganisationService
{
    use FileHandler;
    private $orgRepository;

    public function __construct($conn)
    {
        $this->orgRepository = new OrganisationRepository($conn);
    }

    public function getOrgByUserId($id)
    {
        $data = $this->orgRepository->getOrgByUserId($id);
        if (!$data)
            return null;

        return $data;
    }

    public function getOrgById($id)
    {
        $data = $this->orgRepository->getOrgById($id);
        if (!$data)
            return null;

        return $data;
    }

    public function create($org)
    {
        $utilisateur = $org->getAdherent();
        $dossierBase = dirname(__DIR__, 2) . "/public/storage/organisations/";

        $fileFields = [
            'recepisse' => 'Recepisse',
            'pvElection' => 'PvElection',
            'statuts' => 'Statuts',
            'attestationRib' => 'AttestationRib',
            'cniPresidentRecto' => 'CniPresidentRecto',
            'cniPresidentVerso' => 'CniPresidentVerso',
        ];

        try {
            $this->orgRepository->beginTransaction();

            foreach ($fileFields as $key => $method) {
                $getter = "get" . $method;
                $setter = "set" . $method;
                $file = $org->$getter();

                $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                $nomFichier = "{$key}_{$utilisateur->getId()}_" . time() . ".{$extension}";

                $this->uploadFile($file, $dossierBase . $nomFichier);

                $org->$setter($nomFichier);
            }

            $this->orgRepository->create($org);

            $this->orgRepository->commit();

        } catch (\PDOException $e) {
            $this->orgRepository->rollBack();

            throw new \Exception("Une erreur est survenue lors de l'enregistrement de l'organisation. Veuillez réessayer.");
        }
    }
}