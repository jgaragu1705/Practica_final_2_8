-- Primero borro las tablas si ya existen, así puedo importar este archivo
-- las veces que quiera sin que me dé error de "la tabla ya existe".
-- OJO con el orden: tengo que borrar primero comments, luego posts y al final users.
-- Si borrara users antes, fallaría, porque posts y comments dependen de ella.
DROP TABLE IF EXISTS comments;
DROP TABLE IF EXISTS posts;
DROP TABLE IF EXISTS users;

-- TABLA USERS: aquí guardo a las personas que se registran
CREATE TABLE users (
    -- id: el número que identifica a cada usuario. AUTO_INCREMENT hace que
    -- MySQL lo ponga solo (1, 2, 3...) y PRIMARY KEY quiere decir que no se puede repetir
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,      -- NOT NULL = este campo es obligatorio
    -- UNIQUE hace que no puedan existir dos usuarios con el mismo email
    email VARCHAR(100) NOT NULL UNIQUE,
    -- Aquí NO va la contraseña tal cual, va el hash que genera password_hash().
    -- Pongo 255 porque ese hash es largo y con menos podría cortarse
    password VARCHAR(255) NOT NULL,
    -- Se rellena solo con la fecha y hora en que se crea el usuario
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
-- (InnoDB es el tipo de tabla que permite las claves foráneas, y utf8mb4
-- sirve para poder guardar tildes, ñ y emojis sin que salgan símbolos raros)

-- TABLA POSTS: los anuncios que publica la gente
CREATE TABLE posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    -- user_id guarda el id del usuario que escribió el anuncio.
    -- Así sé de quién es cada post (y luego comprobar si puede editarlo o borrarlo)
    user_id INT NOT NULL,
    title VARCHAR(150) NOT NULL,
    body TEXT NOT NULL,                 -- TEXT es para textos largos, VARCHAR se queda corto
    category VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    -- CLAVE FORÁNEA: obliga a que user_id sea el id de un usuario que existe de verdad.
    -- ON DELETE CASCADE: si se borra el usuario, sus posts se borran solos
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- TABLA COMMENTS: los comentarios dentro de cada anuncio
CREATE TABLE comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    post_id INT NOT NULL,               -- en qué anuncio está este comentario
    user_id INT NOT NULL,               -- quién lo escribió
    body TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    -- Dos claves foráneas porque el comentario está unido a DOS cosas:
    -- si se borra el post, se borran sus comentarios;
    -- si se borra el usuario, se borran los comentarios que escribió
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;