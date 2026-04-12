<?php
namespace App\Services;

use App\Repositories\AuthentificationRepository;
use App\Entities\User;
use App\Entities\Role;

class AuthentificationService
{
    private $authRepository;

    public function __construct($conn)
    {
        $this->authRepository = new AuthentificationRepository($conn);
    }

    public function store($user)
    {
        try {
            $data = $this->authRepository->store($user);
            if (!$data)
                return null;

            if (password_verify($user->getPassword(), $data["password"])) {
                $foundUser = new User($data["nom"], $data["prenom"], $data["email"], $data["password"]);
                $foundUser->setId($data["id"]);
                $foundUser->setRole(new Role($data["role"]));
                return $foundUser;
            }
            return null;
        } catch (\PDOException $e) {
            $error = (isset($e->errorInfo[2])) ? $e->errorInfo[2] : $e->getMessage();
            throw new \Exception($error);
        }
    }

    public function loginWithGoogle($user)
    {
        try {
            $foundUser = $this->authRepository->findByGoogleId($user->getIdGoogle());
            if (!$foundUser) {
                $foundUser = $this->authRepository->findByEmail($user->getEmail());
                if ($foundUser) {
                    $this->authRepository->updateGoogleId($foundUser->getId(), $user->getIdGoogle());
                } else {
                    $foundUser = $this->authRepository->registerGoogleUser($user);
                }
            }
            return $foundUser;
        } catch (\Exception $e) {
            throw new \Exception("Erreur lors de la connexion Google.");
        }
    }

    public function getAdherentByUserId($userId)
    {
        $data = $this->authRepository->getAdherentByUserId($userId);
        if (!$data)
            throw new \Exception("Adherent introuvable.");
        return $data;
    }

    public function reloadUserData($userId)
    {
        return $this->authRepository->getAdherentByUserId($userId);
    }
}