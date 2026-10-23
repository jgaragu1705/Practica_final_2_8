<?php

namespace App\Controllers;

use App\Core\Database;
use App\Models\Comment;
use App\Models\Post;

class PostController
{
    private Post $postModel;
    private Comment $commentModel;   // NUEVO: para cargar los comentarios de cada anuncio

    // Igual que en AuthController: el router no le pasa nada, así que lo creo aquí
    public function __construct()
    {
        $db = new Database();
        $this->postModel = new Post($db);
        $this->commentModel = new Comment($db);
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

    // Muestra un solo anuncio con sus comentarios. El id llega en la URL: /posts/show?id=3
    public function show(): void
    {
        // (int) convierte a número: si ponen letras, queda 0 y no se encuentra nada
        $id = (int) ($_GET['id'] ?? 0);

        $post = $this->postModel->findWithAuthor($id);

        // Si no existe un anuncio con ese id, error 404 y paro
        if ($post === null) {
            http_response_code(404);
            echo 'Anuncio no encontrado.';
            return;
        }

        // NUEVO: los comentarios de este anuncio (array vacío si no tiene ninguno)
        $comments = $this->commentModel->findByPost($id) ?? [];

        require __DIR__ . '/../Views/posts/show.php';
    }

    // Muestra el formulario para crear un anuncio nuevo
    public function create(): void
    {
        // Si no está logueado, no le dejo ni ver el formulario
        $this->requireLogin('Tienes que iniciar sesión para publicar un anuncio.');

        require __DIR__ . '/../Views/posts/create.php';
    }

    // Recibe el formulario de create() (llega por POST) y guarda el anuncio
    public function store(): void
    {
        // Hay que proteger también esta acción: alguien podría mandar el POST
        // a mano sin haber pasado por el formulario
        $this->requireLogin('Tienes que iniciar sesión para publicar un anuncio.');

        $title = trim($_POST['title'] ?? '');
        $body = trim($_POST['body'] ?? '');
        $category = trim($_POST['category'] ?? '');

        // validatePost devuelve un texto con el error, o null si todo está bien
        $error = $this->validatePost($title, $body, $category);
        if ($error !== null) {
            $_SESSION['error'] = $error;
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

    // Muestra el formulario de edición con los datos actuales del anuncio.
    // La URL es /posts/edit?id=3
    public function edit(): void
    {
        $id = (int) ($_GET['id'] ?? 0);

        // findOwnPost comprueba login, que exista y que sea mío. Si algo falla, no sigue.
        $post = $this->findOwnPost($id);

        require __DIR__ . '/../Views/posts/edit.php';
    }

    // Recibe el formulario de edit() (llega por POST) y modifica el anuncio
    public function update(): void
    {
        // El id viene de un campo oculto del formulario, así que HAY que volver a comprobar
        // que el anuncio es mío: ese campo se puede cambiar desde el navegador
        $id = (int) ($_POST['id'] ?? 0);
        $this->findOwnPost($id);

        $title = trim($_POST['title'] ?? '');
        $body = trim($_POST['body'] ?? '');
        $category = trim($_POST['category'] ?? '');

        $error = $this->validatePost($title, $body, $category);
        if ($error !== null) {
            $_SESSION['error'] = $error;
            header('Location: /posts/edit?id=' . $id);   // vuelvo al formulario de edición
            exit;
        }

        // Solo cambio estos tres campos. El user_id (el autor) no se toca nunca.
        $this->postModel->update($id, [
            'title'    => $title,
            'body'     => $body,
            'category' => $category,
        ]);

        $_SESSION['success'] = 'Anuncio actualizado correctamente.';
        header('Location: /posts/show?id=' . $id);
        exit;
    }

    // Borra un anuncio (llega por POST desde el botón de la vista de detalle)
    public function destroy(): void
    {
        $id = (int) ($_POST['id'] ?? 0);

        // Si el anuncio no es mío, findOwnPost corta aquí y no llega al delete
        $this->findOwnPost($id);

        $this->postModel->delete($id);

        $_SESSION['success'] = 'Anuncio eliminado.';
        header('Location: /posts');
        exit;
    }

    // Si no hay nadie logueado, lo mando al login con un mensaje y paro la ejecución
    private function requireLogin(string $mensaje): void
    {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = $mensaje;
            header('Location: /login');
            exit;   // sin este exit, el código de la acción seguiría ejecutándose
        }
    }

    // AUTORIZACIÓN. Devuelve el anuncio solo si hay alguien logueado,
    // el anuncio existe y el usuario logueado es su autor. Si no, responde con
    // el error que toque y PARA (exit), así quien la llame no necesita comprobar nada más.
    private function findOwnPost(int $id): array
    {
        // 1) ¿Está logueado?
        $this->requireLogin('Tienes que iniciar sesión para hacer esto.');

        // 2) ¿Existe el anuncio?
        $post = $this->postModel->getById($id);
        if ($post === null) {
            http_response_code(404);
            echo 'Anuncio no encontrado.';
            exit;
        }

        // 3) ¿Es suyo? Comparo el autor del anuncio con el usuario de la sesión.
        // (int) en los dos lados para comparar números con números.
        if ((int) $post['user_id'] !== (int) $_SESSION['user_id']) {
            http_response_code(403);   // 403 = Prohibido: existe, pero no tienes permiso
            echo 'No tienes permiso para hacer esto: este anuncio es de otro usuario.';
            exit;
        }

        return $post;
    }

    // Comprueba los datos de un anuncio. Devuelve un texto con el error,
    // o null si todo está bien. Lo usan store() y update() para no repetir código.
    private function validatePost(string $title, string $body, string $category): ?string
    {
        // Compruebo que no haya campos vacíos
        if ($title === '' || $body === '' || $category === '') {
            return 'Rellena el título, la categoría y el texto del anuncio.';
        }

        // Compruebo que no se pasen de largo (en la tabla: título 150 y categoría 50).
        // mb_strlen cuenta bien los caracteres con tilde o ñ.
        if (mb_strlen($title) > 150 || mb_strlen($category) > 50) {
            return 'El título admite 150 caracteres como máximo y la categoría 50.';
        }

        return null;
    }
}