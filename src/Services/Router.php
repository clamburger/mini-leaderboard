<?php

namespace App\Services;

use Exception;
use ReflectionClass;
use ReflectionMethod;

class Router
{
    private array $routes = [];

    public function __construct(
        private readonly Container $container
    ) {
    }

    public function add(string $path, mixed $callback): void
    {
        $this->routes[$path] = $callback;
    }

    public function run(): void
    {
        $path = $_SERVER['REQUEST_URI'];

        foreach ($this->routes as $route => $callback) {
            if ($path !== $route) {
                continue;
            }

            if (is_callable($callback)) {
                $params = $this->container->getDependenciesForCallable($callback);
                $callback(...$params);
                exit;
            }

            if (is_array($callback)) {
                $controllerReflection = new ReflectionClass($callback[0]);
                $constructor = $controllerReflection->getConstructor();
                if ($constructor) {
                    $params = $this->container->getDependenciesForMethod($constructor);
                    $controller = new $callback[0](...$params);
                } else {
                    $controller = new $callback[0];
                }

                $method = new ReflectionMethod($controller, $callback[1]);
                $params = $this->container->getDependenciesForMethod($method);
                $controller->{$callback[1]}(...$params);
                exit;
            }

            throw new Exception('Route is not callable');
        }

        http_response_code(404);
    }
}
