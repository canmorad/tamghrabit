<?php
namespace App\Services;

use App\Repositories\ProfileRepository;
use App\Traits\FileHandler;
use Exception;

class ProfileService
{
    use FileHandler;
    private $profileRepository;

    public function __construct($conn)
    {
        $this->profileRepository = new ProfileRepository($conn);
    }

    public function updateProfile($adherent)
    {
        return $this->profileRepository->updateProfile($adherent);
    }

    public function updateImageProfile($userSession, $file)
    {
        $baseDossier = dirname(__DIR__, 2) . "/public/storage/profiles/";
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $nomFichierUnique = "profile_" . $userSession->getId() . "_" . time() . "." . $extension;
        $fullDestination = $baseDossier . $nomFichierUnique;

        try {
            $oldImagePath = $baseDossier . $userSession->getImageProfile();
            $this->deleteFile($oldImagePath);
            $this->uploadFile($file, $fullDestination);

            $this->profileRepository->updateImageProfile($userSession->getId(), $nomFichierUnique);

            return $nomFichierUnique;

        } catch (Exception $e) {
            throw $e;
        }
    }
}