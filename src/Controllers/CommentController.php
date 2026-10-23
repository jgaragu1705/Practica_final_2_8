<?php

namespace App\Controllers;

use App\Core\Database;
use App\Models\Comment;
use App\Models\Post;

class CommentController
{
    private Comment $commentModel;
    private Post $postModel;   // lo necesito para comprobar que el anuncio existe

    public function __construct()
    {
        $db = new Database();
        $this->commentModel = new Comment($db);
        $this->postModel = new Post($db);
    }

    // Recibe el formulario de comentar (llega por POST) y guarda el comentario
    public function store(): void
    {
        // Solo pueden comentar los usuarios logueados
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = 'Tienes que iniciar sesión para comentar.';
            header('Location: /login');
            exit;
        }

        // El id del anuncio viene del formulario (campo oculto). Se puede manipular,
        // así que compruebo que exista de verdad.
        $postId = (int) ($_POST['post_id'] ?? 0);
        $body = trim($_POST['body'] ?? '');

        if ($this->postModel->getById($postId) === null) {
            http_response_code(404);
            echo 'Anuncio no encontrado.';
            exit;
        }

        // Compruebo el texto del comentario. En los dos casos de error vuelvo al anuncio.
        if ($body === '') {
            $_SESSION['error'] = 'El comentario no puede estar vacío.';
            header('Location: /posts/show?id=' . $postId);
            exit;
        }

        if (mb_strlen($body) > 1000) {
            $_SESSION['error'] = 'El comentario admite 1000 caracteres como máximo.';
            header('Location: /posts/show?id=' . $postId);
            exit;
        }

        // Guardo el comentario. El user_id sale de la SESIÓN, nunca del formulario:
        // si no, cualquiera podría comentar haciéndose pasar por otro.
        $this->commentModel->insert([
            'post_id' => $postId,
            'user_id' => $_SESSION['user_id'],
            'body'    => $body,
        ]);

        $_SESSION['success'] = 'Comentario publicado.';
        header('Location: /posts/show?id=' . $postId);
        exit;
    }
}