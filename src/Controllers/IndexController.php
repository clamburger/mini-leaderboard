<?php

namespace App\Controllers;

use App\Services\Database;

class IndexController
{
    public function index(Database $database): void
    {
        echo 'Hello, world!';
    }
}
