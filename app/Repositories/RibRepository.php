<?php
namespace App\Repositories;

use App\Entities\Rib;
use PDO;

class RibRepository
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function findById($id)
    {
        $requete = $this->conn->prepare("select * from ribs where id = ?");
        $requete->execute([$id]);
        $data = $requete->fetch(PDO::FETCH_ASSOC);

        if (!$data) {
            return null;
        }

        $rib = new Rib($data['rib'], $data['attestationRib']);
        $rib->setId($data['id']);

        return $rib;
    }

    public function update($rib)
    {
        $sql = "insert into ribs (id, rib, attestationrib) 
                values (:id, :rib, :attestation)
                on duplicate key update 
                rib = values(rib), 
                attestationrib = values(attestationrib),
                datemodifier = now()";

        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            'id' => $rib->getAdherent()->getId(),
            'rib' => $rib->getRib(),
            'attestation' => $rib->getAttestationRib()
        ]);
    }
}