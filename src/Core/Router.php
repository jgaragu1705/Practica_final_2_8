<?php

namespace App\Core;

class Router
{
    // Aquí guardo las rutas que existen. Quedará algo como:
    // ['GET' => ['/login' => [AuthController::class, 'showLoginForm']], 'POST' => [...]]
    private array $routes = [];

    // Registra una ruta que se pide por GET (escribir la URL o pulsar un enlace)
    public function get(string $path, array $action): void
    {
        $this->routes['GET'][$path] = $action;
    }

    // Registra una ruta que se pide por POST (enviar un formulario)
    public function post(string $path, array $action): void
    {
        $this->routes['POST'][$path] = $action;
    }

    // Busca la ruta pedida y ejecuta el método del controlador que le toca
    public function dispatch(string $method, string $path): void
    {
        // El ?? null significa: si esa ruta no existe en el array, $action vale null
        $action = $this->routes[$method][$path] ?? null;

        // Si no hay ruta registrada, mostramos un error 404 y paramos
        if ($action === null) {
            http_response_code(404);
            echo '404 - Página no encontrada';
            return;
        }

        // $action es un array de dos cosas: [clase del controlador, nombre del método]
        // Esta línea las separa en dos variables
        [$controllerClass, $methodName] = $action;

        // Creo el controlador (new + el nombre de la clase guardado en una variable)
        $controller = new $controllerClass();

        // Llamo al método cuyo nombre está guardado en la variable
        $controller->$methodName();
    }
}