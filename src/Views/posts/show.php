<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($post['title']) ?></title>
</head>
<body>
    <?php require __DIR__ . '/../partials/header.php'; ?>

    <h1><?= htmlspecialchars($post['title']) ?></h1>

    <p>
        Categoría: <?= htmlspecialchars($post['category']) ?> |
        Publicado por <?= htmlspecialchars($post['author']) ?> |
        <?= htmlspecialchars($post['created_at']) ?>
    </p>

    <?php // Hasta el body, que es texto libre, pasa por htmlspecialchars ?>
    <p><?= htmlspecialchars($post['body']) ?></p>

    <p><a href="/posts">← Volver al listado</a></p>
</body>
</html>