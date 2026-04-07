<?php
namespace App\Controllers\Auth;

use App\Core\Controller;
use App\Helpers\Session;
use App\Services\AuthentificationService;
use App\Core\Connection;
use App\Entities\User;
use App\Entities\Adherent;

class AuthenticatedSessionController extends Controller
{
    private $authService;

    public function __construct()
    {
        parent::__construct();
        $this->authService = new AuthentificationService(Connection::getInstance());
    }

    public function index(){
        return $this->view('admin/users');
    }
    public function create()
    {
        return $this->view('auth/login');
    }

    public function store()
    {
        Session::start();

        $data = [
            'email' => $_POST['email'],
            'password' => $_POST['password'],
        ];

        try {
            $user = new User("", "", $data['email'], $data['password']);
            $foundUser = $this->authService->store($user);

            if (!$foundUser) {
                Session::flush("errors", "Email ou mot de passe incorrect");
                return $this->view('auth/login');
            }

            if ($foundUser) {
                if ($foundUser->getRole()->getNom() === "adherent") {
                    $adherentData = $this->authService->getAdherentByUserId($foundUser->getId());
                    $adherent = new Adherent(
                        $adherentData['nom'],
                        $adherentData['prenom'],
                        $adherentData['email'],
                        $adherentData['sexe'],
                        $adherentData['password'],
                    );
 
                    $adherent->setId($adherentData["id"]);
                    $adherent->setDateNaissance($adherentData["dateNaissance"]);
                    $adherent->setAdresse($adherentData["adresse"]);
                    $adherent->setTelephone($adherentData["telephone"]);
                    $adherent->setVille($adherentData["ville"]);
                    $adherent->setPays($adherentData["Pays"]);
                    $adherent->setImageProfile($adherentData["imageProfile"]);
                    $adherent->setRole($foundUser->getRole());

                    Session::set("user", $adherent);
                }else{
                    Session::set("user", $foundUser);
                }

                if ($foundUser->getRole()->getNom() === "admin") {
                    header("Location: admin/users");
                    exit;
                }

                if ($foundUser->getRole()->getNom() === "adherent") {
                    header("Location: profile/edit");
                    exit;
                }
            }
        } catch (\Exception $e) {
            Session::flush("errors", $e->getMessage());
            return $this->view('auth/login');
        }
    }
}