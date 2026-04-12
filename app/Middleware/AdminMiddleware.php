<?php

namespace App\Middleware;

use App\Helpers\Session;

namespace App\Middleware;

use App\Helpers\Session;

class AdminMiddleware
{
    public function handle()
    {
        Session::start();
        $user = Session::get('user');

        if (!$user) {
            header('Location: /login');
            exit();
        }

        if ($user->getRole()->getNom() !== "admin") {
            http_response_code(403);
            echo "Accès refusé : Réservé aux administrateurs.";
            exit();
        }
    }
}
