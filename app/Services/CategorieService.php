<?php
namespace App\Services;

use App\Repositories\CategorieRepository;
use App\Entities\Categorie;

class CategorieService
{
    private $categorieRepo;

    public function __construct($conn)
    {
        $this->categorieRepo = new CategorieRepository($conn);
    }

    public function getAllCategories()
    {
        $data = $this->categorieRepo->findAll();
        $categories = [];

        foreach ($data as $row) {
            $cat = new Categorie($row['nom']);
            $cat->setId($row['id']);
            $categories[] = $cat;
        }

        return $categories;
    }
}