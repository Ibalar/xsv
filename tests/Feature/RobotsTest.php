<?php

namespace Tests\Feature;

use Tests\TestCase;

class RobotsTest extends TestCase
{
    public function test_robots_uses_current_app_url_for_sitemap(): void
    {
        config([
            'app.url' => 'https://example.com',
        ]);

        $response = $this->get('/robots.txt');

        $response->assertOk();
        $response->assertHeader('Content-Type', 'text/plain; charset=UTF-8');
        $response->assertSee('User-agent: *', false);
        $response->assertSee('Disallow: /admin', false);
        $response->assertSee('Sitemap: https://example.com/sitemap.xml', false);
    }
}
