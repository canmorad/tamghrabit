<?php
namespace App\Repositories;

class CampagneRepository
{
    private $conn;
    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function beginTransaction()
    {
        return $this->conn->beginTransaction();
    }
    public function commit()
    {
        return $this->conn->commit();
    }
    public function rollBack()
    {
        return $this->conn->rollBack();
    }

    public function storeBase($campagne)
    {
        $sql = "insert into campagnes (idadherent, idcategorie, titre, description, image, telephone, datedebut, datefin, justificatif, type) 
                values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stm = $this->conn->prepare($sql);
        $stm->execute([
            $campagne->getAdherent()->getId(),
            $campagne->getCategorie()->getId(),
            $campagne->getTitre(),
            $campagne->getDescription(),
            $campagne->getImage(),
            $campagne->getTelephone(),
            $campagne->getDateDebut(),
            $campagne->getDateFin(),
            $campagne->getJustificatif(),
            $campagne->getType()
        ]);
        return $this->conn->lastInsertId();
    }

    public function storeFinanciere($campagne)
    {
        $sql = "insert into campagnesFinancieres (id, objectifmontant) values (?, ?)";
        $this->conn->prepare($sql)->execute([
            $campagne->getId(),
            $campagne->getObjectifMontant()
        ]);
    }

    public function storeArgent($campagne)
    {
        $sql = "insert into campagnesArgent (id) values (?)";
        return $this->conn->prepare($sql)->execute([
            $campagne->getId(),
        ]);
    }

    public function storeParrainage($campagne)
    {
        $sql = "insert into campagnesParrainage (id, frequence) values (?, ?)";
        return $this->conn->prepare($sql)->execute([
            $campagne->getId(),
            $campagne->getFrequence()
        ]);
    }

    public function storeAssociation($campagne)
    {
        $sql = "insert into campagnesAssociation (id, idorganisation) values (?, ?)";
        return $this->conn->prepare($sql)->execute([
            $campagne->getId(),
            $campagne->getOrganisation()->getId()
        ]);
    }

    public function storeNature($campagne)
    {
        $sql = "insert into campagnesNature (id, typedon, nomarticle) values (?, ?, ?)";
        return $this->conn->prepare($sql)->execute([
            $campagne->getId(),
            $campagne->getTypeDon(),
            $campagne->getNomArticle()
        ]);
    }

    public function findById($id)
    {
        $sql = "SELECT c.*, cat.nom as categorie_nom, 
            cf.objectifMontant, cf.montantCollecte,
            cp.frequence,
            ca.idOrganisation, org.nom as organisation_nom,
            cn.typeDon, cn.nomArticle, u.nom as nomDestinataire, u.prenom as prenomDestinataire, u.imageProfile
            FROM campagnes c
            JOIN categories cat ON c.idCategorie = cat.id
            LEFT JOIN campagnesFinancieres cf ON c.id = cf.id
            LEFT JOIN campagnesParrainage cp ON cf.id = cp.id
            LEFT JOIN campagnesAssociation ca ON cf.id = ca.id
            LEFT JOIN campagnesNature cn ON c.id = cn.id
            LEFT JOIN organisations org ON ca.idOrganisation = org.id
            LEFT JOIN users u on c.idAdherent = u.id
            WHERE c.id = ? AND c.datesupprimer IS NULL";

        $stm = $this->conn->prepare($sql);
        $stm->execute([$id]);
        return $stm->fetch(\PDO::FETCH_ASSOC);
    }

    public function getCampagnesByUser($userId)
    {
        $sql = "SELECT c.*, cat.nom as categorie_nom, 
                cf.objectifMontant, cf.montantCollecte,
                cp.frequence,
                ca.idOrganisation, org.nom as organisation_nom,
                cn.typeDon, cn.nomArticle
                FROM campagnes c
                JOIN categories cat ON c.idCategorie = cat.id
                LEFT JOIN campagnesFinancieres cf ON c.id = cf.id
                LEFT JOIN campagnesParrainage cp ON cf.id = cp.id
                LEFT JOIN campagnesAssociation ca ON cf.id = ca.id
                LEFT JOIN campagnesNature cn ON c.id = cn.id
                LEFT JOIN organisations org ON ca.idOrganisation = org.id
                WHERE c.idAdherent = ? AND c.datesupprimer IS NULL
                ORDER BY c.datecreation DESC";

        $stm = $this->conn->prepare($sql);
        $stm->execute([$userId]);
        return $stm->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function findAll()
    {
        $sql = "SELECT c.*, cat.nom as categorie_nom, 
            cf.objectifMontant, cf.montantCollecte,
            cp.frequence,
            ca.idOrganisation, org.nom as organisation_nom,
            cn.typeDon, cn.nomArticle
            FROM campagnes c
            JOIN categories cat ON c.idCategorie = cat.id
            LEFT JOIN campagnesFinancieres cf ON c.id = cf.id
            LEFT JOIN campagnesParrainage cp ON cf.id = cp.id
            LEFT JOIN campagnesAssociation ca ON cf.id = ca.id
            LEFT JOIN campagnesNature cn ON c.id = cn.id
            LEFT JOIN organisations org ON ca.idOrganisation = org.id
            WHERE c.datesupprimer IS NULL 
            AND c.status = 'approuvee'
            ORDER BY c.datecreation DESC";

        $stm = $this->conn->prepare($sql);
        $stm->execute();
        return $stm->fetchAll(\PDO::FETCH_ASSOC);
    }
}