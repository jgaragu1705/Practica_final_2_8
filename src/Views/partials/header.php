<?php // Cabecera común. La incluyo con require en cada vista, justo después de <body> ?>
<nav>
    <?php if (isset($_SESSION['user_id'])): ?>
        <?php // Hay alguien logueado: muestro su email y el botón de cerrar sesión ?>
        <span>Conectado como <?= htmlspecialchars($_SESSION['user_email']) ?></span>

        <?php // El logout es un formulario POST (no un enlace) para que nadie pueda cerrarte la sesión con un simple link ?>
        <form action="/logout" method="post" style="display:inline">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            <button type="submit">Cerrar sesión</button>
        </form>
    <?php else: ?>
        <?php // No hay nadie logueado: enlaces para entrar o registrarse ?>
        <a href="/login">Iniciar sesión</a> |
        <a href="/register">Registrarse</a>
    <?php endif; ?>
</nav>
<hr>