<?php

namespace App\Models;

use App\Core\Database;

class Post extends BaseModel
{
    public function __construct(Database $db)
    {
        // Le digo a BaseModel que mi tabla es "posts"
        parent::__construct($db, 'posts');
    }

    // Devuelve los anuncios con el nombre de su autor, del más nuevo al más antiguo.
    // Si me pasan un texto, solo devuelve los que lo tengan en el título o en la categoría.
    // Devuelve null si no hay ninguno.
    public function search(string $term = ''): ?array
    {
        // JOIN: junto cada post con su autor (el user cuyo id es el user_id del post)
        $sql = 'SELECT posts.*, users.username AS author
                FROM posts
                JOIN users ON posts.user_id = users.id';
        $params = [];

        // Si han escrito algo en el buscador, añado el filtro
        if ($term !== '') {
            // Uso dos marcadores distintos (:term1 y :term2) porque cada uno
            // solo se puede usar una vez en la consulta
            $sql .= ' WHERE posts.title LIKE :term1 OR posts.category LIKE :term2';
            // Los % van en el valor, no en el SQL: significa "lo que sea antes y lo que sea después"
            $params = [
                'term1' => '%' . $term . '%',
                'term2' => '%' . $term . '%',
            ];
        }

        // Los más nuevos primero
        $sql .= ' ORDER BY posts.created_at DESC';

        return $this->db->fetchAll($sql, $params);
    }

    // NUEVO: devuelve UN anuncio con el nombre de su autor, o null si no existe
    public function findWithAuthor(int $id): ?array
    {
        return $this->db->fetchOne(
            'SELECT posts.*, users.username AS author
             FROM posts
             JOIN users ON posts.user_id = users.id
             WHERE posts.id = :id',
            ['id' => $id]
        );
    }
}