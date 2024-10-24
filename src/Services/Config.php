<?php

namespace App\Services;

use ArrayAccess;
use Exception;

class Config implements ArrayAccess
{
    private array $config = [];

    public function __construct()
    {
        $envs = [
            __DIR__ . '/../../.env',
            __DIR__ . '/../../.env.local',
        ];

        foreach ($envs as $env) {
            if (!file_exists($env)) {
                continue;
            }

            $lines = file($env, FILE_IGNORE_NEW_LINES & FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                [$key, $value] = explode('=', $line, 2);
                $this->config[trim($key)] = trim($value);
            }
        }
    }

    public function offsetExists(mixed $offset): bool
    {
        return isset($this->config[$offset]);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->config[$offset] ?? null;
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        throw new Exception('Config is read only');
    }

    public function offsetUnset(mixed $offset): void
    {
        throw new Exception('Config is read only');
    }
}
