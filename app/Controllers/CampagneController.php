<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Helpers\Session;
class CampagneController extends Controller
{
    public function create()
    {
        return $this->view("campagne/create");
    }

    public function store(){
      

    }
}