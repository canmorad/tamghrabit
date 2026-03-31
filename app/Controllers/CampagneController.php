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
class CampagneController extends Controller
{
    private $campagneService;

    public function __construct()
    {
        parent::__construct();
        $this->campagneService = new CampagneService(Connection::getInstance());
    }

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
        $uri = $_SERVER['REQUEST_URI'];
        $parts = explode('/', trim($uri, '/'));
        $type = end($parts);

        header('Content-Type: application/json');

        $adherent = Session::get('user');

        $data = [
            "titre" => $_POST['titre'] ?? '',
            "description" => $_POST['description'] ?? '',
            "telephone" => $_POST['telephone'] ?? '',
            "dateDebut" => $_POST['dateDebut'] ?? '',
            "dateFin" => $_POST['dateFin'] ?? '',
            "idCategorie" => $_POST['idCategorie'] ?? '',
            "image" => $_FILES['image'] ?? null,
            "justificatif" => $_FILES['justificatif'] ?? null,
            "idAdherent" => $adherent->getId()
        ];

        $categorie = new Categorie('');
        $categorie->setId($data['idCategorie']);

        $validateur = new Validator($data);
        $validateur->field("titre", "Titre")->required();
        $validateur->field("description", "Description")->required();
        $validateur->field("telephone", "Téléphone")->required();
        $validateur->field("dateDebut", "Date de début")->required();
        $validateur->field("dateFin", "Date de fin")->required();
        $validateur->field("idCategorie", "Catégorie")->required();
        $validateur->field("image", "Image")->file_required()->is_image();
        $validateur->field("justificatif", "Justificatif")->file_required();

        if (in_array($type, ['argent', 'parrainage', 'association'])) {
            $data['objectifMontant'] = $_POST['objectifMontant'] ?? '';
            $validateur->set('objectifMontant', $data['objectifMontant']);
            $validateur->field("objectifMontant", "Montant")->required();
        }

        if ($type === 'parrainage') {
            $data['frequence'] = $_POST['frequence'] ?? '';
            $validateur->set('frequence', $data['frequence']);
            $validateur->field("frequence", "Fréquence")->required();
        }

        if ($type === 'association') {
            $data['idOrganisation'] = $_POST['idOrganisation'] ?? '';
            $validateur->set('idOrganisation', $data['idOrganisation']);
            $validateur->field("idOrganisation", "Organisation")->required();
        }

        if ($type === 'nature') {
            $data['nomArticle'] = $_POST['nomArticle'] ?? '';
            $data['typeDon'] = $_POST['typeDon'] ?? '';
            $validateur->set('nomArticle', $data['nomArticle']);
            $validateur->set('typeDon', $data['typeDon']);
            $validateur->field("nomArticle", "Article")->required();
            $validateur->field("typeDon", "Type de don")->required();
        }

        if (!$validateur->isValid()) {
            header('Content-Type: application/json');
            echo json_encode(["type" => "error", "message" => $validateur->errorMessages]);
            exit;
        }

        $campagne = null;

        if ($type === 'parrainage') {
            $campagne = new CampagneParrainage($data['titre'], $data['description'], $data['image'], $data['telephone'], $data['dateDebut'], $data['dateFin'], $categorie, $adherent, $data['justificatif'], $data['objectifMontant'], $data['frequence']);
        } elseif ($type === 'association') {
            $organisation = new Organisation('', '', '', '', '', '', '', '', '', '');
            $organisation->setId($data['idOrganisation']);
            $campagne = new CampagneAssociation($data['titre'], $data['description'], $data['image'], $data['telephone'], $data['dateDebut'], $data['dateFin'], $categorie, $adherent, $data['justificatif'], $data['objectifMontant'], $organisation);
        } elseif ($type === 'argent') {
            $campagne = new CampagneArgent($data['titre'], $data['description'], $data['image'], $data['telephone'], $data['dateDebut'], $data['dateFin'], $categorie, $adherent, $data['justificatif'], $data['objectifMontant']);
        } elseif ($type === 'nature') {
            $campagne = new CampagneNature($data['titre'], $data['description'], $data['image'], $data['telephone'], $data['dateDebut'], $data['dateFin'], $categorie, $adherent, $data['justificatif'], $data['typeDon'], $data['nomArticle']);
        }

        try {
            $this->campagneService->store($campagne);
            echo json_encode(["type" => "success", "message" => "Campagne créée avec succès !"]);
        } catch (\Exception $e) {
            echo json_encode(["type" => "error", "message" => $e->getMessage()]);
        }
    }
}