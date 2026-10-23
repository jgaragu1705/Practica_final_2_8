<?php

namespace App\Models;

use App\Core\Database;

class User extends BaseModel
{
    public function __construct(Database $db)
    {
        // Le digo a BaseModel que mi tabla es "users"
        parent::__construct($db, 'users');
    }

    // Comprueba si el email y la contraseña son correctos.
    // Devuelve el id del usuario si lo son, o null si no.
    // OJO: $password es la contraseña que escribió el usuario en el formulario,
    // tal cual, sin encriptar. La comparación con el hash la hago aquí dentro.
    public function findByEmail(string $email, string $password): ?int
    {
        // Paso 1: busco al usuario SOLO por email (el hash no se puede buscar en el WHERE)
        $usuario = $this->db->fetchOne(
            'SELECT * FROM users WHERE email = :email',
            ['email' => $email]
        );

        // Si no hay ningún usuario con ese email, devuelvo null
        if ($usuario === null) {
            return null;
        }

        // Paso 2: password_verify compara la contraseña escrita con el hash guardado
        if (password_verify($password, $usuario['password'])) {
            return (int) $usuario['id'];   // todo correcto: devuelvo su id
        }

        // El email existía pero la contraseña no coincide
        return null;
    }
}