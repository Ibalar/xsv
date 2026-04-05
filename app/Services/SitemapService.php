<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Page;
use App\Models\Product;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class SitemapService
{
    /**
     * @return Collection<int, array{loc: string, lastmod: string, changefreq: string, priority: string, images: list<string>}>
     */
    public function build(): Collection
    {
        return Cache::remember(
            config('sitemap.cache.key', 'sitemap.xml'),
            now()->addHours($this->resolveCacheTtlHours()),
            fn (): Collection => collect()
                ->merge($this->buildStaticUrls())
                ->merge($this->buildPageUrls())
                ->merge($this->buildCategoryUrls())
                ->merge($this->buildProductUrls())
                ->unique('loc')
                ->values()
        );
    }

    /**
     * @return Collection<int, array{loc: string, lastmod: string, changefreq: string, priority: string, images: list<string>}>
     */
    protected function buildStaticUrls(): Collection
    {
        $timestamp = now()->toAtomString();

        return collect([
            $this->makeUrl(route('home'), $timestamp, 'daily', '1.0'),
            $this->makeUrl(route('catalog.index'), $timestamp, 'daily', '0.9'),
            $this->makeUrl(route('contacts'), $timestamp, 'monthly', '0.6'),
            $this->makeUrl(route('privacy-policy'), $timestamp, 'yearly', '0.3'),
        ]);
    }

    /**
     * @return Collection<int, array{loc: string, lastmod: string, changefreq: string, priority: string, images: list<string>}>
     */
    protected function buildPageUrls(): Collection
    {
        return Page::query()
            ->where('is_active', true)
            ->orderBy('slug')
            ->get()
            ->map(fn (Page $page): array => $this->makeUrl(
                route('pages.show', ['slug' => $page->slug]),
                $this->formatLastModified($page->updated_at),
                'monthly',
                '0.7',
            ));
    }

    /**
     * @return Collection<int, array{loc: string, lastmod: string, changefreq: string, priority: string, images: list<string>}>
     */
    protected function buildCategoryUrls(): Collection
    {
        return Category::query()
            ->active()
            ->orderBy('parent_id')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->map(fn (Category $category): array => $this->makeUrl(
                route('catalog.show', ['path' => $category->getFullPath()]),
                $this->formatLastModified($category->updated_at),
                'weekly',
                '0.8',
            ));
    }

    /**
     * @return Collection<int, array{loc: string, lastmod: string, changefreq: string, priority: string, images: list<string>}>
     */
    protected function buildProductUrls(): Collection
    {
        return Product::query()
            ->active()
            ->orderBy('id')
            ->get()
            ->map(fn (Product $product): array => $this->makeUrl(
                route('products.show', ['slug' => $product->slug]),
                $this->formatLastModified($product->updated_at),
                'weekly',
                '0.7',
                $this->buildProductImageUrls($product),
            ));
    }

    /**
     * @return array{loc: string, lastmod: string, changefreq: string, priority: string, images: list<string>}
     */
    protected function makeUrl(
        string $loc,
        string $lastmod,
        string $changefreq,
        string $priority,
        array $images = [],
    ): array
    {
        return [
            'loc' => $loc,
            'lastmod' => $lastmod,
            'changefreq' => $changefreq,
            'priority' => $priority,
            'images' => array_values(array_unique($images)),
        ];
    }

    protected function formatLastModified(?Carbon $timestamp): string
    {
        return ($timestamp ?? now())->toAtomString();
    }

    protected function resolveCacheTtlHours(): int
    {
        return max(1, min(24, (int) config('sitemap.cache.ttl_hours', 6)));
    }

    /**
     * @return list<string>
     */
    protected function buildProductImageUrls(Product $product): array
    {
        $images = collect();

        $mainImage = Product::normalizeImagePath($product->getRawOriginal('image'));
        if (\is_string($mainImage) && $mainImage !== '') {
            $images->push($this->makeProductImageUrl($mainImage));
        }

        $gallery = $product->gallery;
        if (\is_array($gallery)) {
            foreach (Product::normalizeImagePath($gallery) as $image) {
                if (\is_string($image) && $image !== '') {
                    $images->push($this->makeProductImageUrl($image));
                }
            }
        }

        return $images
            ->filter(fn (mixed $url): bool => \is_string($url) && $url !== '')
            ->unique()
            ->values()
            ->all();
    }

    protected function makeProductImageUrl(string $image): string
    {
        return asset('storage/products/' . ltrim($image, '/'));
    }
}
