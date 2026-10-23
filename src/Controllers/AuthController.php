<?php

namespace App\Controllers;

class AuthController
{
    // Muestra la página con el formulario de login
    public function showLoginForm(): void
    {
        // __DIR__ es la carpeta de este archivo (src/Controllers),
        // así que subo un nivel y entro en Views/auth
        require __DIR__ . '/../Views/auth/login.php';
    }

    // Muestra la página con el formulario de registro
    public function showRegisterForm(): void
    {
        require __DIR__ . '/../Views/auth/register.php';
    }
}