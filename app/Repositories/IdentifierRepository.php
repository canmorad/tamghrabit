<?php
namespace App\Repositories;

use App\Entities\Identifier;
use App\Entities\Adherent;
use PDO;

class IdentifierRepository
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function getPendingIdentifiers()
    {
        $sql = "SELECT i.*, u.nom, u.prenom, u.email, u.imageProfile
            FROM identifiers i
            JOIN users u ON i.id = u.id
            WHERE i.status = 'en_attente'";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateStatus($id, $status)
    {
        $sql = "update identifiers set status = :status, dateModifier = NOW() where id = :id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute(['status' => $status, 'id' => $id]);
    }

    public function getIdentifierByUserId($id)
    {
        $requete = $this->conn->prepare("
            select i.*, u.nom, u.email 
            from identifiers i
            join users u on i.id = u.id 
            WHERE i.id = ?
        ");

        $requete->execute([$id]);
        $data = $requete->fetch(PDO::FETCH_ASSOC);

        if (!$data) {
            return null;
        }

        $identifier = new Identifier(
            $data['cniRecto'],
            $data['cniVerso'],
            $data['passport']
        );
        
        $identifier->setId($data['id']);

        $user = new Adherent($data['nom'], "", $data['email'], "", "");

        $identifier->setAdherent($user);

        return $identifier;
    }

    public function update($identifier)
    {
        $sql = "insert into identifiers (id, cniRecto, cniVerso, passport) 
                values (:id, :cniRecto, :cniVerso, :passport)
                on duplicate key update 
                cnirecto = values(cniRecto), 
                cniverso = values(cniverso), 
                passport = values(passport),
                datemodifier = now()";

        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            'id' => $identifier->getAdherent()->getId(),
            'cniRecto' => $identifier->getCniRecto(),
            'cniVerso' => $identifier->getCniVerso(),
            'passport' => $identifier->getPassport()
        ]);
    }
}