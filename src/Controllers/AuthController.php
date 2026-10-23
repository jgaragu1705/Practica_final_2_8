<?php

namespace App\Controllers;

class AuthController
{
    // De momento solo muestra un texto, para comprobar que el router funciona
    public function showLoginForm(): void
    {
        echo 'Aquí irá el formulario de login';
    }
}