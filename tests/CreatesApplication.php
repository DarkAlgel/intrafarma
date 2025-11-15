<?php

namespace Tests;

use Illuminate\Foundation\Application;

trait CreatesApplication
{
    public function createApplication(): Application
    {
        $app = require __DIR__.'/../bootstrap/app.php';
        $app->loadEnvironmentFrom('.env');
        $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
        return $app;
    }
}