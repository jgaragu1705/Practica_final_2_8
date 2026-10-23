<?php

namespace App\Controllers;

use App\Core\Database;
use App\Models\User;

class AuthController
{
    // Aquí guardo el modelo de usuarios para usarlo en todos los métodos
    private User $userModel;

    // El router hace "new AuthController()" sin pasarle nada,
    // así que el controlador crea aquí lo que necesita
    public function __construct()
    {
        $db = new Database();
        $this->userModel = new User($db);
    }

    // Muestra la página con el formulario de login
    public function showLoginForm(): void
    {
        require __DIR__ . '/../Views/auth/login.php';
    }

    // Muestra la página con el formulario de registro
    public function showRegisterForm(): void
    {
        require __DIR__ . '/../Views/auth/register.php';
    }

    // Recibe los datos del formulario de registro (llega por POST) y crea el usuario
    public function register(): void
    {
        // Recojo los datos. trim quita espacios al principio y al final.
        // El ?? '' hace que, si el campo no llega, valga '' en vez de dar error.
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';   // la contraseña no se recorta: los espacios cuentan

        // Compruebo que no haya campos vacíos
        if ($username === '' || $email === '' || $password === '') {
            $_SESSION['error'] = 'Rellena todos los campos.';
            header('Location: /register');   // vuelvo al formulario
            exit;                            // paro aquí para que no siga ejecutándose
        }

        // Compruebo que el email tenga forma de email (algo@algo.algo)
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = 'El email no es válido.';
            header('Location: /register');
            exit;
        }

        // Compruebo que ese email no esté ya registrado
        if ($this->userModel->emailExists($email)) {
            $_SESSION['error'] = 'Ya existe un usuario con ese email.';
            header('Location: /register');
            exit;
        }

        // Todo correcto: guardo el usuario.
        // password_hash encripta la contraseña: NUNCA se guarda en texto plano.
        $this->userModel->insert([
            'username' => $username,
            'email'    => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT),
        ]);

        // Dejo un mensaje de éxito (la vista de login lo mostrará) y mando al login
        $_SESSION['success'] = 'Cuenta creada correctamente. Ya puedes iniciar sesión.';
        header('Location: /login');
        exit;
    }
}