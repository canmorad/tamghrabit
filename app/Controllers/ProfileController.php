<?php
namespace App\Controllers;
use App\Core\Controller;
use App\Helpers\Session;
use App\Helpers\Validator;
use App\Services\OrganisationService;
use App\Services\ProfileService;
use App\Core\Connection;
use Exception;

Session::start();
class ProfileController extends Controller
{
    private $profileServive;
    private $orgService;
    public function __construct()
    {
        parent::__construct();
        $this->orgService = new OrganisationService(Connection::getInstance());
        $this->profileServive = new ProfileService(Connection::getInstance());
    }
    public function edit()
    {
        $user = Session::get("user");
        $Organisations = $this->orgService->getOrgByUserId($user->getId());

        $this->view('profile/edit', [
            "user" => $user,
            "organisations" => $Organisations,
            'current_uri' => 'edit_profile'
        ]);
    }

    public function updateProfile()
    {
        header('Content-Type: application/json');
        $userSession = Session::get("user");

        $data = [
            'sexe' => $_POST['sexe'] ?? '',
            'nom' => $_POST['nom'] ?? '',
            'prenom' => $_POST['prenom'] ?? '',
            'email' => $_POST['email'],
            'dateNaissance' => $_POST['dateNaissance'] ?? '',
            'pays' => $_POST['pays'] ?? '',
            'telephoneCode' => $_POST['telephone'] ? $_POST['telephoneCode'] : '',
            'telephone' => $_POST['telephone'] ?? '',
            'ville' => $_POST['ville'] ?? '',
            'adresse' => $_POST['adresse'] ?? '',
        ];

        $validate = new Validator($data);

        $validate->field('nom')->required()->alpha([' ']);
        $validate->field('prenom')->required()->alpha([' ']);

        if (!$validate->isValid()) {
            echo json_encode([
                'type' => 'error',
                'message' => $validate->errorMessages
            ]);
            exit;
        }

        try {
            $userSession->setNom($data['nom']);
            $userSession->setPrenom($data['prenom']);
            $userSession->setDateNaissance($data['dateNaissance'] ?? '');
            $userSession->setPays($data['pays'] ?? '');
            $userSession->setTelephoneCode($data['telephoneCode'] ?? '');
            $userSession->setTelephone($data['telephone'] ?? '');
            $userSession->setVille($data['ville'] ?? '');
            $userSession->setAdresse($data['adresse'] ?? '');

            $this->profileServive->updateProfile($userSession);
            Session::set("user", $userSession);

            echo json_encode([
                'type' => 'success',
                'message' => 'Profil mis à jour avec succès'
            ]);

        } catch (Exception $e) {
            echo json_encode([
                'type' => 'error',
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function updateImageProfile()
    {
        header('Content-Type: application/json');
        $userSession = Session::get("user");
        $file = $_FILES['imageProfile'] ?? null;

        $validate = new Validator(['imageProfile' => $file]);
        $validate->field('imageProfile', "La photo")
            ->file_required()
            ->is_image()
            ->file_size(2 * 1024 * 1024);
        if (!$validate->isValid()) {
            echo json_encode(['type' => 'error', 'message' => $validate->errorMessages['imageProfile']]);
            exit;
        }

        try {
            $nomFichierUnique = $this->profileServive->updateImageProfile($userSession, $file);
            $userSession->setImageProfile($nomFichierUnique);
            Session::set("user", $userSession);

            echo json_encode(['type' => 'success', 'message' => "Photo mise à jour !"]);

        } catch (Exception $e) {
            echo json_encode(['type' => 'error', 'message' => $e->getMessage()]);
        }
    }

}