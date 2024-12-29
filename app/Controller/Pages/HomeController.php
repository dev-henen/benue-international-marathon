<?php
namespace App\Controllers\Pages;

class HomeController
{
    public function index()
    {
        global $twig;
        echo $twig->render('home.twig');
    }
}
