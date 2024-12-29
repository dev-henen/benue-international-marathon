<?php
namespace App\Controllers\Pages;

class AboutController
{
    public function index()
    {
        global $twig;
        echo $twig->render('about-us.twig');
    }
}
