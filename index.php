<?php
require_once 'vendor/autoload.php';

use App\Core\Router;

const BASE_PATH = __DIR__ . '/';
const BASE_URL = '/Tamghrabit/';

require_once "./app/Helpers/helpers.php";

$request = $_SERVER['REQUEST_URI'];

$script_name = dirname($_SERVER['SCRIPT_NAME']);

$url = str_replace($script_name, '', $request);

$url = parse_url($url, PHP_URL_PATH);

$url = trim($url, '/');

$method = $_SERVER['REQUEST_METHOD'];

$router = Router::getRouter();
$router->get("accueil", ['App\Controllers\HomeController', 'index'])->name('accueil');

$router->get("adherent/dashboard", ['App\Controllers\AdherentDashboardController', 'dashboard'])->name('adherent.dashboard');

$router->get("login", ['App\Controllers\Auth\AuthenticatedSessionController', 'create'])->name('login.create');
$router->post("login", ['App\Controllers\Auth\AuthenticatedSessionController', 'store'])->name('login.store');
$router->get("register", ['App\Controllers\Auth\RegisteredUserController', 'create'])->name('register.create');
$router->post("register", ['App\Controllers\Auth\RegisteredUserController', 'store'])->name('register.store');

$router->get("campagne/create", ['App\Controllers\CampagneController', 'create'])->name('campagne.create');
$router->get("explorer", ['App\Controllers\CampagneController', 'index'])->name('explorer');

$router->get("profile/edit", ['App\Controllers\ProfileController', 'edit'])->name('profile.edit');
$router->post("profile/update", ['App\Controllers\ProfileController', 'updateProfile'])->name('profile.update');
$router->post("profile/image/update", ['App\Controllers\ProfileController', 'updateImageProfile'])->name('profile.update.image');

$router->post("identifier/update", ['App\Controllers\IdentifierController', 'update'])->name('identifier.update');

$router->get("identifier/index", ['App\Controllers\IdentifierController', 'index']);
$router->post("bank/update", ['App\Controllers\RibController', 'update'])->name('bank.update');
$router->get("bank/index", ['App\Controllers\RibController', 'index']);

$router->post("organisation/store", ['App\Controllers\OrganisationController', 'store']);
$router->post("organisation/update", ['App\Controllers\OrganisationController', 'update']);
$router->get("organisation/index", ['App\Controllers\OrganisationController', 'index']);
$router->get("organisation/get", ['App\Controllers\OrganisationController', 'show']);

$router->dispatch($method, $url);

