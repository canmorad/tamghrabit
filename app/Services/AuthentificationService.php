<?php
namespace App\Services;

use App\Repositories\AuthentificationRepository;
use App\Entities\Adherent;
class AuthentificationService
{
    private $authRepository;

    public function __construct($conn)
    {
        $this->authRepository = new AuthentificationRepository($conn);
    }

    public function getAdherentByUserId($userId)
    {
        $adherentData = $this->authRepository->getAdherentByUserId($userId);

        if (!$adherentData) {
            throw new \Exception("adherent introuvable pour l'utilisateur id $userId");
        }

        return $adherentData;
    }

    public function store($user)
    {
        $foundUser = $this->authRepository->store($user);

        if (!$foundUser) {
            return null;
        }

        if (password_verify($user->getPassword(), $foundUser->getPassword())) {
            return $foundUser;
        }

        return null;
    }

    public function reloadUserData($userId)
    {
        return $this->authRepository->getAdherentByUserId($userId);
    }
}