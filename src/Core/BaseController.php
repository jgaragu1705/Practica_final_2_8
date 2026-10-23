<?php

namespace App\Core;

// abstract: no se usa directamente, solo sirve para que los controladores hereden de ella
abstract class BaseController
{
    // protected: la pueden usar las clases que heredan (AuthController, PostController...)

    // Si no hay nadie logueado, lo mando al login con un mensaje y paro la ejecución
    protected function requireLogin(string $mensaje): void
    {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = $mensaje;
            header('Location: /login');
            exit;   // sin este exit, el código de la acción seguiría ejecutándose
        }
    }

    // Comprueba el token CSRF. Hay que llamarlo AL PRINCIPIO de toda acción que reciba un POST.
    // Si el token no coincide, para en seco y no se ejecuta nada más de esa acción.
    protected function checkCsrfToken(): void
    {
        // El token que guardé en la sesión (index.php lo crea en cada sesión)
        $tokenSesion = $_SESSION['csrf_token'] ?? '';
        // El token que llega en el formulario
        $tokenFormulario = $_POST['csrf_token'] ?? '';

        // Rechazo la petición si:
        // - el token del formulario no es un texto (alguien puede mandar csrf_token[]=x
        //   y hash_equals daría un error), o
        // - no hay token en la sesión (así nunca comparamos "vacío con vacío), o
        // - los dos tokens no coinciden.
        // hash_equals compara en tiempo constante, para que nadie pueda deducir el token
        // midiendo cuánto tarda la respuesta.
        if (!is_string($tokenFormulario) || $tokenSesion === '' || !hash_equals($tokenSesion, $tokenFormulario)) {
            http_response_code(419);
            die('Token CSRF inválido. Recarga la página e inténtalo de nuevo.');
        }
    }
}