<?php
namespace App\Core;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFunction;
use App\Helpers\Session;
class Controller
{
    protected $twig;

    public function __construct()
    {
        $loader = new FilesystemLoader(__DIR__ . '/../../views');
        $this->twig = new Environment($loader);

        Session::start();

        $this->twig->addGlobal('currentUser', Session::get('user'));

        $this->twig->addFunction(new TwigFunction('url', 'url'));
        $this->twig->addFunction(new TwigFunction('route', 'route'));
        $this->twig->addFunction(new TwigFunction('basePath', 'basePath'));
        $this->twig->addFunction(
            new TwigFunction('session_errors', function () {
                $errors = Session::flush('errors');
                if (!$errors) {
                    return [];
                }
                return is_array($errors) ? $errors : [$errors];
            })
        );

        $this->twig->addFunction(
            new TwigFunction('old', function ($key) {
                return Session::old($key);
            })
        );

        $this->twig->addFunction(
            new TwigFunction('flush', function ($key) {
                return Session::flush($key);
            })
        );
    }

    protected function view($template, $data = [])
    {
        echo $this->twig->render($template . ".twig", $data);
    }

    protected function abort($message, $code = 404)
    {
        http_response_code($code);


        $titles = [
            404 => 'Page non trouvée',
            403 => 'Accès refusé',
            500 => 'Erreur interne du serveur',
            401 => 'Non autorisé'
        ];

        echo $this->twig->render("errors/layoutError.twig", [
            'code' => $code,
            'title' => $titles[$code] ?? 'Erreur',
            'message' => $message
        ]);
        exit();
    }
    protected function redirect($url)
    {
        header('Location: ' . $url);
        exit();
    }

}