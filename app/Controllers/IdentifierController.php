<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Helpers\Session;
use App\Helpers\Validator;
use App\Entities\Identifier;
use App\Core\Connection;
use App\Services\IdentifierService;
use Exception;

Session::start();

class IdentifierController extends Controller
{
    private $identifierService;

    public function __construct()
    {
        parent::__construct();
        $this->identifierService = new IdentifierService(Connection::getInstance());
    }

    public function index()
    {
        $user = Session::get("user");
        $identifiers = $this->identifierService->getIdentifierByUserId($user->getId());

        echo json_encode([
            "cniRecto" => $identifiers->getCniRecto() ? url("public/storage/identifiers/" . $identifiers->getCniRecto()) : '',
            "cniVerso" => $identifiers->getCniVerso() ? url("public/storage/identifiers/" . $identifiers->getCniVerso()) : '',
            "passport" => $identifiers->getPassport() ? url("public/storage/identifiers/" . $identifiers->getPassport()) : '',
        ]);
    }

    public function update()
    {
        header('Content-Type: application/json');
        $userSession = Session::get("user");
        $idType = $_POST['idType'] ?? '';

        $data = [
            "cniRecto" => $_FILES["cniRecto"] ?? null,
            "cniVerso" => $_FILES["cniVerso"] ?? null,
            "passport" => $_FILES["passport"] ?? null,
        ];

        $validate = new Validator($data);

        if ($idType === 'cni') {
            $validate->field("cniRecto", "Carte Recto")->file_required()->is_image();
            $validate->field("cniVerso", "Carte Verso")->file_required()->is_image();
        } elseif ($idType === 'passport') {
            $validate->field("passport", "Passport")->file_required()->is_image();
        } else {
            echo json_encode(['type' => 'error', 'message' => "Veuillez choisir un type d'identité."]);
            exit;
        }

        if (!$validate->isValid()) {
            echo json_encode(['type' => 'error', 'message' => $validate->errorMessages]);
            exit;
        }

        try {
            $identifier = new Identifier($data["cniRecto"], $data["cniVerso"], $data["passport"]);
            $identifier->setAdherent($userSession);

            $this->identifierService->update($identifier);

            echo json_encode(['type' => 'success', 'message' => "Documents d'identité mis à jour avec succès !"]);
        } catch (Exception $e) {
            echo json_encode(['type' => 'error', 'message' => $e->getMessage()]);
        }
    }
}