<?php
namespace App\Repositories;

use App\Entities\Identifier;
use PDO;

class IdentifierRepository
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function findById($id)
    {
        $requete = $this->conn->prepare("select * from identifiers where id = ?");
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

        return $identifier;
    }

    public function update($identifier)
    {
        $sql = "insert into identifiers (id, cnirecto, cniverso, passport) 
                values (:id, :cnirecto, :cniverso, :passport)
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