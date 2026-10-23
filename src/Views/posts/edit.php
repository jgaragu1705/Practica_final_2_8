<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar anuncio</title>
</head>
<body>
    <?php require __DIR__ . '/../partials/header.php'; ?>

    <h1>Editar anuncio</h1>

    <?php if (!empty($_SESSION['error'])): ?>
        <p style="color: red;"><?= htmlspecialchars($_SESSION['error']) ?></p>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <form action="/posts/update" method="post">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
        <?php // Le digo al servidor qué anuncio estoy editando ?>
        <input type="hidden" name="id" value="<?= (int) $post['id'] ?>">

        <?php // value="..." precarga el dato actual. Todo con htmlspecialchars. ?>
        <label>Título:
            <input type="text" name="title" maxlength="150" value="<?= htmlspecialchars($post['title']) ?>" required>
        </label><br>

        <label>Categoría:
            <input type="text" name="category" maxlength="50" value="<?= htmlspecialchars($post['category']) ?>" required>
        </label><br>

        <?php // En un textarea el valor va ENTRE las etiquetas, no en un atributo value ?>
        <label>Texto del anuncio:<br>
            <textarea name="body" rows="6" cols="50" required><?= htmlspecialchars($post['body']) ?></textarea>
        </label><br>

        <button type="submit">Guardar cambios</button>
    </form>

    <p><a href="/posts/show?id=<?= (int) $post['id'] ?>">← Cancelar</a></p>
</body>
</html>