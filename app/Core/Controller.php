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
    }

    protected function view($template, $data = [])
    {
        echo $this->twig->render($template . ".twig", $data);
    }

}