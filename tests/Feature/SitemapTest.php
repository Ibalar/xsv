<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Page;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class SitemapTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'cache.default' => 'array',
            'sitemap.cache.ttl_hours' => 1,
        ]);

        Cache::flush();
    }

    public function test_sitemap_contains_only_public_urls_and_product_images(): void
    {
        $category = Category::query()->create([
            'name' => 'Category',
            'slug' => 'kategoriya',
            'is_active' => true,
        ]);

        $inactiveCategory = Category::query()->create([
            'name' => 'Hidden Category',
            'slug' => 'skrytaya-kategoriya',
            'is_active' => false,
        ]);

        Product::query()->create([
            'name' => 'Product',
            'slug' => 'tovar',
            'sku' => 'SKU00001',
            'category_id' => $category->id,
            'is_active' => true,
            'price' => 100,
            'image' => 'main.jpg',
            'gallery' => ['gallery-1.jpg', 'gallery-2.jpg'],
        ]);

        Product::query()->create([
            'name' => 'Hidden Product',
            'slug' => 'skrytyy-tovar',
            'sku' => 'SKU00002',
            'category_id' => $inactiveCategory->id,
            'is_active' => false,
            'price' => 100,
        ]);

        Page::query()->create([
            'title' => 'About',
            'slug' => 'o-kompanii',
            'is_active' => true,
            'in_menu' => true,
            'sort' => 10,
        ]);

        Page::query()->create([
            'title' => 'Draft',
            'slug' => 'chernovik',
            'is_active' => false,
            'in_menu' => false,
            'sort' => 20,
        ]);

        $response = $this->get(route('sitemap'));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/xml; charset=UTF-8');
        $response->assertSee(url('/'), false);
        $response->assertSee(route('catalog.index'), false);
        $response->assertSee(route('catalog.show', ['path' => $category->getFullPath()]), false);
        $response->assertSee(route('products.show', ['slug' => 'tovar']), false);
        $response->assertSee(route('pages.show', ['slug' => 'o-kompanii']), false);
        $response->assertSee('xmlns:image="http://www.google.com/schemas/sitemap-image/1.1"', false);
        $response->assertSee(asset('storage/products/main.jpg'), false);
        $response->assertSee(asset('storage/products/gallery-1.jpg'), false);
        $response->assertSee(asset('storage/products/gallery-2.jpg'), false);

        $response->assertDontSee(route('catalog.show', ['path' => $inactiveCategory->getFullPath()]), false);
        $response->assertDontSee(route('products.show', ['slug' => 'skrytyy-tovar']), false);
        $response->assertDontSee(route('pages.show', ['slug' => 'chernovik']), false);
        $response->assertDontSee(url('/search'), false);
        $response->assertDontSee(url('/checkout'), false);
    }

    public function test_sitemap_response_is_cached_until_content_changes(): void
    {
        $category = Category::query()->create([
            'name' => 'Category',
            'slug' => 'kategoriya',
            'is_active' => true,
        ]);

        Product::query()->create([
            'name' => 'First Product',
            'slug' => 'pervyy-tovar',
            'sku' => 'SKU00003',
            'category_id' => $category->id,
            'is_active' => true,
            'price' => 100,
        ]);

        $firstResponse = $this->get(route('sitemap'));
        $firstResponse->assertSee(route('products.show', ['slug' => 'pervyy-tovar']), false);

        Product::query()->create([
            'name' => 'Second Product',
            'slug' => 'vtoroy-tovar',
            'sku' => 'SKU00004',
            'category_id' => $category->id,
            'is_active' => true,
            'price' => 100,
        ]);

        $cachedResponse = $this->get(route('sitemap'));
        $cachedResponse->assertDontSee(route('products.show', ['slug' => 'vtoroy-tovar']), false);

        Product::query()->create([
            'name' => 'Third Product',
            'slug' => 'tretiy-tovar',
            'sku' => 'SKU00005',
            'category_id' => $category->id,
            'is_active' => true,
            'price' => 100,
        ]);

        $freshResponse = $this->get(route('sitemap'));
        $freshResponse->assertSee(route('products.show', ['slug' => 'tretiy-tovar']), false);
    }
}
