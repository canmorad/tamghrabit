<?php
namespace App\Repositories;

use App\Entities\Rib;
use App\Entities\Adherent;
use PDO;

class RibRepository
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function getRibByUserId($id)
    {
        $sql = "SELECT r.*, u.prenom, u.nom, u.email 
                FROM ribs r 
                JOIN users u ON r.id = u.id 
                WHERE r.id = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$data) {
            return null;
        }

        $rib = new Rib($data['rib'], $data['attestationRib']);
        $rib->setId($data['id']);
        $rib->setStatus($data['status']);

        $adherent = new Adherent($data['nom'], $data['prenom'], $data['email'], "", "");
        $adherent->setId($data['id']);

        $rib->setAdherent($adherent);

        return $rib;
    }
    public function getPendingRibs()
    {
        $sql = "SELECT r.*, u.prenom, u.nom 
                FROM ribs r 
                JOIN users u ON r.id = u.id 
                WHERE r.status = 'en_attente'
                ORDER BY r.dateCreation ASC";

        $stmt = $this->conn->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateStatus($id, $status)
    {
        $sql = "UPDATE ribs SET status = :status, dateModifier = NOW() WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            'status' => $status,
            'id' => $id
        ]);
    }

    public function update($rib)
    {
        $sql = "insert into ribs (id, rib, attestationrib) 
                values (:id, :rib, :attestation)
                on duplicate key update 
                rib = values(rib), 
                attestationrib = values(attestationrib),
                status = 'en_attente',
                datemodifier = now()";

        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            'id' => $rib->getAdherent()->getId(),
            'rib' => $rib->getRib(),
            'attestation' => $rib->getAttestationRib()
        ]);
    }
}