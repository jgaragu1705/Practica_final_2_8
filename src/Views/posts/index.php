<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tablón de anuncios</title>
</head>
<body>
    <?php require __DIR__ . '/../partials/header.php'; ?>

    <h1>Tablón de anuncios</h1>

    <?php if (!empty($_SESSION['success'])): ?>
        <p style="color: green;"><?= htmlspecialchars($_SESSION['success']) ?></p>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (!empty($_SESSION['error'])): ?>
        <p style="color: red;"><?= htmlspecialchars($_SESSION['error']) ?></p>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <?php // Solo muestro el enlace de publicar si hay alguien logueado ?>
    <?php if (isset($_SESSION['user_id'])): ?>
        <p><a href="/posts/create">➕ Nuevo anuncio</a></p>
    <?php endif; ?>

    <?php // El buscador va por GET: así la búsqueda queda en la URL (/posts?q=libro) ?>
    <form action="/posts" method="get">
        <?php // value=... mantiene lo que se buscó dentro de la caja. htmlspecialchars SIEMPRE que se imprime algo del usuario ?>
        <input type="text" name="q" placeholder="Buscar por título o categoría" value="<?= htmlspecialchars($search) ?>">
        <button type="submit">Buscar</button>
    </form>

    <?php if (empty($posts)): ?>
        <p>No hay anuncios que mostrar.</p>
    <?php else: ?>
        <?php foreach ($posts as $post): ?>
            <article>
                <?php // El id es un número que viene de la base de datos, pero lo convierto a (int) igualmente por seguridad ?>
                <h2><a href="/posts/show?id=<?= (int) $post['id'] ?>"><?= htmlspecialchars($post['title']) ?></a></h2>
                <p>
                    Categoría: <?= htmlspecialchars($post['category']) ?> |
                    Publicado por <?= htmlspecialchars($post['author']) ?> |
                    <?= htmlspecialchars($post['created_at']) ?>
                </p>
                <p><?= htmlspecialchars($post['body']) ?></p>
            </article>
            <hr>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>