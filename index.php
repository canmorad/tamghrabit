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
$router->get("explorer", ['App\Controllers\CampagneController', 'explorer'])->name('explorer');
$router->get("campagnes", ['App\Controllers\CampagneController', 'mesCampagnes'])->name('mes.ampagnes');
$router->get("campagne/show", ['App\Controllers\CampagneController', 'show']);

$router->get("admin/profile", ['App\Controllers\ProfileController', 'adminProfile']);
$router->get("profile/settings", ['App\Controllers\ProfileController', 'settings'])->name('settings');
$router->post("password/change", ['App\Controllers\ProfileController', 'updatePassword']);
$router->post("confirm/email", ['App\Controllers\ProfileController', 'confirmerMiseAjourEmail']);
$router->post("update/email", ['App\Controllers\ProfileController', 'demanderMiseAjourEmail']);



$router->get("profile/edit", ['App\Controllers\ProfileController', 'edit'])->name('profile.edit');
$router->post("profile/update", ['App\Controllers\ProfileController', 'updateProfile'])->name('profile.update');
$router->post("profile/image/update", ['App\Controllers\ProfileController', 'updateImageProfile'])->name('profile.update.image');

$router->post("identifier/update", ['App\Controllers\IdentifierController', 'update'])->name('identifier.update');
$router->get("identifier/show", ['App\Controllers\IdentifierController', 'show']);

$router->post("bank/update", ['App\Controllers\RibController', 'update'])->name('bank.update');
$router->get("bank/index", ['App\Controllers\RibController', 'index']);

$router->post("organisation/store", ['App\Controllers\OrganisationController', 'store']);
$router->post("organisation/update", ['App\Controllers\OrganisationController', 'update']);
$router->get("organisation/index", ['App\Controllers\OrganisationController', 'index']);
$router->get("organisation/get", ['App\Controllers\OrganisationController', 'show']);

$router->post("campagne/store/argent", ['App\Controllers\CampagneController', 'store']);
$router->post("campagne/store/nature", ['App\Controllers\CampagneController', 'store']);
$router->post("campagne/store/parrainage", ['App\Controllers\CampagneController', 'store']);
$router->post("campagne/store/association", ['App\Controllers\CampagneController', 'store']);

$router->get("messages/getOrCreate", ['App\Controllers\MessageController', 'getOrCreate']);
$router->get("messages/history", ['App\Controllers\MessageController', 'getHistory']);
$router->post("message/send", ['App\Controllers\MessageController', 'send']);
$router->get("chat", ['App\Controllers\MessageController', 'index'])->name('chat');
$router->get("conversation/delete", ['App\Controllers\MessageController', 'delete']);

$router->get("admin/users", ['App\Controllers\Auth\AuthenticatedSessionController', 'index'])->name('admin.users');

$router->get("admin/identities/pending", ['App\Controllers\IdentifierController', 'pending'])->name('admin.identities.pending');


$router->get("admin/organisation/pending", ['App\Controllers\OrganisationController', 'pending']);
$router->get("admin/ribs/pending", ['App\Controllers\RibController', 'pending']);

$router->post("admin/identities/verify", ['App\Controllers\IdentifierController', 'verify'])
    ->name('admin.identities.verify');


$router->get("admin/dashboard", ['App\Controllers\AdminController', 'dashboard']);
$router->get("support", ['App\Controllers\AdminController', 'support']);


$router->get('auth/google/redirect', ['App\Controllers\Auth\GoogleAuthController', 'redirectToGoogle']);
$router->get('auth/google/callback', ['App\Controllers\Auth\GoogleAuthController', 'handleGoogleCallback']);
$router->get("verify/email", ['App\Controllers\Auth\VerifyEmailController', 'verify'])->name('verify.email');




$router->dispatch($method, $url);

