<?php

namespace willvincent\Feeds\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use willvincent\Feeds\FeedsServiceProvider;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            FeedsServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('feeds', [
            ...require __DIR__.'/../src/config/feeds.php',
            'cache.disabled' => true,
        ]);
    }

    /**
     * Absolute path of a feed stored in tests/fixtures.
     *
     * @param  string  $name
     * @return string
     */
    protected function fixture(string $name): string
    {
        return __DIR__.'/fixtures/'.$name;
    }
}
