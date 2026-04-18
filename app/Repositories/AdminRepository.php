<?php
namespace App\Repositories;

class AdminRepository
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function countUsers()
    {
        return $this->conn->query("select count(*) as total from users where idRole = 2")->fetch()['total'];
    }

    public function getCampagnesByType()
{
    $sql = "select type, count(*) as total 
            from campagnes 
            group by type";
    return $this->conn->query($sql)->fetchAll();
}

    public function countActiveCampagnes()
    {
        return $this->conn->query("select count(*) as total from campagnes where status = 'approuvee'")->fetch()['total'];
    }

    public function getTotalRevenues()
    {
        return $this->conn->query("select sum(montant) as total from donations where status = 'complete'")->fetch()['total'] ?? 0;
    }

    public function getRibValidationRate()
    {
        $total = $this->conn->query("select count(*) as total from ribs")->fetch()['total'];
        if ($total == 0)
            return 0;
        
        $approved = $this->conn->query("select count(*) as total from ribs where status = 'approuvee'")->fetch()['total'];
        return round(($approved / $total) * 100);
    }

    public function getMonthlyRevenues()
    {
        $sql = "select 
                    date_format(dateDon, '%b') as month, 
                    sum(montant) as amount 
                from donations 
                where status = 'complete' 
                group by month 
                order by dateDon asc 
                limit 6";
        return $this->conn->query($sql)->fetchAll();
    }

    public function getUserDistribution()
    {
        $sql = "select 
                    sum(case when estVerifie = 1 then 1 else 0 end) as actifs,
                    sum(case when estVerifie = 0 then 1 else 0 end) as en_attente
                from adherents";
        return $this->conn->query($sql)->fetch();
    }
}