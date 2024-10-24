<?php

namespace App;

use App\Services\Config;
use App\Services\Container;
use App\Services\Database;
use App\Services\Router;

class Kernel
{
    private Container $container;

    public function __construct()
    {
        $this->container = new Container();
        $this->container->configure('config', Config::class);
        $this->container->configure('database', Database::class);
        $this->container->configure('router', Router::class);
    }

    public function getContainer(): Container
    {
        return $this->container;
    }
}
