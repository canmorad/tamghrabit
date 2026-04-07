<?php

namespace App\Middleware;

use App\Core\Controller;
use App\Helpers\Session;

class AuthMiddleware extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }
    public function handle()
    {
        Session::start();

        if (!Session::get('user')) {
            return $this->view('auth/login');
            exit();
        }
    }
}
