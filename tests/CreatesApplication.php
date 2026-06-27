<?php

namespace Tests;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\DB;

trait CreatesApplication
{
    public function createApplication(): Application
    {
        $app = require __DIR__.'/../bootstrap/app.php';

        $app->make(Kernel::class)->bootstrap();

        // .env in Docker uses DB_DATABASE=hairdresser; tests must never RefreshDatabase on it.
        $app['config']->set('database.connections.mysql.database', 'hairdresser_test');
        $app['config']->set('database.connections.mysql.host', env('DB_HOST', 'mysql'));

        DB::purge('mysql');

        return $app;
    }
}
