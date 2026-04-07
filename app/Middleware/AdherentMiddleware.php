<?php

namespace App\Middleware;

use App\Helpers\Session;

namespace App\Middleware;

use App\Helpers\Session;

class AdherentMiddleware
{
    public function handle()
    {
        Session::start();
        $user = Session::get('user');

        if (!$user) {
            header('Location: /login');
            exit();
        }

        if ($user->getRole()->role !== "adherent") {
            http_response_code(403);
            echo "Accès refusé : Réservé aux adhérents.";
            exit();
        }
    }
}
