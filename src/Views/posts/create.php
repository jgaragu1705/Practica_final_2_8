<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo anuncio</title>
</head>
<body>
    <?php require __DIR__ . '/../partials/header.php'; ?>

    <h1>Nuevo anuncio</h1>

    <?php if (!empty($_SESSION['error'])): ?>
        <p style="color: red;"><?= htmlspecialchars($_SESSION['error']) ?></p>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <form action="/posts" method="post">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

        <label>Título:
            <input type="text" name="title" maxlength="150" required>
        </label><br>

        <label>Categoría:
            <input type="text" name="category" maxlength="50" required>
        </label><br>

        <label>Texto del anuncio:<br>
            <textarea name="body" rows="6" cols="50" required></textarea>
        </label><br>

        <button type="submit">Publicar</button>
    </form>

    <p><a href="/posts">← Volver al listado</a></p>
</body>
</html>