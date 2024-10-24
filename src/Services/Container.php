<?php

namespace App\Services;

use ReflectionClass;
use ReflectionFunction;
use ReflectionMethod;

class Container
{
    private array $aliases = [];
    private array $services = [];
    private array $instances = [];

    public function __construct()
    {
        $this->add('container', $this);
    }

    public function add(string $name, object $service): void
    {
        $className = get_class($service);
        if (!isset($this->services[$className])) {
            $this->services[$className] = [
                'className' => get_class($service),
                'shared' => true,
            ];
        }

        $this->instances[$className] = $service;
        $this->aliases[$name] = $className;
    }

    public function configure(string $name, string $className, bool $shared = true): void
    {
        $this->services[$className] = [
            'className' => $className,
            'shared' => $shared,
        ];
        $this->aliases[$name] = $className;
    }

    public function get(string $name)
    {
        if (isset($this->services[$name])) {
            $serviceConfig = $this->services[$name];
        } elseif (isset($this->aliases[$name])) {
            $name = $this->aliases[$name];
            $serviceConfig = $this->services[$name];
        } else {
            throw new \Exception("Service $name not found");
        }

        if ($serviceConfig['shared'] && isset($this->instances[$name])) {
            return $this->instances[$name];
        }

        $reflection = new ReflectionClass($this->services[$name]['className']);

        $params = [];

        $constructor = $reflection->getConstructor();
        if ($constructor) {
            $params = $this->getDependenciesForMethod($constructor);
        }

        $object = new $serviceConfig['className'](...$params);
        if ($serviceConfig['shared']) {
            $this->instances[$name] = $object;
        }
        return $object;
    }

    public function getDependenciesForMethod(ReflectionMethod $method): array
    {
        $params = [];
        foreach ($method->getParameters() as $parameter) {
            $params[] = $this->get($parameter->getType()->getName());
        }
        return $params;
    }

    public function getDependenciesForCallable(callable $callable): array
    {
        $reflection = new ReflectionFunction($callable);
        $params = [];
        foreach ($reflection->getParameters() as $parameter) {
            $params[] = $this->get($parameter->getType()->getName());
        }
        return $params;
    }
}
