<?php

// Cargo el autoload de Composer para que PHP encuentre mis clases solo
require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Router;
use App\Controllers\AuthController;
use App\Controllers\PostController;

// Activo las sesiones. Se hace una sola vez, aquí arriba, antes de escribir nada en pantalla
session_start();

// Creo el token CSRF una sola vez por sesión (lo usaremos en el Paso 6)
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$router = new Router();

// Rutas de autenticación: URL => [controlador, método]
$router->get('/login', [AuthController::class, 'showLoginForm']);
$router->post('/login', [AuthController::class, 'login']);
$router->get('/register', [AuthController::class, 'showRegisterForm']);
$router->post('/register', [AuthController::class, 'register']);
$router->post('/logout', [AuthController::class, 'logout']);

// Rutas de anuncios
$router->get('/posts', [PostController::class, 'index']);
$router->get('/posts/create', [PostController::class, 'create']);   // NUEVA: formulario
$router->post('/posts', [PostController::class, 'store']);          // NUEVA: guardar

// Saco solo la parte de la URL sin el ?id=... (por ejemplo "/login")
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/';
// Saco el tipo de petición: GET o POST
$method = $_SERVER['REQUEST_METHOD'];

// Si entran a la raíz "/", los mando al listado de anuncios
if ($path === '/') {
    header('Location: /posts');
    exit;
}

// Le paso la petición al router para que haga su trabajo
$router->dispatch($method, $path);