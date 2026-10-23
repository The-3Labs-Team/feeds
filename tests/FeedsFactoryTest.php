<?php

namespace willvincent\Feeds\Tests;

use willvincent\Feeds\FeedsFactory;

class FeedsFactoryTest extends TestCase
{
    public function test_it_reads_the_items_of_a_feed(): void
    {
        $items = app('Feeds')->make($this->fixture('feed.xml'))->get_items();

        $this->assertCount(3, $items);
        $this->assertSame('Primo articolo', $items[0]->get_title());
        $this->assertSame('https://example.com/primo-articolo', $items[0]->get_permalink());
        $this->assertSame('Mario Rossi', $items[0]->get_author()->get_name());
        $this->assertSame('2026-10-22 10:00:00', $items[0]->get_date('Y-m-d H:i:s'));
    }

    public function test_it_returns_only_the_requested_number_of_items(): void
    {
        $items = app('Feeds')->make($this->fixture('feed.xml'))->get_items(0, 2);

        $this->assertSame(
            ['Primo articolo', 'Secondo articolo'],
            array_map(fn ($item) => $item->get_title(), $items),
        );
    }

    public function test_it_strips_the_configured_tags_and_attributes(): void
    {
        $content = app('Feeds')->make($this->fixture('feed.xml'))->get_item(0)->get_content();

        $this->assertStringContainsString('Testo del primo articolo', $content);
        $this->assertStringNotContainsString('<script', $content);
        $this->assertStringNotContainsString('style=', $content);
    }

    public function test_it_keeps_tags_and_attributes_when_stripping_is_disabled(): void
    {
        $factory = new FeedsFactory([...config('feeds'), 'strip_html_tags.disabled' => true]);

        $content = $factory->make($this->fixture('feed.xml'))->get_item(0)->get_content();

        $this->assertStringContainsString('<script', $content);
        $this->assertStringContainsString('style=', $content);
    }

    public function test_it_applies_the_cache_settings(): void
    {
        $factory = new FeedsFactory([...config('feeds'), 'cache.disabled' => false, 'cache.location' => '/tmp/feeds', 'cache.life' => 600]);

        $feed = $factory->make($this->fixture('feed.xml'));

        $this->assertSame('/tmp/feeds', $feed->cache_location);
        $this->assertSame(600, $feed->cache_duration);
    }

    public function test_it_applies_the_connection_settings(): void
    {
        $factory = new FeedsFactory([...config('feeds'), 'ssl_check.disabled' => true, 'user_agent' => 'Feeds test', 'curl.timeout' => 5]);

        $feed = $factory->make($this->fixture('feed.xml'));

        $this->assertSame('Feeds test', $feed->useragent);
        $this->assertSame(5, $feed->timeout);
        $this->assertFalse($feed->curl_options[CURLOPT_SSL_VERIFYPEER]);
        $this->assertFalse($feed->curl_options[CURLOPT_SSL_VERIFYHOST]);
    }
}
