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

    public function getUsersList() {
        $users = $this->authRepository->getAllUsers();
        
        foreach ($users as &$user) {
            $user['time_ago'] = $this->formatTimeAgo($user['dateCreation']);
        }
        
        return $users;
    }

    private function formatTimeAgo($date) {
        $timestamp = strtotime($date);
        $diff = time() - $timestamp;
        
        if ($diff < 86400) return "Aujourd'hui";
        if ($diff < 2592000) return "Il y a " . round($diff / 86400) . " j";
        if ($diff < 31536000) return "Il y a " . round($diff / 2592000) . " mois";
        return "Il y a " . round($diff / 31536000) . " an(s)";
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