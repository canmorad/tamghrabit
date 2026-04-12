<?php
namespace App\Repositories;

class CategorieRepository
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function findAll()
    {
        $sql = "select id, nom from categories order by nom asc";
        $stm = $this->conn->prepare($sql);
        $stm->execute();
        
        return $stm->fetchAll(\PDO::FETCH_ASSOC);
    }
}