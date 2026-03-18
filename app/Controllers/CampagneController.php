<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Helpers\Session;
class CampagneController extends Controller
{
    public function create()
    {
        return $this->view("adherent/campagne/create");
    }

    public function store(){
       Session::start();

       $data = [
          
       ];
    }
}