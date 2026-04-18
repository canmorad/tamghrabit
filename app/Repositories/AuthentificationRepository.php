<?php
namespace App\Repositories;

use App\Entities\User;
use App\Entities\Role;

class AuthentificationRepository
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function findByEmail($email)
    {
        $sql = "select u.*, r.nom as role from users u join roles r ON u.idRole = r.id where u.email = ?";
        $stm = $this->conn->prepare($sql);
        $stm->execute([$email]);
        $data = $stm->fetch(\PDO::FETCH_ASSOC);

        if (!$data)
            return null;

        $user = new User($data["nom"], $data["prenom"], $data["email"], $data["password"]);
        $user->setId($data["id"]);
        $user->setRole(new Role($data["role"]));
        if (isset($data["imageProfile"]))
            $user->setImageProfile($data["imageProfile"]);
        return $user;
    }

    public function getAllUsers() {
        $sql = "SELECT u.*, a.estVerifie 
                FROM users u 
                LEFT JOIN adherents a ON u.id = a.id 
                ORDER BY u.dateCreation DESC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function findByGoogleId($googleId)
    {
        $sql = "select u.*, r.nom as role from users u join roles r ON u.idRole = r.id where u.idGoogle = ?";
        $stm = $this->conn->prepare($sql);
        $stm->execute([$googleId]);
        $data = $stm->fetch(\PDO::FETCH_ASSOC);

        if (!$data)
            return null;

        $user = new User($data['nom'], $data['prenom'], $data['email'], "");
        $user->setId($data['id']);
        $user->setRole(new Role($data['role']));
        $user->setImageProfile($data['imageProfile']);
        return $user;
    }

    public function registerGoogleUser($user)
    {
        $roleStm = $this->conn->prepare("select id from roles where nom = 'adherent'");
        $roleStm->execute();
        $idRole = $roleStm->fetchColumn();

        $sql = "insert into users (nom, prenom, email, idGoogle, imageProfile, idRole, password, estVerifieGmail) 
                values (?, ?, ?, ?, ?, ?, 'GOOGLE_USER', true)";
        $stm = $this->conn->prepare($sql);
        $stm->execute([$user->getNom(), $user->getPrenom(), $user->getEmail(), $user->getIdGoogle(), $user->getImageProfile(), $idRole]);

        $userId = $this->conn->lastInsertId();
        $stmAdherent = $this->conn->prepare("insert into adherents (id, sexe) values (?, 'homme')");
        $stmAdherent->execute([$userId]);

        return $this->findByGoogleId($user->getIdGoogle());
    }

    public function updateGoogleId($userId, $googleId)
    {
        $stm = $this->conn->prepare("update users set idGoogle = ?, dateModifier = NOW() where id = ?");
        $stm->execute([$googleId, $userId]);
    }

    public function store($user)
    {
        $stm = $this->conn->prepare("call login(?)");
        $stm->execute([$user->getEmail()]);
        return $stm->fetch(\PDO::FETCH_ASSOC);
    }

    public function getAdherentByUserId($userId)
    {
        $sql = "select u.nom, u.prenom, u.email, u.password, u.imageProfile, a.id, a.sexe, a.dateNaissance, a.adresse, a.telephone, a.ville, a.pays 
                from adherents a join users u on u.id = a.id where a.id = ?";
        $stm = $this->conn->prepare($sql);
        $stm->execute([$userId]);
        return $stm->fetch(\PDO::FETCH_ASSOC);
    }
}