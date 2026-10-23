<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión</title>
</head>
<body>
    <?php require __DIR__ . '/../partials/header.php'; ?>

    <h1>Iniciar sesión</h1>

    <?php // Si el controlador dejó un mensaje de error en la sesión, lo muestro y lo borro ?>
    <?php if (!empty($_SESSION['error'])): ?>
        <p style="color: red;"><?= htmlspecialchars($_SESSION['error']) ?></p>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <?php if (!empty($_SESSION['success'])): ?>
        <p style="color: green;"><?= htmlspecialchars($_SESSION['success']) ?></p>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <form action="/login" method="post">
        <?php // Campo oculto con el token CSRF (la comprobación la hacemos en el Paso 6) ?>
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

        <label>Email:
            <input type="email" name="email" required>
        </label><br>

        <label>Contraseña:
            <input type="password" name="password" required>
        </label><br>

        <button type="submit">Entrar</button>
    </form>

    <p>¿No tienes cuenta? <a href="/register">Regístrate</a></p>
</body>
</html>