<?php

namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use RuntimeException;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use RefreshDatabase;

    /**
     * RefreshDatabase runs migrate:fresh — never on the dev database.
     */
    protected function beforeRefreshingDatabase(): void
    {
        $database = (string) config('database.connections.'.config('database.default').'.database');

        if ($database !== 'hairdresser_test') {
            throw new RuntimeException(
                "Refusing migrate:fresh on [{$database}]. Tests must use hairdresser_test (run: make test)."
            );
        }
    }
}
