<?php

namespace willvincent\Feeds\Tests;

use RuntimeException;
use willvincent\Feeds\Facades\FeedsFacade;
use willvincent\Feeds\FeedsFactory;

class FeedsServiceProviderTest extends TestCase
{
    public function test_it_registers_the_factory_as_a_singleton(): void
    {
        $factory = app(FeedsFactory::class);

        $this->assertInstanceOf(FeedsFactory::class, $factory);
        $this->assertSame($factory, app('Feeds'));
        $this->assertSame($factory, FeedsFacade::getFacadeRoot());
    }

    public function test_it_requires_the_feeds_configuration(): void
    {
        config()->set('feeds', null);

        $this->expectException(RuntimeException::class);

        app('Feeds');
    }
}
