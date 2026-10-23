<?php

namespace App\Models;

use App\Core\Database;

class Comment extends BaseModel
{
    public function __construct(Database $db)
    {
        // Le digo a BaseModel que mi tabla es "comments"
        parent::__construct($db, 'comments');
    }

    // Devuelve los comentarios de UN anuncio, con el nombre de quien los escribió,
    // del más antiguo al más nuevo. Devuelve null si no tiene ninguno.
    public function findByPost(int $postId): ?array
    {
        return $this->db->fetchAll(
            'SELECT comments.*, users.username AS author
             FROM comments
             JOIN users ON comments.user_id = users.id
             WHERE comments.post_id = :post_id
             ORDER BY comments.created_at ASC, comments.id ASC',
            ['post_id' => $postId]
        );
    }
}