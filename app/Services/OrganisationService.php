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

    public function validateOrganisation($orgId, $action, $reason = null)
    {
        $status = ($action === 'approve') ? 'approuvee' : 'refuse';

        return $this->orgRepository->updateStatus($orgId, $status, $reason);
    }

    public function getPendingOrganisations()
    {
        return $this->orgRepository->getAllPendingWithUsers();
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

    public function save(Organisation $org, $id = null)
    {
        $utilisateur = $org->getAdherent();
        $dossierBase = dirname(__DIR__, 2) . "/public/storage/organisations/";

        $oldData = $id ? $this->getOrgById($id) : null;

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

                if (isset($file['name']) && !empty($file['name'])) {
                    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                    $nomFichier = "{$key}_{$utilisateur->getId()}_" . time() . ".{$extension}";
                    $this->uploadFile($file, $dossierBase . $nomFichier);
                    $org->$setter($nomFichier);
                } elseif ($oldData && isset($oldData[$key])) {
                    $org->$setter($oldData[$key]);
                } else {
                    $org->$setter(null);
                }
            }

            if ($id) {
                $this->orgRepository->update($org, $id);
            } else {
                $this->orgRepository->store($org);
            }

            $this->orgRepository->commit();
            return true;

        } catch (\Exception $e) {
            $this->orgRepository->rollBack();
            throw $e;
        }
    }
}