<?php

namespace App\Controllers;

use App\Core\BaseController;
use App\Core\Database;
use App\Models\User;

// extends BaseController: heredo requireLogin() y checkCsrfToken()
class AuthController extends BaseController
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
        // Lo primero de todo: compruebo el token CSRF
        $this->checkCsrfToken();

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

    // Recibe el formulario de login (llega por POST)
    public function login(): void
    {
        // Lo primero de todo: compruebo el token CSRF
        $this->checkCsrfToken();

        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        // findByEmail me devuelve el id si email y contraseña son correctos, o null si no
        $userId = $this->userModel->findByEmail($email, $password);

        if ($userId === null) {
            // Mismo mensaje si falla el email o la contraseña, para no dar pistas
            $_SESSION['error'] = 'Email o contraseña incorrectos.';
            header('Location: /login');
            exit;
        }

        // Login correcto. Cambio el identificador de sesión por uno nuevo
        // (evita que alguien con el identificador antiguo herede esta sesión)
        session_regenerate_id(true);

        // Saco el email tal y como está guardado en la base de datos
        $usuario = $this->userModel->getById($userId);

        // Guardo en la sesión quién es. A partir de ahora, en cualquier página,
        // si existe $_SESSION['user_id'] sé que hay alguien logueado y quién es.
        $_SESSION['user_id'] = $userId;
        $_SESSION['user_email'] = $usuario['email'];

        // Ahora que ya existe el listado de anuncios, voy allí
        $_SESSION['success'] = 'Has iniciado sesión correctamente.';
        header('Location: /posts');
        exit;
    }

    // Cierra la sesión (llega por POST desde el botón de la cabecera)
    public function logout(): void
    {
        // Lo primero de todo: compruebo el token CSRF
        // (si no, cualquier web podría cerrarte la sesión sin que lo pidieras)
        $this->checkCsrfToken();

        // Borra todos los datos de la sesión (el id, el email...)
        session_destroy();

        // En la siguiente petición, index.php abrirá una sesión nueva
        // y creará un token CSRF nuevo
        header('Location: /login');
        exit;
    }
}