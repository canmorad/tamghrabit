<?php
namespace App\Controllers;

use App\Core\Controller;
class ChatController extends Controller{
     
public function __construct(){
    parent::__construct();
}
    public function index(){
       return $this->view('chat');
    }
}