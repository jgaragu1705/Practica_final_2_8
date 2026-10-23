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

    <p><a href="/posts">← Volver al listado</a></p>
</body>
</html>