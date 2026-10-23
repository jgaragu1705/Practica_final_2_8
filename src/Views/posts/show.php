<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($post['title']) ?></title>
</head>
<body>
    <?php require __DIR__ . '/../partials/header.php'; ?>

    <?php if (!empty($_SESSION['success'])): ?>
        <p style="color: green;"><?= htmlspecialchars($_SESSION['success']) ?></p>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (!empty($_SESSION['error'])): ?>
        <p style="color: red;"><?= htmlspecialchars($_SESSION['error']) ?></p>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <h1><?= htmlspecialchars($post['title']) ?></h1>

    <p>
        Categoría: <?= htmlspecialchars($post['category']) ?> |
        Publicado por <?= htmlspecialchars($post['author']) ?> |
        <?= htmlspecialchars($post['created_at']) ?>
    </p>

    <?php // Hasta el body, que es texto libre, pasa por htmlspecialchars ?>
    <p><?= htmlspecialchars($post['body']) ?></p>

    <?php // Solo el autor ve los botones. OJO: esto es solo comodidad visual;
          // la protección de verdad está en el controlador (findOwnPost). ?>
    <?php if (isset($_SESSION['user_id']) && (int) $_SESSION['user_id'] === (int) $post['user_id']): ?>
        <p>
            <a href="/posts/edit?id=<?= (int) $post['id'] ?>">✏️ Editar</a>

            <?php // Borrar va por POST, nunca por un enlace GET ?>
            <form action="/posts/delete" method="post" style="display:inline"
                  onsubmit="return confirm('¿Seguro que quieres eliminar este anuncio?');">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                <input type="hidden" name="id" value="<?= (int) $post['id'] ?>">
                <button type="submit">🗑️ Eliminar</button>
            </form>
        </p>
    <?php endif; ?>

    <hr>

    <?php // NUEVO: sección de comentarios ?>
    <h2>Comentarios (<?= count($comments) ?>)</h2>

    <?php if (empty($comments)): ?>
        <p>Todavía no hay comentarios.</p>
    <?php else: ?>
        <?php foreach ($comments as $comment): ?>
            <div>
                <p>
                    <strong><?= htmlspecialchars($comment['author']) ?></strong>
                    <small>(<?= htmlspecialchars($comment['created_at']) ?>)</small>
                </p>
                <p><?= htmlspecialchars($comment['body']) ?></p>
            </div>
            <hr>
        <?php endforeach; ?>
    <?php endif; ?>

    <?php // El formulario solo se muestra a quien está logueado ?>
    <?php if (isset($_SESSION['user_id'])): ?>
        <h3>Añadir un comentario</h3>
        <form action="/comments" method="post">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            <?php // Le digo al servidor en qué anuncio estoy comentando ?>
            <input type="hidden" name="post_id" value="<?= (int) $post['id'] ?>">

            <textarea name="body" rows="4" cols="50" maxlength="1000" required></textarea><br>
            <button type="submit">Comentar</button>
        </form>
    <?php else: ?>
        <p><a href="/login">Inicia sesión</a> para dejar un comentario.</p>
    <?php endif; ?>

    <p><a href="/posts">← Volver al listado</a></p>
</body>
</html>