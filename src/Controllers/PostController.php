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
}