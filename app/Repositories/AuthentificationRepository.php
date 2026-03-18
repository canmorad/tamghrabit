<?php
namespace App\Repositories;

use App\Entities\User;
use App\Entities\Adherent;
use App\Entities\Role;
class AuthentificationRepository
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function store($foundUserser)
    {
        try {
            $stm = $this->conn->prepare("call login(?)");
            $stm->execute([$foundUserser->getEmail()]);
            $foundUserserData = $stm->fetch(\PDO::FETCH_ASSOC);

            if (!$foundUserserData) {
                return null;
            }

            $role = new Role($foundUserserData["role"]);
            $foundUser = new User($foundUserserData["nom"], $foundUserserData["prenom"], $foundUserserData["email"], $foundUserserData["password"]);
            $foundUser->setId($foundUserserData["id"]);
            $foundUser->setRole($role);
            return $foundUser;

        } catch (\PDOException $e) {
            $errorInfo = $e->errorInfo;

            if (isset($errorInfo[2])) {
                throw new \Exception($errorInfo[2]);
            }

            throw new \Exception("Erreur serveur");
        }
    }

    public function getAdherentByUserId($userId)
    {
        $sql = "select u.nom, u.prenom, u.email, u.password, u.imageProfile,
                   a.id, a.sexe, a.dateNaissance, a.adresse,
                   a.telephone, a.ville, a.pays
            from adherents a
            join users u on u.id = a.id
            where a.id = ?";
        try {
            $stm = $this->conn->prepare($sql);
            $stm->execute([$userId]);

            $adherent = $stm->fetch(\PDO::FETCH_ASSOC);
            return $adherent;
        } catch (\PDOException $e) {
            $errorInfo = $e->errorInfo;

            if (isset($errorInfo[2])) {
                throw new \Exception($errorInfo[2]);
            }

            throw new \Exception("Erreur serveur");
        }
    }
}