<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Helpers\Session;
use App\Entities\CampagneParrainage;
use App\Entities\CampagneAssociation;
use App\Entities\Organisation;
use App\Entities\CampagneArgent;
use App\Entities\CampagneNature;
use App\Services\CampagneService;
use App\Entities\Categorie;
use App\Core\Connection;
use App\Helpers\Validator;

Session::start();
class AdminController extends Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function dashboard()
    {
        return $this->view('admin/dashboard', [
            'current_uri' => 'dashboard'
        ]);
    }

    public function support()
    {
        return $this->view('admin/support', [
            'current_uri' => 'support'
        ]);
    }


}