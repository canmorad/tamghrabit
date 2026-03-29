<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Helpers\Session;
use App\Core\Connection;
use App\Services\OrganisationService;
use App\Helpers\Validator;
use App\Entities\Organisation;
use Exception;

Session::start();

class OrganisationController extends Controller
{
    private $orgService;

    public function __construct()
    {
        parent::__construct();
        $this->orgService = new OrganisationService(Connection::getInstance());
    }

    public function index()
    {
        $user = Session::get('user');
        $organisationsList = $this->orgService->getOrgByUserId($user->getId());

        $orgs = [];

        if ($organisationsList) {
            foreach ($organisationsList as $row) {
                $orgs[] = [
                    "id" => $row["id"],
                    "nom" => $row["nom"],
                    "estVerifie" => $row["estVerifie"]
                ];
            }
        }

        echo json_encode([
            "orgs" => $orgs
        ]);
    }

    public function show()
    {
        $id = $_GET['id'] ;

        $organisation = $this->orgService->getOrgById($id);

        header('Content-Type: application/json');
        echo json_encode([
            'id' => $organisation['id'],
            'nom' => $organisation['nom'],
            'identifiantFiscal' => $organisation['identifiantFiscal'],
            'adresse' => $organisation['adresse'],
            'ribAssociation' => $organisation['ribAssociation'],
            'recepisse' => $organisation['recepisse'] ? url("public/storage/organisations/" . $organisation['recepisse']) : '',
            'pvElection' => $organisation['pvElection'] ? url("public/storage/organisations/" . $organisation['pvElection']) : '',
            'statuts' => $organisation['statuts'] ? url("public/storage/organisations/" . $organisation['statuts']) : '',
            'attestationRib' => $organisation['attestationRib'] ? url("public/storage/organisations/" . $organisation['attestationRib']) : '',
            'cniPresidentRecto' => $organisation['cniPresidentRecto'] ? url("public/storage/organisations/" . $organisation['cniPresidentRecto']) : '',
            'cniPresidentVerso' => $organisation['cniPresidentVerso'] ? url("public/storage/organisations/" . $organisation['cniPresidentVerso']) : '',
        ]);
    }

    public function create()
    {
        $userSession = Session::get('user');

        $data = [
            "nom" => $_POST['nom'] ?? '',
            "identifiantFiscal" => $_POST['identifiantFiscal'] ?? '',
            "adresse" => $_POST['adresse'] ?? '',
            "ribAssociation" => $_POST['ribAssociation'] ?? '',
            "recepisse" => $_FILES['recepisse'] ?? null,
            "pvElection" => $_FILES['pvElection'] ?? null,
            "statuts" => $_FILES['statuts'] ?? null,
            "attestationRib" => $_FILES['attestationRib'] ?? null,
            "cniPresidentRecto" => $_FILES['cniPresidentRecto'] ?? null,
            "cniPresidentVerso" => $_FILES['cniPresidentVerso'] ?? null
        ];

        $validate = new Validator($data);
        $validate->field("nom", "Nom de l'ONG")->required();
        $validate->field("identifiantFiscal", "Identifiant Fiscal")->required();
        $validate->field("adresse", "Adresse")->required();
        $validate->field("ribAssociation", "RIB")->required();


        if (!$validate->isValid()) {
            echo json_encode([
                "type" => "error",
                "message" => $validate->errorMessages
            ]);
            exit;
        }

        try {
            $org = new Organisation(
                $data['nom'],
                $data['identifiantFiscal'],
                $data['adresse'],
                $data['ribAssociation'],
                $data['recepisse'],
                $data['pvElection'],
                $data['statuts'],
                $data['attestationRib'],
                $data['cniPresidentRecto'],
                $data['cniPresidentVerso']
            );

            $org->setAdherent($userSession);

            $this->orgService->create($org);
            echo json_encode(['type' => 'success', 'message' => "Organisation enregistrées !"]);

        } catch (Exception $e) {
            echo json_encode([
                "type" => "error",
                "message" => $e->getMessage()
            ]);
        }
    }

    public function update()
    {

    }
}