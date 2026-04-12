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

$router->middleware('guest')->group(function ($router) {
    $router->get("login", ['App\Controllers\Auth\AuthenticatedSessionController', 'create'])->name('login.create');
    $router->post("login", ['App\Controllers\Auth\AuthenticatedSessionController', 'store'])->name('login.store');
    $router->get("register", ['App\Controllers\Auth\RegisteredUserController', 'create'])->name('register.create');
    $router->post("register", ['App\Controllers\Auth\RegisteredUserController', 'store'])->name('register.store');

    $router->get('auth/google/redirect', ['App\Controllers\Auth\GoogleAuthController', 'redirectToGoogle']);
    $router->get('auth/google/callback', ['App\Controllers\Auth\GoogleAuthController', 'handleGoogleCallback']);
});


$router->middleware('admin')->group(function ($router) {
    $router->get("admin/dashboard", ['App\Controllers\AdminController', 'dashboard']);
    $router->get("admin/users", ['App\Controllers\Auth\AuthenticatedSessionController', 'index'])->name('admin.users');
    $router->get("admin/identities/pending", ['App\Controllers\IdentifierController', 'pending'])->name('admin.identities.pending');
    $router->get("admin/organisation/pending", ['App\Controllers\OrganisationController', 'pending']);
    $router->get("admin/ribs/pending", ['App\Controllers\RibController', 'pending']);
    $router->post("admin/identities/verify", ['App\Controllers\IdentifierController', 'verify'])->name('admin.identities.verify');
    $router->get("admin/profile", ['App\Controllers\ProfileController', 'adminProfile']);
});


$router->middleware('adherent')->group(function ($router) {
    $router->get("adherent/dashboard", ['App\Controllers\AdherentDashboardController', 'dashboard'])->name('adherent.dashboard');
    $router->get("campagnes", ['App\Controllers\CampagneController', 'mesCampagnes'])->name('mes.ampagnes');
    $router->get("profile/edit", ['App\Controllers\ProfileController', 'edit'])->name('profile.edit');
});


$router->middleware('auth')->group(function ($router) {
    $router->get("profile/settings", ['App\Controllers\ProfileController', 'settings'])->name('settings');
    $router->post("profile/update", ['App\Controllers\ProfileController', 'updateProfile'])->name('profile.update');
    $router->get("chat", ['App\Controllers\MessageController', 'index'])->name('chat');
    $router->post("message/send", ['App\Controllers\MessageController', 'send']);

   
    $router->get("campagne/create", ['App\Controllers\CampagneController', 'create'])->name('campagne.create');
    $router->post("campagne/store/argent", ['App\Controllers\CampagneController', 'store']);
});

$router->get("accueil", ['App\Controllers\HomeController', 'index'])->name('accueil');
$router->get("explorer", ['App\Controllers\CampagneController', 'explorer'])->name('explorer');
$router->get("campagne/show", ['App\Controllers\CampagneController', 'show']);
$router->get("verify/email", ['App\Controllers\Auth\VerifyEmailController', 'verify'])->name('verify.email');

$router->dispatch($method, $url);

