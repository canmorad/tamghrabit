<?php
namespace App\Controllers\Auth;
use App\Core\Controller;
use App\Helpers\Session;
use App\Entities\Adherent;
use App\Entities\Role;
use App\Core\Connection;
use App\Services\RegisterService;
use App\Helpers\Validator;
use Exception;

class RegisteredUserController extends Controller
{
    private $registerService;
    public function __construct()
    {
        parent::__construct();
        $this->registerService = new RegisterService(Connection::getInstance());
    }
    public function create()
    {
        return $this->view('auth/register');
    }

    public function store()
    {
        Session::start();

        $data = [
            'sexe' => $_POST['genre'] ?? '',
            'nom' => $_POST['nom'],
            'prenom' => $_POST['prenom'],
            'email' => $_POST['email'],
            'dateNaissance' => $_POST['dateNaissance'],
            'password' => $_POST['password'],
            'confirmePassword' => $_POST['confirmePassword'],
        ];

        $validate = new Validator($data);

        $validate->field('sexe')->required();
        $validate->field('nom')->required()->alpha([' ']);
        $validate->field('prenom')->required()->alpha([' ']);
        $validate->field('email')->required()->email();
        $validate->field('dateNaissance', 'Date de naissance')->required()->date();
        $validate->field('password', 'Mot de passe')->required()->min_len(8);
        $validate->field('confirmePassword', 'Confirmer le mot de passe')->required()->equals($data['password']);


        if (!$validate->isValid()) {
            Session::flush('errors', $validate->errorMessages);
            return $this->view('auth/register');
        } else {
            $adherent = new Adherent($data['nom'], $data['prenom'], $data['email'], $data['sexe'], $data['password']);
            $adherent->setDateNaissance($data['dateNaissance']);
            $role = new Role('adherent');
            $adherent->setRole($role);

            try {
                $this->registerService->store($adherent);
                Session::flush('success', 'Compte créé avec succès !');
                return $this->view('auth/login');

            } catch (Exception $e) {
                Session::flush('errors', $e->getMessage());
                return $this->view('auth/register');
                exit();
            }
        }
    }
}