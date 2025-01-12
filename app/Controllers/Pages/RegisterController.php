<?php
namespace App\Controllers\Pages;

class RegisterController
{
    public function index()
    {
        global $twig;
        echo $twig->render('register.twig', ['REGISTRATION_DETAILS' => REGISTRATION_DETAILS, 'PAYSTACK_PAYMENT_PUBLIC_KEY' => $_ENV['PAYSTACK_PAYMENT_PUBLIC_KEY']]);
    }
}
