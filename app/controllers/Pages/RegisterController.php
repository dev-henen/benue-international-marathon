<?php
namespace App\Controllers\Pages;

class RegisterController
{
    public function index()
    {
        global $twig;
        echo $twig->render('register.twig', );
    }
}
