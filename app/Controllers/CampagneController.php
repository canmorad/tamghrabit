<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Helpers\Session;
class CampagneController extends Controller
{
    public function index()
    {
        return $this->view("explorer/campagnes");
    }
    public function create()
    {
        return $this->view("campagne/create", [
            'current_uri' => 'create_campagne'
        ]);
    }

    public function store()
    {


    }
}