<?php

use FastRoute\Dispatcher;

function dispatch($dispatcher, $httpMethod, $uri)
{
    $routeInfo = $dispatcher->dispatch($httpMethod, $uri);

    switch ($routeInfo[0]) {
        case Dispatcher::NOT_FOUND:
            http_response_code(404);
            echo "<h1>Error 404</h1><p>Página no encontrada</p>";
            break;

        case Dispatcher::METHOD_NOT_ALLOWED:
            http_response_code(405);
            echo "<h1>Error 405</h1><p>Método no permitido</p>";
            break;

        case Dispatcher::FOUND:
            [$handler, $vars] = [$routeInfo[1], $routeInfo[2]];
            [$controllerName, $method] = explode("@", $handler);

            $controller = createController($controllerName);
            echo call_user_func_array([$controller, $method], $vars);
            break;
    }
}

function createController($name)
{
    $controllers = [
        'HomeController' => 'Shared\Presentation\HomeController',
        'AuthController' => 'Usuarios\Presentation\AuthController',
    ];

    $class = $controllers[$name] ?? null;

    if (!$class || !class_exists($class)) {
        throw new Exception("Controller not found: {$name}");
    }

    if ($name === 'AuthController') {
        $db = \Shared\Infrastructure\Database\Database::getInstance();
        $userRepo = new \Usuarios\Infrastructure\UserRepository($db->getConnection());
        $userService = new \Usuarios\Application\UserService($userRepo);
        $authService = new \Usuarios\Application\AuthService($userRepo, $userService);
        return new $class($authService);
    }

    return new $class();
}
