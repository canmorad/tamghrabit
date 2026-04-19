<?php
namespace App\Repositories;

class CampagneRepository
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function updateBase($campagne)
    {
        $sql = "update campagnes set 
                idCategorie = ?, titre = ?, description = ?, 
                image = ?, telephone = ?, dateDebut = ?, 
                dateFin = ?, justificatif = ? 
                where id = ?";
        $stm = $this->conn->prepare($sql);
        $stm->execute([
            $campagne->getCategorie()->getId(),
            $campagne->getTitre(),
            $campagne->getDescription(),
            $campagne->getImage(),
            $campagne->getTelephone(),
            $campagne->getDateDebut(),
            $campagne->getDateFin(),
            $campagne->getJustificatif(),
            $campagne->getId()
        ]);
    }

    public function updateFinanciere($campagne)
    {
        $sql = "update campagnesFinancieres set objectifMontant = ? where id = ?";
        $this->conn->prepare($sql)->execute([
            $campagne->getObjectifMontant(),
            $campagne->getId()
        ]);
    }

    public function updateParrainage($campagne)
    {
        $sql = "update campagnesParrainage set frequence = ? where id = ?";
        $this->conn->prepare($sql)->execute([
            $campagne->getFrequence(),
            $campagne->getId()
        ]);
    }

    public function updateAssociation($campagne)
    {
        $sql = "update campagnesAssociation set idOrganisation = ? where id = ?";
        $this->conn->prepare($sql)->execute([
            $campagne->getOrganisation()->getId(),
            $campagne->getId()
        ]);
    }

    public function updateNature($campagne)
    {
        $sql = "update campagnesNature set typeDon = ?, nomArticle = ? where id = ?";
        $this->conn->prepare($sql)->execute([
            $campagne->getTypeDon(),
            $campagne->getNomArticle(),
            $campagne->getId()
        ]);
    }

    public function getPendingCampagnes()
    {
        $sql = "select c.*, cat.nom as categorieNom, 
            cf.objectifMontant, cf.montantCollecte,
            cp.frequence,
            ca.idOrganisation, org.nom as organisationNom,
            cn.typeDon, cn.nomArticle, u.nom as nomAdherent, u.prenom as prenomAdherent, u.email as emailAdherent
            from campagnes c
            join categories cat on c.idCategorie = cat.id
            left join campagnesFinancieres cf on c.id = cf.id
            left join campagnesParrainage cp on cf.id = cp.id
            left join campagnesAssociation ca on cf.id = ca.id
            left join campagnesNature cn on c.id = cn.id
            left join organisations org on ca.idOrganisation = org.id
            join users u on c.idAdherent = u.id
            where c.dateSupprimer is null 
            and c.status = 'en_attente'
            order by c.dateCreation asc";

        $stm = $this->conn->prepare($sql);
        $stm->execute();
        return $stm->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function updateStatus($campagneId, $status)
    {
        $sql = "update campagnes set status = ?, dateModifier = now() where id = ?";
        return $this->conn->prepare($sql)->execute([$status, $campagneId]);
    }

    public function storeBase($campagne)
    {
        $sql = "insert into campagnes (idAdherent, idCategorie, titre, description, image, telephone, dateDebut, dateFin, justificatif, type) 
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
        $sql = "insert into campagnesFinancieres (id, objectifMontant) values (?, ?)";
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
        $sql = "insert into campagnesAssociation (id, idOrganisation) values (?, ?)";
        return $this->conn->prepare($sql)->execute([
            $campagne->getId(),
            $campagne->getOrganisation()->getId()
        ]);
    }

    public function storeNature($campagne)
    {
        $sql = "insert into campagnesNature (id, typeDon, nomArticle) values (?, ?, ?)";
        return $this->conn->prepare($sql)->execute([
            $campagne->getId(),
            $campagne->getTypeDon(),
            $campagne->getNomArticle()
        ]);
    }

    public function findById($id)
    {
        $sql = "select c.*, cat.nom as categorieNom, 
                cf.objectifMontant, cf.montantCollecte,
                cp.frequence,
                ca.idOrganisation, org.nom as organisationNom,
                cn.typeDon, cn.nomArticle, u.nom as nomDestinataire, u.prenom as prenomDestinataire, u.imageProfile, u.email
                from campagnes c
                join categories cat on c.idCategorie = cat.id
                left join campagnesFinancieres cf on c.id = cf.id
                left join campagnesParrainage cp on cf.id = cp.id
                left join campagnesAssociation ca on cf.id = ca.id
                left join campagnesNature cn on c.id = cn.id
                left join organisations org on ca.idOrganisation = org.id
                left join users u on c.idAdherent = u.id
                where c.id = ? and c.dateSupprimer is null";

        $stm = $this->conn->prepare($sql);
        $stm->execute([$id]);
        return $stm->fetch(\PDO::FETCH_ASSOC);
    }

    public function getActiveCampagnes()
    {
        $sql = "select c.*, cat.nom as categorieNom, 
            cf.objectifMontant, cf.montantCollecte,
            cn.typeDon, cn.nomArticle, -- هادو مهمين للـ Nature
            a.prenom as prenomAdherent, a.nom as nomAdherent, a.email as emailAdherent, a.imageProfile
            from campagnes c 
            join users a on c.idAdherent = a.id 
            join categories cat on c.idCategorie = cat.id
            left join campagnesFinancieres cf on c.id = cf.id
            left join campagnesNature cn on c.id = cn.id
            where c.status = 'approuvee' 
            order by c.dateCreation desc";

        return $this->conn->query($sql)->fetchAll();
    }

    public function getCampagnesByUser($userId)
    {
        $sql = "select c.*, cat.nom as categorieNom, 
                cf.objectifMontant, cf.montantCollecte,
                cp.frequence,
                ca.idOrganisation, org.nom as organisationNom,
                cn.typeDon, cn.nomArticle
                from campagnes c
                join categories cat on c.idCategorie = cat.id
                left join campagnesFinancieres cf on c.id = cf.id
                left join campagnesParrainage cp on cf.id = cp.id
                left join campagnesAssociation ca on cf.id = ca.id
                left join campagnesNature cn on c.id = cn.id
                left join organisations org on ca.idOrganisation = org.id
                where c.idAdherent = ? and c.dateSupprimer is null
                order by c.dateCreation desc";

        $stm = $this->conn->prepare($sql);
        $stm->execute([$userId]);
        return $stm->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function delete($id)
    {
        $sql = "update campagnes set dateSupprimer = now() where id = ?";
        $this->conn->prepare($sql)->execute([$id]);
    }

    public function terminerCampagne($id)
    {
        $sql = "update campagnes set status = 'terminee' where id = ?";
        $this->conn->prepare($sql)->execute([$id]);
    }

    public function findAll()
    {
        $sql = "select c.*, cat.nom as categorieNom, 
                cf.objectifMontant, cf.montantCollecte,
                cp.frequence,
                ca.idOrganisation, org.nom as organisationNom,
                cn.typeDon, cn.nomArticle, u.imageProfile, u.nom as nomDestinataire, u.prenom as prenomDestinataire
                from campagnes c
                join categories cat on c.idCategorie = cat.id
                left join campagnesFinancieres cf on c.id = cf.id
                left join campagnesParrainage cp on cf.id = cp.id
                left join campagnesAssociation ca on cf.id = ca.id
                left join campagnesNature cn on c.id = cn.id
                left join organisations org on ca.idOrganisation = org.id
                join users u on c.idAdherent = u.id
                where c.dateSupprimer is null 
                and c.status = 'approuvee'
                order by c.dateCreation desc";

        $stm = $this->conn->prepare($sql);
        $stm->execute();
        return $stm->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getDonorsByCampagne($campagneId)
    {
        $sql = "select d.*, u.nom, u.prenom 
                from donations d
                join users u on d.idAdherent = u.id
                where d.idCampagne = ? and d.status = 'complete'
                order by d.dateDon desc";
        $stm = $this->conn->prepare($sql);
        $stm->execute([$campagneId]);
        return $stm->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function findByIdForManage($id)
    {
        $sql = "select c.*, cat.nom as categorieNom, 
                cf.objectifMontant, cf.montantCollecte,
                cp.frequence,
                ca.idOrganisation, org.nom as nomOrg, org.ribAssociation,
                cn.typeDon, cn.nomArticle
                from campagnes c
                join categories cat on c.idCategorie = cat.id
                left join campagnesFinancieres cf on c.id = cf.id
                left join campagnesParrainage cp on cf.id = cp.id
                left join campagnesAssociation ca on cf.id = ca.id
                left join campagnesNature cn on c.id = cn.id
                left join organisations org on ca.idOrganisation = org.id
                where c.id = ? and c.dateSupprimer is null";

        $stm = $this->conn->prepare($sql);
        $stm->execute([$id]);
        return $stm->fetch(\PDO::FETCH_ASSOC);
    }
}