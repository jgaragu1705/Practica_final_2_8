<?php

namespace App\Controllers;

use App\Core\Database;
use App\Models\Post;

class PostController
{
    private Post $postModel;

    // Igual que en AuthController: el router no le pasa nada, así que lo creo aquí
    public function __construct()
    {
        $db = new Database();
        $this->postModel = new Post($db);
    }

    // Muestra el listado de anuncios. Si en la URL viene ?q=algo, los filtra.
    public function index(): void
    {
        // El texto que escribió el usuario en el buscador (o '' si no escribió nada)
        $search = trim($_GET['q'] ?? '');

        // Si no hay resultados, search() devuelve null; lo cambio por un array vacío
        // para que la vista pueda hacer foreach sin problemas
        $posts = $this->postModel->search($search) ?? [];

        require __DIR__ . '/../Views/posts/index.php';
    }

    // Muestra el formulario para crear un anuncio nuevo
    public function create(): void
    {
        // Si no está logueado, no le dejo ni ver el formulario
        $this->requireLogin();

        require __DIR__ . '/../Views/posts/create.php';
    }

    // Recibe el formulario de create() (llega por POST) y guarda el anuncio
    public function store(): void
    {
        // Hay que proteger también esta acción: alguien podría mandar el POST
        // a mano sin haber pasado por el formulario
        $this->requireLogin();

        $title = trim($_POST['title'] ?? '');
        $body = trim($_POST['body'] ?? '');
        $category = trim($_POST['category'] ?? '');

        // Compruebo que no haya campos vacíos
        if ($title === '' || $body === '' || $category === '') {
            $_SESSION['error'] = 'Rellena el título, la categoría y el texto del anuncio.';
            header('Location: /posts/create');
            exit;
        }

        // Compruebo que no se pasen de largo (en la tabla: título 150 y categoría 50).
        // mb_strlen cuenta bien los caracteres con tilde o ñ.
        if (mb_strlen($title) > 150 || mb_strlen($category) > 50) {
            $_SESSION['error'] = 'El título admite 150 caracteres como máximo y la categoría 50.';
            header('Location: /posts/create');
            exit;
        }

        // Guardo el anuncio. El user_id sale de la SESIÓN, nunca del formulario,
        // porque un campo del formulario se puede manipular.
        $this->postModel->insert([
            'user_id'  => $_SESSION['user_id'],
            'title'    => $title,
            'body'     => $body,
            'category' => $category,
        ]);

        $_SESSION['success'] = 'Anuncio publicado correctamente.';
        header('Location: /posts');
        exit;
    }

    // Si no hay nadie logueado, lo mando al login con un mensaje y paro la ejecución
    private function requireLogin(): void
    {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = 'Tienes que iniciar sesión para publicar un anuncio.';
            header('Location: /login');
            exit;   // sin este exit, el código de la acción seguiría ejecutándose
        }
    }
}