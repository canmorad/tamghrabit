<?php
namespace App\Services;

use App\Repositories\IdentifierRepository;
use App\Entities\Identifier;
use App\Traits\FileHandler;
use Exception;

class IdentifierService
{
    use FileHandler;
    private $identifierRepository;

    public function __construct($conn)
    {
        $this->identifierRepository = new IdentifierRepository($conn);
    }

    public function getById($id)
    {
        return $this->identifierRepository->findById($id);
    }

    public function update($identifier)
    {
        $utilisateur = $identifier->getAdherent();
        $dossierBase = dirname(__DIR__, 2) . "/public/storage/identifiers/";

        $ancienDoc = $this->identifierRepository->findById($utilisateur->getId());

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